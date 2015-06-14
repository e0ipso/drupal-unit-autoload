<?php

namespace Drupal\Composer\ClassLoader;

return function ($loader) {
  $bootstrap = new AutoloaderBootstrap($loader);
  // Register the class loader.
  $bootstrap->register();
};

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
    $this->registerDrupalPaths($composer_config);
    $this->registerPsr($composer_config);
  }

  /**
   * Destructs an AutoloaderBootstrap object.
   */
  public function __destruct() {
    $this::unload();
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
    Loader::setSeed('composer.json');
    $this::load();
  }

  /**
   * Use Composer's autoloader to register the PRS-0 and PSR-4 paths.
   *
   * @param object $composer_config
   *   The Composer configuration.
   */
  protected function registerPsr($composer_config) {
    // TODO: Implement this.
  }

}
