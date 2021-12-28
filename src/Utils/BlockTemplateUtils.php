<?php
namespace Elje\Blocks\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

/**
 * BlockTemplateUtils class used for serving block templates from our Blocks.
 *
 * @since 1.0.0
 */
class BlockTemplateUtils {
	/**
	 * Returns an array containing the references of
	 * the passed blocks and their inner blocks.
	 *
	 * @param  array  $blocks  array of blocks.
	 *
	 * @return array block references to the passed blocks and their inner blocks.
	 * @since 1.0.0
	 */
	public static function gutenberg_flatten_blocks( array &$blocks )
	: array {
		$all_blocks = [];
		$queue      = [];
		foreach ( $blocks as &$block ) {
			$queue[] = &$block;
		}
		$queue_count = count( $queue );

		while ( $queue_count > 0 ) {
			$block = &$queue[0];
			array_shift( $queue );
			$all_blocks[] = &$block;

			if ( ! empty( $block['innerBlocks'] ) ) {
				foreach ( $block['innerBlocks'] as &$inner_block ) {
					$queue[] = &$inner_block;
				}
			}

			$queue_count = count( $queue );
		}

		return $all_blocks;
	}

	/**
	 * Parses wp_template content and injects the current theme's
	 * stylesheet as a theme attribute into each wp_template_part
	 *
	 * @param  string  $template_content  serialized wp_template content.
	 *
	 * @return string Updated wp_template content.
	 * @since 1.0.0
	 */
	public static function gutenberg_inject_theme_attribute_in_content( string $template_content )
	: string {
		$has_updated_content = FALSE;
		$new_content         = '';
		$template_blocks     = parse_blocks( $template_content );

		$blocks = self::gutenberg_flatten_blocks( $template_blocks );
		foreach ( $blocks as &$block ) {
			if (
				'core/template-part' === $block['blockName'] &&
				! isset( $block['attrs']['theme'] )
			) {
				$block['attrs']['theme'] = wp_get_theme()->get_stylesheet();
				$has_updated_content     = TRUE;
			}
		}

		if ( $has_updated_content ) {
			/** @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection */
			foreach ( $template_blocks as &$block ) {
				$new_content .= serialize_block( $block );
			}

			return $new_content;
		}

		return $template_content;
	}

	/**
	 * Build a unified template object based a post Object.
	 *
	 * @param  \WP_Post  $post  Template post.
	 *
	 * @return \WP_Block_Template|\WP_Error Template.
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 * @noinspection PhpUndefinedFieldInspection
	 */
	public static function gutenberg_build_template_result_from_post( \WP_Post $post ) {
		$terms = get_the_terms( $post, 'wp_theme' );

		if ( is_wp_error( $terms ) ) {
			return $terms;
		}

		if ( ! $terms ) {
			return new \WP_Error( 'template_missing_theme', __( 'No theme is defined for this template.', 'elje' ) );
		}

		$theme          = $terms[0]->name;
		$has_theme_file = TRUE;

		$template                 = new \WP_Block_Template();
		$template->wp_id          = $post->ID;
		$template->id             = $theme . '//' . $post->post_name;
		$template->theme          = 'elje' === $theme ? 'Elje' : $theme;
		$template->content        = $post->post_content;
		$template->slug           = $post->post_name;
		$template->source         = 'custom';
		$template->type           = $post->post_type;
		$template->description    = $post->post_excerpt;
		$template->title          = $post->post_title;
		$template->status         = $post->post_status;
		$template->has_theme_file = $has_theme_file;
		$template->is_custom      = FALSE;
		$template->post_types     = []; // Don't appear in any Edit Post template selector dropdown.

		if ( 'wp_template_part' === $post->post_type ) {
			$type_terms = get_the_terms( $post, 'wp_template_part_area' );
			if ( ! is_wp_error( $type_terms ) && FALSE !== $type_terms ) {
				$template->area = $type_terms[0]->name;
			}
		}

		if ( 'elje' === $theme ) {
			$template->origin = 'plugin';
		}

		return $template;
	}

	/**
	 * Build a unified template object based on a theme file.
	 *
	 * @param  array         $template_file  Theme file.
	 * @param  string|array  $template_type  wp_template or wp_template_part.
	 *
	 * @return \WP_Block_Template Template.
	 * @since        1.0.0
	 * @noinspection PhpUndefinedFieldInspection
	 * @noinspection MissingParameterTypeDeclarationInspection
	 */
	public static function gutenberg_build_template_result_from_file( array $template_file, $template_type )
	: \WP_Block_Template {
		$template_file          = (object) $template_file;
		$template_is_from_theme = 'theme' === $template_file->source;
		$theme_name             = wp_get_theme()->get( 'TextDomain' );

		$template_content         = file_get_contents( $template_file->path );
		$template                 = new \WP_Block_Template();
		$template->id             = $template_is_from_theme ? $theme_name . '//' . $template_file->slug : 'elje//' . $template_file->slug;
		$template->theme          = $template_is_from_theme ? $theme_name : 'Elje';
		$template->content        = self::gutenberg_inject_theme_attribute_in_content( $template_content );
		$template->source         = $template_file->source ? $template_file->source : 'plugin';
		$template->slug           = $template_file->slug;
		$template->type           = $template_type;
		$template->title          = ! empty( $template_file->title ) ? $template_file->title : self::convert_slug_to_title( $template_file->slug );
		$template->status         = 'publish';
		$template->has_theme_file = TRUE;
		$template->origin         = $template_file->source;
		$template->is_custom      = FALSE;
		$template->post_types     = [];
		$template->area           = 'uncategorized';

		return $template;
	}

