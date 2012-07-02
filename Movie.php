<?php
/**
 * Helper class to hold the Movie Info API calls
 *
 * @link http://help.themoviedb.org/kb/api/movie-info-2
 */
class Movie extends Asset {

  public static $type = 'movie';

  /**
   * @link http://help.themoviedb.org/kb/api/movie-alternative-titles
   */
  public function alternative_titles($country=''){
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'alternative_titles', array('country'=>$country));
    return $info;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/movie-casts
   */
  public function casts(){
    $casts = array();
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'casts');
    foreach($info as $group => $persons){
      if(!is_array($persons)) continue;
      foreach($persons as $index => $person){
        $casts[$group][$person->id] = new Person($person);
      }
    }
    return $casts;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/movie-images
   * @param language string, you can seperate multi language selects by using a ;
   *        the API currently does not include null or empty language values in the result
   *        so if you want all images in english including the ones that have null or empty language in database,
   *        you can use 'null;;en' as a language parameter value.
   */
  public function images($language=null, $size=false){

    if(!is_null($language)){
      $languages = explode(';',$language);
      if($language != $languages[0]){
        $language = '';
      }
    }

    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'images', array('language'=>$language));
    foreach($info as $type => $images){
      if(!is_array($images)) continue;
      foreach($images as $index => $data) {
        if(is_null($language) || in_array($data->iso_639_1, $languages)) {
          if($size){
            $info->{$type}[$index]->file_path = $db->image_url(substr($type, 0, strlen($type)-1), $size, $data->file_path);
          }
        } else {
          unset($info->{$type}[$index]);
        }
      }
    }

    return $info;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/movie-keywords
   */
  public function keywords(){
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'keywords');
    return $info;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/movie-release-info
   */
  public function releases(){
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'releases');
    return $info;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/movie-trailers
   */
  public function trailers($language=null){
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'trailers', array('language'=>$language));
    return $info;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/movie-translations
   */
  public function translations(){
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'translations');
    return $info;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/movie-similar-movies
   */
  public function similar_movies($language=null, $page=1){
    $db = TMDB::getInstance();
    $movies = array();
    $info = $db->info(self::$type, $this->id, 'similar_movies', array('language'=>$language, 'page'=>$page));
    foreach($info->results as $index => $movie){
      $movies[$movie->id] = new Movie($movie);
    }
    return $movies;
  }

  ////////////////////////////////////////////////////////////////////
  // Additional helpers

  /**
   * Get the collection as an Collection object
   */
  public function collection(){
    $collection = false;

    if(isset($this->belongs_to_collection)){
      $collection = new Collection($this->belongs_to_collection->id);
    }

    return $collection;
  }

  /**
   * Get the backdrops
   */
  public function backdrop($size=false, $random=false, $language=null){
    return $this->image('backdrop', $size, $random, $language);
  }

  public function backdrops($size, $language=null){
    $images = $this->images($language, $size);
    return $images->backdrops;
  }

  /**
   * Get the posters
   */
  public function poster($size=false, $random=false, $language=null){
    return $this->image('poster', $size, $random, $language);
  }

  public function posters($size, $language=null){
    $images = $this->images($language, $size);
    return $images->posters;
  }

}
