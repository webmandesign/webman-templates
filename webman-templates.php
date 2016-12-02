<?php if ( ! defined( 'WPINC' ) ) exit;

/**
 * Plugin Name:        WebMan Templates
 * Plugin URI:         https://github.com/webmandesign/webman-templates
 * Description:        Adding predefined custom Beaver Builder page builder templates.
 * Version:            0.4.0
 * Author:             WebMan Design - Oliver Juhas
 * Author URI:         https://www.webmandesign.eu
 * License:            GNU General Public License v3
 * License URI:        http://www.gnu.org/licenses/gpl-3.0.txt
 * Requires at least:  4.5
 * Tested up to:       4.7
 *
 * @copyright  WebMan Design, Oliver Juhas
 * @license    GPL-3.0, http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @link  https://github.com/webmandesign/webman-templates
 * @link  https://www.webmandesign.eu
 *
 * @package  WebMan Templates
 *
 * @version  1.0.0
 */





// Constants

	define( 'WMTEMPLATES_FILE', __FILE__ );

	define( 'WMTEMPLATES_PATH', plugin_dir_path( WMTEMPLATES_FILE ) ); // Trailing slashed
	define( 'WMTEMPLATES_URL', plugin_dir_url( WMTEMPLATES_FILE ) ); // Trailing slashed

	define( 'WMTEMPLATES_THEME', get_template() );



// Load functionality

	require_once WMTEMPLATES_PATH . 'includes/classes/class-webman-templates.php';

	$template_setup_file_path = trailingslashit( WMTEMPLATES_PATH . 'templates/' . WMTEMPLATES_THEME ) . 'setup.php';

	if ( file_exists( $template_setup_file_path ) ) {
		require_once $template_setup_file_path;
	}

	require_once WMTEMPLATES_PATH . 'includes/updater.php';
