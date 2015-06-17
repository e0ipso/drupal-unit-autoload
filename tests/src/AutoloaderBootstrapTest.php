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
    $reflection_property = new \ReflectionProperty(get_class($autoloader), 'classLoader');
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
   * @covers ::registerPsr()
   */
  public function test_register() {
    $loader = m::mock('\Composer\Autoload\ClassLoader');
    $loader
      ->shouldReceive('add')
      ->once();
    $loader
      ->shouldReceive('addPsr4')
      ->once();
    $autoloader = new AutoloaderBootstrap($loader, 'data/docroot/sites/all/modules/testmodule/composer.json');
    $autoloader->register();

    $this->assertTrue($autoloader->checkLoadedAutoloader());
    // Make sure that calling to register a second time does not fail.
    $autoloader->register();
    $this->assertTrue($autoloader->checkLoadedAutoloader());
  }

  /**
   * Tests the ::checkLoadedAutoloader() method.
   */
  public function test_checkLoadedAutoloader() {
    $class_loader = m::mock('\Composer\Autoload\ClassLoader');
    $loader = m::mock('\Drupal\Composer\ClassLoader\LoaderInterface');
    $loader
      ->shouldReceive(AutoloaderBootstrap::AUTOLOAD_METHOD);
    $autoloader = new AutoloaderBootstrap($class_loader, 'data/docroot/sites/all/modules/testmodule/composer.json', $loader);
    $this->assertFalse($autoloader->checkLoadedAutoloader());
    spl_autoload_register(array($loader, AutoloaderBootstrap::AUTOLOAD_METHOD));
    $this->assertTrue($autoloader->checkLoadedAutoloader());
  }

  /**
   * Tests the protected ::unload() method.
   *
   * @covers ::unload()
   */
  public function test_unload() {
    $loader = m::mock('\Composer\Autoload\ClassLoader');
    $autoloader = new AutoloaderBootstrap($loader, 'data/docroot/sites/all/modules/testmodule/composer.json');

    // First load it.
    $reflection_method = new \ReflectionMethod(get_class($autoloader), 'load');
    $reflection_method->setAccessible(TRUE);
    $reflection_method->invoke($autoloader);

    // Make sure it's added
    $this->assertTrue($autoloader->checkLoadedAutoloader());

    // Then unload it.
    $reflection_method = new \ReflectionMethod(get_class($autoloader), 'unload');
    $reflection_method->setAccessible(TRUE);
    $reflection_method->invoke($autoloader);

    // Make sure it's not added
    $this->assertFalse($autoloader->checkLoadedAutoloader());
  }

  /**
   * Tests the ::registerDrupalPaths() method.
   *
   * @covers ::registerDrupalPaths()
   */
  public function test_registerDrupalPaths_empty() {
    $loader = m::mock('\Composer\Autoload\ClassLoader');
    $autoloader = new AutoloaderBootstrap($loader, 'data/docroot/sites/all/modules/testmodule/composer.json');
    $reflection_method = new \ReflectionMethod(get_class($autoloader), 'registerDrupalPaths');
    $reflection_method->setAccessible(TRUE);
    $value = $reflection_method->invokeArgs($autoloader, [new \stdClass()]);
    $this->assertNull($value);
  }

  /**
   * Tests the ::registerPsr() method.
   *
   * @covers ::registerPsr()
   */
  public function test_registerPsr_empty() {
    $loader = m::mock('\Composer\Autoload\ClassLoader');
    $autoloader = new AutoloaderBootstrap($loader, 'data/docroot/sites/all/modules/testmodule/composer.json');
    $reflection_method = new \ReflectionMethod(get_class($autoloader), 'registerPsr');
    $reflection_method->setAccessible(TRUE);
    $value = $reflection_method->invokeArgs($autoloader, [new \stdClass()]);
    $this->assertNull($value);
  }

}
