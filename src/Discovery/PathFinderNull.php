<?php
/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderNull.
 */

namespace Drupal\Composer\ClassLoader\Discovery;


class PathFinderNull extends PathFinderBase implements PathFinderInterface {

  /**
   * {@inheritdoc}
   */
  public function find($seed) {
    return '';
  }

}
