<?php if ( ! defined( 'WPINC' ) ) exit;
/**
 * Plugin Name:  WebMan Templates
 * Plugin URI:   https://www.webmandesign.eu/portfolio/webman-templates-wordpress-plugin/
 * Description:  Provides collection of pre-designed row templates for Beaver Builder page builder.
 * Version:      2.2.3
 * Author:       WebMan Design, Oliver Juhas
 * Author URI:   https://www.webmandesign.eu/
 * License:      GPL-3.0-or-later
 * License URI:  http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Requires PHP:       7.0
 * Requires at least:  6.0
 *
 * @copyright  WebMan Design, Oliver Juhas
 * @license    GPL-3.0, https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @link  https://www.webmandesign.eu/portfolio/webman-templates-wordpress-plugin/
 * @link  https://github.com/webmandesign/webman-templates
 * @link  https://www.webmandesign.eu
 *
 * @package  WebMan Templates
 */





/**
 * Constants
 */

	define( 'WMTEMPLATES_FILE', __FILE__ );

	define( 'WMTEMPLATES_PATH', plugin_dir_path( WMTEMPLATES_FILE ) ); // Trailing slashed

	define( 'WMTEMPLATES_URL', plugin_dir_url( WMTEMPLATES_FILE ) ); // Trailing slashed





/**
 * Load the functionality
 */

	// Main plugin functionality

		require_once WMTEMPLATES_PATH . 'includes/classes/class-webman-templates.php';
