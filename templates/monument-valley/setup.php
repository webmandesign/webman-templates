<?php if ( ! defined( 'WPINC' ) ) exit;

/**
 * Theme templates setup
 *
 * @since    1.0
 * @version  1.0
 */
function webman_templates_monument_valley() {

	// Output

		return array(
				'rows-content.dat',
				'rows-intro.dat',
				'rows-shop.dat',
			);

} // /webman_templates_monument_valley

add_filter( 'webman_templates/theme_template_files', 'webman_templates_monument_valley' );
