<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderContrib.
 */

namespace Drupal\Composer\ClassLoader\Discovery;


use Drupal\Composer\ClassLoader\ClassLoaderException;

class PathFinderContrib extends PathFinderBase implements PathFinderInterface {

  /**
   * The module name.
   *
   * @var string
   */
  protected $moduleName;

  /**
   * Constructs a PathFinderContrib object.
   *
   * @param string[] $options
   *   Constructor options. It contains, at least the relative path in the first
   *   position and the module name in the second.
   */
  public function __construct(array $options) {
    $this->path = $options[0];
    $this->moduleName = $options[1];
  }

  /**
   * {@inheritdoc}
   */
  public function find($seed) {
    // To find contrib, we need to know where core is first. Pass the empty
    // string to as the path so we get the path for core itself -and not a path
    // relative to the core install-.
    $core_finder = new PathFinderCore(array(''));
    $core_path = $core_finder->find($seed);

    // Create the RecursiveDirectoryIterator on the core directory.
    $core_directory = new \RecursiveDirectoryIterator($core_path . '/sites', \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
    // Create an iterator that will go recursively through all the files and
    // directories -because of SELF_FIRST-.
    $files_iterator = new \RecursiveIteratorIterator($core_directory, \RecursiveIteratorIterator::SELF_FIRST);
    // Iterate over all of the directories under the sites directory.
    foreach ($files_iterator as $path_name => $dir) {
      /** @var $dir \SplFileInfo */
      if (!$dir->isDir()) {
        // Do not scan all files, just directories.
        continue;
      }
      // Check if the current directory corresponds to the contrib we are
      // looking for.
      if ($this->isWantedContrib($dir)) {
        return $this->cleanDirPath($dir->getPathName()) . $this->path;
      }
    }
    throw new ClassLoaderException(sprintf('Drupal module "%s" could not be found in the Drupal tree that contains: %s.', $this->moduleName, $seed));
  }

  /**
   * Checks if the passed directory is the contrib module we are looking for.
   *
   * @param \SplFileInfo $dir
   *   The info object about the directory.
   * @return bool
   *   TRUE if the contrib is detected. FALSE otherwise.
   */
  protected function isWantedContrib(\SplFileInfo $dir) {
    $info_file = $this->cleanDirPath($dir->getPathName()) . DIRECTORY_SEPARATOR . $this->moduleName . '.info';
    return file_exists($info_file);
  }

}
