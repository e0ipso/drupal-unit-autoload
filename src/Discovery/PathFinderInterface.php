<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderInterface.
 */

namespace Drupal\Composer\ClassLoader\Discovery;


interface PathFinderInterface {

  /**
   * Finds and requires the current file.
   *
   * @throws \Drupal\Composer\ClassLoader\ClassLoaderException
   *   If the file could not be found.
   */
  public function requireFile($seed);

  /**
   * Finds the current file in the file system.
   *
   * @param string $seed
   *   A path where to start looking for. Typically it will be the composer.json
   *   file name that contains the configuration.
   *
   * @throws \Drupal\Composer\ClassLoader\ClassLoaderException
   *   If the file could not be found.
   *
   * @return string
   *   The name of the real path. NULL if the file was not found.
   */
  public function find($seed);

}
