<?php
require 'environment.php';
require 'src/require.php';

// Create router
$app = new \Slim\Slim();

$app->get('/', function () {
   $data = [];
   $json = json_encode($data);

   echo isset($_GET['callback'])
      ? "{$_GET['callback']}($json)"
      : $json;
});

$app->run();
