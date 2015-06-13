<?php

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

  /**
   * Register the autoloader if it is not registered.
   */
  public function register() {
    if ($functions = spl_autoload_functions()) {
      if (array_search(static::AUTOLOAD_FUNCTION, $functions)) {
        return;
      }
    }
    $this::load();
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

}

// Register the class loader.
$bootstrap = new AutoloaderBootstrap();
$bootstrap->register();
