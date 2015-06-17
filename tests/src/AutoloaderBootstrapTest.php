<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Tests\AutoloaderBootstrapTest.
 */

namespace Drupal\Composer\ClassLoader\Tests;

use Drupal\Composer\ClassLoader\AutoloaderBootstrap;
use Mockery as m;
/**
 * Class AutoloaderBootstrapTest
 *
 * @coversDefaultClass Drupal\Composer\ClassLoader\AutoloaderBootstrap
 *
 * @package Drupal\Composer\ClassLoader\Tests
 */
class AutoloaderBootstrapTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests the ::__construct() method.
   */
  public function test___construct() {
    $loader = m::mock('\Composer\Autoload\ClassLoader');
    $autoloader = new AutoloaderBootstrap($loader);
    $reflection_property = new \ReflectionProperty(get_class($autoloader), 'loader');
    $reflection_property->setAccessible(TRUE);
    $value = $reflection_property->getValue($autoloader);
    $this->assertEquals($loader, $value);
    $reflection_property = new \ReflectionProperty(get_class($autoloader), 'seed');
    $reflection_property->setAccessible(TRUE);
    $value = $reflection_property->getValue($autoloader);
    $this->assertEquals('composer.json', $value);
  }

  /**
   * Tests the ::register() method.
   *
   * @covers ::register()
   * @covers ::load()
   * @covers ::registerDrupalPaths()
   * @covers ::checkLoadedAutoloader()
   */
  public function test_register() {
    $loader = m::mock('\Composer\Autoload\ClassLoader');
    $autoloader = new AutoloaderBootstrap($loader, 'data/docroot/sites/all/modules/testmodule/composer.json');
    $autoloader->register();

    $this->assertTrue($autoloader::checkLoadedAutoloader());
    // Make sure that calling to register a second time does not fail.
    $autoloader->register();
    $this->assertTrue($autoloader::checkLoadedAutoloader());
  }


}
