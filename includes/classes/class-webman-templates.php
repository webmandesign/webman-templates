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
 * 20) Admin notices
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

					// Actions

						add_action( 'admin_notices', __CLASS__ . '::notice_webman_amplifier' );

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





	/**
	 * 20) Admin notices
	 */

		/**
		 * Admin notice container
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $text   Notice text.
		 * @param  string $id     Unique notice id.
		 * @param  string $class  Notice CSS class.
		 */
		public static function notice( $text, $id, $class = 'update' ) {

			// Helper variables

				$id = 'webman-templates-notice-' . sanitize_html_class( trim( $id ) ) . '-forever';


			// Requirements check

				if ( ! PAnD::is_admin_notice_active( $id ) ) {
					return;
				}


			// Output

				return '<div data-dismissible="' . esc_attr( $id ) . '" class="wm-notice notice is-dismissible ' . esc_attr( trim( $class ) ) . '">' . $text . '</div>';

		} // /notice


		/**
		 * Admin notice: WebMan Amplifier plugin
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public static function notice_webman_amplifier() {

			// Requirements check

				if ( class_exists( 'WM_Amplifier' ) ) {
					return;
				}


			// Helper variables

				$output = '';


			// Processing

				$output .= '<p>';
				$output .= 'You need to <strong>install and activate</strong> the <a href="https://wordpress.org/plugins/webman-amplifier/" target="_blank"><strong>WebMan Amplifier</strong></a> plugin to use the custom Beaver Builder page builder templates provided by WebMan Templates plugin. ';
				$output .= '<br>Also, make sure your <strong>theme is compatible</strong> both with WebMan Amplifier and WebMan Templates plugins. ';
				$output .= 'Please visit <a href="https://www.webmandesign.eu" target="_blank">WebMan Design website</a> for selection of compatible themes. ';
				$output .= '</p>';


			// Output

				echo self::notice( $output, 'wmamp', 'error notice-error' );

		} // /notice_webman_amplifier





} // /WebMan_Templates

add_action( 'init', 'WebMan_Templates::init' );
