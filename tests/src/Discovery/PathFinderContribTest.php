<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderContribTest.
 */

namespace Drupal\Composer\ClassLoader\Discovery\Tests;

use Drupal\Composer\ClassLoader\Discovery\PathFinderContrib;

/**
 * Class PathFinderContribTest
 * @coversDefaultClass \Drupal\Composer\ClassLoader\Discovery\PathFinderContrib
 * @package Drupal\Composer\ClassLoader\Discovery\Tests
 */
class PathFinderContribTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests that constructor works properly.
   *
   * @covers ::__construct()
   */
  public function testConstructor() {
    $pathFinder = new PathFinderContrib(['./testFolder/', 'mymodule']);

    // Assert path is set.
    $property = new \ReflectionProperty($pathFinder, 'path');
    $property->setAccessible(true);
    $value = $property->getValue($pathFinder);
    $this->assertEquals('./testFolder/', $value);

    // Assert module name is set.
    $property = new \ReflectionProperty($pathFinder, 'moduleName');
    $property->setAccessible(true);
    $value = $property->getValue($pathFinder);
    $this->assertEquals('mymodule', $value);
  }

}

