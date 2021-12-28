<?php
/** @noinspection PhpUndefinedClassInspection */

namespace Elje\Blocks\Tests\Bootstrap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Package;
use Elje\Blocks\Registry\Container;
use WP_UnitTestCase;

/**
 * Test class for the bootstrap in the plugin main file
 * Contains Tests for the main file (elje.php) bootstrap.
 *
 * @since 0.0.1
 */
class MainFile extends WP_UnitTestCase {
	/**
	 * Holds an instance of the dependency injection container
	 *
	 * @var Container|null
	 * @since 1.0.0
	 */
	private $container;

	/**
	 * Ensure that container is reset between tests.
	 *
	 * @since 1.0.0
	 */
	public function setUp()
	: void {
		// reset container
		$this->container = Package::container( TRUE );
	}

	/**
	 * @since 1.0.0
	 */
	public function test_container_returns_same_instance()
	: void {
		$container = Package::container();
		$this->assertSame( $container, $this->container );
	}

	/**
	 * @since 1.0.0
	 */
	public function test_container_reset()
	: void {
		$container = Package::container( TRUE );
		$this->assertNotSame( $container, $this->container );
	}
}
