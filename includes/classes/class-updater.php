<?php if ( ! defined( 'WPINC' ) ) exit;

/**
 * Github updater class
 *
 * Based on "How To Deploy WordPress Plugins With GitHub Using Transients" article at SmashingMagazine.com.
 *
 * @link  https://www.smashingmagazine.com/2015/08/deploy-wordpress-plugins-with-github-using-transients/
 * @link  https://github.com/rayman813/smashing-updater-plugin
 *
 * @since    1.0.0
 * @version  1.0.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Setup
 * 20) Github response
 * 30) WordPress update
 */
class WebMan_Templates_Updater {





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
		 * @since    1.0.0
		 * @version  1.0.0
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
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public function initialize() {

			// Processing

				// Hooks

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
		 * @since    1.0.0
		 * @version  1.0.0
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
		 * @since    1.0.0
		 * @version  1.0.0
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
		 * @since    1.0.0
		 * @version  1.0.0
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
		 * @since    1.0.0
		 * @version  1.0.0
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
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		private function get_repository_info() {

			// Requirements check

				if ( ! empty( $this->github_response ) ) {
					return;
				}


			// Helper variables

				$request_uri = sprintf( 'https://api.github.com/repos/%s/%s/releases', $this->username, $this->repository );

				if ( $this->authorize_token ) {
					$request_uri = add_query_arg( 'access_token', $this->authorize_token, $request_uri );
				}

				$response = wp_remote_retrieve_body( wp_remote_get( $request_uri ) );


			// Processing

				if ( ! is_wp_error( $response ) && ! empty( $response ) ) {
					$response = json_decode( $response, true );

					if ( is_array( $response ) ) {
						$response = current( $response );
					}

					if ( $this->authorize_token ) {
						$response['zipball_url'] = add_query_arg( 'access_token', $this->authorize_token, $response['zipball_url'] );
					}

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
		 * @since    1.0.0
		 * @version  1.0.0
		 *
		 * @param  array $transient  Value of `update_plugins` transient.
		 */
		public function modify_transient( $transient ) {

			// Requirements check

				if ( ! isset( $transient->response ) ) {
					return $transient;
				}


			// Processing

				$this->get_repository_info();

				// Requirements check

					if ( ! isset( $this->github_response['zipball_url'] ) ) {
						return $transient;
					}

				// Set checked plugin updates array if it is not defined

					if ( ! isset( $transient->checked ) ) {
						$transient->checked = array(
							$this->basename => $this->plugin['Version'],
						);
					}

				// Compare the plugin update versions

					if ( version_compare( $this->github_response['tag_name'], $this->plugin['Version'], 'gt' ) ) {
						$plugin = array(
							'slug'        => current( explode( '/', $this->basename ) ),
							'plugin'      => $this->basename,
							'new_version' => $this->github_response['tag_name'],
							'url'         => $this->plugin['PluginURI'],
							'package'     => $this->github_response['zipball_url'],
							'tested'      => $GLOBALS['wp_version'],
						);

						$transient->response[ $this->basename ] = (object) $plugin;
					}


			// Output

				return $transient;

		} // /modify_transient



		/**
		 * Set content of plugin update info popup
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 *
		 * @param  false|object|array $result  The result object or array. Default false.
		 * @param  string             $action  The type of information being requested from the Plugin Install API.
		 * @param  object             $args    Plugin API arguments.
		 */
		public function plugin_popup( $result, $action, $args ) {

			// Requirements check

				if (
						! isset( $args->slug )
						|| empty( $args->slug )
						|| current( explode( '/' , $this->basename ) ) !== $args->slug
					) {
					return $result;
				}


			// Processing

				$this->get_repository_info();

				// Requirements check

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
		 * @since    1.0.0
		 * @version  1.0.0
		 *
		 * @param  bool  $response    Install response.
		 * @param  array $hook_extra  Extra arguments passed to hooked filters.
		 * @param  array $result      Installation result data.
		 */
		public function after_install( $response, $hook_extra, $result ) {

			// Helper variables

				global $wp_filesystem;

				$install_directory = plugin_dir_path( $this->file );


			// Processing

				// Move new update files to the plugin directory

					$wp_filesystem->move( $result['destination'], $install_directory );

				// Make sure we do not change our plugin directory name

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





} // /WebMan_Templates_Updater
