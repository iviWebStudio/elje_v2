<?php
/** @noinspection PhpUndefinedClassInspection */
namespace Elje\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Assets\Api as AssetApi;
use Elje\Blocks\Assets\AssetDataRegistry;
use Elje\Blocks\BlockTypes\AtomicBlock;
use Elje\Blocks\Integrations\IntegrationRegistry;

/**
 * BlockTypesController class.
 *
 * @since 1.0.0
 */
class BlockTypesController {

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
	 * @since        1.0.0
	 */
	protected $asset_data_registry;

	/**
	 * Constructor.
	 *
	 * @param  AssetApi           $asset_api            Instance of the asset API.
	 * @param  AssetDataRegistry  $asset_data_registry  Instance of the asset data registry.
	 *
	 * @since        1.0.0
	 */
	public function __construct( AssetApi $asset_api, AssetDataRegistry $asset_data_registry ) {
		$this->asset_api           = $asset_api;
		$this->asset_data_registry = $asset_data_registry;
		$this->init();
	}

	/**
	 * Initialize class features.
	 *
	 * @since 1.0.0
	 */
	private function init()
	: void {
		add_action( 'init', [
			$this,
			'register_blocks',
		] );
		add_filter( 'render_block', [
			$this,
			'add_data_attributes',
		], 10, 2 );
	}

	/**
	 * Register blocks, hooking up assets and render functions as needed.
	 *
	 * @throws \Exception
	 * @since        1.0.0
	 */
	public function register_blocks()
	: void {

		$block_types = $this->get_block_types();

		foreach ( $block_types as $block_type ) {
			$block_type_class = __NAMESPACE__ . '\\BlockTypes\\' . $block_type;
			new $block_type_class( $this->asset_api, $this->asset_data_registry, new IntegrationRegistry() );
		}

		foreach ( self::get_atomic_blocks() as $block_type ) {
			new AtomicBlock( $this->asset_api, $this->asset_data_registry, new IntegrationRegistry(), $block_type );
		}
	}

	/**
	 * Add data- attributes to blocks when rendered if the block is under the elje/ namespace.
	 *
	 * @param  string  $content  Block content.
	 * @param  array   $block    Parsed block data.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function add_data_attributes( string $content, array $block )
	: string {
		$block_name      = $block['blockName'];
		$block_namespace = strtok( $block_name ?? '', '/' );

		/*
		 * Filters the list of allowed block namespaces.
		 * This hook defines which block namespaces should have block name and attribute `data-` attributes appended on render.
		 */
		$allowed_namespaces = [ 'elje' ];

		/*
		 * Filters the list of allowed Block Names
		 * This hook defines which block names should have block name and attribute data- attributes appended on render.
		 */
		$allowed_blocks = [];

		if ( ! in_array( $block_namespace, $allowed_namespaces, TRUE ) && ! in_array( $block_name, $allowed_blocks, TRUE ) ) {
			return $content;
		}

		$attributes              = (array) $block['attrs'];
		$exclude_attributes      = [
			'className',
			'align',
		];
		$escaped_data_attributes = [
			'data-block-name="' . esc_attr( $block['blockName'] ) . '"',
		];

		foreach ( $attributes as $key => $value ) {
			if ( in_array( $key, $exclude_attributes, TRUE ) ) {
				continue;
			}
			if ( is_bool( $value ) ) {
				$value = $value ? 'true' : 'false';
			}
			if ( ! is_scalar( $value ) ) {
				$value = wp_json_encode( $value );
			}
			/** @noinspection RegExpRedundantEscape */
			$escaped_data_attributes[] = 'data-' . esc_attr( strtolower( preg_replace( '/(?<!\ )[A-Z]/', '-$0', $key ) ) ) . '="' . esc_attr( $value ) . '"';
		}

		return preg_replace( '/^<div /', '<div ' . implode( ' ', $escaped_data_attributes ) . ' ', trim( $content ) );
	}

	/**
	 * Get list of block types.
	 *
	 * @return array
	 * @throws \Exception
	 * @since        1.0.0
	 */
	protected function get_block_types()
	: array {
		global $pagenow;

		$block_types = [
			'PostCard',
		];

		if ( Package::feature()->is_feature_plugin_build() ) {
			$block_types[] = 'PostListings';
		}

		/** @noinspection PhpStatementHasEmptyBodyInspection */
		if ( Package::feature()->is_experimental_build() ) {
			//$block_types[] = '';
		}

		/**
		 * This disables specific blocks in Widget Areas by not registering them.
		 */
		if ( in_array( $pagenow, [
			'widgets.php',
			'themes.php',
			'customize.php',
		], TRUE ) ) {
			$block_types = array_diff(
				$block_types,
				[]
			);
		}

		return $block_types;
	}

	/**
	 * Get atomic blocks types.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function get_atomic_blocks()
	: array {
		return [
			'card-title',
			'card-button',
			'card-image',
			'card-description',
			'card-category-list',
			'card-tag-list',
		];
	}
}
