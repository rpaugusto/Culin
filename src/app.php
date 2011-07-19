<?php

$app = require_once __DIR__.'/bootstrap.php';

$app->get('/', function() {
  return 'hello, world';
});

return $app;