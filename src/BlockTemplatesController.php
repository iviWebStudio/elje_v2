<?php /** @noinspection PhpUndefinedClassInspection */
namespace Elje\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Wanna hack?' );
}

use Elje\Blocks\Utils\BlockTemplateUtils;

/**
 * BlockTypesController class.
 *
 * @since 1.0.0
 */
class BlockTemplatesController {

	/**
	 * Holds the path for the directory where the block templates will be kept.
	 *
	 * @var string
	 */
	private $templates_directory;

	/**
	 * Holds the path for the directory where the block template parts will be kept.
	 *
	 * @var string
	 */
	private $template_parts_directory;

	/**
	 * Directory name of the block template directory.
	 *
	 * @var string
	 */
	const TEMPLATES_DIR_NAME = 'block-templates';

	/**
	 * Directory name of the block template parts directory.
	 *
	 * @var string
	 */
	const TEMPLATE_PARTS_DIR_NAME = 'block-template-parts';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->templates_directory      = plugin_dir_path( __DIR__ ) . 'templates/' . self::TEMPLATES_DIR_NAME;
		$this->template_parts_directory = plugin_dir_path( __DIR__ ) . 'templates/' . self::TEMPLATE_PARTS_DIR_NAME;
		$this->init();
	}

	/**
	 * Initialization method.
	 *
	 * @since 1.0.0
	 */
	protected function init()
	: void {
		add_filter( 'pre_get_block_file_template', [
			$this,
			'maybe_return_blocks_template',
		], 10, 3 );
		add_filter( 'get_block_templates', [
			$this,
			'add_block_templates',
		], 10, 3 );
	}

	/**
	 * This function checks if there's a blocks template (ultimately it resolves either a saved blocks template from the
	 * database or a template file in `/templates/block-templates/`)
	 * to return to pre_get_posts short-circuiting the query in Gutenberg.
	 *
	 * @param  \WP_Block_Template|null  $template       Return a block template object to short-circuit the default query,
	 *                                                  or null to allow WP to run its normal queries.
	 * @param  string                   $id             Template unique identifier (example: theme_slug//template_slug).
	 * @param  string                   $template_type  wp_template or wp_template_part.
	 *
	 * @return mixed|\WP_Block_Template|\WP_Error
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	public function maybe_return_blocks_template( ?\WP_Block_Template $template, string $id, string $template_type ) {
		/*
		 * 'get_block_template' was introduced in WP 5.9. We need to support
		 * 'gutenberg_get_block_template' for previous versions of WP with
		 * Gutenberg enabled.
		 */
		if (
			! function_exists( 'gutenberg_get_block_template' ) &&
			! function_exists( 'get_block_template' )
		) {
			return $template;
		}
		$template_name_parts = explode( '//', $id );
		if ( count( $template_name_parts ) < 2 ) {
			return $template;
		}
		[
			,
			$slug,
		] = $template_name_parts;

		remove_filter( 'pre_get_block_file_template', [
			$this,
			'maybe_return_blocks_template',
		], 10 );

		/*
		 * Check if the theme has a saved version of this template before falling back.
		 * Note how the slug has not been modified at this point, we're still using the default one passed to this hook.
		 */
		$maybe_template = function_exists( 'gutenberg_get_block_template' ) ?
			gutenberg_get_block_template( $id, $template_type ) :
			get_block_template( $id, $template_type );

		if ( NULL !== $maybe_template ) {
			add_filter( 'pre_get_block_file_template', [
				$this,
				'maybe_return_blocks_template',
			], 10, 3 );

			return $maybe_template;
		}

		/*
		 * Theme-based template didn't exist.
		 * Try switching the theme to elje folder and try again.
		 * This function has been unhooked so won't run again.
		 */
		add_filter( 'get_block_file_template', [
			$this,
			'get_single_block_template',
		], 10, 3 );

		$maybe_template = function_exists( 'gutenberg_get_block_template' ) ?
			gutenberg_get_block_template( 'elje//' . $slug, $template_type ) :
			get_block_template( 'elje//' . $slug, $template_type );

		// Re-hook this function, it was only unhooked to stop recursion.
		add_filter( 'pre_get_block_file_template', [
			$this,
			'maybe_return_blocks_template',
		], 10, 3 );
		remove_filter( 'get_block_file_template', [
			$this,
			'get_single_block_template',
		], 10 );
		if ( NULL !== $maybe_template ) {
			return $maybe_template;
		}

		return $template;
	}

	/**
	 * Runs on the get_block_template hook. If a template is already found and passed to this function,
	 * then return it and don't run.
	 * If a template is *not* passed, try to look for one that matches the ID in the database,
	 * if that's not found defer to Blocks templates files.
	 * Priority goes: DB-Theme, DB-Blocks, Filesystem-Theme, Filesystem-Blocks.
	 *
	 * @param  \WP_Block_Template  $template       The found block template.
	 * @param  string              $id             Template unique identifier (example: theme_slug//template_slug).
	 * @param  string              $template_type  wp_template or wp_template_part.
	 *
	 * @return mixed|null
	 * @since        1.0.0
	 * @noinspection MissingReturnTypeInspection
	 */
	public function get_single_block_template( \WP_Block_Template $template, string $id, string $template_type ) {
		/*
		 * The template was already found before the filter runs,
		 * just return it immediately.
		 */
		if ( NULL !== $template ) {
			return $template;
		}

		$template_name_parts = explode( '//', $id );
		if ( count( $template_name_parts ) < 2 ) {
			return NULL;
		}
		[
			,
			$slug,
		] = $template_name_parts;

		// If this blocks template doesn't exist then we should just skip the function and let Gutenberg handle it.
		if ( ! $this->block_template_is_available( $slug, $template_type ) ) {
			return NULL;
		}

		$available_templates = $this->get_block_templates( [ $slug ], $template_type );

		if ( is_array( $available_templates ) && count( $available_templates ) > 0 ) {
			$first_available_template = array_shift( $available_templates );

			return BlockTemplateUtils::gutenberg_build_template_result_from_file( $first_available_template, $first_available_template->type );
		}

		return NULL;
	}

	/**
	 * Add the block template objects to be used.
	 *
	 * @param  array   $query_result   Array of template objects.
	 * @param  array   $query          Optional. Arguments to retrieve templates.
	 * @param  string  $template_type  wp_template or wp_template_part.
	 *
	 * @return array
	 * @since        1.0.0
	 */
	public function add_block_templates( array $query_result, array $query, string $template_type )
	: array {
		if ( ! BlockTemplateUtils::supports_block_templates() ) {
			return $query_result;
		}

		$post_type      = ! empty( $query['post_type'] ) ? $query['post_type'] : '';
		$slugs          = ! empty( $query['slug__in'] ) ? $query['slug__in'] : [];
		$template_files = $this->get_block_templates( $slugs, $template_type );

		foreach ( $template_files as $template_file ) {
			// Avoid adding the same template if it's already in the array of $query_result.
			if (
			array_filter(
				$query_result, function( $query_result_template ) use ( $template_file ) {
				return $query_result_template->slug === $template_file->slug &&
				       $query_result_template->theme === $template_file->theme;
			}
			)
			) {
				continue;
			}

			/*
			 * If the current $post_type is set (e.g. on an Edit Post screen), and isn't included in the available post_types
			 * on the template file, then lets skip it so that it doesn't get added. This is typically used to hide templates
			 * in the template dropdown on the Edit Post page.
			 */
			if ( $post_type &&
			     isset( $template_file->post_types ) &&
			     ! in_array( $post_type, $template_file->post_types, TRUE )
			) {
				continue;
			}

			/*
			 * It would be custom if the template was modified in the editor,
			 * so if it's not custom we can load it from the filesystem.
			 */
			if ( 'custom' !== $template_file->source ) {
				$template = BlockTemplateUtils::gutenberg_build_template_result_from_file( $template_file, $template_type );
			} else {
				$template_file->title = BlockTemplateUtils::convert_slug_to_title( $template_file->slug );
				$query_result[]       = $template_file;
				continue;
			}

			$is_not_custom   = FALSE === array_search(
					wp_get_theme()->get_stylesheet() . '//' . $template_file->slug,
					array_column( $query_result, 'id' ),
					TRUE
				);
			$fits_slug_query =
				! isset( $query['slug__in'] ) || in_array( $template_file->slug, $query['slug__in'], TRUE );
			$fits_area_query =
				! isset( $query['area'] ) || $template_file->area === $query['area'];
			$should_include  = $is_not_custom && $fits_slug_query && $fits_area_query;
			if ( $should_include ) {
				$query_result[] = $template;
			}
		}

		$query_result = $this->remove_theme_templates_with_custom_alternative( $query_result );

		return $query_result;
	}

	/**
	 * Removes templates that were added to a theme's block-templates directory,
	 * but already had a customised version saved in the database.
	 *
	 * @param  \WP_Block_Template[]|\stdClass[]  $templates  List of templates to run the filter on.
	 *
	 * @return array List of templates with duplicates removed. The customised alternative is preferred over the theme default.
	 * @since        1.0.0
	 * @noinspection MissingParameterTypeDeclarationInspection
	 */
	public function remove_theme_templates_with_custom_alternative( $templates )
	: array {
		$customised_template_slugs = array_map( function( $template ) {
			return $template->slug;
		},
			array_values(
				array_filter(
					$templates, function( $template ) {
					return 'custom' === $template->source;
				}
				)
			)
		);

		/*
		 * Remove theme (i.e. filesystem) templates that have the same slug as a customised one.
		 * We don't need to check for `elje` in $template->source here because plugin related templates
		 * won't have been added to $templates if a saved version was found in the db.
		 * This only affects saved templates that were saved BEFORE a theme template with the same slug was added.
		 */

		return array_values(
			array_filter(
				$templates, function( $template ) use ( $customised_template_slugs ) {
				return ! ( 'theme' === $template->source && in_array( $template->slug, $customised_template_slugs, TRUE ) );
			}
			)
		);
	}

	/**
	 * Gets the templates saved in the database.
	 *
	 * @param  array   $slugs          An array of slugs to retrieve templates for.
	 * @param  string  $template_type  wp_template or wp_template_part.
	 *
	 * @return array An array of found templates \WP_Block_Template or \WP_Error.
	 * @since        1.0.0
	 */
	public function get_block_templates_from_db( array $slugs = [], string $template_type = 'wp_template' )
	: array {
		$check_query_args = [
			'post_type'      => $template_type,
			'posts_per_page' => - 1,
			'no_found_rows'  => TRUE,
			'tax_query'      => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				[
					'taxonomy' => 'wp_theme',
					'field'    => 'name',
					'terms'    => [
						'elje',
						get_stylesheet(),
					],
				],
			],
		];
		if ( is_array( $slugs ) && count( $slugs ) > 0 ) {
			$check_query_args['post_name__in'] = $slugs;
		}
		$check_query          = new \WP_Query( $check_query_args );
		$saved_elje_templates = $check_query->posts;

		return array_map( function( $saved_elje_template ) {
			return BlockTemplateUtils::gutenberg_build_template_result_from_post( $saved_elje_template );
		},
			$saved_elje_templates
		);
	}

	/**
	 * Gets the templates from the plugin blocks directory,
	 * skipping those for which a template already exists in the theme directory.
	 *
	 * @param  string[]  $slugs                    An array of slugs to filter templates by. Templates whose slug does not match will not be returned.
	 * @param  array     $already_found_templates  Templates that have already been found, these are customised templates that are loaded from the database.
	 * @param  string    $template_type            wp_template or wp_template_part.
	 *
	 * @return array Templates from the Elje plugin directory.
	 * @since        1.0.0
	 */
	public function get_block_templates_from_elje( array $slugs, array $already_found_templates, string $template_type = 'wp_template' )
	: array {
		$directory      = $this->get_templates_directory( $template_type );
		$template_files = BlockTemplateUtils::gutenberg_get_template_paths( $directory );
		$templates      = [];

		if ( 'wp_template_part' === $template_type ) {
			$dir_name = self::TEMPLATE_PARTS_DIR_NAME;
		} else {
			$dir_name = self::TEMPLATES_DIR_NAME;
		}

		foreach ( $template_files as $template_file ) {
			$template_slug = BlockTemplateUtils::generate_template_slug_from_path( $template_file, $dir_name );

			if ( is_array( $slugs ) && count( $slugs ) > 0 && ! in_array( $template_slug, $slugs, TRUE ) ) {
				continue;
			}

			/*
			 * If the theme already has a template, or the template is already in the list (i.e. it came
			 * from the database) then we should not overwrite it with the one from the filesystem.
			 */
			$count = array_filter(
				$already_found_templates, function( $template ) use ( $template_slug ) {
				$template_obj = (object) $template; //phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.Found

				return $template_obj->slug === $template_slug;
			} );
			if ( BlockTemplateUtils::theme_has_template( $template_slug ) || count( $count ) > 0 ) {
				continue;
			}

			/*
			 * At this point the template only exists in the Blocks filesystem
			 * and has not been saved in the DB, or superseded by the theme.
			 */
			$templates[] = BlockTemplateUtils::create_new_block_template_object( $template_file, $template_type, $template_slug );
		}

		return $templates;
	}

	/**
	 * Get and build the block template objects from the block template files.
	 *
	 * @param  array   $slugs          An array of slugs to retrieve templates for.
	 * @param  string  $template_type  wp_template or wp_template_part.
	 *
	 * @return array
	 * @since        1.0.0
	 */
	public function get_block_templates( array $slugs = [], string $template_type = 'wp_template' )
	: array {
		$templates_from_db   = $this->get_block_templates_from_db( $slugs, $template_type );
		$templates_from_elje = $this->get_block_templates_from_elje( $slugs, $templates_from_db, $template_type );

		return array_merge( $templates_from_db, $templates_from_elje );
	}

	/**
	 * Gets the directory where templates of a specific template type can be found.
	 *
	 * @param  string  $template_type  wp_template or wp_template_part.
	 *
	 * @return string Template directory.
	 * @since        1.0.0
	 */
	protected function get_templates_directory( string $template_type = 'wp_template' )
	: string {
		if ( 'wp_template_part' === $template_type ) {
			return $this->template_parts_directory;
		}

		return $this->templates_directory;
	}

	/**
	 * Checks whether a block template with that name exists in Elje.
	 *
	 * @param  string  $template_name  Template to check.
	 * @param  string  $template_type  wp_template or wp_template_part.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function block_template_is_available( string $template_name, string $template_type = 'wp_template' )
	: bool {
		if ( ! $template_name ) {
			return FALSE;
		}
		$directory = $this->get_templates_directory( $template_type ) . '/' . $template_name . '.html';

		return is_readable( $directory ) || $this->get_block_templates( [ $template_name ], $template_type );
	}

}
