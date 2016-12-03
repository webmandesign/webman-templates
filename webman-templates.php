<?php if ( ! defined( 'WPINC' ) ) exit;

/**
 * Plugin Name:        WebMan Templates
 * Plugin URI:         https://github.com/webmandesign/webman-templates
 * Description:        Adding predefined custom Beaver Builder page builder templates.
 * Version:            0.6.0
 * Author:             WebMan Design - Oliver Juhas
 * Author URI:         https://www.webmandesign.eu
 * License:            GNU General Public License v3
 * License URI:        http://www.gnu.org/licenses/gpl-3.0.txt
 * Requires at least:  4.5
 * Tested up to:       4.7
 * GitHub Plugin URI:  webmandesign/webman-templates
 *
 * @copyright  WebMan Design, Oliver Juhas
 * @license    GPL-3.0, https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @link  https://github.com/webmandesign/webman-templates
 * @link  https://www.webmandesign.eu
 *
 * @package  WebMan Templates
 *
 * @version  0.6.0
 */





/**
 * Constants
 */

	define( 'WMTEMPLATES_FILE', __FILE__ );

	define( 'WMTEMPLATES_PATH', plugin_dir_path( WMTEMPLATES_FILE ) ); // Trailing slashed

	define( 'WMTEMPLATES_URL', plugin_dir_url( WMTEMPLATES_FILE ) ); // Trailing slashed

	define( 'WMTEMPLATES_THEME', get_template() );





/**
 * Load the functionality
 */

	// Main plugin functionality

		require_once WMTEMPLATES_PATH . 'includes/classes/class-webman-templates.php';

	// Theme-specific templates

		$template_setup_file_path = trailingslashit( WMTEMPLATES_PATH . 'templates/' . WMTEMPLATES_THEME ) . 'setup.php';

		if ( file_exists( $template_setup_file_path ) ) {
			require_once $template_setup_file_path;
		}

	// Plugin automatic updates

		if ( ! class_exists( 'Fragen\\GitHub_Updater\\Base' ) ) {
			require_once WMTEMPLATES_PATH . 'includes/updater.php';
		}
