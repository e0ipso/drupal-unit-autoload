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
    $real_path = $this->find($seed) . $this->path;
    require_once $real_path;
  }

  /**
   * Cleans a directory path by removing /. from the end.
   *
   * @param string $dir_path
   *   The path name to clean.
   *
   * @return string
   *   The clean path name.
   */
  protected function cleanDirPath($dir_path) {
    // Remove annoying /. at the end.
    $dir_path = rtrim($dir_path, '.');
    $dir_path = rtrim($dir_path, '/');
    return $dir_path;
  }

}
