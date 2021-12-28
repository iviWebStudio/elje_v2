<?php
namespace Elje\Blocks\Registry;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * Definition for the FactoryType dependency type.
 *
 * @since 1.0.0
 */
class FactoryType extends AbstractDependencyType {
	/**
	 * Invokes and returns the value from the stored internal callback.
	 *
	 * @param  Container  $container  An instance of the dependency injection
	 *                                container.
	 *
	 * @return mixed
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	public function get( Container $container ) {
		return $this->resolve_value( $container );
	}
}
