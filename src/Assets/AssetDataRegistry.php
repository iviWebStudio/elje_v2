<?php
namespace Elje\Blocks\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Exception;
use InvalidArgumentException;

/**
 * Class instance for registering data used on the current view session by
 * assets.
 * Holds data registered for output on the current view session when
 * `elje-settings` is enqueued( directly or via dependency )
 *
 * @since 1.0.0
 */
class AssetDataRegistry {
	/**
	 * Contains registered data.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $data = [];

	/**
	 * Contains preloaded API data.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $preloaded_api_requests = [];

	/**
	 * Lazy data is an array of closures that will be invoked just before
	 * asset data is generated for the enqueued script.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $lazy_data = [];

	/**
	 * Asset handle for registered data.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $handle = 'elje-settings';

	/**
	 * Asset API interface for various asset registration.
	 *
	 * @var API
	 * @since 1.0.0
	 */
	private $api;

	/**
	 * Constructor
	 *
	 * @param  Api  $asset_api  Asset API interface for various asset registration.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Api $asset_api ) {
		$this->api = $asset_api;
		$this->init();
	}

	/**
	 * Hook into WP asset registration for enqueueing asset data.
	 *
	 * @since 1.0.0
	 */
	protected function init()
	: void {
		add_action( 'init', [
			$this,
			'register_data_script',
		] );
		add_action( 'wp_print_footer_scripts', [
			$this,
			'enqueue_asset_data',
		], 1 );
		add_action( 'admin_print_footer_scripts', [
			$this,
			'enqueue_asset_data',
		], 1 );
	}

	/**
	 * Exposes core data via the eljeSettings global. This data is shared throughout the client.
	 * Settings that are used by various components or multiple blocks should be added here. Note, that settings here are
	 * global so be sure not to add anything heavy if possible.
	 *
	 * @return array  An array containing core data.
	 * @since 1.0.0
	 */
	protected function get_core_data()
	: array {
		return [
			'adminUrl'           => admin_url(),
			'currentUserIsAdmin' => current_user_can( 'manage_elje' ),
			'homeUrl'            => esc_url( home_url( '/' ) ),
			'locale'             => $this->get_locale_data(),
			'siteTitle'          => get_bloginfo( 'name' ),
			'assetUrl'           => plugins_url( 'assets/', ELJE_PLUGIN_FILE ),
			'version'            => ELJE_PLUGIN_VERSION,
			'wpLoginUrl'         => wp_login_url(),
			'wpVersion'          => get_bloginfo( 'version' ),
		];
	}

	/**
	 * Get locale data to include in settings.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function get_locale_data()
	: array {
		global $wp_locale;

		return [
			'siteLocale'    => get_locale(),
			'userLocale'    => get_user_locale(),
			'weekdaysShort' => array_values( $wp_locale->weekday_abbrev ),
		];
	}

	/**
	 * Used for on demand initialization of asset data and registering it with
	 * the internal data registry.
	 *
	 * @since 1.0.0
	 */
	protected function initialize_core_data()
	: void {
		// note this WILL wipe any data already registered to these keys because they are protected.
		$this->data = array_replace_recursive( $this->data, $this->get_core_data() );
	}

	/**
	 * Loops through each registered lazy data callback and adds the returned
	 * value to the data array.
	 * This method is executed right before preparing the data for printing to
	 * the rendered screen.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function execute_lazy_data()
	: void {
		foreach ( $this->lazy_data as $key => $callback ) {
			$this->data[ $key ] = $callback();
		}
	}

	/**
	 * Exposes private registered data to child classes.
	 *
	 * @return array  The registered data on the private data property
	 * @since 1.0.0
	 */
	protected function get()
	: array {
		return $this->data;
	}

	/**
	 * Allows checking whether a key exists.
	 *
	 * @param  string  $key  The key to check if exists.
	 *
	 * @return bool  Whether the key exists in the current data registry.
	 * @since 1.0.0
	 */
	public function exists( string $key )
	: bool {
		return array_key_exists( $key, $this->data );
	}

