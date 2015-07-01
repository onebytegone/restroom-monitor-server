<?php

/**
* This is used to parse the data
*/
class DataAnalyzer {

   public function convertEntrySetToRanges($entrySet, $timestampFromKey) {
      return array_reduce(array_keys($entrySet), function($carry, $key) use ($entrySet, $timestampFromKey) {
         $lastRange = end($carry);
         if (count($carry) == 0 || $lastRange['length'] != -1) {
            if ($entrySet[$key] == 'closed') {
               $carry[] = array(
                  'start' => call_user_func($timestampFromKey, $key),
                  'length' => -1
               );
            }
         } else if ($lastRange['length'] == -1) {
            if ($entrySet[$key] == 'open') {
               $range = array_pop($carry);
               $range['length'] = call_user_func($timestampFromKey, $key) - $range['start'];
               $carry[] = $range;
            }
         }

         return $carry;
      }, array());
   }

   public function filterUnexpectedRanges($ranges) {
      // filter the too short and too long ranges
      return array_values(array_filter(array_map(function($range) {
         // Discard those shorter than 10sec and longer than 25 min
         if ($range['length'] < 10 || $range['length'] > 25 * 60) {
            return null;
         }
         return $range;
      }, $ranges)));
   }

   public function divideRangesIntoHours($ranges) {
      return array_reduce($ranges, function($carry, $range) {
         $hour = intval(date('H', $range['start']));
         $carry[$hour][] = $range;

         return $carry;
      }, array_fill(0, 24, array()));
   }
}
