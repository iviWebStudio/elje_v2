<?php /** @noinspection PhpIncludeInspection */

namespace Elje\Blocks\Tests;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

// Require composer dependencies.
use Elje\Blocks\Installer;
use Elje\Blocks\Package;

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );
}

if ( ! $_tests_dir ) {
	$_try_tests_dir = __DIR__ . '/../../../../../tests/phpunit';
	if ( file_exists( $_try_tests_dir . '/includes/functions.php' ) ) {
		$_tests_dir = $_try_tests_dir;
	}
}

if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 *
 * @since 1.0.0
 */
function manually_load_plugins() {
	require ELJE_PLUGIN_DIR . '/elje.php';
}

tests_add_filter( 'muplugins_loaded', __NAMESPACE__ . '\\manually_load_plugins' );

/**
 * Manually install plugins being tested.
 *
 * @throws \Exception
 * @since 1.0.0
 */
function manually_install_plugins() {
	Package::container()->get( Installer::class )->maybe_create_tables();
}

tests_add_filter( 'setup_theme', __NAMESPACE__ . '\\manually_install_plugins' );

require $_tests_dir . '/includes/bootstrap.php';
