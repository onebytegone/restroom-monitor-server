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

      $newEntry = array(
         $keyGen->generate() => $value
      );

      $totalData = array_merge($data, $newEntry);

      $self->dataStore->write($totalData);
   }

   public function mostRecent($key, &$historicalKey = '') {
      $data = $this->dataStore->read();
      $items = $data[$key];

      $historicalKeys = array_keys($items);
      $historicalKey = $this->keyGen->findMostRecentKey($historicalKeys);

      return $items[$historicalKey];
   }
}
