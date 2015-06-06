<?php

/**
 * This stores and retrieves data.
 *
 * @copyright 2015 Ethan Smith - ethan@onebytegone.com
 */

class FlatFile {

   private $dataStore = null;
   private $keyGen = null;

   function __construct($dataStore, $keyGen) {
      $this->dataStore = $dataStore;
      $this->keyGen = $keyGen;
   }


   public function store($key, $value) {
      $data = $this->dataStore->read();

      $pastEntries = isset($data[$key]) ? $data[$key] : array();

      $newEntries = array(
         $this->keyGen->generate() => $value
      );

      $allEntries = $pastEntries + $newEntries;
      $data[$key] = $allEntries;

      $this->dataStore->write($data);
   }


   public function mostRecent($key, &$historicalKey = '') {
      $data = $this->dataStore->read();
      $items = $data[$key];

      $historicalKeys = array_keys($items);
      $historicalKey = $this->keyGen->findMostRecentKey($historicalKeys);

      return $items[$historicalKey];
   }
}
