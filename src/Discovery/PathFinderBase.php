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
   * @param string[] $options
   *   Constructor options. It contains, at least the relative path in the first
   *   position.
   */
  public function __construct(array $options) {
    $this->path = $options[0];
  }

  /**
   * {@inheritdoc}
   */
  public function requireFile($seed) {
    $real_path = $this->find($seed);
    require_once $real_path;
  }

}