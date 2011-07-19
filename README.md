# Culin, a Silex Crud application

Culin is a CRUD app for silex, that you can mount in your own applications.

It's still a big WiP/PoC, but it's already working \o/

Also, Culin relies on [a patch for Silex that I made a PR for](https://github.com/fabpot/Silex/pull/131), but that is still not merged.

Expected usage:

    // get your app
    $app = require_once __DIR__.'/some/place/app.php';

    // mount Culin
    $app->mount('/contact', new Silex\LazyApplication('/path/to/culin.php', function($app) {
      $app['culin.entity'] = 'Document\Contact';
      $app['culin.form']   = 'Form\ContactType';
    }));

    // you're set!

## Todo

* be RESTful
* clean up the code
* paginate the listing
* a ton of things

## Why Culin?

Culin is english for "Crudités" in french, which 4 first letters are CRUD.