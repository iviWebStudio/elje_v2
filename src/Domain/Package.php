<?php
namespace Elje\Blocks\Domain;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Domain\Services\FeatureGating;

/**
 * Main package class.
 * Returns information about the package and handles init.
 *
 * @since 1.0.0
 */
class Package {

	/**
	 * Holds the current version of the blocks plugin.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $version;

	/**
	 * Holds the main path to the blocks plugin directory.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $path;

	/**
	 * Holds the feature gating class instance.
	 *
	 * @var FeatureGating
	 * @since 1.0.0
	 */
	private $feature_gating;

	/**
	 * Constructor
	 *
	 * @param  string         $version         Version of the plugin.
	 * @param  string         $plugin_path     Path to the main plugin file.
	 * @param  FeatureGating  $feature_gating  Feature gating class instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $version, string $plugin_path, FeatureGating $feature_gating ) {
		$this->version        = $version;
		$this->path           = $plugin_path;
		$this->feature_gating = $feature_gating;
	}

	/**
	 * Returns the version of the plugin.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_version()
	: string {
		return $this->version;
	}

	/**
	 * Returns the path to the plugin directory.
	 *
	 * @param  string  $relative_path  If provided, the relative path will be
	 *                                 appended to the plugin path.
	 *
	 * @return string Plugin dir path.
	 * @since 1.0.0
	 */
	public function get_path( string $relative_path = '' )
	: string {
		return trailingslashit( $this->path ) . $relative_path;
	}

	/**
	 * Returns the url to the blocks plugin directory.
	 *
	 * @param  string  $relative_url  If provided, the relative url will be
	 *                                appended to the plugin url.
	 *
	 * @return string Plugin dir uri.
	 * @since 1.0.0
	 */
	public function get_url( string $relative_url = '' )
	: string {
		return plugin_dir_url( $this->path . '/index.php' ) . $relative_url;
	}

	/**
	 * Returns an instance of the the FeatureGating class.
	 *
	 * @return FeatureGating
	 * @since 1.0.0
	 */
	public function feature()
	: FeatureGating {
		return $this->feature_gating;
	}

	/**
	 * Checks if we're executing the code in an experimental build mode.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_experimental_build()
	: bool {
		return $this->feature()->is_experimental_build();
	}

	/**
	 * Checks if we're executing the code in an feature plugin or experimental build mode.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_feature_plugin_build()
	: bool {
		return $this->feature()->is_feature_plugin_build();
	}
}
