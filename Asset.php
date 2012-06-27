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

}
