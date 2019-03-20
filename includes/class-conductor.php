<?php

/**
 * @link       https://github.com/JoshuaMcKendall/Conductor-Plugin
 * @since      1.0.0
 *
 * @package    Conductor
 * @subpackage Conductor/includes
 */

/**
 * The core conductor class.
 *
 * @since      1.0.0
 * @package    Conductor
 * @subpackage Conductor/includes
 * @author     Joshua McKendall <mail@joshuamckendall.com>
 */

class Conductor {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Conductor_Loader    $loader    Maintains and registers all hooks for the plugin.
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


	public function __construct() {

		$this->plugin_name = __('Conductor', 'conductor');
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/conductor-core-definitions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/conductor-core-functions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-conductor-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-conductor-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-conductor-user-meta-widget.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-conductor-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-conductor-public.php';

		$this->loader = new Conductor_Loader();

	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Conductor_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Conductor_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		//$this->loader->add_action( 'bp_loaded', $plugin_admin, 'conductor_disable_bp_registration_page' );


		//$this->loader->add_filter( 'register_url', $plugin_admin, 'conductor_get_registration_page', 1 ); // CHANGE THIS

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Conductor_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'redirect_from_wp_admin' );
		$this->loader->add_action( 'set_current_user', $plugin_public, 'hide_admin_bar' );
		$this->loader->add_action( 'bp_init', $plugin_public, 'add_conductor_template_stack' );
		$this->loader->add_action( 'bp_init', $plugin_public, 'template_overload_settings' );
		$this->loader->add_action( 'bp_init', $plugin_public, 'conductor_set_multisite_upload_dir', 1 );
		$this->loader->add_action( 'bp_actions', $plugin_public, 'rename_settings_to_account' );
		$this->loader->add_action( 'bp_actions', $plugin_public, 'bp_remove_general_settings_subnav', 20 );
		$this->loader->add_action( 'bp_actions', $plugin_public, 'conductor_new_general_settings_subnav', 25 );
		$this->loader->add_action( 'bp_actions', $plugin_public, 'conductor_messages_action_create_message' );
		$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'bp_setup_account', 5 );
		$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'bp_account_default', 20 );
		//$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'bp_woo_navigation', 25 );
		$this->loader->add_action( 'bp_before_member_settings_template', $plugin_public, 'conductor_render_back_button' );
		$this->loader->add_action( 'bp_loaded', $plugin_public, 'conductor_account_url', 5, 1 ); 
		$this->loader->add_action( 'bp_loaded', $plugin_public, 'get_endpoint_url', 5, 4 );
		$this->loader->add_action( 'woocommerce_loaded', $plugin_public, 'conductor_integrate_wc_and_bp_account' );
		$this->loader->add_action( 'bp_before_member_plugin_template', $plugin_public, 'conductor_render_back_button' );

		$this->loader->add_filter( 'bp_located_template', $plugin_public, 'bp_conductor_load_template_filter', 10, 2 );
		$this->loader->add_filter( 'bp_get_options_nav_dashboard', $plugin_public, 'conductor_remove_dashboard_subnav', 10, 3 );
		$this->loader->add_filter( 'wpcf7_posted_data', $plugin_public, 'conductor_messages_action_create_message', 10, 1);
		$this->loader->add_filter( 'woocommerce_customer_edit_account_url', $plugin_public, 'conductor_account_url', 10, 4 );
		$this->loader->add_filter( 'woocommerce_get_endpoint_url', $plugin_public, 'get_endpoint_url', 10, 4 );
		//$this->loader->add_filter( 'login_redirect', $plugin_public, 'get_account_url', 10, 3 );
		//$this->loader->add_filter( 'bp_core_no_access', $plugin_public, 'redirect_to_home', 10, 1 );

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
	 * @return    Conductor_Loader    Orchestrates the hooks of the plugin.
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