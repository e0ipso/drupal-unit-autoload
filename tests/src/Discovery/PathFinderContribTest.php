<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Discovery\PathFinderContribTest.
 */

namespace Drupal\Composer\ClassLoader\Discovery\Tests;

use Drupal\Composer\ClassLoader\Discovery\PathFinderContrib;
use Mockery as m;

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

    // Assert path finder is set.
    $property = new \ReflectionProperty($pathFinder, 'coreFinder');
    $property->setAccessible(true);
    $value = $property->getValue($pathFinder);
    $this->assertInstanceOf('\Drupal\Composer\ClassLoader\Discovery\PathFinderInterface', $value);
  }

  /**
   * Tests that find() works properly.
   *
   * @covers ::find()
   */
  public function testFind() {
    // 1. Test successful path.
    $pathFinder = new PathFinderContrib(['', 'testmodule']);
    $path = $pathFinder->find('data/docroot/sites/all/modules/testmodule/composer.json');
    $this->assertEquals(realpath('data/docroot/sites/all/modules/testmodule'), $path);
  }

  /**
   * Tests that ::find() works properly.
   *
   * @expectedException \Drupal\Composer\ClassLoader\ClassLoaderException
   *
   * @covers ::find()
   */
  public function test_find__noDrupal() {
    // 2. Test seed not in Drupal root.
    $pathFinder = new PathFinderContrib(['data/acme.inc', 'testmodule']);
    $pathFinder->find(__DIR__);
  }

  /**
   * Tests that ::find() works properly.
   *
   * @expectedException \Drupal\Composer\ClassLoader\ClassLoaderException
   *
   * @covers ::find()
   */
  public function test_find__noContrib() {
    // 3. Test seed not in Drupal contrib.
    $coreFinder = m::mock('\Drupal\Composer\ClassLoader\Discovery\PathFinderCore');
    $coreFinder->shouldReceive('find')->andReturn('data/docroot/')->once();
    $pathFinder = new PathFinderContrib(['data/docroot/sites/all/modules/testmodule/composer.json', 'testmodule2'], $coreFinder);
    $pathFinder->find(__DIR__);
  }

  /**
   * Tests that ::isWantedContrib() works properly.
   *
   * @covers ::isWantedContrib()
   */
  public function test_isWantedContrib() {
    $pathFinder = new PathFinderContrib(['', 'testmodule']);
    $dir = new \SplFileInfo('data/docroot/sites/all/modules/testmodule');

    $reflection_object = new \ReflectionObject($pathFinder);
    $method = $reflection_object->getMethod('isWantedContrib');
    $method->setAccessible(true);
    $output = $method->invokeArgs($pathFinder, [$dir]);

    $this->assertTrue($output);
  }

}

