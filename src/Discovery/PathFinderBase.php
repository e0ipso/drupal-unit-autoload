<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderBase.
 */

namespace Drupal\Composer\ClassLoader\Discovery;


class PathFinderBase implements PathFinderInterface {

  /**
   * The relative path.
   *
   * @var string
   */
  protected $path;

  /**
   * Constructs a PathFinderBase object.
   *
   * @param string $path
   *   The relative path to find.
   */
  public function __construct($path) {
    $this->path = $path;
  }

}
