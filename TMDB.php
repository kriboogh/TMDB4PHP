<?php
require_once(__DIR__ . '/Asset.php');
require_once(__DIR__ . '/Movie.php');
require_once(__DIR__ . '/Person.php');
require_once(__DIR__ . '/Collection.php');
require_once(__DIR__ . '/Company.php');
require_once(__DIR__ . '/Genre.php');

class TMDB {

  protected $api_url = "http://api.themoviedb.org";
  protected $api_version = "3";

  public $configuration;

  public $api_key = 'invalid';
  public $adult = false;
  public $language = 'en';

  public $paged = true;
  public $error;
  public $response; // last response result;

  public $debug = false;

  /**
   * SINGLETON (with enheritance support)
   */
  protected static $instance;

  final public static function getInstance($api_key = null) {
    $class = static::getClass();
    if (!isset(static::$instance[$class])) {
      static::$instance[$class] = new $class($api_key);
    }
    return static::$instance[$class];
  }

  final public static function getClass() {
    return get_called_class();
  }

  /**
   * Constructor
   */

  public function __construct($api_key = null) {
    if (!empty($api_key)) {
      $this->api_key = $api_key;
      $this->configuration();
    }
  }

  /**
   * SEARCHING
   */

  public function search($type, $params, $expand = false) {
    $results = array();

    $response = $this->send_request('search/' . $type, $params);
    if (!$response->error) {

      $asset_class = ucfirst($type); // NOTE: As long as we can map the methods to the class name, this works...
      $results = array();
      foreach ($response->data->results as $asset) {
        if ($expand) {
          $info = $this->info($type, $asset->id);
          if ($info) {
            $asset = $info;
          }
        }
        $results[$asset->id] = new $asset_class($asset);
      }

    }
    else {
      $this->error = $response->error;
    }

    return $results;
  }

  /**
   * CONFIGURATION
   */

  public function configuration() {

    if (!isset($this->configuration)) {
      $response = $this->send_request('configuration');
      if (!$response->error) {
        $this->configuration = $response->data;
      }
      else {
        $this->error = $response->error;
      }
    }

    return $this->configuration;
  }

  /**
   * Asset information API
   */

  public function info($type, $id, $method = false, $params = array()) {
    $result = array();
    if ($method) {
      $response = $this->send_request($type . '/' . $id . '/' . $method, $params);
    }
    else {
      $response = $this->send_request($type . '/' . $id);
    }
    if (!$response->error) {
      $result = $response->data;
    }
    else {
      $this->error = $response->error;
    }

    return $result;
  }

  /**
   * Sending requests to TMDB
   *
   * @returns a response array object containing:
   *  - headers -> response http headers
   *  - data -> json decoded data
   *  - error -> error code and message
   */

  private function send_request($method, $params = array(), $data = array()) {

    $response = new stdClass();

    $params = $this->params_merge($params);

    $query = http_build_query($params);

    $url = $this->api_url . '/' . $this->api_version . '/' . $method . '?' . $query;

    // Initializing curl
    $ch = curl_init();
    if ($ch) {
      curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FAILONERROR => true,
        CURLOPT_HTTPHEADER => array(
          'Accept: application/json',
          'Content-type: application/json'
        ),
      ));
      if ($this->debug) {
        error_log("DEBUG: Calling URL: {$url}");
      }
      if (!empty($data) && is_array($data)) {
        if ($this->debug) {
          error_log("DEBUG: POSTDATA: " . var_export($data, true));
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      }

      $results = curl_exec($ch);

      $response->headers = curl_getinfo($ch);

      if ($results) {
        $response->data = json_decode($results);

        if (!$this->paged && isset($response->data->total_pages) && $response
          ->data->page < $response->data->total_pages) {
          $paged_response = $this
            ->send_request($method, $params + array(
              'page' => $response->data->page + 1
            ));

          if (!$paged_response->error) {
            $response->data->page = 1;
            $response->data->results = array_merge($response->data->results, $paged_response
              ->data->results);
            $response->data->total_pages = 1;
          }
          else {
            $results = array();
            $this->error = $response->error;
            curl_close($ch);
            return $results;
          }
        }

        $response->error = false;
      }
      else {
        $response->data = false;
        $response->error = array(
          'code' => curl_errno($ch),
          'message' => curl_error($ch)
        );
      }

      curl_close($ch);

    }
    else {
      $response->error = array(
        'code' => -1,
        'message' => 'Failed to init CURL'
      );
    }

    $this->response = $response;

    return $response;
  }

  /**
   * @param type - backdrop, profile, poster, logo,....
   * @param width - mixed, string it is used as a preset, if number it will use the closest bigger preset to this width
   * @link http://help.themoviedb.org/kb/api/configuration
   */

  public function image_url($type, $width, $file_path) {
    $preset = 'original';
    $type .= '_sizes';

    if (is_numeric($width)) {
      foreach ($this->configuration->images->$type as $size) {
        $matches = array();
        if (preg_match('/w([0-9]+)/', $size, $matches) && $matches[1] >= $width) {
          $preset = $size;
          break;
        }
      }
    }
    else if (in_array($width, $this->configuration->images->$type)) {
      $preset = $width;
    }

    return $this->configuration->images->base_url . $preset . $file_path;
    ;
  }

  public function params_merge($params) {
    $defaults = $defaults = array(
      'api_key' => $this->api_key,
      'include_adult' => $this->adult,
      'language' => $this->language,
    );

    $result = $defaults;
    foreach($params as $key =>$value){
      if(!is_null($value)) { // overwrite all values in array1 with array2, except when its null (array_merge or + does not do this)
        $result[$key] = $value;
      }
    }

    // Filter out empty string keys
    foreach ($result as $key => $value) {
      if ($value == '') {
        unset($result[$key]);
      }
    }

    return $result;
  }

}
