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
      $hoursDivided = $this->divideByTimeSection($ranges, 'H', array_fill(0, 24, array()));

      $mapped = array_reduce(array_keys($hoursDivided), function($carry, $hour) use ($hoursDivided) {
         $key = date('ga', strtotime("{$hour}:00"));
         $carry[$key] = $hoursDivided[$hour];
         return $carry;
      });

      return $mapped;
   }

   public function divideRangesIntoDays($ranges) {
      return $this->divideByTimeSection($ranges, 'w', array_fill(0, 7, array()));
   }

   public function divideByTimeSection($ranges, $tag, $preload) {
      return array_reduce($ranges, function($carry, $range) use ($tag) {
         $hour = intval(date($tag, $range['start']));
         $carry[$hour][] = $range;

         return $carry;
      }, $preload);
   }

   public function allDaysFoundForRanges($ranges) {
      return array_unique(array_map(function ($range) {
            return date('Y/m/d', $range['start']);
      }, $ranges));
   }

   public function timeUsedInRanges($ranges) {
      return array_reduce($ranges, function ($carry, $range) {
         return $carry + $range['length'];
      });
   }

   public function mapToRange($value, $oldMin, $oldMax, $newMin, $newMax) {
      $oldRange = $oldMax - $oldMin;
      if ($oldRange == 0) {
         return $newMin;
      }

      $newRange = $newMax - $newMin;
      return ((($value - $oldMin) * $newRange) / $oldRange) + $newMin;
   }
}
