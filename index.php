<?php
require 'environment.php';
require 'src/require.php';


class StorageName {
   const VOLTAGE = 'voltage';
   const STATUS = 'status';
   const COMM = 'comm';

   static $ALL_TYPES = array(
      self::VOLTAGE,
      self::STATUS,
      self::COMM
   );
}

// Create router
$app = new \Slim\Slim();

$statusStorage = new FlatFile(new DataStore('data/statusdata.json'), new HistoricalKeyGenerator());

function outputJSONP($app, $json) {
   $app->contentType('application/javascript');
   $callback = $app->request()->get('callback');
   if ($callback) {
      echo "{$callback}($json)";
   } else {
      echo $json;
   }
}

$app->get('/v1/status', function () use ($statusStorage, $app) {

   $voltage = $statusStorage->mostRecent(StorageName::VOLTAGE);
   $status = $statusStorage->mostRecent(StorageName::STATUS, $lastChange);
   $statusStorage->mostRecent(StorageName::COMM, $lastComm);

   $data = array(
      StorageName::VOLTAGE => $voltage,
      StorageName::STATUS => $status,
      StorageName::COMM => $lastComm,
      "date" => $lastChange,
   );
   $json = json_encode($data);

   outputJSONP($app, $json);
});

$app->get('/v1/ping', function () use ($app) {
   $data = array( "time" => time() );
   $json = json_encode($data);

   outputJSONP($app, $json);
});

$app->post('/v1/update', function () use ($statusStorage, $app) {
   $status = "success";

   try {
      $decoded = JWT::decode($app->request()->headers('jwt'), PRIVATE_SHARED_JWT_KEY, array('HS512'));

      // Sanitize input
      $updateTo = ($decoded->status === 'closed' ? 'closed' : 'open');

      updateIfChanged($statusStorage, StorageName::VOLTAGE, $decoded->voltage);
      updateIfChanged($statusStorage, StorageName::STATUS, $updateTo);
      $statusStorage->store(StorageName::COMM, "updated");
   } catch (ExpiredException $e) {
      $status = "failed";
   }

   $data = array( "status" => $status );
   $json = json_encode($data);
   outputJSONP($app, $json);
});

$app->get('/v1/history/raw/:type/(:limit)', function ($type, $limit = 9999) use ($statusStorage, $app) {
   $type = strtolower($type);
   if (!in_array($type, StorageName::$ALL_TYPES)) {
      $app->response()->status(404);
      return;
   }

   $limit = intval($limit);

   $status = $statusStorage->getEntrySet($type, $limit);
   $json = json_encode($status);

   outputJSONP($app, $json);
});

$app->get('/v1/history/day', function () use ($statusStorage, $app) {
   $statusSet = $statusStorage->getEntrySet(StorageName::STATUS);
   $keyGen = $statusStorage->keyGenerator();

   $analyzer = new DataAnalyzer();

   $usedRanges = $analyzer->convertEntrySetToRanges($statusSet, array($keyGen, 'timestampFromKey'));
   $filteredRanges = $analyzer->filterUnexpectedRanges($usedRanges);
   $hourDivided = $analyzer->divideRangesIntoHours($filteredRanges);

   // calculate hour stat info
   $hourStats = array_map(function ($hourRanges) {
      $percent = 0;
      $timeUsed = 0;
      $secondsTracked = 0;
      $pointCount = count($hourRanges);
      if ($pointCount > 0) {
         $allDays = array_unique(array_map(function ($range) {
            return date('Y/m/d', $range['start']);
         }, $hourRanges));

         $timeUsed = array_reduce($hourRanges, function ($carry, $range) {
            return $carry + $range['length'];
         });

         // days * 1 hour * 60 min * 60 sec
         $secondsTracked = ( count($allDays) * 60 * 60 );
         $percent = $timeUsed / $secondsTracked;
      }

      return array(
         'count' => $pointCount,
         'percent' => $percent,
         'timeUsed' => $timeUsed,
         'secondsTracked' => $secondsTracked
      );
   }, $hourDivided);

   $json = json_encode($hourStats);

   outputJSONP($app, $json);
});

$app->run();


function updateIfChanged($store, $key, $newValue) {
   $lastValue = $store->mostRecent($key);
   if ($lastValue !== $newValue) {
      $store->store($key, $newValue);
   }
}
