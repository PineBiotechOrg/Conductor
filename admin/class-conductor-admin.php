<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/JoshuaMcKendall/Conductor-Plugin/admin/
 * @since      1.0.0
 *
 * @package    Conductor
 * @subpackage Conductor/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Conductor
 * @subpackage Conductor/admin
 * @author     Joshua McKendall <mail@joshuamckendall.com>
 */
class Conductor_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	/**
	 * Render admin views.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @param     string    $view       The view to render from admin/views.
	 */
	private function render( $view ) {


	}

	/**
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {


	}

	/**
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {


	}

	public function conductor_disable_bp_registration_page() {

	  remove_action( 'bp_init',    'bp_core_wpsignup_redirect' );
	  remove_action( 'bp_screens', 'bp_core_screen_signup' );

	}
}