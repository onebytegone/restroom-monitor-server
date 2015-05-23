<?php
require 'environment.php';
require 'src/require.php';

// Create router
$app = new \Slim\Slim();

$dataHistory = new DataHistory('data/statusdata.txt');

function outputJSONP($app, $json) {
   $app->contentType('application/javascript');
   $callback = $app->request()->get('callback');
   if ($callback) {
      echo "{$callback}($json)";
   } else {
      echo $json;
   }
}

$app->get('/v1/status', function () use ($dataHistory, $app) {
   $data = $dataHistory->getItems(1)[0];
   $json = json_encode($data);

   outputJSONP($app, $json);
});

$app->get('/v1/ping', function () use ($dataHistory, $app) {
   $data = array( "time" => time() );
   $json = json_encode($data);

   outputJSONP($app, $json);
});

$app->post('/v1/update', function () use ($dataHistory, $app) {
   $status = "success";

   try {
      $decoded = JWT::decode($app->request()->headers('jwt'), PRIVATE_SHARED_JWT_KEY, array('HS512'));

      // Sanitize input
      $updateTo = ($decoded->status === 'closed' ? 'closed' : 'open');
      $dataHistory->saveItem(array('status' => $updateTo));
   } catch (ExpiredException $e) {
      $status = "failed";
   }

   $data = array( "status" => $status );
   $json = json_encode($data);
   outputJSONP($app, $json);
});

$app->run();
