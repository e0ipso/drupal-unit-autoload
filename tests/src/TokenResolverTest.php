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
   */
  public function test_resolve_unexisting() {
    // 4. Unexisting path without a real token.
    $resolver = new TokenResolver('Lorem');
    $finder = $resolver->resolve();
    $this->assertNull($finder);
  }

}
