<?php
require 'environment.php';
require 'src/require.php';

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

   $voltage = $statusStorage->mostRecent("voltage");
   $status = $statusStorage->mostRecent("status");
   $statusStorage->mostRecent("comm", $lastComm);

   $data = array(
      "voltage" => $voltage,
      "status" => $status,
      "comm" => $lastComm,
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

      $statusStorage->store("status", $updateTo);
      $statusStorage->store("voltage", $decoded->voltage);
      $statusStorage->store("comm", "updated");
   } catch (ExpiredException $e) {
      $status = "failed";
   }

   $data = array( "status" => $status );
   $json = json_encode($data);
   outputJSONP($app, $json);
});

$app->run();
