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
    if($size) {
      foreach($info->profiles as $index => $profile) {
        $info->profiles[$index]->file_path = $db->image_url('profile', $size, $profile->file_path);
      }
    }
    return $info;
  }

  ////////////////////////////////////////////////////////////////////
  // Additional helpers

  /**
   * Get the profile picture
   */
  public function profile($size) {
    $db = TMDB::getInstance();
    return $db->image_url('profile', $size, $this->profile_path);
  }

}
