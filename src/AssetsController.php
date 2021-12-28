<?php /** @noinspection PhpUndefinedClassInspection */
namespace Elje\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Assets\Api as AssetApi;

/**
 * AssetsController class.
 *
 * @since 1.0.0
 */
class AssetsController {

	/**
	 * Asset API interface for various asset registration.
	 *
	 * @var AssetApi
	 * @since 1.0.0
	 */
	private $api;

	/**
	 * Constructor.
	 *
	 * @param  AssetApi  $asset_api  Asset API interface for various asset registration.
	 *
	 * @since 1.0.0
	 */
	public function __construct( AssetApi $asset_api ) {
		$this->api = $asset_api;
		$this->init();
	}

	/**
	 * Initialize class features.
	 *
	 * @since 1.0.0
	 */
	protected function init()
	: void {
		add_action( 'init', [
			$this,
			'register_assets',
		] );
		add_action( 'body_class', [
			$this,
			'add_theme_body_class',
		], 1 );
		add_action( 'admin_body_class', [
			$this,
			'add_theme_body_class',
		], 1 );
		add_action( 'admin_enqueue_scripts', [
			$this,
			'update_block_style_dependencies',
		], 20 );
		add_action( 'wp_enqueue_scripts', [
			$this,
			'update_block_settings_dependencies',
		], 100 );
		add_action( 'admin_enqueue_scripts', [
			$this,
			'update_block_settings_dependencies',
		], 100 );
	}

	/**
	 * Register block scripts & styles.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function register_assets()
	: void {
		$this->register_style( 'elje-blocks-vendors-style', plugins_url( $this->api->get_block_asset_build_path( 'elje-blocks-vendors-style', 'css' ), __DIR__ ) );
		$this->register_style( 'elje-blocks-editor-style', plugins_url( $this->api->get_block_asset_build_path( 'elje-blocks-editor-style', 'css' ), __DIR__ ), [ 'wp-edit-blocks' ], 'all', TRUE );
		$this->register_style( 'elje-blocks-style', plugins_url( $this->api->get_block_asset_build_path( 'elje-blocks-style', 'css' ), __DIR__ ), [ 'elje-blocks-vendors-style' ], 'all', TRUE );

		$this->api->register_script( 'elje-blocks-middleware', 'build/elje-blocks-middleware.js', [], FALSE );
		$this->api->register_script( 'elje-blocks-data-store', 'build/elje-blocks-data.js', [ 'elje-blocks-middleware' ] );
		$this->api->register_script( 'elje-blocks-vendors', $this->api->get_block_asset_build_path( 'elje-blocks-vendors' ), [], FALSE );
		$this->api->register_script( 'elje-blocks-registry', 'build/elje-blocks-registry.js', [], FALSE );
		$this->api->register_script( 'elje-blocks', $this->api->get_block_asset_build_path( 'elje-blocks' ), [ 'elje-blocks-vendors' ], FALSE );
		$this->api->register_script( 'elje-blocks-shared-context', 'build/elje-blocks-shared-context.js', [] );
		$this->api->register_script( 'elje-blocks-shared-hocs', 'build/elje-blocks-shared-hocs.js', [], FALSE );

		/** @noinspection PhpStatementHasEmptyBodyInspection */
		if ( Package::feature()->is_feature_plugin_build() ) {
		}

		/** @noinspection PhpStatementHasEmptyBodyInspection */
		if ( Package::feature()->is_experimental_build() ) {
		}

		wp_add_inline_script(
			'elje-blocks-middleware',
			"var eljeBlocksMiddlewareConfig={" .
			"SiteApiNonce: '" . esc_js( wp_create_nonce( 'elje_api' ) ) . "'," .
			"eljeSiteApiNonceTimestamp: '" . esc_js( time() ) . "'};",
			'before'
		);
	}

	/**
	 * Add body classes to the frontend and within admin.
	 *
	 * @param  string|array  $classes  Array or string of CSS classnames.
	 *
	 * @return string|array Modified classnames.
	 * @since        1.0.0
	 * @noinspection MissingParameterTypeDeclarationInspection
	 * @noinspection MissingReturnTypeInspection
	 */
	public function add_theme_body_class( $classes ) {
		$class = 'theme-' . get_template();

		if ( is_array( $classes ) ) {
			$classes[] = $class;
		} else {
			$classes .= ' ' . $class . ' ';
		}

		return $classes;
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param  string  $file  Local path to the file.
	 *
	 * @return string The cache buster value to use for the given file.
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function get_file_version( string $file )
	: string {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( Package::get_path() . $file ) ) {
			return filemtime( Package::get_path() . $file );
		}

		return Package::get_version();
	}

	/**
	 * Registers a style according to `wp_register_style`.
	 *
	 * @param  string   $handle  Name of the stylesheet. Should be unique.
	 * @param  string   $src     Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param  array    $deps    Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param  string   $media   Optional. The media for which this stylesheet has been defined. Default 'all'. Accepts media types like
	 *                           'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * @param  boolean  $rtl     Optional. Whether or not to register RTL styles.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	protected function register_style( string $handle, string $src, array $deps = [], string $media = 'all', bool $rtl = FALSE )
	: void {
		$filename = str_replace( plugins_url( '/', __DIR__ ), '', $src );
		$ver      = self::get_file_version( $filename );

		wp_register_style( $handle, $src, $deps, $ver, $media );

		if ( $rtl ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Update block style dependencies after they have been registered.
	 *
	 * @since 1.0.0
	 */
	public function update_block_style_dependencies()
	: void {
		$wp_styles = wp_styles();
		$style     = $wp_styles->query( 'elje-blocks-style', 'registered' );

		if ( ! $style ) {
			return;
		}
		if (
			wp_style_is( 'elje-general', 'registered' ) &&
			! in_array( 'elje-general', $style->deps, TRUE )
		) {
			$style->deps[] = 'elje-general';
		}
	}

	/**
	 * Fix scripts with elje-settings dependency.
	 * The elje-settings script only works correctly when enqueued in the footer. This is to give blocks etc time to
	 * register their settings data before it's printed.
	 * This code will look at registered scripts, and if they have a elje-settings dependency, force them to print in the
	 * footer instead of the header.
	 * This only supports packages known to require elje-settings!
	 */
	public function update_block_settings_dependencies()
	: void {
		$wp_scripts     = wp_scripts();
		$known_packages = [
			'elje-settings',
		];

		foreach ( $wp_scripts->registered as $handle => $script ) {
			if ( array_intersect( $known_packages, $script->deps ) && ! isset( $script->extra['group'] ) ) {
				$wp_scripts->add_data( $handle, 'group', 1 );
				$error_handle  = 'elje-settings-dep-in-header';
				$used_deps     = implode( ', ', array_intersect( $known_packages, $script->deps ) );
				$error_message = "Scripts that have a dependency on [$used_deps] must be loaded in the footer, {$handle} was registered to load in the header, but has been switched to load in the footer instead.";

				wp_register_script( $error_handle, '' );
				wp_enqueue_script( $error_handle );
				wp_add_inline_script(
					$error_handle,
					sprintf( 'console.warn( "%s" );', $error_message )
				);

			}
		}
	}
}
