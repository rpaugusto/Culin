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
      'to_headers' => new \Twig_Filter_Method($this, 'filterToHeaders'),
      'to_array'   => new \Twig_Filter_Method($this, 'filterToArray'),
      'to_fields'  => new \Twig_Filter_Method($this, 'filterToFields'),
    );
  }

  private function getOption($name, $default = null)
  {
    return isset($this->app[$name]) ? $this->app[$name] : $default;
  }

  public function getTests()
  {
    return array(
      'edit_link'  => new \Twig_Test_Method($this, 'testEditLink'),
    );
  }

  public function testEditLink($field)
  {
    $config = $this->getOption('culin.fields', array());

    return isset($config[$field]) && isset($config[$field]['link']) && $config[$field]['link'];
  }

  public function filterToHeaders(\Iterator $object)
  {
    $object = clone $object;
    $object->rewind();

    $fields = $this->filterToFields($object->current());

    $headers = array();

    $config = $this->getOption('culin.fields');

    foreach ($fields as $field) {
      if (isset($config[$field]) && isset($config[$field]['label'])) {
        $headers[] = $config[$field]['label'] ?: $field;
      } else {
        $headers[] = $field;
      }
    }

    return $headers;
  }

  public function filterToFields($object)
  {
    $array  = (array) $object;
    $import = array_keys($this->getOption('culin.fields', array_keys($array)));

    return array_intersect(array_keys($array), $import);
  }

  public function filterToArray($object)
  {
    $array  = (array) $object;
    $import = array_keys($this->getOption('culin.fields', array_keys($array)));
    return array_intersect_key($array, array_combine($import, $import));
  }
}