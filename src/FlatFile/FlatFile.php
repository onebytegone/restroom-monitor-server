<?php

/**
 * This stores and retrieves data.
 *
 * @copyright 2015 Ethan Smith - ethan@onebytegone.com
 */

class FlatFile {

   private $dataStore = null;
   private $keyGen = null;
   public $filterLimit = 2000;
   public $maxHistoryLookup = 2000;

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
      $filteredEntries = $this->filterOldData($allEntries);
      $data[$key] = $filteredEntries;

      $this->dataStore->write($data);
   }


   public function mostRecent($key, &$historicalKey = '') {
      $data = $this->dataStore->read();
      $items = $data[$key];

      $historicalKeys = array_keys($items);
      $historicalKey = $this->keyGen->findMostRecentKey($historicalKeys);

      return $items[$historicalKey];
   }


   public function getEntrySet($key, $limit = -1) {
      // Cap the amount we will return
      if ($limit <= 0) {
         $limit = $this->maxHistoryLookup;
      }

      $data = $this->dataStore->read();
      $items = $data[$key];

      $historicalKeys = array_keys($items);
      usort($historicalKeys, array($this->keyGen, 'compareKeys'));

      $wantedKeys = array_slice($historicalKeys, -$limit);
      $filtered = array_intersect_key($items, array_flip($wantedKeys));

      return $filtered;
   }


   public function filterOldData($entries) {
      return count($entries) > $this->filterLimit ? array_slice($entries, -$this->filterLimit, null, true) : $entries;
   }

   public function keyGenerator() {
      return $this->keyGen;
   }
}