	/**
	 * Build a new template object so that we can make Elje Blocks default templates
	 * available in the current theme should they not have any.
	 *
	 * @param  string  $template_file           Block template file path.
	 * @param  string  $template_type           wp_template or wp_template_part.
	 * @param  string  $template_slug           Block template slug e.g. post-card.
	 * @param  bool    $template_is_from_theme  If the block template file is being loaded from the current theme
	 *                                          instead of our Blocks.
	 *
	 * @return object Block template object.
	 * @since 1.0.0
	 */
	public static function create_new_block_template_object( string $template_file, string $template_type, string $template_slug, bool $template_is_from_theme = FALSE )
	: \stdClass {
		$theme_name = wp_get_theme()->get( 'TextDomain' );

		$new_template_item = [
			'slug'        => $template_slug,
			'id'          => $template_is_from_theme ? $theme_name . '//' . $template_slug : 'elje//' . $template_slug,
			'path'        => $template_file,
			'type'        => $template_type,
			'theme'       => $template_is_from_theme ? $theme_name : 'elje',
			'source'      => $template_is_from_theme ? 'theme' : 'plugin',
			'title'       => self::convert_slug_to_title( $template_slug ),
			'description' => '',
			'post_types'  => [],
		];

		return (object) $new_template_item;
	}

	/**
	 * Finds all nested template part file paths in a theme's directory.
	 *
	 * @param  string  $base_directory  The theme's file path.
	 *
	 * @return array $path_list A list of paths to all template part files.
	 * @since 1.0.0
	 */
	public static function gutenberg_get_template_paths( string $base_directory )
	: array {
		$path_list = [];
		if ( file_exists( $base_directory ) ) {
			$nested_files      = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $base_directory ) );
			$nested_html_files = new \RegexIterator( $nested_files, '/^.+\.html$/i', \RecursiveRegexIterator::GET_MATCH );
			foreach ( $nested_html_files as $path => $file ) {
				$path_list[] = $path;
			}
		}

		return $path_list;
	}

	/**
	 * Converts template slugs into readable titles.
	 *
	 * @param  string  $template_slug  The templates slug (e.g. post-card).
	 *
	 * @return string Human friendly title converted from the slug.
	 * @since 1.0.0
	 */
	public static function convert_slug_to_title( string $template_slug )
	: string {
		return ucwords( preg_replace( '/[\-_]/', ' ', $template_slug ) );
	}

	/**
	 * Converts template paths into a slug
	 *
	 * @param  string  $path            The template's path.
	 * @param  string  $directory_name  The template's directory name.
	 *
	 * @return string slug
	 * @since 1.0.0
	 */
	public static function generate_template_slug_from_path( string $path, string $directory_name = 'block-templates' )
	: string {
		return substr(
			$path,
			strpos( $path, $directory_name . DIRECTORY_SEPARATOR ) + 1 + strlen( $directory_name ),
			- 5
		);
	}

	/**
	 * Check if the theme has a template. So we know if to load our own in or not.
	 *
	 * @param  string  $template_name  name of the template file without .html extension e.g. 'post-card'.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public static function theme_has_template( string $template_name )
	: bool {
		return is_readable( get_template_directory() . '/block-templates/' . $template_name . '.html' ) ||
		       is_readable( get_stylesheet_directory() . '/block-templates/' . $template_name . '.html' );
	}

	/**
	 * Check if the theme has a template. So we know if to load our own in or not.
	 *
	 * @param  string  $template_name  name of the template file without .html extension e.g. 'post-card'.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public static function theme_has_template_part( string $template_name )
	: bool {
		return is_readable( get_template_directory() . '/block-template-parts/' . $template_name . '.html' ) ||
		       is_readable( get_stylesheet_directory() . '/block-template-parts/' . $template_name . '.html' );
	}

	/**
	 * Checks to see if they are using a compatible version of WP,
	 * or if not they have a compatible version of the Gutenberg plugin installed.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public static function supports_block_templates()
	: bool {
		return function_exists( 'wp_is_block_theme' ) &&
		       function_exists( 'gutenberg_supports_block_templates' ) &&
		       wp_is_block_theme() &&
		       gutenberg_supports_block_templates();
	}
}
