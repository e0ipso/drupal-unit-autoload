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
    if (!empty($directory) && is_dir($directory) && file_exists($directory . DIRECTORY_SEPARATOR . '/index.php')) {
      // Drupal 7 root.
      // We check for the presence of 'modules/field/field.module' to differentiate this from a D6 site
      return (file_exists($directory . DIRECTORY_SEPARATOR . 'includes/common.inc')
        && file_exists($directory . DIRECTORY_SEPARATOR . 'misc/drupal.js')
        && file_exists($directory . DIRECTORY_SEPARATOR . 'modules/field/field.module'));
    }
    return FALSE;
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
