<?php

require_once __DIR__.'/../vendor/silex/autoload.php';
require_once __DIR__.'/../vendor/SilexExtensions/DoctrineMongoDB/src/autoload.php';

$app = new Silex\Application();

$app = new Silex\Application();
$app['autoloader']->registerNamespaces(array(
  'Symfony'  => __DIR__.'/../vendor',
  'Document' => __DIR__,
  'Form'     => __DIR__,
  'Culin'    => __DIR__,
));
$app['autoloader']->registerPrefixes(array(
  'Twig_Extensions_' => __DIR__.'/../vendor/Twig-extensions/lib',
));

use Silex\Extension\SymfonyBridgesExtension;
use Silex\Extension\UrlGeneratorExtension;
use Silex\Extension\TwigExtension;
use Silex\Extension\DoctrineMongoDBExtension;
use Silex\Extension\FormExtension;
use Silex\Extension\TranslationExtension;
use Culin\Twig\Extension\CulinExtension;

$app->register(new SymfonyBridgesExtension());
$app->register(new UrlGeneratorExtension());

$app->register(new FormExtension());
$app->register(new TranslationExtension(), array(
  'translator.messages' => array()
));

$app->register(new TwigExtension(), array(
  'twig.path' => array(__DIR__.'/templates', __DIR__.'/../vendor/Symfony/Bridge/Twig/Resources/views/Form'),
  'twig.class_path' => __DIR__.'/../vendor/silex/vendor/twig/lib',
));

$app->register(new DoctrineMongoDBExtension, array(
  'doctrine.odm.mongodb.connection_options' => array(
    'database' => 'springbok-silex',
    'host'     => 'localhost',
  ),
  'doctrine.odm.mongodb.documents' => array(
    array('type' => 'annotation', 'path' => __DIR__.'/Document', 'namespace' => 'Document'),
  ),
  'doctrine.odm.mongodb.metadata_cache' => 'array',
  'doctrine.common.class_path'          => __DIR__.'/../vendor/mongodb-odm/lib/vendor/doctrine-common/lib',
  'doctrine.mongodb.class_path'         => __DIR__.'/../vendor/mongodb-odm/lib/vendor/doctrine-mongodb/lib',
  'doctrine.odm.mongodb.class_path'     => __DIR__.'/../vendor/mongodb-odm/lib',
));

$app['doctrine.odm.mongodb.hydrators_dir'] = __DIR__.'/../cache/doctrine/hydrators';

Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::registerAnnotationClasses();

require_once __DIR__.'/config.php';

if (!is_dir($app['doctrine.odm.mongodb.hydrators_dir'])) {
  mkdir($app['doctrine.odm.mongodb.hydrators_dir'], 0777, true);
}

$app->before(function() use ($app) {
  if (!isset($app['culin.entity'])) {
    throw new \RuntimeException('Configuration entry "culin.entity" is not set');
  }

  $app['repository']    = $app['doctrine.odm.mongodb.dm']->getRepository($app['culin.entity']);
  $app['query_builder'] = $app->share(function() use ($app) {
    return $app['doctrine.odm.mongodb.dm']->createQueryBuilder($app['culin.entity']);
  });

  $app['twig']->addExtension(new CulinExtension($app));
  $app['twig']->addExtension(new \Twig_Extensions_Extension_Debug());
  $app['twig']->enableDebug();
});

return $app;