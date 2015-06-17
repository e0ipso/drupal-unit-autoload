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
    // Try to create the iterator with the seed.
    $directory = NULL;
    if (is_dir($seed)) {
      $directory = new \DirectoryIterator($seed);
    }
    else {
      $directory = new \DirectoryIterator(dirname($seed));
    }
    // Starting at the directory containing the seed path, we go one directory
    // up and up and up until we reach the Drupal root.
    do {
      if ($this->isDrupalRoot($directory)) {
        return dirname($this->cleanDirPath($directory->getPathName())) . $this->path;
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
   * @param \DirectoryIterator $directory
   *   The directory iterator item.
   *
   * @return bool
   *   TRUE if the passed directory is the Drupal root.
   */
  protected function isDrupalRoot(\DirectoryIterator $directory) {
    // We need to clone the $directory object to avoid modifying its internal
    // operator.
    $d = clone $directory;
    // Check if there is a COPYRIGHT.txt file in the directory.
    $copyrightPath = $d->getPath() . DIRECTORY_SEPARATOR . 'COPYRIGHT.txt';
    $check = file_exists($copyrightPath);
    if ($check) {
      // Make sure that the COPYRIGHT.txt file corresponds to Drupal.
      $line = fgets(fopen($copyrightPath, 'r'));
      $check = (strpos($line, 'All Drupal code is Copyright') === 0);
    }
    return $check;
  }

  /**
   * Gets the parent directory iterator.
   *
   * @param \DirectoryIterator $directory
   *   The current directory iterator.
   *
   * @throws ClassLoaderException
   *   If no parent directory could be found.
   *
   * @return \DirectoryIterator
   *   The parent directory.
   */
  protected function getParentDirectory(\DirectoryIterator $directory) {
    // Get the path name of the directory.
    $path_name = dirname($directory->getPathname());

    // Get the parent directory and return a DirectoryIterator.
    $path_info = pathinfo($path_name);
    if (!empty($path_info['dirname']) && $path_info['dirname'] !== '/') {
      try {
        return new \DirectoryIterator($path_info['dirname']);
      }
      // @codeCoverageIgnoreStart
      catch (\UnexpectedValueException $e) {}
    }
    // @codeCoverageIgnoreEnd
    throw new ClassLoaderException(sprintf('Could not find the parent directory of "%s".', $path_name));
  }

}
