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
  protected $classMap;

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
  protected $psrClassMap;

  /**
   * Seed.
   *
   * This is the path of the composer.json file that triggered the bootstrap.
   *
   * @var
   */
  protected $seed;

  /**
   * The token resolver instance.
   *
   * @var TokenResolverFactoryInterface
   */
  protected $tokenResolverFactory;

  /**
   * Constructs a Loader object.
   *
   * @param string $seed.
   *   This is the path of the composer.json file that triggered the bootstrap.
   * @param TokenResolverFactoryInterface $token_resolver_factory
   *   Factory object to create token resolvers.
   */
  public function __construct($seed, TokenResolverFactoryInterface $token_resolver_factory = NULL) {
    $this->seed = $seed;
    $this->tokenResolverFactory = $token_resolver_factory ?: new TokenResolverFactory();
  }

  /**
   * {@inheritdoc}
   */
  public function autoload($class) {
    return $this->autoloadPaths($class);
  }

  /**
   * {@inheritdoc}
   */
  public function setClassMap(array $class_map) {
    // Remove the leading \ from the class names.
    $unprefixed_class_map = array();
    foreach ($class_map as $class_name => $tokenized_path) {
      $unprefixed_class_map[static::unprefixClass($class_name)] = $tokenized_path;
    }
    $this->classMap = $unprefixed_class_map;
  }

  /**
   * {@inheritdoc}
   */
  public function setPsrClassMap(array $class_map) {
    // Remove the leading \ from the partial namespaces.
    $unprefixed_class_map = array();
    foreach ($class_map as $psr => $psr_class_map) {
      $unprefixed_class_map[$psr] = array();
      foreach ($psr_class_map as $class_name => $tokenized_path) {
        $unprefixed_class_map[$psr][static::unprefixClass($class_name)] = $tokenized_path;
      }
    }
    $this->psrClassMap = $unprefixed_class_map;
  }

  /**
   * {@inheritdoc}
   */
  public function setSeed($seed) {
    $this->seed = $seed;
  }

  /**
   * Strips a class name of a preceding backslash if necessary.
   *
   * @param string $class
   *   The class to prefix.
   *
   * @return string
   *   The prefixed class.
   */
  protected static function unprefixClass($class) {
    if (strpos($class, '\\') !== 0) {
      return $class;
    }
    return substr($class, 1);
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
  protected function autoloadPaths($class) {
    $class = static::unprefixClass($class);
    // If the class that PHP is trying to find is not in the class map, built
    // from the composer configuration, then bail.
    if (!in_array($class, array_keys($this->classMap))) {
      return FALSE;
    }
    try {
      $resolver = $this->tokenResolverFactory->factory($this->classMap[$class]);
      $finder = $resolver->resolve();
      // Have the path finder require the file and return TRUE or FALSE if it
      // found the file or not.
      $finder->requireFile($this->seed);
      return TRUE;
    }
    catch (ClassLoaderException $e) {
      // If there was an error, inform PHP that the class could not be found.
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function registerPsr(\Composer\Autoload\ClassLoader $loader) {
    // Composer's autoloader uses a different method to add each PSR partial
    // namespace.
    $psrs = array(
      'psr-0' => 'add',
      'psr-4' => 'addPsr4',
    );
    foreach ($psrs as $psr => $loader_method) {
      // The $psrClassMap contains an array for psr-0 and an array for psr-4.
      // Each one of those contains an array where the keys are the partial
      // namespace, and the value is an array of tokenized paths where those
      // partial namespaces can be found.
      // Ex:
      //  [
      //    'psr-0' => [
      //      [ 'Drupal\\plug\\' => ['DRUPAL_CONTRIB<plug>/lib', …] ],
      //    ],
      //  ]
      foreach ($this->psrClassMap[$psr] as $partial_namespace => $tokenized_paths) {
        if (!is_array($tokenized_paths)) {
          // If a string was passed, then convert it to an array for
          // consistency.
          $tokenized_paths = array($tokenized_paths);
        }
        foreach ($tokenized_paths as $tokenized_path) {
          try {
            // Find the real path for the tokenized one.
            $resolver = $this->tokenResolverFactory->factory($tokenized_path);
            $finder = $resolver->resolve();
            // Get the real path of the prefix.
            $real_path = $finder->find($this->seed);
            $loader->{$loader_method}($partial_namespace, $real_path);
          }
          catch (ClassLoaderException $e) {}
        }
      }
    }
  }

}
