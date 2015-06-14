<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderBase.
 */

namespace Drupal\Composer\ClassLoader\Discovery;


abstract class PathFinderBase implements PathFinderInterface {

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

  /**
   * {@inheritdoc}
   */
  public function requireFile($seed) {
    $real_path = $this->find($seed);
    require_once $real_path;
  }

}
