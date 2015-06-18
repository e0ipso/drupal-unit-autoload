<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Tests\Discovery\PathFinderCoreTest.
 */

namespace Drupal\Composer\ClassLoader\Tests\Discovery;
use Drupal\Composer\ClassLoader\Discovery\PathFinderCore;

/**
 * Class PathFinderCoreTest
 *
 * @coversDefaultClass \Drupal\Composer\ClassLoader\Discovery\PathFinderCore
 *
 * @package Drupal\Composer\ClassLoader\Tests\Discovery
 */
class PathFinderCoreTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests that ::find() works properly and covers the protected methods.
   *
   * @covers ::find()
   * @covers ::getParentDirectory()
   * @covers ::isDrupalRoot()
   */
  public function test_find() {
    // 1. Test successful path.
    $finder = new PathFinderCore(['/file.inc']);
    $found = $finder->find('data/docroot/sites/all/modules/testmodule/composer.json');

    $this->assertEquals(realpath('data/docroot/file.inc'), $found);
  }

  /**
   * Tests that ::find() works properly.
   *
   * @expectedException \Drupal\Composer\ClassLoader\ClassLoaderException
   *
   * @covers ::getParentDirectory()
   */
  public function test_getParentDirectory__noParent() {
    // 2. Test seed not in Drupal root.
    $pathFinder = new PathFinderCore(['data/acme.inc']);
    $pathFinder->find('file.inc');
  }

  /**
   * Tests that ::find() works properly.
   *
   * @expectedException \Drupal\Composer\ClassLoader\ClassLoaderException
   *
   * @covers ::getParentDirectory()
   */
  public function test_getParentDirectory__root() {
    // 3. Test seed in Drupal root throws exception.
    $pathFinder = new PathFinderCore(['/']);
    $class = new \ReflectionClass('\Drupal\Composer\ClassLoader\Discovery\PathFinderCore');
    $method = $class->getMethod('getParentDirectory');
    $method->setAccessible(true);
    $output = $method->invokeArgs($pathFinder, ['/']);
  }

}
