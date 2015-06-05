<?php

/**
 * @copyright 2015 Ethan Smith - ethan@onebytegone.com
 */

class HistoricalKeyGenerator {
   public function generate($data) {
      return strval(time());
   }

   public function findMostRecentKey($keys) {
      return array_reduce($keys, function($carry, $item) {
         $value = intval($item);
         $compare = intval($carry);

         return $value > $compare ? $item : $carry;
      }, "0");
   }
}
