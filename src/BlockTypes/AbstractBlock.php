<?php
namespace Elje\Blocks\BlockTypes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Assets\Api as AssetApi;
use Elje\Blocks\Assets\AssetDataRegistry;
use Elje\Blocks\Integrations\IntegrationRegistry;
use Elje\Blocks\Package;
use WP_Block;

/**
 * AbstractBlock class.
 *
 * @since 1.0.0
 */
abstract class AbstractBlock {

	/**
	 * Block namespace.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $namespace = 'elje';

	/**
	 * Block name within this namespace.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $block_name = '';

	/**
	 * Tracks if assets have been enqueued.
	 *
	 * @var boolean
	 * @since 1.0.0
	 */
	protected $enqueued_assets = FALSE;

	/**
	 * Instance of the asset API.
	 *
	 * @var AssetApi
	 * @since 1.0.0
	 */
	protected $asset_api;

	/**
	 * Instance of the asset data registry.
	 *
	 * @var AssetDataRegistry
	 * @since 1.0.0
	 */
	protected $asset_data_registry;

	/**
	 * Instance of the integration registry.
	 *
	 * @var IntegrationRegistry
	 * @since 1.0.0
	 */
	protected $integration_registry;

	/**
	 * Api version.
	 *
	 * @var null
	 * @since 1.0.0
	 */
	protected $api_version = NULL;

	/**
	 * Constructor.
	 *
	 * @param  AssetApi             $asset_api             Instance of the asset API.
	 * @param  AssetDataRegistry    $asset_data_registry   Instance of the asset data registry.
	 * @param  IntegrationRegistry  $integration_registry  Instance of the integration registry.
	 * @param  string               $block_name            Optionally set block name during construct.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function __construct( AssetApi $asset_api, AssetDataRegistry $asset_data_registry, IntegrationRegistry $integration_registry, string $block_name = '' ) {
		$this->asset_api            = $asset_api;
		$this->asset_data_registry  = $asset_data_registry;
		$this->integration_registry = $integration_registry;
		$this->block_name           = $block_name ? $block_name : $this->block_name;
		$this->initialize();
	}

	/**
	 * The default render_callback for all blocks. This will ensure assets are enqueued just in time,
	 * then render the block (if applicable).
	 *
	 * @param  array|WP_Block  $attributes  Block attributes, or an instance of a WP_Block. Defaults to an empty array.
	 * @param  string          $content     Block content. Default empty string.
	 *
	 * @return string Rendered block type output.
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function render_callback( array $attributes = [], string $content = '' )
	: string {
		$render_callback_attributes = $this->parse_render_callback_attributes( $attributes );
		if ( ! is_admin() ) {
			$this->enqueue_assets( $render_callback_attributes );
		}

		return $this->render( $render_callback_attributes, $content );
	}

	/**
	 * Enqueue assets used for rendering the block in editor context.
	 * This is needed if a block is not yet within the post content--`render` and `enqueue_assets` may not have ran.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function enqueue_editor_assets()
	: void {
		if ( $this->enqueued_assets ) {
			return;
		}
		$this->enqueue_data();
	}

	/**
	 * Initialize this block type.
	 * - Hook into WP lifecycle.
	 * - Register the block with WordPress.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	protected function initialize()
	: void {
		if ( empty( $this->block_name ) ) {
			_doing_it_wrong( __METHOD__, esc_html__( 'Block name is required.', 'elje' ), '1.0.0' );

			return;
		}
		$this->integration_registry->initialize( $this->block_name . '_block' );
		$this->register_block_type_assets();
		$this->register_block_type();
		add_action( 'enqueue_block_editor_assets', [
			$this,
			'enqueue_editor_assets',
		] );
	}

	/**
	 * Register script and style assets for the block type before it is registered.
	 * This registers the scripts; it does not enqueue them.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	protected function register_block_type_assets()
	: void {
		if ( NULL !== $this->get_block_type_editor_script() ) {
			$data     = $this->asset_api->get_script_data( $this->get_block_type_editor_script( 'path' ) );
			$has_i18n = in_array( 'wp-i18n', $data['dependencies'], TRUE );

			$this->asset_api->register_script(
				$this->get_block_type_editor_script( 'handle' ),
				$this->get_block_type_editor_script( 'path' ),
				array_merge(
					$this->get_block_type_editor_script( 'dependencies' ),
					$this->integration_registry->get_all_registered_editor_script_handles()
				),
				$has_i18n
			);
		}
		if ( NULL !== $this->get_block_type_script() ) {
			$data     = $this->asset_api->get_script_data( $this->get_block_type_script( 'path' ) );
			$has_i18n = in_array( 'wp-i18n', $data['dependencies'], TRUE );

			$this->asset_api->register_script(
				$this->get_block_type_script( 'handle' ),
				$this->get_block_type_script( 'path' ),
				array_merge(
					$this->get_block_type_script( 'dependencies' ),
					$this->integration_registry->get_all_registered_script_handles()
				),
				$has_i18n
			);
		}
	}

	/**
	 * Injects Chunk Translations into the page so translations work for lazy loaded components.
	 * The chunk names are defined when creating lazy loaded components using webpackChunkName.
	 *
	 * @param  string[]  $chunks  Array of chunk names.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	protected function register_chunk_translations( array $chunks )
	: void {
		foreach ( $chunks as $chunk ) {
			$handle = 'elje-blocks-' . $chunk . '-chunk';
			$this->asset_api->register_script( $handle, $this->asset_api->get_block_asset_build_path( $chunk ), [], TRUE );
			wp_add_inline_script(
				$this->get_block_type_script( 'handle' ),
				wp_scripts()->print_translations( $handle, FALSE ),
				'before'
			);
			wp_deregister_script( $handle );
		}
	}

	/**
	 * Registers the block type with WordPress.
	 *
	 * @since 1.0.0
	 */
	protected function register_block_type()
	: void {
		$block_settings = [
			'render_callback' => $this->get_block_type_render_callback(),
			'editor_script'   => $this->get_block_type_editor_script( 'handle' ),
			'editor_style'    => $this->get_block_type_editor_style(),
			'style'           => $this->get_block_type_style(),
			'attributes'      => $this->get_block_type_attributes(),
			'supports'        => $this->get_block_type_supports(),
		];

		if ( ! empty( $this->api_version ) ) {
			$block_settings['api_version'] = $this->api_version;
		}

		register_block_type(
			$this->get_block_type(),
			$block_settings
		);
	}

