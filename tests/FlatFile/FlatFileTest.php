<?php

/**
 * @copyright 2015 Ethan Smith
 */
class FlatFileTest extends BaseTest {

   public function testLifecycle() {
      /*$mockDataStore = $this->getMock(
         'DataStore',
         array('write', 'read'),
         array('random/file/path.txt')
      );

      $flatFile = new FlatFile($mockDataStore);*/
   }

   public function testMostRecent() {
      $mockDataStore = $this->getMock(
         'DataStore',
         array('write', 'read'),
         array('random/file/path.txt')
      );

      $mockDataStore->method('read')
         ->will(
            $this->returnValue(
               array(
                  "hello" => array(
                     "1234" => "greetings",
                     "2345" => "salutations",
                     "3456" => "yo"
                  )
               )
            )
         );

      $mockKeyGen = $this->getMock(
         'HistoricalKeyGenerator',
         array('findMostRecentKey')
      );

      $mockKeyGen->method('findMostRecentKey')
         ->with($this->equalTo(array('1234', '2345', '3456')))
         ->will($this->returnValue('3456'));

      $flatFile = new FlatFile($mockDataStore, $mockKeyGen);
      $mostRecent = $flatFile->mostRecent("hello", $time);
      $this->assertEquals("yo", $mostRecent);
      $this->assertEquals("3456", $time);
   }

}

