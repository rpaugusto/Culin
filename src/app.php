<?php

use Symfony\Component\HttpFoundation\RedirectResponse;

$app = require_once __DIR__.'/bootstrap.php';

/**
 *
 */
$app->get('/', function() use ($app) {
  $query = $app['query_builder']->limit(20)->getQuery();

  $entities = $query->execute();

  return $app['twig']->render('index.twig', array(
    'entities' => $entities,
  ));
})->bind('homepage');

/**
 *
 */
$app->get('/new', function() use ($app) {
  $form = $app['form']();

  return $app['twig']->render('new.twig', array(
    'form' => $form->createView(),
  ));
})->bind('new');

/**
 *
 */
$app->post('/create', function() use ($app) {
  $form = $app['form'](new $app['culin.entity']());

  $form->bindRequest($app['request']);

  if ($form->isValid()) {
    $app['doctrine.odm.mongodb.dm']->persist($form->getData());
    $app['doctrine.odm.mongodb.dm']->flush();

    return new RedirectResponse($app['url_generator']->generate('edit', array('id' => $form->getData()->id)));
  }

  return $app['twig']->render('new.twig', array(
    'form' => $form->createView(),
  ));
})->bind('create');

/**
 *
 */
$app->get('/edit/{id}', function($id) use ($app) {
  $entity = $app['repository']->find($id);
  $form   = $app['form']($entity);

  return $app['twig']->render('edit.twig', array(
    'form'   => $form->createView(),
    'entity' => $entity,
  ));
})->bind('edit');

/**
 *
 */
$app->post('/update', function() use ($app) {
  $form   = $app['form']();
  $data   = $app['request']->get($form->getName());
  $entity = $app['repository']->find($data['id']);

  $form->setData($entity);
  $form->bindRequest($app['request']);

  if ($form->isValid()) {
    $app['doctrine.odm.mongodb.dm']->persist($form->getData());

    return new RedirectResponse($app['url_generator']->generate('edit', array('id' => $form->getData()->id)));
  }

  return $app['twig']->render('edit.twig', array(
    'form'   => $form->createView(),
    'entity' => $form->getData(),
  ));
})->bind('update');

/**
 *
 */
$app->get('/delete/{id}', function($id) use ($app) {
  $entity = $app['repository']->find($id);
  $app['doctrine.odm.mongodb.dm']->remove($entity);

  return new RedirectResponse($app['url_generator']->generate('homepage'));
})->bind('delete');

return $app;