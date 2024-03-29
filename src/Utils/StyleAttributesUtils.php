<?php
namespace Elje\Blocks\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * StyleAttributesUtils class used for getting class and style from attributes.
 *
 * @since 1.0.0
 */
class StyleAttributesUtils {

	/**
	 * Get class and style for font-size from attributes.
	 *
	 * @param  array  $attributes  Block attributes.
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public static function get_font_size_class_and_style( array $attributes )
	: ?array {

		$font_size = $attributes['fontSize'] ?? '';

		$custom_font_size = $attributes['style']['typography']['fontSize'] ?? '';

		if ( ! $font_size && '' === $custom_font_size ) {
			return NULL;
		}

		if ( $font_size ) {
			return [
				'class' => sprintf( 'has-font-size has-%s-font-size', $font_size ),
				'style' => NULL,
			];
		} elseif ( '' !== $custom_font_size ) {
			return [
				'class' => NULL,
				'style' => sprintf( 'font-size: %s;', $custom_font_size ),
			];
		}

		return NULL;
	}

	/**
	 * Get class and style for text-color from attributes.
	 *
	 * @param  array  $attributes  Block attributes.
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public static function get_text_color_class_and_style( array $attributes )
	: ?array {

		$text_color = $attributes['textColor'] ?? '';

		$custom_text_color = $attributes['style']['color']['text'] ?? '';

		if ( ! $text_color && ! $custom_text_color ) {
			return NULL;
		}

		if ( $text_color ) {
			return [
				'class' => sprintf( 'has-text-color has-%s-color', $text_color ),
				'style' => NULL,
			];
		} elseif ( $custom_text_color ) {
			return [
				'class' => NULL,
				'style' => sprintf( 'color: %s;', $custom_text_color ),
			];
		}

		return NULL;
	}

	/**
	 * Get class and style for link-color from attributes.
	 *
	 * @param  array  $attributes  Block attributes.
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public static function get_link_color_class_and_style( array $attributes )
	: ?array {

		if ( ! isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
			return NULL;
		}

		$link_color = $attributes['style']['elements']['link']['color']['text'];

		$index_named_link_color = strrpos( $link_color, '|' );

		if ( ! empty( $index_named_link_color ) ) {
			$parsed_named_link_color = substr( $link_color, $index_named_link_color + 1 );

			return [
				'class' => NULL,
				'style' => sprintf( 'color: %s;', $parsed_named_link_color ),
			];
		} else {
			return [
				'class' => NULL,
				'style' => sprintf( 'color: %s;', $link_color ),
			];
		}
	}

	/**
	 * Get class and style for line height from attributes.
	 *
	 * @param  array  $attributes  Block attributes.
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public static function get_line_height_class_and_style( array $attributes )
	: ?array {

		$line_height = $attributes['style']['typography']['lineHeight'] ?? '';

		if ( ! $line_height ) {
			return NULL;
		}

		return [
			'class' => NULL,
			'style' => sprintf( 'line-height: %s;', $line_height ),
		];
	}

	/**
	 * Get class and style for background-color from attributes.
	 *
	 * @param  array  $attributes  Block attributes.
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public static function get_background_color_class_and_style( array $attributes )
	: ?array {

		$background_color = $attributes['backgroundColor'] ?? '';

		$custom_background_color = $attributes['style']['color']['background'] ?? '';

		if ( ! $background_color && '' === $custom_background_color ) {
			return NULL;
		}

		if ( $background_color ) {
			return [
				'class' => sprintf( 'has-background has-%s-background-color', $background_color ),
				'style' => NULL,
			];
		} elseif ( '' !== $custom_background_color ) {
			return [
				'class' => NULL,
				'style' => sprintf( 'background-color: %s;', $custom_background_color ),
			];
		}

		return NULL;
	}

	/**
	 * Get classes and styles from attributes.
	 *
	 * @param  array  $attributes  Block attributes.
	 * @param  array  $properties  Properties to get classes/styles from.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_classes_and_styles_by_attributes( array $attributes, array $properties = [] )
	: array {
		$classes_and_styles = [
			'line_height'      => self::get_line_height_class_and_style( $attributes ),
			'text_color'       => self::get_text_color_class_and_style( $attributes ),
			'font_size'        => self::get_font_size_class_and_style( $attributes ),
			'link_color'       => self::get_link_color_class_and_style( $attributes ),
			'background_color' => self::get_background_color_class_and_style( $attributes ),
		];

		if ( ! empty( $properties ) ) {
			foreach ( $classes_and_styles as $key => $value ) {
				if ( ! in_array( $key, $properties, TRUE ) ) {
					unset( $classes_and_styles[ $key ] );
				}
			}
		}

		$classes_and_styles = array_filter( $classes_and_styles );

		$classes = array_map(
			function( $item ) {
				return $item['class'];
			},
			$classes_and_styles
		);

		$styles = array_map(
			function( $item ) {
				return $item['style'];
			},
			$classes_and_styles
		);

		$classes = array_filter( $classes );
		$styles  = array_filter( $styles );

		return [
			'classes' => implode( ' ', $classes ),
			'styles'  => implode( ' ', $styles ),
		];
	}

	/**
	 * Get space-separated classes from block attributes.
	 *
	 * @param  array  $attributes  Block attributes.
	 * @param  array  $properties  Properties to get classes from.
	 *
	 * @return string Space-separated classes.
	 * @since 1.0.0
	 */
	public static function get_classes_by_attributes( array $attributes, array $properties = [] )
	: string {
		$classes_and_styles = self::get_classes_and_styles_by_attributes( $attributes, $properties );

		return $classes_and_styles['classes'];
	}

	/**
	 * Get space-separated style rules from block attributes.
	 *
	 * @param  array  $attributes  Block attributes.
	 * @param  array  $properties  Properties to get styles from.
	 *
	 * @return string Space-separated style rules.
	 * @since 1.0.0
	 */
	public static function get_styles_by_attributes( array $attributes, array $properties = [] )
	: string {
		$classes_and_styles = self::get_classes_and_styles_by_attributes( $attributes, $properties );

		return $classes_and_styles['styles'];
	}
}
