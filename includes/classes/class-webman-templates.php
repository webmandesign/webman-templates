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
 * 10) Featured images
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

				if ( ! is_callable( 'FLBuilder::register_templates' ) ) {
					return;
				}


			// Helper variables

				$templates_path = trailingslashit( WMTEMPLATES_PATH . 'templates' );

				$global_template_files = array( '/templates.dat' );
				$theme_template_files  = array_filter( (array) apply_filters( 'wmhook_webman_templates_theme_template_path', array() ) );


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
								current_theme_supports( 'webman-templates-global' );
								&& ! empty( $global_template_files )
							) {
							foreach ( $global_template_files as $path ) {

								$file = trailingslashit( $templates_path . '_global' ) . $file;

								if ( file_exists( $file ) ) {
									FLBuilder::register_templates( $file );
								}

							}
						}

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
	 * 10) Featured images
	 */

		/**
		 * Template featured image path
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $path           Path to folder with template featured images.
		 * @param  string $template_file  Template file being processed.
		 */
		public static function featured_image_path( $path, $template_file ) {

			// Output

				return trailingslashit( WMTEMPLATES_URL . 'templates/' . WMTEMPLATES_THEME );

		} // /featured_image_path





} // /WebMan_Templates

add_action( 'after_setup_theme', 'WebMan_Templates::init' );
