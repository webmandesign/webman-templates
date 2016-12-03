<?php if ( ! defined( 'WPINC' ) ) exit;

/**
 * Plugin automatic updates
 *
 * Compatible with GitHub Updater plugin.
 * @link  https://github.com/afragen/github-updater
 * @link  https://github.com/afragen/github-updater/wiki/Installation
 */
if ( class_exists( 'Fragen\\GitHub_Updater\\Base' ) ) {
	return;
}





/**
 * Configure and run the updater
 */

	require_once WMTEMPLATES_PATH . 'includes/classes/class-updater.php';

	$updater = new WebMan_Templates_Updater( WMTEMPLATES_FILE );

	$updater->set_username( 'webmandesign' );
	$updater->set_repository( 'webman-templates' );

	$updater->initialize();





/**
 * We need an admin notice for multisite network plugin activation
 */

	require_once WMTEMPLATES_PATH . 'includes/vendor/persist-admin-notices-dismissal/persist-admin-notices-dismissal.php';

	add_action( 'admin_init', array( 'PAnD', 'init' ) );

	/**
	 * Admin notice for multisite network plugin activation
	 *
	 * @since    1.0
	 * @version  1.0
	 */
	function webman_templates_admin_notice_updater() {

		// Helper variables

			$screen = get_current_screen();


		// Requirements check

			if (
					! is_multisite()
					|| is_plugin_active_for_network( plugin_basename( WMTEMPLATES_FILE ) )
					|| ! is_admin()
					|| ! in_array( $screen->id, array( 'plugins', 'dashboard', 'appearance_page_tgmpa-install-plugins' ) )
					|| ! current_user_can( 'activate_plugins' )
				) {
				return;
			}

			if ( ! PAnD::is_admin_notice_active( 'webman-templates-notice-updater-forever' ) ) {
				return;
			}


		// Output

			?>

			<div data-dismissible="webman-templates-notice-updater-forever" class="wm-notice notice error notice-error is-dismissible">
				<p style="margin: 1em;">
					WARNING: <strong>WebMan Templates</strong> plugin has to be activated network wide to receive automatic plugin updates.
					<?php if ( current_user_can( 'manage_network_plugins' ) ) : ?>
					<a href="<?php echo esc_url( network_admin_url( 'plugins.php' ) ); ?>" class="button button-primary" style="margin: 0 1em;">Activate the plugin network wide &raquo;</a>
					<?php else : ?>
					Please, <strong>contact your WordPress network administrator to activate the plugin</strong> for you!
					<?php endif; ?>
					<br>
					<em>Alternatively you can also install a <a href="https://github.com/afragen/github-updater/wiki/Installation" target="_blank"><strong>Github Updater</strong> plugin</a> to manage <strong>WebMan Templates</strong> automatic updates.</em>
				</p>
			</div>

			<?php

	} // /webman_templates_admin_notice_updater

	add_action( 'admin_notices', 'webman_templates_admin_notice_updater' );
