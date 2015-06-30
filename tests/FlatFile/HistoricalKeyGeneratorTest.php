<?php

/**
 * @copyright 2015 Ethan Smith
 */
class HistoricalKeyGeneratorTest extends BaseTest {

   public function testFindMostRecentKey() {
      $gen = new HistoricalKeyGenerator();

      $this->assertEquals("4123", $gen->findMostRecentKey(
         array(
            "1234",
            "4123",
            "0"
         )
      ));
   }

   public function testSortCompare() {
      $gen = new HistoricalKeyGenerator();

      $this->assertEquals(1, $gen->compareKeys('2', '1'));
      $this->assertEquals(-1, $gen->compareKeys('0', '1'));
      $this->assertEquals(0, $gen->compareKeys('1', '1'));
   }
}
