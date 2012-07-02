<?php
/**
 * Base class for all API helper classes
 */
class Asset {

  public static $type = 'abstract';

  public function __construct($data){
    if(is_numeric($data)){
      $db = TMDB::getInstance();
      $data = $db->info(self::$type, $data);
    }
    foreach($data as $key => $value){
      $this->{$key} = $value;
    }
  }


  /**
   * Get an image base method
   * @param unknown_type $type 'backdrop', 'poster', 'profile', 'logo', ...
   * @param unknown_type $size int or preset
   * @param unknown_type $random true or false
   * @param unknown_type $language optional language to return the image in
   */
  public function image($type, $size=false, $random=false, $language=null){
    $image = false;
    $typeset = $type . 's'; // multiple
    $path_key = $type . '_path';

    if($random){
      $images = $this->images($language, false);
      if(count($images)>1) {
        $index = rand(0, count($images->{$typeset})-1);
        $this->{$path_key} = $images->{$typeset}[$index]->file_path;
      }
    }

    if(isset($this->{$path_key})){
      if($size){
        $db = TMDB::getInstance();
        $image = $db->image_url($type, $size, $this->{$path_key});
      } else {
        $image = $this->{$path_key};
      }
    }

    return $image;
  }
}
