<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\Tests\TokenResolverFactoryTest.
 */

namespace Drupal\Composer\ClassLoader\Tests;
use Drupal\Composer\ClassLoader\TokenResolverFactory;

/**
 * Class TokenResolverFactoryTest
 *
 * @coversDefaultClass Drupal\Composer\ClassLoader\TokenResolverFactory
 *
 * @package Drupal\Composer\ClassLoader\Tests
 */
class TokenResolverFactoryTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests that ::factory() works.
   *
   * @covers ::factory()
   */
  public function test_factory() {
    $factory = new TokenResolverFactory();
    $resolver = $factory->factory('Lorem');
    $this->assertInstanceOf('\Drupal\Composer\ClassLoader\TokenResolverInterface', $resolver);
  }

}
