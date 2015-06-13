<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\TokenResolverInterface.
 */

namespace Drupal\Composer\ClassLoader;


interface TokenResolverInterface {

  /**
   * Finds the token in the give path and returns the discovery object.
   *
   * @return Discovery\PathFinderInterface
   *   The path finder class or NULL.
   */
  public function resolve();
}
