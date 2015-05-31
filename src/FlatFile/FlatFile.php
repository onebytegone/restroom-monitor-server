<?php

/**
 * This stores and retrieves data.
 *
 * @copyright 2015 Ethan Smith - ethan@onebytegone.com
 */

class FlatFile {

   private $dataStore = null;

   function __construct($dataStore, $identifierGenerator) {
      $this->dataStore = $dataStore;
   }


   public function store($key, $value) {

   }

   public function mostRecent($key, &$time = 0) {
      return "";
   }
}
