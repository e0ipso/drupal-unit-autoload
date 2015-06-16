<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Tests\LoaderTest.
 */

namespace Drupal\Composer\ClassLoader\Tests;
use Drupal\Composer\ClassLoader\Loader;

/**
 * Class LoaderTest
 * @coversDefaultClass \Drupal\Composer\ClassLoader\Loader
 * @package Drupal\Composer\ClassLoader\Tests
 */
class LoaderTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests that Loader::autoload() works properly.
   *
   * @covers ::autoload()
   * @covers ::autoloadPaths()
   * @covers ::unprefixClass()
   */
  public function test_autoload() {
    Loader::setClassMap([
      '\\Acme' => './data/acme.inc',
    ]);
    Loader::setSeed(__DIR__);
    $this->assertTrue(Loader::autoload('Acme'));
  }

  /**
   * Tests that Loader::autoload() works properly.
   *
   * @covers ::autoload()
   * @covers ::autoloadPaths()
   */
  public function test_autoload_unexisting() {
    Loader::setClassMap([
      '\\Acme' => './data/acme.inc',
    ]);
    Loader::setSeed(__DIR__);
    $this->assertFalse(Loader::autoload('Invalid'));
  }

  /**
   * Tests that Loader::autoload() works properly.
   *
   * @covers ::autoload()
   * @covers ::autoloadPaths()
   */
  public function test_autoload_finderException() {
    Loader::setClassMap([
      '\\Acme' => 'DRUPAL_ROOT/file.inc',
    ]);
    Loader::setSeed(__DIR__);
    $this->assertFalse(Loader::autoload('Acme'));
  }

  /**
   * Tests that Loader::setClassMap works properly.
   *
   * @dataProvider setClassMapProvider
   *
   * @covers ::setClassMap()
   */
  public function test_setClassMap($given, $expected) {
    Loader::setClassMap($given);
    $reflection_property = new \ReflectionProperty('\Drupal\Composer\ClassLoader\Loader', 'classMap');
    $reflection_property->setAccessible(TRUE);
    $value = $reflection_property->getValue();
    $this->assertEquals($expected, $value);
  }

  /**
   * Provider for test_setClassMap.
   */
  public static function setClassMapProvider() {
    return [
      [['\\Foo' => 'bar'], ['Foo' => 'bar']],
      [['Foo' => 'bar'], ['Foo' => 'bar']],
    ];
  }

  /**
   * Tests that Loader::setSeed works properly.
   *
   * @covers ::setSeed()
   */
  public function test_setSeed() {
    $seed = 'Lorem ipsum';
    Loader::setSeed($seed);
    $reflection_property = new \ReflectionProperty('\Drupal\Composer\ClassLoader\Loader', 'seed');
    $reflection_property->setAccessible(TRUE);
    $value = $reflection_property->getValue();
    $this->assertEquals($seed, $value);
  }

}
