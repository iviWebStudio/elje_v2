<?php
namespace Elje\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * Installer class.
 * Handles installation of the plugin dependencies.
 *
 * @since   1.0.0
 * @package Elje\Blocks
 */
class Installer {
	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Installation tasks ran on admin_init callback.
	 *
	 * @since 1.0.0
	 */
	public function install()
	: void {
		$this->maybe_create_tables();
	}

	/**
	 * Initialize class features.
	 *
	 * @since 1.0.0
	 */
	private function init()
	: void {
		add_action( 'admin_init', [
			$this,
			'install',
		] );
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	public function maybe_create_tables() {
		global $wpdb;

		$schema_version    = 1;
		$db_schema_version = (int) get_option( 'elje_db_schema_version', 0 );

		if ( $db_schema_version >= $schema_version && 0 !== $db_schema_version ) {
			return;
		}

		$show_errors = $wpdb->hide_errors();
		$table_name  = $wpdb->prefix . 'elje_settings';
		$collate     = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
		/** @noinspection SqlNoDataSourceInspection */
		$exists = $this->maybe_create_table(
			$table_name,
			"
			CREATE TABLE {$table_name} (
			    `option_id` bigint(20) unsigned NOT NULL auto_increment,
			    `option_name` varchar(191) NOT NULL default '',
			    `option_value` longtext NOT NULL,
			    PRIMARY KEY  (option_id),
			    UNIQUE KEY option_name (option_name)
			) $collate;
			"
		);

		if ( $show_errors ) {
			$wpdb->show_errors();
		}

		if ( ! $exists ) {
			$this->add_create_table_notice( $table_name );

			return;
		}

		update_option( 'elje_db_schema_version', $schema_version );
	}

	/**
	 * Create database table, if it doesn't already exist.
	 * Based on admin/install-helper.php maybe_create_table function.
	 *
	 * @param  string  $table_name  Database table name.
	 * @param  string  $create_sql  Create database table SQL.
	 *
	 * @return bool False on error, true if already exists or success.
	 * @since 1.0.0
	 */
	private function maybe_create_table( string $table_name, string $create_sql )
	: bool {
		global $wpdb;

		if ( in_array( $table_name, $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ), 0 ), TRUE ) ) {
			return TRUE;
		}

		$wpdb->query( $create_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return in_array( $table_name, $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ), 0 ), TRUE );
	}

	/**
	 * Add a notice if table creation fails.
	 *
	 * @param  string  $table_name  Name of the missing table.
	 *
	 * @since 1.0.0
	 */
	private function add_create_table_notice( string $table_name )
	: void {
		eljeAdminNotice( sprintf(
			'`%1$s table` creation failed. Does the `%2$s` user have CREATE privileges on the `%3$s` database?',
			esc_html( $table_name ),
			esc_html( DB_USER ),
			esc_html( DB_NAME )
		), 'error' );
	}
}
