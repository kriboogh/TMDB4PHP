<?php

class Person extends Asset {

  public static $type = 'person';

  /**
   * @link http://help.themoviedb.org/kb/api/person-credits
   */
  public function credits($language=null){
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'credits', array('language'=>$language));
    return $info;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/person-images
   */
  public function images($size=false){
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'images');
    foreach($info as $type => $images){
      if(!is_array($images)) continue;
      foreach($images as $index => $data) {
        if($size){
          $info->{$type}[$index]->file_path = $db->image_url(substr($type, 0, strlen($type)-1), $size, $data->file_path);
        }
      }
    }
    return $info;
  }

  ////////////////////////////////////////////////////////////////////
  // Additional helpers

  /**
   * Get the profile picture
   */
  public function profile($size=false) {
    return $this->image('profile', $size);
  }

}
