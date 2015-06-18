<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderCore.
 */

namespace Drupal\Composer\ClassLoader\Discovery;

use Drupal\Composer\ClassLoader\ClassLoaderException;

class PathFinderCore extends PathFinderBase implements PathFinderInterface {

  /**
   * {@inheritdoc}
   */
  public function find($seed) {
    $seed = realpath($seed);
    $directory = is_dir($seed) ? $seed : dirname($seed);

    // Starting at the directory containing the seed path, we go one directory
    // up and up and up until we reach the Drupal root.
    do {
      if ($this->isDrupalRoot($directory)) {
        return $directory . $this->path;
      }
    }
    while ($directory = $this->getParentDirectory($directory));
    // @codeCoverageIgnoreStart
    // If we have not returned, that means that the Drupal core directory could
    // not be found.
    throw new ClassLoaderException(sprintf('Drupal core directory could not be found as a parent of: %s.', $seed));
    // @codeCoverageIgnoreEnd
  }

  /**
   * Checks if the passed directory is the Drupal root.
   *
   * @param string $directory
   *   The directory path.
   *
   * @return bool
   *   TRUE if the passed directory is the Drupal root.
   */
  protected function isDrupalRoot($directory) {
    // Check if there is a COPYRIGHT.txt file in the directory.
    $copyrightPath = $directory . DIRECTORY_SEPARATOR . 'COPYRIGHT.txt';
    if (!$check = file_exists($copyrightPath)) {
      return FALSE;
    }
    // Make sure that the COPYRIGHT.txt file corresponds to Drupal.
    $line = fgets(fopen($copyrightPath, 'r'));
    return strpos($line, 'All Drupal code is Copyright') === 0;
  }

  /**
   * Gets the parent directory iterator.
   *
   * @param string $directory
   *   The current directory path.
   *
   * @throws ClassLoaderException
   *   If no parent directory could be found.
   *
   * @return string
   *   The parent directory.
   */
  protected function getParentDirectory($directory) {
    // Get the parent directory.
    if ($directory === realpath('/')) {
      throw new ClassLoaderException(sprintf('Could not find the parent directory of "%s".', $directory));
    }
    return dirname($directory);
  }

}
