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
}
