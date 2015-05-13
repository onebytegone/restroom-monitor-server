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
   private $filepath;  // The path to the file to use

   /**
    * Constructor
    *
    * @param $filepath String - This is the path to the file
    *                           with the data stored in it.
    */
   public function __construct($filepath) {
      $this->filepath = $filepath;
   }


   /**
    * This saves the given item. If the item being added has a
    * different length than the other elements in the array,
    * this will throw an exception. A data/time column is
    * automatically added to the item.
    *
    * @param $data Array - Data to save
    */
   public function saveItem($item) {
      $item[self::DATE_COLUMN] = time();

      $data = $this->loadData();
      if (!$this->itemIsOfSameSize($data, $item)) {
          throw new Exception('Adding item with size '.count($item).' expecting item of size '.count($data[0]));
      }
      $data[] = $item;

      $data = $this->cleanUpHistory($data);
      $this->writeData($data);
   }


   /**
    * This gets the most recently stored items. Limit is
    * used to reduce the number of results returned.
    *
    * @param $limit Int - Number of items to limit the results
    *                     to. 0 returns all items
    * @return Array - Number of items requested
    */
   public function getItems($limit = 0) {
      $data = $this->loadData();
      $data = $this->sortByDate($data);

      return array_slice($data, -$limit);
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
    * This fetches the data from storage
    *
    * @return Array - Parsed data from file
    */
   private function loadData() {
      if (!file_exists($this->filepath)) {
         return array();
      }

      $data = file_get_contents($this->filepath);
      return $this->deserializeData($data);
   }


   /**
    * This saves the given data
    *
    * @param $data Array - Data to save
    */
   private function writeData($data) {
      $this->createPathIfNeeded($this->filepath);
      file_put_contents($this->filepath, $this->serializeData($data));
   }


   /**
    * This will create the directories to the final
    * file if it does not already exist.
    *
    * @param $path string - Path to ensure exists
    */
   private function createPathIfNeeded($path) {
      $dir = dirname($path);

      if (!file_exists($dir)) {
         mkdir($dir, 0777, true);
      }
   }


   /**
    * This converts an array to a string. Use
    * deserializeData() to reverse this.
    *
    * @param $value Array - 2 dimensional array to convert
    * @return String - Converted data
    */
   private function serializeData($value) {
      if (count($value) == 0) {
         return "";
      }

      $output = join('-', array_keys($value[0]))."\n";

      $output .= array_reduce($value, function($carry, $item) {
         $row = join('|', array_values($item));
         return "{$carry}{$row}\n";
      }, "");

      return $output;
   }


   /**
    * This converts a properly formatted string
    * to an array. All data is treated as a string.
    *
    * Input:
    * "name-num-fish\nfred|201|beta\nbert|627|false"
    *
    * Output:
    * [
    *    {
    *       'name': 'fred',
    *       'num': '201',
    *       'fish': 'beta'
    *    },
    *    {
    *       'name': 'bert',
    *       'num': '627',
    *       'fish': 'false'
    *    }
    * ]
    *
    * @param $value Array - Data to convert
    * @return String - Converted data
    */
   private function deserializeData($value) {
      $parts = explode("\n", $value);
      $header = explode('-', array_shift($parts));

      $items = array_map(function ($item) use ($header){
            $fields = explode('|', $item);
            if (count($header) != count($fields)) {
               return null;
            }

            return array_combine($header, $fields);
         }, $parts);

      return $items;
   }


   /**
    * This removes all history items that are older than
    * the specified $maxAge.
    *
    * @param $history Array - The data to clean up. It must
    *                         have a field named with DATE_COLUMN.
    * @param $maxAge Int - The time in hours to limit the history to
    */
   private function cleanUpHistory($history, $maxAge = null) {
      if ($maxAge == null) {
         $maxAge = $this->historyAge;
      }

      $timeCutoff = time()-$maxAge*self::SECONDS_IN_HOUR;
      $output = array_map(function($item) use ($timeCutoff){
            if ($item[DataHistory::DATE_COLUMN] < $timeCutoff) {
               return null;
            }
            return $item;
         }, $history);

      $output = array_values(array_filter($output)); // Remove null elements and resets the index
      return $output;
   }


   /**
    * This sorts all the items in the array by their date column.
    *
    * @param $array Array - The array to sort. The subarrays
    *                       must have a field named with DATE_COLUMN.
    * @return Array - sorted array
    */
   private function sortByDate($array){
      function cmp($a, $b) {
         $date_a = $a[DataHistory::DATE_COLUMN];
         $date_b = $b[DataHistory::DATE_COLUMN];

         if ($date_a == $date_b) {
            return 0;
         }
         return ($date_a < $date_b) ? -1 : 1;
      }

      uasort($array, 'cmp');
      return $array;
   }
}
