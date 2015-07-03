<?php
/**
 * Created by PhpStorm.
 * User: e0ipso
 * Date: 03/07/15
 * Time: 13:00
 */

namespace Drupal\Composer\ClassLoader;


interface AutoloaderBootstrapInterface {

  /**
   * Register the autoloader if it is not registered.
   */
  public function register();


  /**
   * Checks if the autoloader has been added.
   *
   * @return bool
   */
  public function checkLoadedAutoloader();

  /**
   * Gets the configuration for the drupal loader from the Composer loader.
   *
   * @return array
   *   The configuration array for the drupal loader.
   */
  public function getConfig();
  /**
   * Gets the class loader.
   *
   * @return \Composer\Autoload\ClassLoader
   *   The loader.
   */
  public function getClassLoader();

}
