<?php
namespace Elje\Blocks\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * Class used for tracking registered integrations with various Block types.
 *
 * @since 1.0.0
 */
class IntegrationRegistry {
	/**
	 * Integration identifier is used to construct hook names and is given when the integration registry is initialized.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $registry_identifier = '';

	/**
	 * Registered integrations, as `$name => $instance` pairs.
	 *
	 * @var IntegrationInterface[]
	 * @since 1.0.0
	 */
	protected $registered_integrations = [];

	/**
	 * Initializes all registered integrations.
	 * Integration identifier is used to construct hook names and is given when the integration registry is initialized.
	 *
	 * @param  string  $registry_identifier  Identifier for this registry.
	 *
	 * @since 1.0.0
	 */
	public function initialize( string $registry_identifier = '' )
	: void {
		if ( $registry_identifier ) {
			$this->registry_identifier = $registry_identifier;
		}

		if ( empty( $this->registry_identifier ) ) {
			_doing_it_wrong( __METHOD__, esc_html__( 'Integration registry requires an identifier.', 'elje' ), '1.0.0' );

			return;
		}

		foreach ( $this->get_all_registered() as $registered_integration ) {
			$registered_integration->initialize();
		}
	}

	/**
	 * Registers an integration.
	 *
	 * @param  IntegrationInterface  $integration  An instance of IntegrationInterface.
	 *
	 * @return boolean True means registered successfully.
	 * @since 1.0.0
	 */
	public function register( IntegrationInterface $integration )
	: bool {
		$name = $integration->get_name();

		if ( $this->is_registered( $name ) ) {
			_doing_it_wrong( __METHOD__, esc_html( sprintf( __( '"%s" is already registered.', 'elje' ), $name ) ), '1.0.0' );

			return FALSE;
		}

		$this->registered_integrations[ $name ] = $integration;

		return TRUE;
	}

	/**
	 * Checks if an integration is already registered.
	 *
	 * @param  string  $name  Integration name.
	 *
	 * @return bool True if the integration is registered, false otherwise.
	 * @since 1.0.0
	 */
	public function is_registered( string $name )
	: bool {
		return isset( $this->registered_integrations[ $name ] );
	}

	/**
	 * Un-register an integration.
	 *
	 * @param  string|IntegrationInterface  $name  Integration name, or alternatively a IntegrationInterface instance.
	 *
	 * @return boolean|IntegrationInterface Returns the unregistered integration instance if unregistered successfully.
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 * @noinspection MissingParameterTypeDeclarationInspection
	 */
	public function unregister( $name ) {
		if ( $name instanceof IntegrationInterface ) {
			$name = $name->get_name();
		}

		if ( ! $this->is_registered( $name ) ) {
			/* translators: %s: Integration name. */
			_doing_it_wrong( __METHOD__, esc_html( sprintf( __( 'Integration "%s" is not registered.', 'elje' ), $name ) ), '1.0.0' );

			return FALSE;
		}

		$unregistered = $this->registered_integrations[ $name ];
		unset( $this->registered_integrations[ $name ] );

		return $unregistered;
	}

	/**
	 * Retrieves a registered Integration by name.
	 *
	 * @param  string  $name  Integration name.
	 *
	 * @return IntegrationInterface|null The registered integration, or null if it is not registered.
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	public function get_registered( string $name ) {
		return $this->is_registered( $name ) ? $this->registered_integrations[ $name ] : NULL;
	}

	/**
	 * Retrieves all registered integrations.
	 *
	 * @return IntegrationInterface[]
	 * @since 1.0.0
	 */
	public function get_all_registered()
	: array {
		return $this->registered_integrations;
	}

	/**
	 * Gets an array of all registered integration's script handles for the editor.
	 *
	 * @return string[]
	 * @since 1.0.0
	 */
	public function get_all_registered_editor_script_handles()
	: array {
		$script_handles          = [];
		$registered_integrations = $this->get_all_registered();

		foreach ( $registered_integrations as $registered_integration ) {
			$script_handles = array_merge(
				$script_handles,
				$registered_integration->get_editor_script_handles()
			);
		}

		return array_unique( array_filter( $script_handles ) );
	}

	/**
	 * Gets an array of all registered integration's script handles.
	 *
	 * @return string[]
	 * @since 1.0.0
	 */
	public function get_all_registered_script_handles()
	: array {
		$script_handles          = [];
		$registered_integrations = $this->get_all_registered();

		foreach ( $registered_integrations as $registered_integration ) {
			$script_handles = array_merge(
				$script_handles,
				$registered_integration->get_script_handles()
			);
		}

		return array_unique( array_filter( $script_handles ) );
	}

	/**
	 * Gets an array of all registered integration's script data.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_all_registered_script_data()
	: array {
		$script_data             = [];
		$registered_integrations = $this->get_all_registered();

		foreach ( $registered_integrations as $registered_integration ) {
			$script_data[ $registered_integration->get_name() . '_data' ] = $registered_integration->get_script_data();
		}

		return array_filter( $script_data );
	}
}
