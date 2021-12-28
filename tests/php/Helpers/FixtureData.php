<?php
/** @noinspection PhpUnused */
/** @noinspection SqlResolve */

namespace Elje\Blocks\Tests\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * Helper used to create fixture data for tests.
 * FixtureData class.
 *
 * @since 1.0.0
 */
class FixtureData {
	/**
	 * Create a simple post and return the result.
	 *
	 * @param  array  $props  Post props.
	 *
	 * @return \WP_Post
	 * @since 1.0.0
	 */
	public function get_post( array $props )
	: \WP_Post {
		$args = wp_parse_args(
			$props,
			[
				'name' => 'Test post',
			]
		);

		$post = new \WP_Post( (object) $args );

		return get_post( $post->ID );
	}


	/**
	 * Upload a sample image and return it's ID.
	 *
	 * @param  integer  $post_id
	 *
	 * @return array
	 * @noinspection SqlNoDataSourceInspection
	 * @since        1.0.0
	 */
	public function sideload_image( int $post_id = 0 )
	: array {
		global $wpdb;
		$image_url = media_sideload_image( 'https://cldup.com/Dr1Bczxq4q.png', $post_id, '', 'src' );

		return $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s", $image_url ) )[0];
	}

}
