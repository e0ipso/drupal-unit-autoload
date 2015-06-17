<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderNullTest.
 */

namespace Drupal\Composer\ClassLoader\Discovery\Tests;

use Drupal\Composer\ClassLoader\Discovery\PathFinderNull;

/**
 * Class PathFinderNullTest
 *
 * @coversDefaultClass \Drupal\Composer\ClassLoader\Discovery\PathFinderNull
 *
 * @package Drupal\Composer\ClassLoader\Discovery\Tests
 */
class PathFinderNullTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests that find() works properly.
   *
   * @covers ::find()
   */
  public function testFind() {
    $pathFinder = new PathFinderNull(['data/acme.inc']);
    $path = $pathFinder->find(NULL);
    $this->assertEquals('data/acme.inc', $path);
  }

}

