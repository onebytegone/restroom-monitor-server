<?php

/**
 * This stores and retrieves historical data.
 *
 * @copyright 2014-2015 Ethan Smith - ethan@onebytegone.com
 */

class DataHistory {
   const DATE_COLUMN = 'date';
   const HOURS_IN_TWO_WEEKS = 336;
   const SECONDS_IN_HOUR = 3600;

   public $historyAge = DataHistory::HOURS_IN_TWO_WEEKS;  // The number of hours to keep the history

   /**
    * This saves the given item. If the item being added has a
    * different length than the other elements in the array,
    * this will throw an exception. A data/time column is
    * automatically added to the item.
    *
    * @param $data Array - Data to save
    */
   public function saveItem($item, $dataStore, $replaceLast = false) {
      $item[self::DATE_COLUMN] = time();

      $data = $dataStore->readData();
      if ($replaceLast) {
         array_pop($data);
      }
      $data[$item[self::DATE_COLUMN]] = $item;

      $data = $this->filterByAge($data);
      $dataStore->write($data);
   }


   /**
    * This gets the most recently stored items. Limit is
    * used to reduce the number of results returned.
    *
    * @param $limit Int - Number of items to limit the results
    *                     to. 0 returns all items
    * @return Array - Number of items requested
    */
   public function getItems($dataStore, $limit = 0) {
      $data = $dataStore->read();

      if ($data) {
         $data = array_slice(array_values(ksort($data)), -$limit);
      }

      return $data;
   }


   /**
    * This compares the length of the item and the first
    * element in the data array. If the are not the same,
    * this will return false. If the data array is empty,
    * this will return true.
    *
    * @param $data Array - Array of arrays
    * @param $item Array - New array item
    * @return Boolean - True if same size, false otherwise.
    */
   private function itemIsOfSameSize($data, $item) {
      if (count($data) == 0) {
         return true;
      }

      return ( count($data[0]) == count($item) );
   }


   /**
    * This removes all history items that are older than
    * the specified $maxAge.
    *
    * @param $history Array - The data to clean up. It must
    *                         have a field named with DATE_COLUMN.
    * @param $maxAge Int - The time in hours to limit the history to
    */
   private function filterByAge($history, $maxAge = null) {
      if ($maxAge == null) {
         $maxAge = $this->historyAge;
      }

      $timeCutoff = time()-$maxAge*self::SECONDS_IN_HOUR;
      $output = array_filter(array_flip($history), function ($item) {
         return $item[DataHistory::DATE_COLUMN] < $timeCutoff;
      });

      return $output;
   }
}
