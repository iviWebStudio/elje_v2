<?php

namespace Elje\Blocks\Tests\Mocks;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * Class MockTestDependency
 *
 * @package Elje\Blocks\Tests\Mocks
 * @since   1.0.0
 */
class MockTestDependency {
	public $dependency;

	/**
	 * MockTestDependency constructor.
	 *
	 * @param  array|null  $dependency
	 * @since 1.0.0
	 */
	public function __construct( ?array $dependency = NULL ) {
		$this->dependency = $dependency;
	}
}
