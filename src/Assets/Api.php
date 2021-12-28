<?php
namespace Elje\Blocks\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Domain\Package;
use Exception;

/**
 * The Api class provides an interface to various asset registration helpers.
 * Contains asset api methods
 *
 * @since 1.0.0
 */
class Api {
	/**
	 * Stores inline scripts already enqueued.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $inline_scripts = [];

	/**
	 * Reference to the Package instance
	 *
	 * @var Package
	 * @since 1.0.0
	 */
	private $package;

	/**
	 * Constructor for class
	 *
	 * @param  Package  $package  An instance of Package.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Package $package ) {
		$this->package = $package;
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param  string  $file  Local path to the file (relative to the plugin
	 *                        directory).
	 *
	 * @return string The cache buster value to use for the given file.
	 * @since 1.0.0
	 */
	protected function get_file_version( string $file )
	: string {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $this->package->get_path() . $file ) ) {
			return filemtime( $this->package->get_path( trim( $file, '/' ) ) );
		}

		return $this->package->get_version();
	}

	/**
	 * Retrieve the url to an asset for this plugin.
	 *
	 * @param  string  $relative_path  An optional relative path appended to the
	 *                                 returned url.
	 *
	 * @return string Assets url.
	 * @since 1.0.0
	 */
	protected function get_asset_url( string $relative_path = '' )
	: string {
		return $this->package->get_url( $relative_path );
	}

	/**
	 * Get src, version and dependencies given a script relative src.
	 *
	 * @param  string  $relative_src  Relative src to the script.
	 * @param  array   $dependencies  Optional. An array of registered script handles this script depends on.
	 *                                Default empty array.
	 *
	 * @return array src, version and dependencies of the script.
	 * @since        1.0.0
	 * @noinspection PhpIncludeInspection
	 */
	public function get_script_data( string $relative_src, array $dependencies = [] )
	: array {
		$src     = '';
		$version = '1.0.0';

		if ( $relative_src ) {
			$src        = $this->get_asset_url( $relative_src );
			$asset_path = $this->package->get_path(
				str_replace( '.js', '.asset.php', $relative_src )
			);

			if ( file_exists( $asset_path ) ) {
				$asset        = require $asset_path;
				$dependencies = isset( $asset['dependencies'] ) ? array_merge( $asset['dependencies'], $dependencies ) : $dependencies;
				$version      = ! empty( $asset['version'] ) ? $asset['version'] : $this->get_file_version( $relative_src );
			} else {
				$version = $this->get_file_version( $relative_src );
			}
		}

		return [
			'src'          => $src,
			'version'      => $version,
			'dependencies' => $dependencies,
		];
	}

	/**
	 * Registers a script according to `wp_register_script`, adding the correct prefix, and additionally loading translations.
	 * When creating script assets, the following rules should be followed:
	 *   1. All asset handles should have a `elje-` prefix.
	 *   2. If the asset handle is for a Block (in editor context) use the `-block` suffix.
	 *   3. If the asset handle is for a Block (in frontend context) use the `-block-frontend` suffix.
	 *   4. If the asset is for any other script being consumed or enqueued by the blocks plugin, use the `elje-blocks-` prefix.
	 *
	 * @param  string  $handle        Unique name of the script.
	 * @param  string  $relative_src  Relative url for the script to the path from plugin root.
	 * @param  array   $dependencies  Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param  bool    $has_i18n      Optional. Whether to add a script translation call to this file. Default: true.
	 *
	 * @throws Exception If the registered script has a dependency on itself.
	 * @since 1.0.0
	 */
	public function register_script( string $handle, string $relative_src, array $dependencies = [], bool $has_i18n = TRUE )
	: void {
		$script_data = $this->get_script_data( $relative_src, $dependencies );

		if ( in_array( $handle, $script_data['dependencies'], TRUE ) ) {
			if ( $this->package->feature()->is_development_environment() ) {
				eljeAdminNotice( printf( 'Script with handle %s had a dependency on itself which has been removed. This is an indicator that your JS code has a circular dependency that can cause bugs.', esc_html( $handle ) ), 'error' );
			} else {
				throw new Exception( sprintf( esc_html__( 'Script with handle %s had a dependency on itself. This is an indicator that your JS code has a circular dependency that can cause bugs.', $handle ), 'elje' ) );
			}
		}

		wp_register_script( $handle, $script_data['src'], $script_data['dependencies'], $script_data['version'], TRUE );

		if ( $has_i18n && function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $handle, 'elje', $this->package->get_path( 'languages' ) );
		}
	}

	/**
	 * Registers a style according to `wp_register_style`.
	 *
	 * @param  string  $handle        Name of the stylesheet. Should be unique.
	 * @param  string  $relative_src  Relative source of the stylesheet to the plugin path.
	 * @param  array   $deps          Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param  string  $media         Optional. The media for which this stylesheet has been defined. Default 'all'. Accepts media types like
	 *                                'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 *
	 * @since 1.0.0
	 */
	public function register_style( string $handle, string $relative_src, array $deps = [], string $media = 'all' )
	: void {
		$filename = str_replace( plugins_url( '/', __DIR__ ), '', $relative_src );
		$src      = $this->get_asset_url( $relative_src );
		$ver      = $this->get_file_version( $filename );
		wp_register_style( $handle, $src, $deps, $ver, $media );
	}

	/**
	 * Returns the appropriate asset path for loading either legacy builds or
	 * current builds.
	 *
	 * @param  string  $filename  Filename for asset path (without extension).
	 * @param  string  $type      File type (.css or .js).
	 *
	 * @return  string             The generated path.
	 * @since 1.0.0
	 */
	public function get_block_asset_build_path( string $filename, string $type = 'js' )
	: string {
		global $wp_version;
		$suffix = version_compare( $wp_version, '5.3', '>=' )
			? ''
			: '-legacy';

		return "build/$filename$suffix.$type";
	}

	/**
	 * Adds an inline script, once.
	 *
	 * @param  string  $handle  Script handle.
	 * @param  string  $script  Script contents.
	 *
	 * @since 1.0.0
	 */
	public function add_inline_script( string $handle, string $script )
	: void {
		if ( ! empty( $this->inline_scripts[ $handle ] ) && in_array( $script, $this->inline_scripts[ $handle ], TRUE ) ) {
			return;
		}

		wp_add_inline_script( $handle, $script );

		if ( isset( $this->inline_scripts[ $handle ] ) ) {
			$this->inline_scripts[ $handle ][] = $script;
		} else {
			$this->inline_scripts[ $handle ] = [ $script ];
		}
	}
}
