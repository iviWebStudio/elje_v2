<?php
namespace Elje\Blocks\Domain\Services;

/**
 * Service class that handles the feature flags.
 *
 * @since 1.0.0
 */
class FeatureGating {

	/**
	 * Current flag value.
	 *
	 * @var int
	 * @since 1.0.0
	 */
	private $flag;

	const EXPERIMENTAL_FLAG   = 3;
	const FEATURE_PLUGIN_FLAG = 2;
	const CORE_FLAG           = 1;

	/**
	 * Current environment
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $environment;

	const PRODUCTION_ENVIRONMENT  = 'production';
	const DEVELOPMENT_ENVIRONMENT = 'development';
	const TEST_ENVIRONMENT        = 'test';

	/**
	 * Constructor
	 *
	 * @param  int     $flag         Hardcoded flag value. Useful for tests.
	 * @param  string  $environment  Hardcoded environment value. Useful for tests.
	 *
	 * @since 1.0.0
	 */
	public function __construct( int $flag = 0, string $environment = 'unset' ) {
		$this->flag        = $flag;
		$this->environment = $environment;
		$this->load_flag();
		$this->load_environment();
	}

	/**
	 * Set correct flag.
	 *
	 * @since 1.0.0
	 */
	public function load_flag()
	: void {
		if ( 0 === $this->flag ) {
			$default_flag = defined( 'ELJE_IS_FEATURE_PLUGIN' ) ? self::FEATURE_PLUGIN_FLAG : self::CORE_FLAG;

			if ( file_exists( __DIR__ . '/../../../blocks.ini' ) ) {
				$allowed_flags  = [
					self::EXPERIMENTAL_FLAG,
					self::FEATURE_PLUGIN_FLAG,
					self::CORE_FLAG,
				];
				$plugin_options = parse_ini_file( __DIR__ . '/../../../blocks.ini' );
				$this->flag     = is_array( $plugin_options ) && in_array( intval( $plugin_options['elje_blocks_phase'] ), $allowed_flags, TRUE ) ? $plugin_options['elje_blocks_phase'] : $default_flag;
			} else {
				$this->flag = $default_flag;
			}
		}
	}

	/**
	 * Set correct environment.
	 *
	 * @since 1.0.0
	 */
	public function load_environment()
	: void {
		if ( 'unset' === $this->environment ) {
			if ( file_exists( __DIR__ . '/../../../blocks.ini' ) ) {
				$allowed_environments = [
					self::PRODUCTION_ENVIRONMENT,
					self::DEVELOPMENT_ENVIRONMENT,
					self::TEST_ENVIRONMENT,
				];
				$plugin_options       = parse_ini_file( __DIR__ . '/../../../blocks.ini' );
				$this->environment    = is_array( $plugin_options ) && in_array( $plugin_options['elje_blocks_env'], $allowed_environments, TRUE ) ? $plugin_options['elje_blocks_env'] : self::PRODUCTION_ENVIRONMENT;
			} else {
				$this->environment = self::PRODUCTION_ENVIRONMENT;
			}
		}
	}

	/**
	 * Returns the current flag value.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function get_flag()
	: int {
		return $this->flag;
	}

	/**
	 * Checks if we're executing the code in an experimental build mode.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_experimental_build()
	: bool {
		return $this->flag >= self::EXPERIMENTAL_FLAG;
	}

	/**
	 * Checks if we're executing the code in an feature plugin or experimental build mode.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_feature_plugin_build()
	: bool {
		return $this->flag >= self::FEATURE_PLUGIN_FLAG;
	}

	/**
	 * Returns the current environment value.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_environment()
	: string {
		return $this->environment;
	}

	/**
	 * Checks if we're executing the code in an development environment.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_development_environment()
	: bool {
		return self::DEVELOPMENT_ENVIRONMENT === $this->environment;
	}

	/**
	 * Checks if we're executing the code in a production environment.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_production_environment()
	: bool {
		return self::PRODUCTION_ENVIRONMENT === $this->environment;
	}

	/**
	 * Checks if we're executing the code in a test environment.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_test_environment()
	: bool {
		return self::TEST_ENVIRONMENT === $this->environment;
	}
}
