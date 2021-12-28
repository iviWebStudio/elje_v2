<?php
namespace Elje\Blocks\BlockTypes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * AbstractDynamicBlock class.
 *
 * @since 1.0.0
 */
abstract class AbstractDynamicBlock extends AbstractBlock {
	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @param  string|null  $key  Data to get, or default to everything.
	 *
	 * @return array|string
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	protected function get_block_type_script( string $key = NULL ) {
		return NULL;
	}

	/**
	 * Get block attributes.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function get_block_type_attributes()
	: array {
		return [];
	}

	/**
	 * Get the schema for the alignment property.
	 *
	 * @return array Property definition for align.
	 * @since 1.0.0
	 */
	protected function get_schema_align()
	: array {
		return [
			'type' => 'string',
			'enum' => [
				'left',
				'center',
				'right',
				'wide',
				'full',
			],
		];
	}

	/**
	 * Get the schema for a list of IDs.
	 *
	 * @return array Property definition for a list of numeric ids.
	 * @since 1.0.0
	 */
	protected function get_schema_list_ids()
	: array {
		return [
			'type'    => 'array',
			'items'   => [
				'type' => 'number',
			],
			'default' => [],
		];
	}

	/**
	 * Get the schema for a boolean value.
	 *
	 * @param  bool|string  $default  The default value.
	 *
	 * @return array Property definition.
	 * @since 1.0.0
	 */
	protected function get_schema_boolean( bool $default = TRUE )
	: array {
		return [
			'type'    => 'boolean',
			'default' => $default,
		];
	}

	/**
	 * Get the schema for a numeric value.
	 *
	 * @param  string|int|float  $default  The default value.
	 *
	 * @return array Property definition.
	 * @since        1.0.0
	 * @noinspection MissingParameterTypeDeclarationInspection
	 */
	protected function get_schema_number( $default )
	: array {
		return [
			'type'    => 'number',
			'default' => $default,
		];
	}

	/**
	 * Get the schema for a string value.
	 *
	 * @param  string  $default  The default value.
	 *
	 * @return array Property definition.
	 * @since        1.0.0
	 * @noinspection PhpUnused
	 */
	protected function get_schema_string( string $default = '' )
	: array {
		return [
			'type'    => 'string',
			'default' => $default,
		];
	}
}