	/**
	 * Get the block type.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function get_block_type()
	: string {
		return $this->namespace . '/' . $this->block_name;
	}

	/**
	 * Get the render callback for this block type.
	 * Dynamic blocks should return a callback, for example, `return [ $this, 'render' ];`
	 *
	 * @return callable|null;
	 * @see   $this->register_block_type()
	 * @since 1.0.0
	 */
	protected function get_block_type_render_callback()
	: ?callable {
		return [
			$this,
			'render_callback',
		];
	}

	/**
	 * Get the editor script data for this block type.
	 *
	 * @param  null|string  $key  Data to get, or default to everything.
	 *
	 * @return array|string
	 * @see          $this->register_block_type()
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	protected function get_block_type_editor_script( string $key = NULL ) {
		$script = [
			'handle'       => 'elje-' . $this->block_name . '-block',
			'path'         => $this->asset_api->get_block_asset_build_path( $this->block_name ),
			'dependencies' => [ 'elje-blocks' ],
		];

		return $key ? $script[ $key ] : $script;
	}

	/**
	 * Get the editor style handle for this block type.
	 *
	 * @return string|null
	 * @see   $this->register_block_type()
	 * @since 1.0.0
	 */
	protected function get_block_type_editor_style()
	: ?string {
		return 'elje-blocks-editor-style';
	}

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @param  string|null  $key  Data to get, or default to everything.
	 *
	 * @return array|string
	 * @see          $this->register_block_type()
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	protected function get_block_type_script( string $key = NULL ) {
		$script = [
			'handle'       => 'elje-' . $this->block_name . '-block-frontend',
			'path'         => $this->asset_api->get_block_asset_build_path( $this->block_name . '-frontend' ),
			'dependencies' => [],
		];

		return $key ? $script[ $key ] : $script;
	}

