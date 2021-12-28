<?php
namespace Elje\Blocks\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * Integration.Interface
 * Integrations must use this interface when registering themselves with blocks,
 *
 * @since 1.0.0
 */
interface IntegrationInterface {
	/**
	 * The name of the integration.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name()
	: string;

	/**
	 * When called invokes any initialization/setup for the integration.
	 *
	 * @since 1.0.0
	 */
	public function initialize()
	: void;

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 * @since 1.0.0
	 */
	public function get_script_handles()
	: array;

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 * @since 1.0.0
	 */
	public function get_editor_script_handles()
	: array;

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_script_data()
	: array;
}
