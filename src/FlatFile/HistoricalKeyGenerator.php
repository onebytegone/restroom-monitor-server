<?php

/**
 * @copyright 2015 Ethan Smith - ethan@onebytegone.com
 */

class HistoricalKeyGenerator {
   public function generate($data = null) {
      return strval(time());
   }

   public function findMostRecentKey($keys) {
      $self = $this;
      return array_reduce($keys, function($carry, $item) use ($self) {
         $compareResult = $self->compareKeys($item, $carry);
         return $compareResult > 0 ? $item : $carry;
      }, "0");
   }

   public function compareKeys($a, $b) {
      $aValue = $this->timestampFromKey($a);
      $bValue = $this->timestampFromKey($b);

      if ($a == $b) {
         return 0;
      }

      return $a > $b ? 1 : -1;
   }

   public function timestampFromKey($key) {
      return intval($key);
   }
}
