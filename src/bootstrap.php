<?php

require_once __DIR__.'/../vendor/silex/autoload.php';
require_once __DIR__.'/../vendor/SilexExtensions/DoctrineMongoDB/src/autoload.php';

$app = new Silex\Application();

$app = new Silex\Application();
$app['autoloader']->registerNamespaces(array(
  'Symfony'  => __DIR__.'/../vendor',
  'Document' => __DIR__,
  'Form'     => __DIR__,
));

use Silex\Extension\SymfonyBridgesExtension;
use Silex\Extension\UrlGeneratorExtension;
use Silex\Extension\TwigExtension;
use Silex\Extension\DoctrineMongoDBExtension;
use Silex\Extension\FormExtension;
use Silex\Extension\TranslationExtension;

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
    array('type' => 'xml', 'path' => __DIR__.'/Document', 'namespace' => 'Document'),
  ),
  'doctrine.odm.mongodb.metadata_cache' => 'array',
  'doctrine.common.class_path'          => __DIR__.'/../vendor/mongodb-odm/lib/vendor/doctrine-common/lib',
  'doctrine.mongodb.class_path'         => __DIR__.'/../vendor/mongodb-odm/lib/vendor/doctrine-mongodb/lib',
  'doctrine.odm.mongodb.class_path'     => __DIR__.'/../vendor/mongodb-odm/lib',
));

$app['doctrine.odm.mongodb.hydrators_dir'] = __DIR__.'/hydrators';
$app['doctrine.odm.mongodb.configuration']->setDefaultDB('springbok-silex');

require_once __DIR__.'/config.php';

return $app;