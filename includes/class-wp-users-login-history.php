<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://chetanvaghela.cf/
 * @since      1.0.0
 *
 * @package    Wp_Users_Login_History
 * @subpackage Wp_Users_Login_History/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Users_Login_History
 * @subpackage Wp_Users_Login_History/includes
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class Wp_Users_Login_History {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Users_Login_History_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_USERS_LOGIN_HISTORY_VERSION' ) ) {
			$this->version = WP_USERS_LOGIN_HISTORY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-users-login-history';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Users_Login_History_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Users_Login_History_i18n. Defines internationalization functionality.
	 * - Wp_Users_Login_History_Admin. Defines all hooks for the admin area.
	 * - Wp_Users_Login_History_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-users-login-history-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-users-login-history-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-users-login-history-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-users-login-history-public.php';

		$this->loader = new Wp_Users_Login_History_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Users_Login_History_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Users_Login_History_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Users_Login_History_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		# add html in admin footer of users.php and profile page only
		$this->loader->add_action( 'admin_footer-users.php', $plugin_admin,'wpulh_admin_footer_user');
		$this->loader->add_action( 'admin_footer-profile.php', $plugin_admin,'wpulh_admin_footer_user');

		# add column in user listing page
		$this->loader->add_filter( 'manage_site-users-network_columns',$plugin_admin,'wpulh_add_column', 1 );
		$this->loader->add_filter( 'manage_users_columns',  $plugin_admin,'wpulh_add_column', 1 );
		$this->loader->add_filter( 'wpmu_users_columns', $plugin_admin,'wpulh_add_column', 1 );

		# add data in user listing page column
		$this->loader->add_action( 'manage_users_custom_column', $plugin_admin,'wpulh_add_data_in_column', 10, 3);

		# when user login update data
		$this->loader->add_action( 'wp_login',$plugin_admin,'wpulh_wp_login');

		# add Section in user profile page
		$this->loader->add_action( 'edit_user_profile',$plugin_admin,'wpulh_add_extra_user_fields');
		$this->loader->add_action( 'show_user_profile',$plugin_admin,'wpulh_add_extra_user_fields');

		# add ajax handle request actions
		$this->loader->add_action( 'wp_ajax_get_wp_users_login_history', $plugin_admin, 'get_wp_users_login_history');
		$this->loader->add_action( 'wp_ajax_nopriv_get_wp_users_login_history', $plugin_admin, 'get_wp_users_login_history');
		$this->loader->add_action( 'wp_ajax_clear_wp_users_login_history', $plugin_admin, 'clear_wp_users_login_history');
		$this->loader->add_action( 'wp_ajax_nopriv_clear_wp_users_login_history', $plugin_admin, 'clear_wp_users_login_history');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Users_Login_History_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		# when user login update data
		//$this->loader->add_action( 'wp_login',$plugin_public,'wpulh_wp_login');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Users_Login_History_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
