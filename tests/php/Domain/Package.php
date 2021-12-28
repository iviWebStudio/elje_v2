<?php
/** @noinspection PhpUndefinedClassInspection */

namespace Elje\Blocks\Tests\Domain\Package;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Domain\Package as TestedPackage;
use Elje\Blocks\Domain\Services\FeatureGating;
use WP_UnitTestCase;

/**
 * Tests the Package class
 *
 * @since $VID:$
 */
class Package extends WP_UnitTestCase {

	private function get_package()
	: TestedPackage {
		return new TestedPackage( '1.0.0', __DIR__, new FeatureGating() );
	}

	public function test_get_version()
	: void {
		$this->assertEquals( '1.0.0', $this->get_package()->get_version() );
	}

	public function test_get_path()
	: void {
		$package = $this->get_package();
		// test without relative
		$this->assertEquals( __DIR__ . '/', $package->get_path() );

		//test with relative
		$expect = __DIR__ . '/build/test';
		$this->assertEquals( $expect, $package->get_path( 'build/test' ) );
	}

	public function test_get_url()
	: void {
		$package  = $this->get_package();
		$test_url = plugin_dir_url( __FILE__ );
		// test without relative
		$this->assertEquals( $test_url, $package->get_url() );

		//test with relative
		$this->assertEquals(
			$test_url . 'build/test',
			$package->get_url( 'build/test' )
		);
	}
}
