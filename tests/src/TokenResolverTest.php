<?php
/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Tests\TokenResolverTest.
 */

namespace Drupal\Composer\ClassLoader\Tests;
use Drupal\Composer\ClassLoader\TokenResolver;

/**
 * Class TokenResolverTest
 *
 * @coversDefaultClass Drupal\Composer\ClassLoader\TokenResolver
 *
 * @package Drupal\Composer\ClassLoader\Tests
 */
class TokenResolverTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests that ::construct() is working.
   *
   * @covers ::__construct()
   */
  public function test___construct() {
    $tokenized_path = 'Lorem';
    $resolver = new TokenResolver($tokenized_path);
    $reflection_property = new \ReflectionProperty(get_class($resolver), 'path');
    $reflection_property->setAccessible(TRUE);
    $value = $reflection_property->getValue($resolver);
    $this->assertEquals($tokenized_path, $value);
  }

  /**
   * Tests that ::resolve() is working.
   *
   * @dataProvider resolveProvider
   *
   * @covers ::resolve()
   * @covers ::cleanToken()
   * @covers ::getToken()
   * @covers ::getClassName()
   * @covers ::parseArguments()
   */
  public function test_resolve($path, $expected) {
    $resolver = new TokenResolver($path);
    $finder = $resolver->resolve();
    $this->assertInstanceOf($expected, $finder);
  }

  /**
   * Provider method for test_resolve.
   */
  public static function resolveProvider() {
    return [
      // 1. Path not tokenized.
      ['data/acme.inc', '\Drupal\Composer\ClassLoader\Discovery\PathFinderNull'],
      // 2. Core path.
      ['DRUPAL_ROOT/file.inc', '\Drupal\Composer\ClassLoader\Discovery\PathFinderCore'],
      // 3. Contrib path.
      ['DRUPAL_CONTRIB<testmodule>/testmodule.info', '\Drupal\Composer\ClassLoader\Discovery\PathFinderContrib'],
    ];
  }

  /**
   * Tests that ::resolve() is working.
   *
   * @covers ::resolve()
   * @covers ::cleanToken()
   * @covers ::getToken()
   * @covers ::getClassName()
   * @covers ::parseArguments()
   */
  public function test_resolve_unexisting() {
    // 4. Unexisting path without a real token.
    $resolver = new TokenResolver('Lorem');
    $finder = $resolver->resolve();
    $this->assertNull($finder);
  }

  /**
   * Tests that the ::cleanToken is working.
   *
   * @expectedException \Drupal\Composer\ClassLoader\ClassLoaderException
   *
   * @covers ::cleanToken()
   */
  public function test_cleanToken() {
    $resolver = new TokenResolver('');
    $reflection_method = new \ReflectionMethod(get_class($resolver), 'cleanToken');
    $reflection_method->setAccessible(TRUE);
    $reflection_method->invoke($resolver);
  }

  /**
   * Tests that the ::cleanToken is working.
   *
   * @dataProvider hasTokenProvider
   *
   * @covers ::hasToken()
   */
  public function test_hasToken($path, $expected) {
    $resolver = new TokenResolver($path);
    $this->assertEquals($resolver->hasToken(), $expected);
  }

  /**
   * Provider method for test_hasToken.
   */
  public function hasTokenProvider() {
    return [
      ['DRUPAL_ROOT/includes', TRUE],
      ['DRUPAL_CONTRIB<my_module>/src/', TRUE],
      ['/lorem/ipsum/DRUPAL_ROOT/src/', TRUE],
      ['/lorem/ipsum/DRUPAL_CONTRIB<my_module>/src/', TRUE],
      ['src', FALSE],
      ['/lorem/ipsum/src', FALSE],
    ];
  }

  /**
   * Tests that the ::cleanToken is working.
   *
   * @dataProvider trimPathProvider
   *
   * @covers ::trimPath()
   */
  public function test_trimPath($path, $expected) {
    $resolver = new TokenResolver($path);
    $this->assertEquals($resolver->trimPath(), $expected);
  }

  /**
   * Provider method for test_trimPath.
   */
  public function trimPathProvider() {
    return [
      ['DRUPAL_ROOT/includes', 'DRUPAL_ROOT/includes'],
      ['DRUPAL_CONTRIB<my_module>/src/', 'DRUPAL_CONTRIB<my_module>/src/'],
      ['/lorem/ipsum/DRUPAL_ROOT/includes/', 'DRUPAL_ROOT/includes/'],
      ['/lorem/ipsum/DRUPAL_CONTRIB<my_module>/src/', 'DRUPAL_CONTRIB<my_module>/src/'],
      ['src', 'src'],
      [NULL, NULL],
    ];
  }

}
