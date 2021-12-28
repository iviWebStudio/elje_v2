<?php

namespace Elje\Blocks\Tests\Registry;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Registry\Container as ContainerTest;
use Elje\Blocks\Registry\FactoryType;
use Elje\Blocks\Tests\Mocks\MockTestDependency;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Tests the Container functionality
 * This also implicitly tests the FactoryType and SharedType classes.
 *
 * @since 1.0.0
 * @group testing
 */
class Container extends TestCase {
	private $container;

	/**
	 * Sets up the fixture.
	 *
	 * @since 1.0.0
	 */
	public function setUp()
	: void {
		$this->container = new ContainerTest;
	}

	/**
	 * @since 1.0.0
	 */
	public function test_factory()
	: void {
		$factory = $this->container->factory( function() {
			return 'foo';
		} );
		$this->assertInstanceOf( FactoryType::class, $factory );
	}

	/**
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function test_registering_factory_type()
	: void {
		$this->container->register(
			MockTestDependency::class,
			$this->container->factory(
				function() {
					return new MockTestDependency;
				}
			)
		);
		$instanceA = $this->container->get( MockTestDependency::class );
		$instanceB = $this->container->get( MockTestDependency::class );

		// should not be the same instance;
		$this->assertNotSame( $instanceA, $instanceB );
	}

	/**
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function test_registering_shared_type()
	: void {
		$this->container->register(
			MockTestDependency::class,
			function() {
				return new MockTestDependency;
			}
		);
		$instanceA = $this->container->get( MockTestDependency::class );
		$instanceB = $this->container->get( MockTestDependency::class );

		// should not be the same instance;
		$this->assertSame( $instanceA, $instanceB );
	}

	/**
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function test_registering_shared_type_dependent_on_another_shared_type()
	: void {
		$this->container->register(
			MockTestDependency::class . 'A',
			function() {
				return new MockTestDependency;
			}
		);
		$this->container->register(
			MockTestDependency::class . 'B',
			function( $container ) {
				return new MockTestDependency(
					$container->get( MockTestDependency::class . 'A' )
				);
			}
		);
		$instanceA = $this->container->get( MockTestDependency::class . 'A' );
		$instanceB = $this->container->get( MockTestDependency::class . 'B' );

		// should not be the same instance
		$this->assertNotSame( $instanceA, $instanceB );

		// dependency on B should be the same as A
		$this->assertSame( $instanceA, $instanceB->dependency );
	}
}
