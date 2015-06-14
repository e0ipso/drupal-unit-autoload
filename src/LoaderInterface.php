<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\LoaderInterface.
 */

namespace Drupal\Composer\ClassLoader;


interface LoaderInterface {

  /**
   * Autoload static method.
   *
   * @param string $class
   *   The name of the class to check or load.
   *
   * @return bool
   *   TRUE if the class is currently available, FALSE otherwise.
   */
  public static function autoload($class);

  /**
   * Sets the class map.
   *
   * @param array $class_map
   */
  public static function setClassMap(array $class_map);

  /**
   * Sets the seed path.
   *
   * @param string $seed.
   */
  public static function setSeed($seed);

}
