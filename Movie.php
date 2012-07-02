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
    $filterArray = array();
    if (!empty($country))
    {
      $filterArray['country'] = $country;
    }
    $info = $db->info(self::$type, $this->id, 'alternative_titles', $filterArray);
    return $info;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/movie-casts
   */
  public function casts(){
    $casts = array();
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'casts');
    //API only returns cast and crew infomation currently
    $casts['cast'] = $info->cast;
    $casts['crew'] = $info->crew;
    return $casts;
  }

  /**
   * @link http://help.themoviedb.org/kb/api/movie-images
   */
  public function images($language='', $size=false){
    $db = TMDB::getInstance();
    $info = $db->info(self::$type, $this->id, 'images', array('language'=>$language));
    if($size) {
        foreach($info->backdrops as $index => $data) {
          $info->backdrops[$index]->file_path = $db->image_url('backdrop', $size, $data->file_path);
        }
        foreach($info->posters as $index => $data) {
          $info->posters[$index]->file_path = $db->image_url('poster', $size, $data->file_path);
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
  public function backdrops($size, $language=null){
    $db = TMDB::getInstance();
    $images = $this->images($language, $size);
    return $images->backdrops;
  }

  /**
   * Get the posters
   */
  public function posters($size, $language=null){
    $db = TMDB::getInstance();
    $images = $this->images($language, $size);
    return $images->posters;
  }


}
