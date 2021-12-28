<?php
namespace Elje\Blocks\BlockTypes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * PostCard class.
 *
 * @since 1.0.0
 */
class PostCard extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $block_name = 'post-card';

	/**
	 * Enqueue frontend assets for this block, just in time for rendering.
	 *
	 * @param  array  $attributes  Any attributes that currently are available from the block.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	protected function enqueue_assets( array $attributes )
	: void {
		parent::enqueue_assets( $attributes );
		$post_id = intval( $attributes['postId'] );
		$this->hydrate_from_api( $post_id );
	}

	/**
	 * Get the editor script handle for this block type.
	 *
	 * @param  string|null  $key  Data to get, or default to everything.
	 *
	 * @return array|string
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
	 * Hydrate the cart block with data from the API.
	 *
	 * @param  int  $post_id  ID of the post.
	 *
	 * @since 1.0.0
	 */
	protected function hydrate_from_api( int $post_id )
	: void {
		$this->asset_data_registry->hydrate_api_request( "/elje/global/posts/$post_id" );
	}
}
