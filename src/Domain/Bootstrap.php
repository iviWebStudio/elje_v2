<?php
namespace Elje\Blocks\Domain;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Assets\Api as AssetApi;
use Elje\Blocks\Assets\AssetDataRegistry;
use Elje\Blocks\AssetsController as AssetsController;
use Elje\Blocks\BlockTemplatesController;
use Elje\Blocks\BlockTypesController;
use Elje\Blocks\Domain\Services\FeatureGating;
use Elje\Blocks\Installer;
use Elje\Blocks\Registry\Container;

/**
 * Takes care of bootstrapping the plugin.
 *
 * @since 1.0.0
 */
class Bootstrap {

	/**
	 * Holds the Dependency Injection Container
	 *
	 * @var Container
	 * @since 1.0.0
	 */
	private $container;

	/**
	 * Holds the Package instance
	 *
	 * @var Package
	 * @since 1.0.0
	 */
	private $package;

	/**
	 * Constructor
	 *
	 * @param  Container  $container  The Dependency Injection Container.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
		$this->package   = $container->get( Package::class );
		$this->init();
	}

	/**
	 * Init the package - load the blocks library and define constants.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	protected function init()
	: void {
		$this->register_dependencies();

		$this->add_build_notice();
		$this->container->get( AssetDataRegistry::class );
		$this->container->get( Installer::class );
		$this->container->get( AssetsController::class );

		$this->container->get( BlockTypesController::class );
		$this->container->get( BlockTemplatesController::class );
	}

	/**
	 * See if files have been built or not.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	protected function is_built()
	: bool {
		return file_exists( $this->package->get_path( 'build/blocks.js' ) );
	}

	/**
	 * Add a notice stating that the build has not been done yet.
	 *
	 * @since 1.0.0
	 */
	protected function add_build_notice()
	: void {
		if ( $this->is_built() ) {
			return;
		}
		eljeAdminNotice( 'Elje Blocks development mode requires files to be built. From the plugin directory, run `npm install to install dependencies, `npm run build` to build the files or `npm start` to build the files and watch for changes.', 'error' );
	}

	/**
	 * Register core dependencies with the container.
	 *
	 * @since        1.0.0
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function register_dependencies()
	: void {
		$this->container->register(
			FeatureGating::class,
			function( Container $container ) {
				return new FeatureGating();
			}
		);
		$this->container->register(
			AssetApi::class,
			function( Container $container ) {
				return new AssetApi( $container->get( Package::class ) );
			}
		);
		$this->container->register(
			AssetDataRegistry::class,
			function( Container $container ) {
				return new AssetDataRegistry( $container->get( AssetApi::class ) );
			}
		);
		$this->container->register(
			AssetsController::class,
			function( Container $container ) {
				return new AssetsController( $container->get( AssetApi::class ) );
			}
		);
		$this->container->register(
			Installer::class,
			function( Container $container ) {
				return new Installer();
			}
		);
		$this->container->register(
			BlockTypesController::class,
			function( Container $container ) {
				$asset_api           = $container->get( AssetApi::class );
				$asset_data_registry = $container->get( AssetDataRegistry::class );

				return new BlockTypesController( $asset_api, $asset_data_registry );
			}
		);
		$this->container->register(
			BlockTemplatesController::class,
			function( Container $container ) {
				return new BlockTemplatesController();
			}
		);
	}
}
