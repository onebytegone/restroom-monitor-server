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

$app->get('/', function () use ($dataHistory, $app) {
   $data = $dataHistory->getItems(1)[0];
   $json = json_encode($data);

   outputJSONP($app, $json);
});

$app->run();
