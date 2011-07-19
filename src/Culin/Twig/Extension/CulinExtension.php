<?php

namespace Culin\Twig\Extension;

use Silex\Application;

class CulinExtension extends \Twig_Extension
{
  public $app;

  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  public function getName()
  {
    return 'culin';
  }

  public function getFilters()
  {
    return array(
      'to_headers' => new \Twig_Filter_Method($this, 'toHeaders'),
      'to_array'   => new \Twig_Filter_Method($this, 'toArray'),
      'to_fields'  => new \Twig_Filter_Method($this, 'toFields'),
    );
  }

  private function getOption($name, $default = null)
  {
    return isset($this->app[$name]) ? $this->app[$name] : $default;
  }

  public function current($object)
  {
    return $object->current();
  }

  public function toHeaders(\Iterator $object)
  {
    $object = clone $object;
    $object->rewind();

    return $this->toFields($object->current());
  }

  public function toFields($object)
  {
    $array  = (array) $object;
    $import = $this->getOption('culin.fields', array_keys($array));

    return array_intersect(array_keys($array), $import);
  }

  public function toArray($object)
  {
    $array  = (array) $object;
    $import = $this->getOption('culin.fields', array_keys($array));
    return array_intersect_key($array, array_combine($import, $import));
  }
}