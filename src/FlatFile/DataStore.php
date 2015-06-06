<?php

/**
 * This stores and retrieves data.
 *
 * @copyright 2014-2015 Ethan Smith - ethan@onebytegone.com
 */

class DataStore {
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
    * This saves the given data
    *
    * @param $data Array - Data to save
    */
   public function write($data) {
      $this->createPathIfNeeded($this->filepath);
      file_put_contents($this->filepath, $this->serializeData($data));
   }


   /**
    * This fetches the data from storage
    *
    * @return Array - Parsed data from file
    */
   public function read() {
      if (!file_exists($this->filepath)) {
         return array();
      }

      $data = file_get_contents($this->filepath);
      return $this->deserializeData($data);
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
    * @param $value Array - Data to convert
    * @return String - Converted data
    */
   private function serializeData($value) {
      return json_encode($value);
   }


   /**
    * This converts a properly formatted string
    * to an array.
    *
    * @param $value Array - Data to convert
    * @return String - Converted data
    */
   private function deserializeData($value) {
      return json_decode($value, true);
   }
}
