<?php
require 'environment.php';
require 'src/require.php';

// Create router
$app = new \Slim\Slim();

$dataHistory = new DataHistory('data/statusdata.txt');


$app->get('/', function () use ($dataHistory, $app) {
   $data = $dataHistory->getItems(1)[0];
   $json = json_encode($data);


   $callback = $app->request()->get('callback');
   $app->contentType('application/javascript');
   echo "{$callback}($json)";
});


$app->run();