	/**
	 * Interface for adding data to the registry.
	 * You can only register data that is not already in the registry identified by the given key. If there is a
	 * duplicate found, unless $ignore_duplicates is true, an exception will be thrown.
	 *
	 * @param  string   $key               The key used to reference the data being registered.
	 * @param  mixed    $data              If not a function, registered to the registry as is. If a function, then the
	 *                                     callback is invoked right before output to the screen.
	 * @param  boolean  $check_key_exists  If set to true, duplicate data will be ignored if the key exists.
	 *                                     If false, duplicate data will cause an exception.
	 *
	 * @throws InvalidArgumentException  Only throws when site is in debug mode. Always logs the error.
	 * @noinspection MissingParameterTypeDeclarationInspection
	 * @since        1.0.0
	 */
	public function add( string $key, $data, bool $check_key_exists = FALSE )
	: void {
		if ( $check_key_exists && $this->exists( $key ) ) {
			return;
		}
		try {
			$this->add_data( $key, $data );
		} catch ( Exception $e ) {
			if ( $this->debug() ) {
				throw new InvalidArgumentException( $e->getMessage(), $e->getCode(), $e->getPrevious() );
			}
			eljeAdminNotice( $e->getMessage(), 'error' );
		}
	}

	/**
	 * Hydrate from API.
	 *
	 * @param  string  $path  REST API path to preload.
	 *
	 * @since 1.0.0
	 */
	public function hydrate_api_request( string $path )
	: void {
		if ( ! isset( $this->preloaded_api_requests[ $path ] ) ) {
			$this->preloaded_api_requests = rest_preload_api_request( $this->preloaded_api_requests, $path );
		}
	}

	/**
	 * Callback for registering the data script via WordPress API.
	 *
	 * @return void
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function register_data_script()
	: void {
		$this->api->register_script(
			$this->handle,
			'build/elje-settings.js',
			[ 'wp-api-fetch' ],
			TRUE
		);
	}

	/**
	 * Callback for enqueuing asset data via the WP api.
	 * Note: while this is hooked into print/admin_print_scripts, it still only
	 * happens if the script attached to `elje-settings` handle is enqueued. This
	 * is done to allow for any potentially expensive data generation to only
	 * happen for routes that need it.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_asset_data()
	: void {
		if ( wp_script_is( $this->handle, 'enqueued' ) ) {
			$this->initialize_core_data();
			$this->execute_lazy_data();

			$data                   = rawurlencode( wp_json_encode( $this->data ) );
			$preloaded_api_requests = rawurlencode( wp_json_encode( $this->preloaded_api_requests ) );

			wp_add_inline_script(
				$this->handle,
				"var eljeSettings=eljeSettings||JSON.parse(decodeURIComponent('" . esc_js( $data ) . "'));" .
				"wp.apiFetch.use(wp.apiFetch.createPreloadingMiddleware(JSON.parse(decodeURIComponent('" . esc_js( $preloaded_api_requests ) . "'))))",
				'before'
			);
		}
	}

	/**
	 * Adding data to the registry.
	 * You can only register data that is not already in the registry identified by the given key. If there is a
	 * duplicate found, unless $ignore_duplicates is true, an exception will be thrown.
	 *
	 * @param  string  $key   Key for the data.
	 * @param  mixed   $data  Value for the data.
	 *
	 * @throws InvalidArgumentException  If key is not a string or already
	 *                                   exists in internal data cache.
	 * @see          self::add()
	 * @since        1.0.0
	 * @noinspection MissingParameterTypeDeclarationInspection
	 */
	protected function add_data( string $key, $data )
	: void {
		if ( ! is_string( $key ) ) {
			if ( $this->debug() ) {
				throw new InvalidArgumentException(
					'Key for the data being registered must be a string'
				);
			}
		}
		if ( isset( $this->data[ $key ] ) ) {
			if ( $this->debug() ) {
				throw new InvalidArgumentException(
					'Overriding existing data with an already registered key is not allowed'
				);
			}

			return;
		}
		if ( \is_callable( $data ) ) {
			$this->lazy_data[ $key ] = $data;

			return;
		}
		$this->data[ $key ] = $data;
	}

	/**
	 * Exposes whether the current site is in debug mode or not.
	 *
	 * @return boolean  True means the site is in debug mode.
	 * @since 1.0.0
	 */
	protected function debug()
	: bool {
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}
}
