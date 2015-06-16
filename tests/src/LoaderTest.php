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

}
