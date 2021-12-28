<?php
/** @noinspection PhpUndefinedClassInspection */

namespace Elje\Blocks\Tests\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Assets\Api;
use Elje\Blocks\Package;
use Elje\Blocks\Tests\Mocks\AssetDataRegistryMock;
use InvalidArgumentException;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectException;

/**
 * Tests for the AssetDataRegistry class.
 *
 * @since 1.0.0
 */
class AssetDataRegistry extends \WP_UnitTestCase {
	use ExpectException;

	private $registry;

	/**
	 * Runs the routine before each test is executed.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function setUp()
	: void {
		$this->registry = new AssetDataRegistryMock(
			Package::container()->get( API::class )
		);
	}

	/**
	 * Initial data
	 *
	 * @since 1.0.0
	 */
	public function test_initial_data()
	: void {
		$this->assertEmpty( $this->registry->get() );
	}

	/**
	 * Add test data.
	 *
	 * @since 1.0.0
	 */
	public function test_add_data()
	: void {
		$this->registry->add( 'test', 'foo' );
		$this->assertEquals( [ 'test' => 'foo' ], $this->registry->get() );
	}

	/**
	 * Check test data.
	 *
	 * @since 1.0.0
	 */
	public function test_data_exists()
	: void {
		$this->registry->add( 'foo', 'lorem-ipsum' );
		$this->assertEquals( TRUE, $this->registry->exists( 'foo' ) );
		$this->assertEquals( FALSE, $this->registry->exists( 'bar' ) );
	}

	/**
	 * Lazy data check.
	 *
	 * @since 1.0.0
	 */
	public function test_add_lazy_data()
	: void {
		$lazy = function() {
			return 'bar';
		};
		$this->registry->add( 'foo', $lazy );
		// should not be in data yet
		$this->assertEmpty( $this->registry->get() );
		$this->registry->execute_lazy_data();
		// should be in data now
		$this->assertEquals( [ 'foo' => 'bar' ], $this->registry->get() );
	}

	/**
	 * Invalid case.
	 *
	 * @since 1.0.0
	 */
	public function test_invalid_key_on_adding_data()
	: void {
		$this->expectException( InvalidArgumentException::class );
		/** @noinspection PhpParamsInspection */
		$this->registry->add( [ 'some_value' ], 'foo' );
	}

	/**
	 * Already exists case.
	 * @since 1.0.0
	 */
	public function test_already_existing_key_on_adding_data()
	: void {
		$this->registry->add( 'foo', 'bar' );
		$this->expectException( InvalidArgumentException::class );
		$this->registry->add( 'foo', 'yar' );
	}
}
