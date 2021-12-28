<?php
namespace Elje\Blocks\BlockTypes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * AtomicBlock class.
 *
 * @since 1.0.0
 */
class AtomicBlock extends AbstractBlock {
	/**
	 * Get the editor script data for this block type.
	 *
	 * @param  string|null  $key  Data to get, or default to everything.
	 *
	 * @return null
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	protected function get_block_type_editor_script( string $key = NULL ) {
		return NULL;
	}

	/**
	 * Get the editor style handle for this block type.
	 *
	 * @return null
	 * @since 1.0.0
	 */
	protected function get_block_type_editor_style()
	: ?string {
		return NULL;
	}

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @param  string|null  $key  Data to get, or default to everything.
	 *
	 * @return array|string|null
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	protected function get_block_type_script( string $key = NULL ) {
		return NULL;
	}

	/**
	 * Get the frontend style handle for this block type.
	 *
	 * @return null
	 * @since 1.0.0
	 */
	protected function get_block_type_style()
	: ?string {
		return NULL;
	}
}