	/**
	 * Get the frontend style handle for this block type.
	 *
	 * @return string|null
	 * @see   $this->register_block_type()
	 * @since 1.0.0
	 */
	protected function get_block_type_style()
	: ?string {
		return 'elje-blocks-style';
	}

	/**
	 * Get the supports array for this block type.
	 *
	 * @return string|array
	 * @see          $this->register_block_type()
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	protected function get_block_type_supports() {
		return [];
	}

	/**
	 * Get block attributes.
	 *
	 * @return array;
	 * @since 1.0.0
	 */
	protected function get_block_type_attributes()
	: array {
		return [];
	}

	/**
	 * Parses block attributes from the render_callback.
	 *
	 * @param  array|WP_Block  $attributes  Block attributes, or an instance of a WP_Block. Defaults to an empty array.
	 *
	 * @return array
	 * @since        1.0.0
	 * @noinspection MissingParameterTypeDeclarationInspection
	 */
	protected function parse_render_callback_attributes( $attributes )
	: array {
		return is_a( $attributes, 'WP_Block' ) ? $attributes->attributes : $attributes;
	}

	/**
	 * Render the block. Extended by children.
	 *
	 * @param  array   $attributes  Block attributes.
	 * @param  string  $content     Block content.
	 *
	 * @return string Rendered block type output.
	 * @since        1.0.0
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function render( array $attributes, string $content )
	: string {
		return $content;
	}

	/**
	 * Enqueue frontend assets for this block, just in time for rendering.
	 *
	 * @param  array  $attributes  Any attributes that currently are available from the block.
	 *
	 * @throws \Exception
	 * @since    1.0.0
	 * @internal This prevents the block script being enqueued on all pages. It is only enqueued as needed. Note that
	 *           we intentionally do not pass 'script' to register_block_type.
	 */
	protected function enqueue_assets( array $attributes )
	: void {
		if ( $this->enqueued_assets ) {
			return;
		}
		$this->enqueue_data( $attributes );
		$this->enqueue_scripts( $attributes );
		$this->enqueued_assets = TRUE;
	}

	/**
	 * Data passed through from server to client for block.
	 *
	 * @param  array  $attributes  Any attributes that currently are available from the block.
	 *                             Note, this will be empty in the editor context when the block is
	 *                             not in the post content on editor load.
	 *
	 * @throws \Exception
	 * @since        1.0.0
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function enqueue_data( array $attributes = [] )
	: void {
		$registered_script_data = $this->integration_registry->get_all_registered_script_data();

		foreach ( $registered_script_data as $asset_data_key => $asset_data_value ) {
			if ( ! $this->asset_data_registry->exists( $asset_data_key ) ) {
				$this->asset_data_registry->add( $asset_data_key, $asset_data_value );
			}
		}

		if ( ! $this->asset_data_registry->exists( 'eljeBlocksConfig' ) ) {
			$this->asset_data_registry->add(
				'eljeBlocksConfig',
				[
					'buildPhase'    => Package::feature()->get_flag(),
					'pluginUrl'     => plugins_url( '/', dirname( __DIR__ ) ),
					'postCount'  => array_sum( (array) wp_count_posts( 'post' ) ),
					'defaultAvatar' => get_avatar_url( 0, [ 'force_default' => TRUE ] ),
					'wordCountType' => _x( 'words', 'Word count type. Do not translate!', 'elje' ),
				]
			);
		}
	}

	/**
	 * Register/enqueue scripts used for this block on the frontend, during render.
	 *
	 * @param  array  $attributes  Any attributes that currently are available from the block.
	 *
	 * @since        1.0.0
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function enqueue_scripts( array $attributes = [] )
	: void {
		if ( NULL !== $this->get_block_type_script() ) {
			wp_enqueue_script( $this->get_block_type_script( 'handle' ) );
		}
	}
}
