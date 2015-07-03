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

  /**
   * Checks if the path contains a known token pattern.
   *
   * @return bool
   *   TRUE if the path contains a token. FALSE otherwise.
   */
  public function hasToken();

  /**
   * Gets the path by removing everything before the token.
   *
   * @return string
   *   The path starting with the token.
   */
  public function trimPath();

}
