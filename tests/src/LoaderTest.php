<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Tests\LoaderTest.
 */

namespace Drupal\Composer\ClassLoader\Tests;
use Drupal\Composer\ClassLoader\Loader;
use Mockery as m;

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
    $loader = new Loader(__DIR__);
    $loader->setClassMap([
      '\\Acme' => './data/acme.inc',
    ]);
    $this->assertTrue($loader->autoload('Acme'));
  }

  /**
   * Tests that Loader::autoload() works properly.
   *
   * @covers ::autoload()
   * @covers ::autoloadPaths()
   */
  public function test_autoload_unexisting() {
    $loader = new Loader(__DIR__);
    $loader->setClassMap([
      '\\Acme' => './data/acme.inc',
    ]);
    $this->assertFalse($loader->autoload('Invalid'));
  }

  /**
   * Tests that Loader::autoload() works properly.
   *
   * @covers ::autoload()
   * @covers ::autoloadPaths()
   */
  public function test_autoload_finderException() {
    $loader = new Loader(__DIR__);
    $loader->setClassMap([
      '\\Acme' => 'DRUPAL_ROOT/file.inc',
    ]);
    $this->assertFalse($loader->autoload('Acme'));
  }

  /**
   * Tests that Loader::setClassMap works properly.
   *
   * @dataProvider setClassMapProvider
   *
   * @covers ::setClassMap()
   */
  public function test_setClassMap($given, $expected) {
    $loader = new Loader(__DIR__);
    $loader->setClassMap($given);
    $reflection_property = new \ReflectionProperty('\Drupal\Composer\ClassLoader\Loader', 'classMap');
    $reflection_property->setAccessible(TRUE);
    $value = $reflection_property->getValue($loader);
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
   * Tests that Loader::setPsrClassMap works properly.
   *
   * @dataProvider setPsrClassMapProvider
   *
   * @covers ::setPsrClassMap()
   */
  public function test_setPsrClassMap($given, $expected) {
    $loader = new Loader(__DIR__);
    $loader->setPsrClassMap($given);
    $reflection_property = new \ReflectionProperty('\Drupal\Composer\ClassLoader\Loader', 'psrClassMap');
    $reflection_property->setAccessible(TRUE);
    $value = $reflection_property->getValue($loader);
    $this->assertEquals($expected, $value);
  }

  /**
   * Provider for test_setClassMap.
   */
  public static function setPsrClassMapProvider() {
    return [
      [
        ['psr-4' => ['\\Foo' => 'bar'], 'psr-0' => ['\\Baz' => 'oof']],
        ['psr-4' => ['Foo' => 'bar'], 'psr-0' => ['Baz' => 'oof']]
      ],
      [
        ['psr-4' => ['Foo' => 'bar'], 'psr-0' => ['Baz' => 'oof']],
        ['psr-4' => ['Foo' => 'bar'], 'psr-0' => ['Baz' => 'oof']]
      ],
    ];
  }

  /**
   * Tests that Loader::setSeed works properly.
   *
   * @covers ::setSeed()
   */
  public function test_setSeed() {
    $loader = new Loader('Lorem ipsum');
    $seed = 'Dolor sit';
    $loader->setSeed($seed);
    $reflection_property = new \ReflectionProperty('\Drupal\Composer\ClassLoader\Loader', 'seed');
    $reflection_property->setAccessible(TRUE);
    $value = $reflection_property->getValue($loader);
    $this->assertEquals($seed, $value);
  }

  /**
   * Tests that Loader::registerPsr works properly.
   *
   * @covers ::registerPsr()
   */
  public function test_registerPsr() {
    $loader = new Loader('data/docroot/sites/all/modules/testmodule/composer.json');
    // Mock the \Composer\Autoload\ClassLoader loader.
    $classLoader = m::mock('\Composer\Autoload\ClassLoader');
    $classLoader
      ->shouldReceive('add')
      ->twice();
    $classLoader
      ->shouldReceive('addPsr4')
      ->once();

    $psrClassMap = [
      'psr-0' => [
        'Drupal\\plug\\' => [
          'DRUPAL_CONTRIB<testmodule>/testmodule.info',
          'DRUPAL_ROOT/file.inc'
        ],
      ],
      'psr-4' => [
        'Drupal\\Kitten\\' => 'DRUPAL_ROOT/file.inc',
      ],
    ];
    $loader->setPsrClassMap($psrClassMap);
    $loader->registerPsr($classLoader);
  }

}
