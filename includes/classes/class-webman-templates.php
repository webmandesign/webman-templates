<?php if ( ! defined( 'WPINC' ) ) exit;
/**
 * Adding custom templates into Beaver Builder.
 *
 * @since    1.0.0
 * @version  2.2.3
 *
 * Contents:
 *
 *   0) Init
 *  10) Functionality
 *  20) Thumbnails
 * 100) Helpers
 */
class WebMan_Templates {





	/**
	 * 0) Init
	 */

		public static $path_templates;

		private static $theme;

		private static $instance;



		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @version  2.2.3
		 */
		private function __construct() {

			// Requirements check

				if ( ! is_callable( 'FLBuilder::register_templates' ) ) {
					// No need to run if Beaver Builder is not active.
					return;
				}


			// Variables

				self::$theme          = apply_filters( 'webman_templates/theme', get_template() );
				self::$path_templates = trailingslashit( WMTEMPLATES_PATH . 'templates' );


			// Processing

				// Load

					self::register_templates();

				// Hooks

					// Filters

						add_filter( 'fl_builder_template_details', __CLASS__ . '::thumbnail_src', 10, 2 );

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public static function init() {

			// Processing

				if ( null === self::$instance ) {
					self::$instance = new self;
				}


			// Output

				return self::$instance;

		} // /init





	/**
	 * 10) Functionality
	 */

		/**
		 * Register template files with Beaver Builder
		 *
		 * IMPORTANT:
		 * Don't use `FLBuilderModel::is_builder_active()` here to check
		 * if Beaver Builder is in edit mode as it won't work.
		 *
		 * @since    1.0.0
		 * @version  2.2.0
		 */
		public static function register_templates() {

			// Requirements check

				if ( ! isset( $_GET['fl_builder'] ) ) {
					return;
				}


			// Helper variables

				$templates_theme  = self::get_template_files( 'theme' );
				$tempaltes_global = self::get_template_files( 'global' );


			// Processing

				// Theme-specific templates
				if ( ! empty( $templates_theme ) ) {
					foreach ( $templates_theme as $file ) {
						$file = trailingslashit( self::$path_templates . self::$theme ) . $file;
						$file = apply_filters( 'webman_templates/template_file', $file, 'theme', self::$theme );

						if ( file_exists( $file ) ) {
							FLBuilder::register_templates( $file );
						}
					}
				}

				// Global templates
				if ( self::get_globals_support() && ! empty( $tempaltes_global ) ) {
					foreach ( $tempaltes_global as $file ) {
						$file = trailingslashit( self::$path_templates . '_global' ) . $file;
						$file = apply_filters( 'webman_templates/template_file', $file, 'global', self::$theme );

						if ( file_exists( $file ) ) {
							FLBuilder::register_templates( $file );
						}
					}
				}

		} // /register_templates





	/**
	 * 20) Thumbnails
	 */

		/**
		 * Template thumbnail source.
		 *
		 * If you want to use local plugin template thumbnails, make sure
		 * your Templates posts are organized into categories which slug
		 * starts with `wm-` or `theme-` prefix.
		 * For global templates the category slug has to include `-example`
		 * somewhere, possibly as a suffix.
		 * Then put the thumbnails into `templates/THEME_SLUG/thumbs/` folder
		 * (or `templates/_global/thumbs/` folder, respectively).
		 * Best thumbnail image size is 256 px wide, the height is up to you.
		 *
		 * @since    1.0.0
		 * @version  2.2.3
		 *
		 * @param  array  $template_details
		 * @param  string $template
		 */
		public static function thumbnail_src( $template_details, $template ) {

			// Requirements check

				if ( ! isset( $template->type ) ) {
					return $template_details;
				}


			// Variables

				$categories = '';
				if ( isset( $template_details['category'] ) ) {
					$categories = implode( '|', array_keys( (array) $template_details['category'] ) );
				}


			// Processing

				// Does our category slug contain `wm-` or `theme-`?
				if (
					false !== stripos( $categories, 'wm-' )
					|| false !== stripos( $categories, 'theme-' )
				) {

					// Additional helper variables

						$extensions = array_filter( (array) apply_filters( 'webman_templates/thumbnail_extension', array( 'png', 'jpg' ) ) );

						if ( false !== stripos( $categories, '-example' ) ) {
							$path     = trailingslashit( WMTEMPLATES_PATH . 'templates/_global' );
							$url_base = trailingslashit( WMTEMPLATES_URL . 'templates/_global' );
						} else {
							$path     = trailingslashit( WMTEMPLATES_PATH . 'templates/' . self::$theme );
							$url_base = trailingslashit( WMTEMPLATES_URL . 'templates/' . self::$theme );
						}

					// Set thumbnail

						if ( $template->image ) {

							$template_details['image'] = $url_base . 'thumbs/' . $template->image;

						} else {

							/**
							 * This image will not be displayed.
							 * That's how Beaver Builder treats `blank.jpg` in its interface.
							 */
							$template_details['image'] = $url_base . 'thumbnail/blank.jpg';

							/**
							 * Or, use thumbnail named as template slug, if it exists.
							 */
							if ( isset( $template->slug ) && $template->slug ) {
								foreach ( $extensions as $extension ) {
									$image_file = 'thumbs/' . $template->slug . '.' . preg_replace( '/[^A-Za-z]/', '', $extension );

									if ( file_exists( $path . $image_file ) ) {
										$template_details['image'] = $url_base . $image_file;
										break;
									}
								}
							}

						}

				}


			// Output

				return $template_details;

		} // /thumbnail_src





	/**
	 * 100) Helpers
	 */

		/**
		 * Get templates files in array
		 *
		 * Getting global template files array is default.
		 *
		 * @since    2.2.0
		 * @version  2.2.0
		 */
		public static function get_template_files( $scope = '' ) {

			// Helper variables

				$template_files = array( 'rows.dat' );

				if ( class_exists( 'WM_Shortcodes' ) ) {
					$template_files[] = 'rows-wmamp.dat';
				}


			// Processing

				// Get theme specific template files
				if ( 'theme' === $scope ) {

					$theme_setup_file_path = apply_filters( 'webman_templates/theme_setup_file_path', trailingslashit( self::$path_templates . self::$theme ) . 'setup.php' );

					if ( file_exists( $theme_setup_file_path ) ) {
						// This file has to contain some `$template_files = array(...);` code!
						include $theme_setup_file_path;
					} else {
						$template_files = array();
					}

				}


			// Output

				return array_filter( (array) apply_filters( 'webman_templates/template_files', $template_files, $scope ) );

		} // /get_template_files



		/**
		 * Get global templates support
		 *
		 * @since    2.2.0
		 * @version  2.2.0
		 */
		public static function get_globals_support() {

			// Helper variables

				$output = true;
				$theme_templates = self::get_template_files( 'theme' );


			// Processing

				if (
						! empty( $theme_templates )
						&& ! current_theme_supports( 'webman-templates-global' )
					) {
					$output = false;
				}


			// Output

				return $output;

		} // /get_globals_support





} // /WebMan_Templates

add_action( 'init', 'WebMan_Templates::init' );
