<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\AutoloaderBootstrap
 */

namespace Drupal\Composer\ClassLoader;

/**
 * Class AutoloaderBootstrap
 *
 * Includes the autoloader when including the package.
 *
 * @package Drupal\Composer\ClassLoader
 */
class AutoloaderBootstrap implements AutoloaderBootstrapInterface {

  const AUTOLOAD_METHOD = 'autoload';
  const COMPOSER_CONFIGURATION_NAME = 'composer.json';

  /**
   * Holds the composer autoloader.
   *
   * @var \Composer\Autoload\ClassLoader
   */
  protected $classLoader;

  /**
   * Holds the class loader.
   *
   * @var \Drupal\Composer\ClassLoader\Loader
   */
  protected $loader;

  /**
   * Holds the token resolver factory.
   *
   * @var TokenResolverFactoryInterface
   */
  protected $tokenFactory;

  /**
   * Holds the seed.
   *
   * @var string
   */
  protected $seed;

  /**
   * Constructs a AutoloaderBootstrap object.
   *
   * @param \Composer\Autoload\ClassLoader $classLoader
   *   The Composer class loader.
   * @param string $seed
   *   The seed to find the drupal projects.
   * @param LoaderInterface $loader
   *   The loader object to use. NULL to auto-create one.
   * @param TokenResolverFactoryInterface $token_factory
   *   The resolver factory.
   */
  public function __construct(\Composer\Autoload\ClassLoader $classLoader, $seed = 'composer.json', LoaderInterface $loader = NULL, TokenResolverFactoryInterface $token_factory = NULL) {
    $this->classLoader = $classLoader;
    $this->seed = $seed;
    $this->loader = $loader ?: new Loader($seed);
    $this->tokenFactory = $token_factory ?: new TokenResolverFactory();
  }

  /**
   * {@inheritdoc}
   */
  public function register() {
    if ($this->checkLoadedAutoloader()) {
      return;
    }
    // Parse the composer.json.
    $composer_config = $this->getConfig();
    $this->registerDrupalPaths($composer_config);
    $this->registerPsr($composer_config);
  }

  /**
   * Registers the autoloader.
   */
  protected function load() {
    spl_autoload_register(array($this->loader, static::AUTOLOAD_METHOD));
  }

  /**
   * Unregisters the autoloader.
   */
  protected function unload() {
    spl_autoload_unregister(array($this->loader, static::AUTOLOAD_METHOD));
  }

  /**
   * Register the path based autoloader.
   *
   * @param object $composer_config
   *   The Composer configuration.
   */
  protected function registerDrupalPaths($composer_config) {
    if (empty($composer_config['class-location'])) {
      return;
    }
    $this->loader->setClassMap((array) $composer_config['class-location']);
    $this->load();
  }

  /**
   * Use Composer's autoloader to register the PRS-0 and PSR-4 paths.
   *
   * @param array $composer_config
   *   The Composer configuration.
   */
  protected function registerPsr(array $composer_config) {
    $psr0 = $psr4 = array();
    if (!empty($composer_config['psr-0'])) {
      $psr0 = (array) $composer_config['psr-0'];
    }
    if (!empty($composer_config['psr-4'])) {
      $psr4 = (array) $composer_config['psr-4'];
    }
    if (empty($psr4) && empty($psr0)) {
      return;
    }
    $this->loader->setPsrClassMap(array(
      'psr-0' => $psr0,
      'psr-4' => $psr4,
    ));
    $this->loader->registerPsr($this->classLoader);
  }

  /**
   * {@inheritdoc}
   */
  public function checkLoadedAutoloader() {
    $functions = spl_autoload_functions();
    return in_array(array($this->loader, static::AUTOLOAD_METHOD), $functions);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig() {
    // Initialize empty configuration.
    $config = [
      'psr-0' => [],
      'psr-4' => [],
      'class-location' => [],
    ];

    // Find the tokenized paths.
    $psrs = array(
      'psr-0' => $this->classLoader->getPrefixes(),
      'psr-4' => $this->classLoader->getPrefixesPsr4(),
    );
    // Get all the PSR-0 and PSR-0 and detect the ones that have Drupal tokens.
    foreach ($psrs as $psr_type => $namespaces) {
      $namespaces = $namespaces ?: [];
      foreach ($namespaces as $prefix => $paths) {
        $token_paths = array();
        if (!is_array($paths)) {
          $paths = array($paths);
        }
        foreach ($paths as $path) {
          $token_resolver = $this->tokenFactory->factory($path);
          if (!$token_resolver->hasToken()) {
            continue;
          }
          $path = $token_resolver->trimPath();
          $token_paths[] = $path;
        }
        // If there were paths, add them to the config.
        if (!empty($token_paths)) {
          $config[$psr_type][$prefix] = $token_paths;
        }
      }
    }

    // Get the drupal path configuration.
    $composer_config = json_decode(file_get_contents(static::COMPOSER_CONFIGURATION_NAME));
    $config['class-location'] = array();
    if (isset($composer_config->autoload->{'class-location'})) {
      $config['class-location'] = array_merge($config['class-location'], (array) $composer_config->autoload->{'class-location'});
    }
    if (isset($composer_config->{'autoload-dev'}->{'class-location'})) {
      $config['class-location'] = array_merge($config['class-location'], (array) $composer_config->{'autoload-dev'}->{'class-location'});
    }

    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function getClassLoader() {
    return $this->classLoader;
  }

}
