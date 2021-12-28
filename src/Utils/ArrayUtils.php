<?php
namespace Elje\Blocks\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * ArrayUtils class used for custom functions to operate on arrays
 *
 * @since 1.0.0
 */
class ArrayUtils {
	/**
	 * Join a string with a natural language conjunction at the end.
	 *
	 * @param  array  $array                      The array to join together with the natural language conjunction.
	 * @param  bool   $enclose_items_with_quotes  Whether each item in the array should be enclosed within quotation marks.
	 *
	 * @return string a string containing a list of items and a natural language conjunction.
	 * @since 1.0.0
	 */
	public static function natural_language_join( array $array, bool $enclose_items_with_quotes = FALSE )
	: string {
		if ( TRUE === $enclose_items_with_quotes ) {
			$array = array_map( function( $item ) {
				return '"' . $item . '"';
			}, $array );
		}
		$last = array_pop( $array );
		if ( $array ) {
			/* translators: 1: The first n-1 items of a list 2: the last item in the list. */
			return sprintf( __( '%1$s and %2$s', 'elje' ), implode( ', ', $array ), $last );
		}

		return $last;
	}
}
