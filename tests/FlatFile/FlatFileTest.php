<?php

/**
 * @copyright 2015 Ethan Smith
 */
class FlatFileTest extends BaseTest {

   public function testLifecycle() {
      $mockDataStore = $this->getMock(
         'DataStore',
         array('write', 'read'),
         array('random/file/path.txt')
      );

      $flatFile = new FlatFile($mockDataStore);
   }

}

