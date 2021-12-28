<?php
namespace Elje\Blocks\Registry;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Closure;
use Exception;

/**
 * A simple Dependency Injection Container
 * This is used to manage dependencies used throughout the plugin.
 *
 * @since 1.0.0
 */
class Container {

	/**
	 * A map of Dependency Type objects used to resolve dependencies.
	 *
	 * @var AbstractDependencyType[]
	 * @since 1.0.0
	 */
	private $registry = [];

	/**
	 * Public api for adding a factory to the container.
	 * Factory dependencies will have the instantiation callback invoked
	 * every time the dependency is requested.
	 * Typical Usage:
	 * ```
	 * $container->register( MyClass::class, $container->factory( $my_callback ) );
	 * ```
	 *
	 * @param  Closure  $instantiation_callback  This will be invoked when the
	 *                                           dependency is required.  It will
	 *                                           receive an instance of this
	 *                                           container so the callback can
	 *                                           retrieve dependencies from the
	 *                                           container.
	 *
	 * @return FactoryType  An instance of the FactoryType dependency.
	 * @since 1.0.0
	 */
	public function factory( Closure $instantiation_callback )
	: FactoryType {
		return new FactoryType( $instantiation_callback );
	}

	/**
	 * Interface for registering a new dependency with the container.
	 * By default, the $value will be added as a shared dependency.  This means
	 * that it will be a single instance shared among any other classes having
	 * that dependency.
	 * If you want a new instance every time it's required, then wrap the value
	 * in a call to the factory method
	 *
	 * @param  string  $id     A unique string identifier for the provided value.
	 *                         Typically it's the fully qualified name for the
	 *                         dependency.
	 * @param  mixed   $value  The value for the dependency. Typically, this is a
	 *                         closure that will create the class instance needed.
	 *
	 * @see          Container::factory for example
	 *      Note: Currently if the provided id already is registered in the container,
	 *      the provided value is ignored.
	 * @since        1.0.0
	 * @noinspection MissingParameterTypeDeclarationInspection
	 */
	public function register( string $id, $value )
	: void {
		if ( empty( $this->registry[ $id ] ) ) {
			if ( ! $value instanceof FactoryType ) {
				$value = new SharedType( $value );
			}
			$this->registry[ $id ] = $value;
		}
	}

	/**
	 * Interface for retrieving the dependency stored in the container for the
	 * given identifier.
	 *
	 * @param  string  $id  The identifier for the dependency being retrieved.
	 *
	 * @return mixed  Typically a class instance.
	 * @throws Exception  If there is no dependency for the given identifier in
	 *                    the container.
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	public function get( string $id ) {
		if ( ! isset( $this->registry[ $id ] ) ) {
			throw new Exception(
				sprintf(
					'Cannot construct an instance of %s because it has not been registered.',
					$id
				)
			);
		}

		return $this->registry[ $id ]->get( $this );
	}
}
