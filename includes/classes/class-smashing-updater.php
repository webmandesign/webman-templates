<?php if ( ! defined( 'WPINC' ) ) exit;

/**
 * Github updater class
 *
 * Based on "How To Deploy WordPress Plugins With GitHub Using Transients" article at SmashingMagazine.com.
 *
 * @link  https://www.smashingmagazine.com/2015/08/deploy-wordpress-plugins-with-github-using-transients/
 * @link  https://github.com/rayman813/smashing-updater-plugin
 *
 * @since    1.0
 * @version  1.0
 *
 * Contents:
 *
 *   0) Init
 *  10) Setup
 *  20) Github response
 *  30) WordPress update
 * 100) Helpers
 */
class Smashing_Updater {





	/**
	 * 0) Init
	 */

		private $file;
		private $plugin;
		private $basename;
		private $active;
		private $username;
		private $repository;
		private $authorize_token;
		private $github_response;



		/**
		 * Class constructor
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $file  Plugin main file.
		 */
		public function __construct( $file ) {

			// Helper variables

				$this->file = $file;


			// Processing

				// Hooking onto `admin_init` to make sure we have the `get_plugin_data()` function available.
				add_action( 'admin_init', array( $this, 'set_plugin_properties' ) );


			// Output

				return $this;

		} // /__construct



		/**
		 * Initialize the class by setting hooks
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public function initialize() {

			// Processing

				// Hooks

					// Actions

						add_action( 'admin_notices', array( $this, 'notice' ) );

					// Filters

						add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'modify_transient' ) );
						add_filter( 'plugins_api', array( $this, 'plugin_popup' ), 10, 3);
						add_filter( 'upgrader_post_install', array( $this, 'after_install' ), 10, 3 );

		} // /initialize





	/**
	 * 10) Setup
	 */

		/**
		 * Setting plugin properties
		 *
		 * List of properties set:
		 * - plugin data
		 * - plugin basename (plugin folder with main plugin file including extension)
		 * - plugin activation state
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public function set_plugin_properties() {

			// Processing

				$this->plugin   = get_plugin_data( $this->file );
				$this->basename = plugin_basename( $this->file );
				$this->active   = is_plugin_active( $this->basename );

		} // /set_plugin_properties



		/**
		 * Setting Github: Username
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $username  Github repository account username.
		 */
		public function set_username( $username ) {

			// Processing

				$this->username = $username;

		} // /set_username



		/**
		 * Setting Github: Repository
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $repository  Github repository slug.
		 */
		public function set_repository( $repository ) {

			// Processing

				$this->repository = $repository;

		} // /set_repository



		/**
		 * Setting Github: Private repository authorization
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $token  Private Github repository authorization token.
		 */
		public function authorize( $token ) {

			// Processing

				$this->authorize_token = $token;

		} // /authorize





	/**
	 * 20) Github response
	 */

		/**
		 * Get Github repository information
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		private function get_repository_info() {

			// Requirements check

				// No need to reset the response if we already have it
				if ( ! empty( $this->github_response ) ) {
					return;
				}


			// Helper variables

				// Build Github request URI
				$request_uri = sprintf( 'https://api.github.com/repos/%s/%s/releases', $this->username, $this->repository );

				// Do we use private Github repository? Add the token to the request URI.
				if ( $this->authorize_token ) {
					$request_uri = add_query_arg( 'access_token', $this->authorize_token, $request_uri );
				}

				// Get Github response. Should be in JSON format.
				$response = wp_remote_retrieve_body( wp_remote_get( $request_uri ) );


			// Processing

				// Do we have a response?
				if ( ! is_wp_error( $response ) && ! empty( $response ) ) {

					// Parse the response
					$response = json_decode( $response, true );

					// If the response is an array, get the most recent record only
					if ( is_array( $response ) ) {
						$response = current( $response );
					}

					// If we use a private Github repository, append the token to the ZIP URL
					if ( $this->authorize_token ) {
						$response['zipball_url'] = add_query_arg( 'access_token', $this->authorize_token, $response['zipball_url'] );
					}

					// Store the response in class variable
					$this->github_response = $response;

				}

		} // /get_repository_info





	/**
	 * 30) WordPress update
	 */

