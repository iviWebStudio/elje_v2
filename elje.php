<?php
/**
 * Plugin Name: ELJE
 * Description: ELJE Extension is a Gutenberg block Plugin for managing site setting and creating dynamic blog listing, grid, slider etc.
 * Version: 1.0.0
 * Author: ELJE Group
 * Author URI: https://elje-group.com/
 * Requires PHP: 7.1.0
 * Requires at least: 5.6
 * Text Domain: elje
 * Domain Path: /languages/
 *
 * @package      Elje
 * @version      1.0.0
 * @noinspection PhpFullyQualifiedNameUsageInspection
 * @noinspection GlobalVariableUsageInspection
 * @noinspection PhpIncludeInspection
 */
if ( ! defined('ABSPATH')) {
	die('Wanna hack?');
}

/**
 * Admin notice helper.
 *
 * @param  string  $message  Message for noticing.
 * @param  string  $type     Notice type. Supported values - "error", "warning", "info", "success". Default-"info".
 *
 * @since 1.0.0
 */
function eljeAdminNotice(string $message = '', string $type = 'info') {
	add_action(
		'admin_notices',
		function() use ($message, $type) {
			printf('<div class="notice notice-%s"><p>ELJE Gutenberg: %s</p></div>', $type, esc_html__($message, 'elje'));
		}
	);
}

/**
 * Checking passed array/object for errors and non-empty.
 *
 * @param  mixed  $scalar
 * @param  mixed  $key
 *
 * @return bool True if no empty/error found.
 * @since 1.0.0
 */
function checkVal($scalar, $key = NULL) {

	if (empty($scalar) || is_wp_error($scalar) || is_array($key) || is_object($key)) {
		return FALSE;
	}

	return (is_array($scalar) && ! empty($scalar[ $key ]) && ! is_wp_error($scalar[ $key ])) || (is_object($scalar) && ! empty($scalar->$key) && ! is_wp_error($scalar->$key));
}

if (defined('ELJE_PLUGIN_FILE') || defined('ELJE_PLUGIN_DIR') || defined('ELJE_INC_DIR') || defined('ELJE_PLUGIN_VERSION')) {
	eljeAdminNotice('One or more required constants reserved. Please reinstall plugin or send feedback.', 'error');

	return;
}

/**
 * @const Plugin file path.
 * @since 1.0.0
 */
define('ELJE_PLUGIN_FILE', __FILE__);

/**
 * @const Plugin directory path.
 * @since 1.0.0
 */
define('ELJE_PLUGIN_DIR', __DIR__);

/**
 * @const Plugin directory url with trailing slash.
 * @since 1.0.0
 */
define('ELJE_PLUGIN_URI', plugin_dir_url(__FILE__));

/**
 * @const Plugin version.
 * @since 1.0.0
 */
define('ELJE_PLUGIN_VERSION', '1.0.0');

/**
 * Autoload packages.
 *
 * @since 1.0.0
 */
$autoloader = __DIR__ . '/vendor/autoload_packages.php';
if ( ! is_readable($autoloader)) {
	eljeAdminNotice('Your installation is incomplete. Please run `composer install`.');

	return;
}

require $autoloader;

add_action('plugins_loaded', [
	'\Elje\Blocks\Package',
	'init',
]);
