<?php

namespace Elje\Blocks\Tests\Mocks;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Assets\AssetDataRegistry;

/**
 * Class AssetDataRegistryMock
 *
 * @package Elje\Blocks\Tests\Mocks
 * @since   1.0.0
 */
class AssetDataRegistryMock extends AssetDataRegistry {

	/**
	 * @var bool
	 * @since 1.0.0
	 */
	private $debug = TRUE;

	/**
	 * @since 1.0.0
	 */
	public function execute_lazy_data()
	: void {
		parent::execute_lazy_data();
	}

	/**
	 * @return array
	 * @since 1.0.0
	 */
	public function get()
	: array {
		return parent::get();
	}

	/**
	 * @param  bool  $debug
	 *
	 * @since 1.0.0
	 */
	public function set_debug( bool $debug )
	: void {
		$this->debug = $debug;
	}

	/**
	 * @since 1.0.0
	 */
	public function initialize_core_data()
	: void {
		parent::initialize_core_data();
	}

	/**
	 * @return bool
	 * @since 1.0.0
	 */
	protected function debug()
	: bool {
		return $this->debug;
	}
}
