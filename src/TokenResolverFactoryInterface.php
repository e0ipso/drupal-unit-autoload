<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\TokenResolverFactoryInterface.
 */

namespace Drupal\Composer\ClassLoader;


interface TokenResolverFactoryInterface {

  /**
   * Creates a token resolver from a tokenized path.
   *
   * @param string $tokenized_path
   *   The path containing the tokens.
   *
   * @return TokenResolverInterface
   *   The token resolver object.
   */
  public function factory($tokenized_path);

}
