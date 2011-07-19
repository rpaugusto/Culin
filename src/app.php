<?php

$app = require_once __DIR__.'/bootstrap.php';

$app->before(function() use ($app) {
  if (!isset($app['crud.entity'])) {
    throw new \RuntimeException('Configuration entry "crud.entity" is not set');
  }
});

$app->get('/', function() {
  return 'hello, world';
});

return $app;