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
class AutoloaderBootstrap {

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
   * @param Loader $loader
   *   The loader object to use. NULL to auto-create one.
   */
  public function __construct(\Composer\Autoload\ClassLoader $classLoader, $seed = 'composer.json', Loader $loader = NULL) {
    $this->classLoader = $classLoader;
    $this->seed = $seed;
    $this->loader = $loader ?: new Loader($seed);
  }

  /**
   * Register the autoloader if it is not registered.
   */
  public function register() {
    if ($this->checkLoadedAutoloader()) {
      return;
    }
    // Parse the composer.json.
    $composer_config = json_decode(file_get_contents(static::COMPOSER_CONFIGURATION_NAME));
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
    if (empty($composer_config->{'class-loader'}->{'drupal-path'})) {
      return;
    }
    $this->loader->setClassMap((array) $composer_config->{'class-loader'}->{'drupal-path'});
    $this->load();
  }

  /**
   * Use Composer's autoloader to register the PRS-0 and PSR-4 paths.
   *
   * @param object $composer_config
   *   The Composer configuration.
   */
  protected function registerPsr($composer_config) {
    $psr0 = $psr4 = array();
    if (!empty($composer_config->{'class-loader'}->{'psr-0'})) {
      $psr0 = (array) $composer_config->{'class-loader'}->{'psr-0'};
    }
    if (!empty($composer_config->{'class-loader'}->{'psr-4'})) {
      $psr4 = (array) $composer_config->{'class-loader'}->{'psr-4'};
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
   * Checks if the autoloader has been added.
   *
   * @return bool
   */
  public function checkLoadedAutoloader() {
    $functions = spl_autoload_functions();
    return in_array(array($this->loader, static::AUTOLOAD_METHOD), $functions);
  }

}
