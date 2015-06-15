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
   * @covers ::autoload()
   */
  public function testAutoload() {
    Loader::setClassMap([
      '\\Acme' => './data/acme.inc'
    ]);
    Loader::setSeed(__DIR__);
    $this->assertTrue(Loader::autoload('Acme'));
  }

}
