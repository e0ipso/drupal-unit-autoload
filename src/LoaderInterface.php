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
   * Sets the PSR class map.
   *
   * @param array $class_map
   */
  public static function setPsrClassMap(array $class_map);

  /**
   * Sets the seed path.
   *
   * @param string $seed.
   */
  public static function setSeed($seed);

  /**
   * Helper function to register PSR-0 and PSR-4 based files.
   *
   * @param string $partial_namespace
   *   The requested class.
   * @param Composer\Autoload\ClassLoader $loader
   *   The Composer's autoloader.
   *
   * @return bool
   *   TRUE if the class was found. FALSE otherwise.
   */
  public static function registerPsr($partial_namespace, Composer\Autoload\ClassLoader $loader);

}
