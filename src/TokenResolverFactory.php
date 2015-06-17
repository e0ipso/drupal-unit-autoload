<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\TokenResolverFactory
 */

namespace Drupal\Composer\ClassLoader;

class TokenResolverFactory implements TokenResolverFactoryInterface {

  /**
   * {@inheritdoc}
   */
  public function factory($tokenized_path) {
    return new TokenResolver($tokenized_path);
  }

}
