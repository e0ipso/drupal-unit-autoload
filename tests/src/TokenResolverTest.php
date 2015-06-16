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

}
