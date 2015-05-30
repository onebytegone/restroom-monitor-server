<?php

/**
 * This stores and retrieves data.
 *
 * @copyright 2015 Ethan Smith - ethan@onebytegone.com
 */

class FlatFile {

   private $dataStore = null;

   function __construct($dataStore) {
      $this->dataStore = $dataStore;
   }

}
