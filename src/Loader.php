<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\LoaderInterface.
 */

namespace Drupal\Composer\ClassLoader;


class Loader implements LoaderInterface {

  /**
   * Class maps.
   *
   * Contains the name of the class, including the namespace, as the key. The
   * value is the file name with path tokens.
   *
   * @var
   */
  protected static $classMap;

  /**
   * {@inheritdoc}
   */
  public static function autoload($class) {
    if (!in_array($class, static::$classMap)) {
      return FALSE;
    }
    try {
      $resolver = new TokenResolver(static::$classMap[$class]);
      $finder = $resolver->resolve();
      // Have the path finder require the file and return TRUE or FALSE if it
      // found the file or not.
      $finder->requireFile();
      return TRUE;
    }
    catch (ClassLoaderException $e) {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function setClassMap(array $class_map) {
    static::$classMap = $class_map;
  }

}
