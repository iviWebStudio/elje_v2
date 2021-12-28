<?php
namespace Elje\Blocks\BlockTypes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * PostListings class.
 *
 * @since 1.0.0
 */
class PostListings extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $block_name = 'post-listings';

	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param  array  $attributes  Any attributes that currently are available from the block.
	 *                             Note, this will be empty in the editor con
	 *                             text when the block is
	 *                             not in the post content on editor load.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	protected function enqueue_data( array $attributes = [] )
	: void {
		parent::enqueue_data( $attributes );
		$this->asset_data_registry->add( 'min_columns', 1, TRUE );
		$this->asset_data_registry->add( 'max_columns', 6, TRUE );
		$this->asset_data_registry->add( 'default_columns', 3, TRUE );
		$this->asset_data_registry->add( 'min_rows', 1, TRUE );
		$this->asset_data_registry->add( 'max_rows', 6, TRUE );
		$this->asset_data_registry->add( 'default_rows', 3, TRUE );
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
		parent::register_block_type_assets();
		$this->register_chunk_translations(
			[
				'atomic-block-components/card-title',
			]
		);
	}
}
