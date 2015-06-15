<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderBaseTest.
 */

namespace Drupal\Composer\ClassLoader\Discovery\Tests;

use Drupal\Composer\ClassLoader\Discovery\PathFinderBase;

/**
 * Class PathFinderBase
 * @coversDefaultClass \Drupal\Composer\ClassLoader\Discovery\PathFinderBase
 * @package Drupal\Composer\ClassLoader\Discovery\Tests
 */
class PathFinderBaseTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests that PathFinderBase::cleanDirPath() works properly.
   * @dataProvider cleanDirPathProvider
   *
   * @covers ::cleanDirPath()
   */
  public function testCleanDirPath($given, $expected) {
    $pathFinder = new FakePathFinder(['']);

    $class = new \ReflectionClass('\Drupal\Composer\ClassLoader\Discovery\PathFinderBase');
    $method = $class->getMethod('cleanDirPath');
    $method->setAccessible(true);
    $output = $method->invokeArgs($pathFinder, [$given]);

    $this->assertEquals($expected, $output);
  }

  public function cleanDirPathProvider()
  {
    return array(
      array('./testFolder/.', './testFolder'),
      array('./testFolder', './testFolder'),
      array('./deep/testFolder/.', './deep/testFolder'),
      array('testFolder/.', 'testFolder'),
      array('deep/testFolder/.', 'deep/testFolder'),
    );
  }

}

class FakePathFinder extends PathFinderBase {

  public function find($seed) {

  }

}
