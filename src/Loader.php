<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Loader.
 */

namespace Drupal\Composer\ClassLoader;

class Loader implements LoaderInterface {

  /**
   * Class maps.
   *
   * Contains the name of the class, including the namespace, as the key. The
   * value is the file name with path tokens.
   *
   * @var array
   */
  protected static $classMap;

  /**
   * PSR Class maps.
   *
   * Contains the namespace, as the key. The value is the file path with tokens.
   * There are two types of PSR autoloaders:
   *   - psr-0
   *   - psr-4
   *
   * @var array
   */
  protected static $psrClassMap;

  /**
   * Seed.
   *
   * This is the path of the composer.json file that triggered the bootstrap.
   *
   * @var
   */
  protected static $seed;

  /**
   * {@inheritdoc}
   */
  public static function autoload($class) {
    return static::autoloadPaths($class);
  }

  /**
   * {@inheritdoc}
   */
  public static function setClassMap(array $class_map) {
    static::$classMap = $class_map;
  }

  /**
   * {@inheritdoc}
   */
  public static function setPsrClassMap(array $class_map) {
    static::$psrClassMap = $class_map;
  }

  /**
   * {@inheritdoc}
   */
  public static function setSeed($seed) {
    static::$seed = $seed;
  }

  /**
   * Prefixes a class with preceding backslash if necessary.
   *
   * @param string $class
   *   The class to prefix.
   *
   * @return string
   *   The prefixed class.
   */
  protected static function prefixClass($class) {
    if (strpos($class, '\\') === 0) {
      return $class;
    }
    return '\\' . $class;
  }

  /**
   * Helper function to autoload path based files.
   *
   * @param string $class
   *   The requested class.
   *
   * @return bool
   *   TRUE if the class was found. FALSE otherwise.
   */
  protected static function autoloadPaths($class) {
    $class = static::prefixClass($class);
    if (!in_array($class, array_keys(static::$classMap))) {
      return FALSE;
    }
    try {
      $resolver = new TokenResolver(static::$classMap[$class]);
      $finder = $resolver->resolve();
      // Have the path finder require the file and return TRUE or FALSE if it
      // found the file or not.
      $finder->requireFile(static::$seed);
      return TRUE;
    }
    catch (ClassLoaderException $e) {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function registerPsr(\Composer\Autoload\ClassLoader $loader) {
    $psrs = array(
      'psr-0' => 'add',
      'psr-4' => 'addPsr4',
    );
    foreach ($psrs as $psr => $loader_method) {
      foreach (static::$psrClassMap[$psr] as $partial_namespace => $tokenized_path) {
        try {
          $resolver = new TokenResolver($tokenized_path);
          $finder = $resolver->resolve();
          // Get the real path of the prefix.
          $real_path = $finder->find(static::$seed);
          $loader->{$loader_method}($partial_namespace, $real_path);
        }
        catch (ClassLoaderException $e) {}
      }
    }
  }

}