		/**
		 * Let WordPress know if we have a new plugin update
		 *
		 * The `pre_set_site_transient_update_plugins` is fired twice, so we make sure
		 * we run the code just once by checking if `$transient->response` is set.
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  array $transient  Value of `update_plugins` transient.
		 */
		public function modify_transient( $transient ) {

			// Requirements check

				// Did WordPress check for updates?
				if ( ! isset( $transient->response ) ) {
					return $transient;
				}


			// Processing

				// Get the repository info
				$this->get_repository_info();

				// Requirements check

					// Do we have the response from Github?
					if ( ! isset( $this->github_response['zipball_url'] ) ) {
						return $transient;
					}

				// Set checked plugin updates array if we have none
				if ( ! isset( $transient->checked ) ) {
					$transient->checked = array(
						$this->basename => $this->plugin['Version'],
					);
				}

				// Compare the plugin update versions
				$out_of_date = version_compare( $this->github_response['tag_name'], $this->plugin['Version'], 'gt' );

				// There is a new update
				if ( $out_of_date ) {

					// Setup our plugin info
					$plugin = array(
						'slug'        => current( explode( '/', $this->basename ) ),
						'plugin'      => $this->basename,
						'new_version' => $this->github_response['tag_name'],
						'url'         => $this->plugin['PluginURI'],
						'package'     => $this->github_response['zipball_url'],
						'tested'      => $GLOBALS['wp_version'],
					);

					// Return our plugin info as object in response
					$transient->response[ $this->basename ] = (object) $plugin;

				}


			// Output

				return $transient;

		} // /modify_transient



		/**
		 * Set content of plugin update info popup
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  false|object|array $result  The result object or array. Default false.
		 * @param  string             $action  The type of information being requested from the Plugin Install API.
		 * @param  object             $args    Plugin API arguments.
		 */
		public function plugin_popup( $result, $action, $args ) {

			// Requirements check

				// Are we checking our plugin?
				if ( ! isset( $args->slug ) || empty( $args->slug ) || current( explode( '/' , $this->basename ) ) !== $args->slug ) {
					return $result;
				}


			// Processing

				// Get Github repository info
				$this->get_repository_info();

				// Requirements check

					// Do we have the response from Github?
					if ( ! isset( $this->github_response['zipball_url'] ) ) {
						return $result;
					}

				// Set the plugin update info
				$result = array(
					'name'              => $this->plugin['Name'],
					'slug'              => $this->basename,
					'requires'          => '4.5',
					'tested'            => $GLOBALS['wp_version'],
					'version'           => $this->github_response['tag_name'],
					'author'            => $this->plugin['AuthorName'],
					'author_profile'    => $this->plugin['AuthorURI'],
					'last_updated'      => $this->github_response['published_at'],
					'homepage'          => $this->plugin['PluginURI'],
					'short_description' => $this->plugin['Description'],
					'download_link'     => $this->github_response['zipball_url'],
					'sections'          => array(
						'Description' => $this->plugin['Description'],
						'Changelog'   => $this->github_response['body'],
					),
				);


			// Output

				return (object) $result;

		} // /plugin_popup



		/**
		 * Filter the install response after the installation has finished
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  bool  $response    Install response.
		 * @param  array $hook_extra  Extra arguments passed to hooked filters.
		 * @param  array $result      Installation result data.
		 */
		public function after_install( $response, $hook_extra, $result ) {

			// Helper variables

				// Get global filesystem object
				global $wp_filesystem;

				// Set our plugin full directory path
				$install_directory = plugin_dir_path( $this->file );


			// Processing

				// Move new update files to the plugin directory
				$wp_filesystem->move( $result['destination'], $install_directory );

				// Make sure we don't change our plugin directory name
				$result['destination']        = $install_directory;
				$result['remote_destination'] = $install_directory;
				$result['destination_name']   = current( explode('/', $this->basename ) );

				// Reactivate the plugin if it was active
				if ( $this->active ) {
					if ( is_multisite() ) {
						activate_plugin( $this->basename, null, true );
					} else {
						activate_plugin( $this->basename );
					}
				}


			// Output

				return $result;

		} // /after_install





	/**
	 * 100) Helpers
	 */

		/**
		 * Admin notice for multisite plugin activation
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public function notice() {

			// Helper variables

				$screen = get_current_screen();


			// Requirements check

				if (
						! is_multisite()
						|| is_plugin_active_for_network( $this->basename )
						|| ! is_admin()
						|| 'plugins' !== $screen->id
						|| ! current_user_can( 'activate_plugins' )
						|| apply_filters( 'wmhook_webman_templates_updater_notice_disable', false )
					) {
					return;
				}


			// Processing

				$output  = '<div class="wm-notice notice error is-dismissible">';
				$output .= '<p>';
				$output .= '<strong>WARNING:</strong><br>';
				$output .= 'The <strong>WebMan Templates</strong> plugin has to be enabled network wide to receive automatic plugin updates. ';
				$output .= '<a href="' . esc_url( network_admin_url( 'plugins.php' ) ) . '">Please activate the plugin network wide.</a>';
				$output .= '</p>';
				$output .= '</div>';


			// Output

				echo $output;

		} // /notice





} // /Smashing_Updater
