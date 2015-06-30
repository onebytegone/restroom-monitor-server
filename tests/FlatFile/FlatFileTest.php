<?php

/**
 * @copyright 2015 Ethan Smith
 */
class FlatFileTest extends BaseTest {

   public function testStore() {
      $mockDataStore = $this->getMock(
         'DataStore',
         array('write', 'read'),
         array('random/file/path.txt')
      );

      $mockDataStore->method('read')
         ->will(
            $this->returnValue(
               array()
            )
         );

      $mockDataStore->method('write')
         ->with(
            $this->equalTo(
               array(
                  "hello" => array(
                     "1234" => "yo"
                  )
               )
            )
         );


      $mockKeyGen = $this->getMock(
         'HistoricalKeyGenerator',
         array('generate')
      );

      $mockKeyGen->method('generate')
         ->will($this->returnValue('1234'));

      $flatFile = new FlatFile($mockDataStore, $mockKeyGen);

      $flatFile->store("hello", "yo");

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

   public function testGetEntrySet() {
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
         array('compareKey')
      );

      $mockKeyGen->method('findMostRecentKey')
         ->will($this->returnValue(1));

      $flatFile = new FlatFile($mockDataStore, $mockKeyGen);

      $entrySet = $flatFile->getEntrySet("hello");
      $this->assertEquals(array(
         "1234" => "greetings",
         "2345" => "salutations",
         "3456" => "yo"
      ), $entrySet);

      $entrySet = $flatFile->getEntrySet("hello", 2);
      $this->assertEquals(array(
         "2345" => "salutations",
         "3456" => "yo"
      ), $entrySet);

      $entrySet = $flatFile->getEntrySet("hello", 1);
      $this->assertEquals(array(
         "3456" => "yo"
      ), $entrySet);
   }

   public function testFilterOldData() {

      $flatFile = new FlatFile(null, null);
      $flatFile->filterLimit = 4;
      $filtered = $flatFile->filterOldData(array(
         "a" => "1",
         "b" => "2",
         "c" => "3",
         "d" => "4",
         "e" => "5",
         "f" => "6"
      ));
      $this->assertEquals(array(
         "c" => "3",
         "d" => "4",
         "e" => "5",
         "f" => "6"
      ), $filtered);
   }

}

