<?php if ( ! defined( 'WPINC' ) ) exit;

/**
 * Theme templates setup
 */
function webman_templates_monument_valley() {

	// Output

		return array(
				'rows-content.dat',
				'rows-intro.dat',
				'rows-shop.dat',
			);

} // /webman_templates_monument_valley

add_filter( 'wmhook_webman_templates_theme_template_path', 'webman_templates_monument_valley' );
