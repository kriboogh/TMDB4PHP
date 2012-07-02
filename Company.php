<?php

class Company extends Asset {

  public static $type = 'company';

  /**
   * @link http://help.themoviedb.org/kb/api/company-movies
   */
  public function movies($language=null, $page=1){
    $db = TMDB::getInstance();
    $movies = array();
    $info = $db->info(self::$type, $this->id, 'movies', array('language'=>$language, 'page'=>$page));
    foreach($info->results as $index => $movie){
      $movies[$movie->id] = new Movie($movie);
    }
    return $movies;
  }

  ////////////////////////////////////////////////////////////////////
  // Additional helpers

  /**
   * Get the logo pictures
   */
  public function logo($size=false) {
    return $this->image('logo', $size);
  }
}
