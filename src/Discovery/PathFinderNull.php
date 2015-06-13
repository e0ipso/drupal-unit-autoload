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
  public function requireFile() {
    require_once $this->path;
    return TRUE;
  }

}
