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

  const AUTOLOAD_FUNCTION = '\Drupal\Composer\ClassLoader\Loader::autoload';
  const COMPOSER_CONFIGURATION_NAME = 'composer.json';

  /**
   * Holds the composer autoloader.
   *
   * @var \Composer\Autoload\ClassLoader
   */
  protected $loader;

  /**
   * Constructs a AutoloaderBootstrap object.
   *
   * @param \Composer\Autoload\ClassLoader $loader
   *   The Composer class loader.
   */
  public function __construct(\Composer\Autoload\ClassLoader $loader) {
    $this->loader = $loader;
  }

  /**
   * Register the autoloader if it is not registered.
   */
  public function register() {
    if ($functions = spl_autoload_functions()) {
      if (array_search(static::AUTOLOAD_FUNCTION, $functions)) {
        return;
      }
    }
    // Parse the composer.json.
    $composer_config = json_decode(file_get_contents(static::COMPOSER_CONFIGURATION_NAME));
    Loader::setSeed('composer.json');
    $this->registerDrupalPaths($composer_config);
    $this->registerPsr($composer_config);
  }

  /**
   * Registers the autoloader.
   */
  protected static function load() {
    spl_autoload_register(static::AUTOLOAD_FUNCTION);
  }

  /**
   * Unregisters the autoloader.
   */
  protected static function unload() {
    spl_autoload_unregister(static::AUTOLOAD_FUNCTION);
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
    Loader::setClassMap((array) $composer_config->{'class-loader'}->{'drupal-path'});
    $this::load();
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
    Loader::setPsrClassMap(array(
      'psr-0' => $psr0,
      'psr-4' => $psr4,
    ));
    Loader::registerPsr($this->loader);
  }

}
