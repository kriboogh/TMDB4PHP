<?php

/**
 * Helper class to hold the Collection Info API calls
 *
 * @link http://help.themoviedb.org/kb/api/collection-info
 */
class Collection extends Asset {

  public static $type = 'collection';

  ////////////////////////////////////////////////////////////////////
  // Additional helpers

  /**
   * Retrieve the parts as Movie objects
   * @link http://help.themoviedb.org/kb/api/collection-info
   */
  public function parts(){
    $parts = array();
    foreach($this->parts as $index => $part){
      $parts[$part->id] = new Movie($part->id);
    }
    return $parts;
  }

}
