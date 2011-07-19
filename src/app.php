<?php

$app = require_once __DIR__.'/bootstrap.php';

$app->get('/', function() use ($app) {
  $query = $app['query_builder']->limit(20)->getQuery();

  $entities = $query->execute();

  return $app['twig']->render('index.twig', array(
    'entities' => $entities,
  ));
});

return $app;