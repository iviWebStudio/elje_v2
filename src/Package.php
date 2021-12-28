<?php
namespace Elje\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}


use Elje\Blocks\Domain\Bootstrap;
use Elje\Blocks\Domain\Package as NewPackage;
use Elje\Blocks\Domain\Services\FeatureGating;
use Elje\Blocks\Registry\Container;

/**
 * Main package class.
 * Returns information about the package and handles init.
 * In the context of this plugin, it handles init and is called from the main
 * plugin file (elje.php).
 *
 * @since 1.0.0
 */
class Package {

	/**
	 * For back compat this is provided. Ideally, you should register your
	 * class with Elje\Blocks\Container and make Package a
	 * dependency.
	 *
	 * @return NewPackage  The Package instance class
	 * @throws \Exception
	 * @since 1.0.0
	 */
	private static function get_package()
	: NewPackage {
		return self::container()->get( NewPackage::class );
	}

	/**
	 * Init the package - load the blocks library and define constants.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public static function init()
	: void {
		self::container()->get( Bootstrap::class );
	}

	/**
	 * Return the version of the package.
	 *
	 * @return string
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public static function get_version()
	: string {
		return self::get_package()->get_version();
	}

	/**
	 * Return the path to the package.
	 *
	 * @return string
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public static function get_path()
	: string {
		return self::get_package()->get_path();
	}

	/**
	 * Returns an instance of the the FeatureGating class.
	 *
	 * @return FeatureGating
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public static function feature()
	: FeatureGating {
		return self::get_package()->feature();
	}

	/**
	 * Checks if we're executing the code in an experimental build mode.
	 *
	 * @return boolean
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public static function is_experimental_build()
	: bool {
		return self::get_package()->is_experimental_build();
	}

	/**
	 * Checks if we're executing the code in an feature plugin or experimental build mode.
	 *
	 * @return boolean
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public static function is_feature_plugin_build()
	: bool {
		return self::get_package()->is_feature_plugin_build();
	}

	/**
	 * Loads the dependency injection container for our blocks.
	 *
	 * @param  boolean  $reset  Used to reset the container to a fresh instance.
	 *                          Note: this means all dependencies will be
	 *                          reconstructed.
	 *
	 * @return \Elje\Blocks\Registry\Container
	 * @since        1.0.0
	 * @noinspection PhpUnusedParameterInspection
	 */
	public static function container( bool $reset = FALSE )
	: Container {
		static $container;
		if (
			! $container instanceof Container
			|| $reset
		) {
			$container = new Container();
			$container->register(
				NewPackage::class,
				function( $container ) {
					$version = '1.0.0';

					return new NewPackage(
						$version,
						dirname( __DIR__ ),
						new FeatureGating()
					);
				}
			);

			$container->register(
				Bootstrap::class,
				function( $container ) {
					return new Bootstrap(
						$container
					);
				}
			);
		}

		return $container;
	}
}
