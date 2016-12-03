<?php if ( ! defined( 'WPINC' ) ) exit;

/**
 * Adding custom templates into Beaver Builder
 *
 * @since    1.0
 * @version	 1.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Thumbnails
 */
class WebMan_Templates {





	/**
	 * 0) Init
	 */

		private static $instance;



		/**
		 * Constructor
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		private function __construct() {

			// Requirements check

				if (
						! is_callable( 'FLBuilder::register_templates' )
						|| ! current_theme_supports( 'webman-templates' )
					) {
					return;
				}


			// Helper variables

				$templates_path = trailingslashit( WMTEMPLATES_PATH . 'templates' );

				$global_template_files = array( 'templates.dat' );
				$theme_template_files  = array_filter( (array) apply_filters( 'webman_templates/theme_template_files', array() ) );


			// Processing

				// Setup

					// Theme-specific templates

						if ( ! empty( $theme_template_files ) ) {
							foreach ( $theme_template_files as $file ) {

								$file = trailingslashit( $templates_path . WMTEMPLATES_THEME ) . $file;

								if ( file_exists( $file ) ) {
									FLBuilder::register_templates( $file );
								}

							}
						}

					// Global templates

						if (
								current_theme_supports( 'webman-templates-global' )
								&& ! empty( $global_template_files )
							) {
							foreach ( $global_template_files as $file ) {

								$file = trailingslashit( $templates_path . '_global' ) . $file;

								if ( file_exists( $file ) ) {
									FLBuilder::register_templates( $file );
								}

							}
						}

				// Hooks

					// Filters

						add_filter( 'fl_builder_template_selector_data', __CLASS__ . '::thumbnail_path', 10, 2 );

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    1.0
		 * @version  1.0
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
	 * 10) Thumbnails
	 */

		/**
		 * Template thumbnail path
		 *
		 * If you want to use local plugin template thumbnails, make sure
		 * your Templates posts are organized into categories which slug
		 * starts with `wm-` or `theme-` prefix.
		 * Then put the thumbnails into `templates/THEME_SLUG/thumbs/` folder.
		 * Best thumbnail image size is 256 px wide, the height is up to you.
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  array  $template_data
		 * @param  object $template
		 */
		public static function thumbnail_path( $template_data, $template ) {

			// Helper variables

				$path       = trailingslashit( WMTEMPLATES_URL . 'templates/' . WMTEMPLATES_THEME );
				$categories = implode( '|', array_keys( (array) $template_data['category'] ) );


			// Processing

				if (
						false !== stripos( $categories, 'theme-' )
						|| false !== stripos( $categories, 'wm-' )
					) {

					if ( $template->image ) {

						$template_data['image'] = $path . 'thumbs/' . $template->image;

					} else {

						/**
						 * This image will not be displayed.
						 * That's how Beaver Builder treats `blank.jpg` in its interface.
						 */
						$template_data['image'] = $path . 'thumbnail/blank.jpg';

					}

				}


			// Output

				return $template_data;

		} // /thumbnail_path





} // /WebMan_Templates

add_action( 'init', 'WebMan_Templates::init' );
