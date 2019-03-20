<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/JoshuaMcKendall/Conductor-Plugin/public
 * @since      1.0.0
 *
 * @package    Conductor
 * @subpackage Conductor/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Conductor
 * @subpackage Conductor/public
 * @author     Joshua McKendall <conductor@joshuamckendall.com>
 */
class Conductor_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * Enqueue front-end styles for Conductor
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/conductor-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * Enqueue front-end Javascript for Conductor
		 */
		 
		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/conductor-public.js', array( 'jquery' ), $this->version, false );

	}

	public function conductor_integrate_wc_and_bp_account() {

		//add_action( 'bp_setup_nav', array( $this, 'bp_woo_navigation' ) );
		add_action( 'bp_template_content', array( $this,  'woo_account_content' ) );	

	}

	public function conductor_messages_action_create_message( $posted_data ) {

		if( is_user_logged_in() && current_user_can( 'read' ) ) {

			add_filter( 'wpcf7_verify_nonce', '__return_true' );

			$this->conductor_create_bp_message_thread( $posted_data );

			return $posted_data;

		}

		return $posted_data;

	}

	/**
	 * Create a BuddyPress message thread from Contact Form 7 submission when the user has an account and is signed in
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function conductor_create_bp_message_thread( $posted_data ) {

		if( ! empty( $posted_data['your-email'] ) ) {

			if(	$posted_data['your-email'] == bp_core_get_user_email( get_current_user_id() ) && bp_is_active( 'messages' ) ) {

				if( empty( $posted_data['your-subject'] ) || empty( $posted_data['your-message'] ) ) {

					return false;

				} else {

					check_admin_referer( 'wp_rest' );

					$recipient = get_user_by( 'email', get_option( 'admin_email' ) );

					$recipients = apply_filters( 'conductor_messages_recipients', array( 

						$recipient->ID

					 ) );

					// Attempt to send the message.
					$send = messages_new_message( array(
						'recipients' => $recipients,
						'subject'    => $posted_data['your-subject'],
						'content'    => $posted_data['your-message'],
						'error_type' => 'wp_error'
					) );

				}

			}

		}

	}

	/**
	 * Prevent email notification from BuddyPress after Contact Form 7 submission.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function conductor_prevent_bp_email_notification() {

		remove_action( 'messages_message_sent', 'messages_notification_new_message', 10 );

	}

	/**
	 * Hide admin bar for front-end users
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function hide_admin_bar() {

	    if( ! current_user_can('administrator') && ! is_admin() ) {

	        show_admin_bar(false);

	    }

	}


	/**
	 * Redirect a logged in user who is not an admin to the front-end of the site
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function redirect_from_wp_admin() {

		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

	    if( is_user_logged_in() ) {

	        if( is_admin() && ! current_user_can( 'edit_posts' ) ) {
	            
	            wp_redirect( home_url() );

	            exit;
	        }

	    }

	}

	public function bp_tol_register_template_location() {

		return dirname( __FILE__ ) . '/views/';

	}


	public function add_conductor_template_stack() {

		if( function_exists( 'bp_register_template_stack' ) )
			bp_register_template_stack( array( $this, 'bp_tol_register_template_location' ), 14 );

	}

	public function get_account_url( $redirect_to, $request, $user ) {

		if( isset( $request ) ) {

			$account_page = get_page_by_path( 'account' );
			$account_permalink = get_permalink( $account_page );

			if( $request != $account_permalink ) {

				return $redirect_to = $request;

			}

		}

	    //is there a user to check?
	    if (isset($user->roles) && is_array($user->roles)) {
	        //check for subscribers
	        if (in_array('subscriber', $user->roles)) {

	       		// redirect them to another URL, in this case, the homepage 
	            $redirect_to =  home_url();

	        	if( function_exists('bp_is_active') ) {

	        		$redirect_to = bp_loggedin_user_domain();

	        	}
	        }
	    }

	    return $redirect_to;		

	}


/*
	 * Use the BuddyPress "Account Settings" page (/members/username/settings/) instead of the WooCommerce "Edit Account" page (/my-account/edit-account)
	 * The WooCommerce page doesn't have "Display name publicly as..."
	 */
	public function conductor_account_url( $edit_account_url = "" ) {

		// Determine user to use.
		if ( bp_displayed_user_domain() ) {

			$user_domain = bp_displayed_user_domain();

		} elseif ( bp_loggedin_user_domain() ) {

			$user_domain = bp_loggedin_user_domain();

		} else {

			return;

		}

		if( bp_is_active( 'settings' ) ) {

			$slug = bp_get_settings_slug();

			$account_url = trailingslashit( $user_domain . $slug );

			$account_general_url = trailingslashit( $account_url . $edit_account_url );

			$edit_account_url = is_ssl() ? str_replace( 'http:', 'https:', $account_general_url ) : $account_general_url;

			return apply_filters( 'conductor_edit_account_url', $edit_account_url );

		}
	
	}



	public function rename_settings_to_account() {

		global $bp;

		$bp->members->nav->edit_nav( array( 'name' => __('Account', 'conductor') ), 'account' );

	}

	/*
	 * Add WooCommerce "My Account" to BuddyPress profile
	 * http://xd3v.com/create-a-premium-social-network-with-woocommerce/
	 */
	public function bp_woo_navigation() {	

		// Determine user to use.
		if ( bp_displayed_user_domain() ) {
			$user_domain = bp_displayed_user_domain();
		} elseif ( bp_loggedin_user_domain() ) {
			$user_domain = bp_loggedin_user_domain();
		} else {
			return;
		}
		
		$slug = bp_get_settings_slug();
		$account_url = trailingslashit( $user_domain . $slug );
		$secure_account_url = is_ssl() ? str_replace( 'http:', 'https:', $account_url ) : $account_url;
		$access = bp_core_can_edit_settings();


		/**
		* bp_is_my_profile() line 2313 
		* buddypress/bp-core/bp-core-template
		**/
		if ( bp_is_active('settings') ) {
		
			// bp_core_new_subnav_item(
			// 	array(
			// 		'name' => __( 'Dashboard', 'buddypress' ),
			// 		'slug' => 'view',
			// 		'parent_url' => $secure_account_url,
			// 		'parent_slug' => $slug,
			// 		'screen_function' => array( $this, 'account_screens' ),
			// 		'user_has_access'	=> $access,
			// 		'position' => 10,
			// 		'item_css_id' => 'account-dashboard',
			// 	)
			// );
			bp_core_new_subnav_item(
				array(
					'name' => __( 'Orders', 'buddypress' ),
					'slug' => 'orders',
					'parent_url' => $secure_account_url,
					'parent_slug' => $slug,
					'screen_function' => array( $this, 'account_screens' ),
					'user_has_access'	=> $access,
					'position' => 20,
					'item_css_id' => 'account-orders',
				)
			);
			bp_core_new_subnav_item(
				array(
					'name' => __( 'Downloads', 'buddypress' ),
					'slug' => 'downloads',
					'parent_url' => $secure_account_url,
					'parent_slug' => $slug,
					'screen_function' => array( $this, 'account_screens' ),
					'user_has_access'	=> $access,
					'position' => 30,
					'item_css_id' => 'account-downloads',
				)
			);
			bp_core_new_subnav_item(
				array(
					'name' => __( 'Addresses', 'buddypress' ),
					'slug' => 'edit-address',
					'parent_url' => $secure_account_url,
					'parent_slug' => $slug,
					'screen_function' => array( $this, 'account_screens' ),
					'user_has_access'	=> $access,
					'position' => 40,
					'item_css_id' => 'account-edit-address',
				)
			);
			bp_core_new_subnav_item(
				array(
					'name' => __( 'Payment Methods', 'buddypress' ),
					'slug' => 'payment-methods',
					'parent_url' => $secure_account_url,
					'parent_slug' => $slug,
					'screen_function' => array( $this, 'account_screens' ),
					'user_has_access'	=> $access,
					'position' => 50,
					'item_css_id' => 'account-payment-methods',
				)
			);
			bp_core_new_subnav_item(
				array(
					'name' => __( 'Add Payment Method', 'buddypress' ),
					'slug' => 'add-payment-method',
					'parent_url' => $secure_account_url,
					'parent_slug' => $slug,
					'screen_function' => array( $this, 'account_screens' ),
					'user_has_access'	=> $access,
					'position' => 60,
					'item_css_id' => 'account-add-payment-method',
				)
			);

			/**
			 * Register a screen function, whether or not a related subnav link exists.
			 *
			 * @since 2.4.0
			 * @since 2.6.0 Introduced the `$component` parameter.
			 *
			 * @param array|string $args {
			 *     Array describing the new subnav item.
			 *     @type string   $slug              Unique URL slug for the subnav item.
			 *     @type string   $parent_slug       Slug of the top-level nav item under which the
			 *                                       new subnav item should be added.
			 *     @type string   $parent_url        URL of the parent nav item.
			 *     @type bool     $user_has_access   Optional. True if the logged-in user has access to the
			 *                                       subnav item, otherwise false. Can be set dynamically
			 *                                       when registering the subnav; eg, use bp_is_my_profile()
			 *                                       to restrict access to profile owners only. Default: true.
			 *     @type bool     $site_admin_only   Optional. Whether the nav item should be visible
			 *                                       only to site admins (those with the 'bp_moderate' cap).
			 *                                       Default: false.
			 *     @type int      $position          Optional. Numerical index specifying where the item
			 *                                       should appear in the subnav array. Default: 90.
			 *     @type callable $screen_function   The callback function that will run
			 *                                       when the nav item is clicked.
			 *     @type string   $link              Optional. The URL that the subnav item should point to.
			 *                                       Defaults to a value generated from the $parent_url + $slug.
			 *     @type bool     $show_in_admin_bar Optional. Whether the nav item should be added into
			 *                                       the group's "Edit" Admin Bar menu for group admins.
			 *                                       Default: false.
			 * }
			 * @param string       $component The component the navigation is attached to. Defaults to 'members'.
			 * @return null|false Returns false on failure.
			 */
			bp_core_register_subnav_screen_function(
				array(
					'slug' => 'view-order',
					'parent_slug' => $slug,
					'screen_function' => array( $this, 'account_screens' ),
					'user_has_access'	=> $access,
				)
			);
			// Remove "Settings > Delete Account" 
			//bp_core_remove_subnav_item( 'settings', 'delete-account' );

		}
	}

	public function bp_remove_general_settings_subnav() {

		if( bp_is_active('settings') ) {

			bp_core_remove_subnav_item( bp_get_settings_slug(), 'general' );

		}		

	}

	public function bp_conductor_load_template_filter( $found_template, $templates ) {

		if( ! bp_is_current_component( bp_get_settings_slug() ) && ! bp_current_action( 'dashboard' ) )
			return $found_template;


		if( empty( $found_template ) ) {

			add_action( 'bp_template_content', array( $this, 'get_dashboard_template_part' ) );

		}

		return apply_filters( 'bp_conductor_load_template_filter', $found_template );

	}

	public function template_overload_settings() {

		if( function_exists( 'bp_register_template_stack' ) )
			bp_register_template_stack( array( $this, 'bp_tol_register_template_location' ) );

		if( bp_is_user() )
			add_filter( 'bp_get_template_part', array( $this, 'bp_conductor_maybe_replace_template' ), 10, 3 );

	}

	public function conductor_set_multisite_upload_dir() {

	  if ( ! bp_is_multiblog_mode() ) {

	    return;

	  }

	  $current_site = get_current_site();

	  if ( (int) bp_get_root_blog_id() !== (int) $current_site->blog_id ) {

	    $switched = true;

	    switch_to_blog( $current_site->blog_id );

	  }

	  buddypress()->upload_dir = wp_upload_dir();

	  if ( ! empty( $switched ) ) {

	    restore_current_blog();
	    
	  }	

	}

	public function bp_conductor_maybe_replace_template( $templates, $slug, $name ) {

		if( 'members/single/settings' != $slug )
			return $templates;


		return array( 'members/single/settings-conductor.php' );

	}

	public function conductor_get_back_link() {

		$current_user = bp_get_displayed_user();

		$link = $current_user->domain . bp_get_settings_slug();

		return apply_filters( 'conductor_back_btn_link', $link, $current_user );

	}

	public function conductor_render_back_button( $html ) {

		if( bp_core_can_edit_settings() && bp_is_active('settings') && bp_is_current_component( bp_get_settings_slug() ) && ! bp_is_current_action('dashboard') ) {

			$current_user = bp_get_displayed_user();

			$link = $this->conductor_get_back_link(); 

			echo apply_filters( 'conductor_render_back_button', '<a href="'. esc_url( $link ) .'" id="conductor-back-button" class="btn btn-default">< '.__('Back', 'conductor').'</a>', esc_url( $link ) );

		}

	}

	public function get_dashboard_template_part() {

		if( bp_core_can_edit_settings() && bp_is_active('settings') && bp_is_current_action('dashboard') ) {

			bp_get_template_part( 'members/single/settings/dashboard' );

		}

	}

	public function bp_setup_account() {

		if( bp_is_active('settings') ) {

			// Determine user to use.
			if ( bp_displayed_user_domain() ) {
				$user_domain = bp_displayed_user_domain();
			} elseif ( bp_loggedin_user_domain() ) {
				$user_domain = bp_loggedin_user_domain();
			} else {
				return;
			}
			
			$slug = bp_get_settings_slug();
			$account_url = trailingslashit( $user_domain . $slug );
			$secure_account_url = is_ssl() ? str_replace( 'http:', 'https:', $account_url ) : $account_url;
			$access = bp_core_can_edit_settings();

			bp_core_new_subnav_item(
				array(
					'name' => __( 'Dashboard', 'conductor' ),
					'slug' => 'dashboard',
					'parent_url' => $secure_account_url,
					'parent_slug' => $slug,
					'screen_function' => array( $this, 'dashboard_screens' ), //array( $this, 'dashboard_screens' )
					'position' => 10,
					'user_has_access' => $access,
					'item_css_id' => 'dashboard',
				)
			);

		}

	}

	public function conductor_new_general_settings_subnav() {

		if( bp_is_active('settings') ) {

			// Determine user to use.
			if ( bp_displayed_user_domain() ) {
				$user_domain = bp_displayed_user_domain();
			} elseif ( bp_loggedin_user_domain() ) {
				$user_domain = bp_loggedin_user_domain();
			} else {
				return;
			}

			$slug = bp_get_settings_slug();
			$account_url = trailingslashit( $user_domain . $slug );
			$secure_account_url = is_ssl() ? str_replace( 'http:', 'https:', $account_url ) : $account_url;
			$access = bp_core_can_edit_settings();

			bp_core_new_subnav_item(
				array(
					'name' => __( 'Email & Password', 'conductor' ),
					'slug' => 'general',
					'parent_url' => $secure_account_url,
					'parent_slug' => $slug,
					'screen_function' => 'bp_settings_screen_general', //array( $this, 'dashboard_screens' )
					'position' => 99,
					'user_has_access' => $access,
					'item_css_id' => 'general-settings',
				)
			);

		}

	}

	/**
	 * Filters the "options nav", the secondary-level single item navigation menu.
	 *
	 * This is a dynamic filter that is dependent on the provided css_id value.
	 *
	 * @since 1.1.0
	 *
	 * @param string $value         HTML list item for the submenu item.
	 * @param array  $subnav_item   Submenu array item being displayed.
	 * @param string $selected_item Current action.
	 */
	public function conductor_remove_dashboard_subnav( $value, $subnav_item, $selected_item ) {

		return false;

	}

	public function bp_account_default() {

		if( bp_is_active('settings') ) {

			$access = bp_core_can_edit_settings();

			$slug = bp_get_settings_slug();

			$args = array(
				'parent_slug'		=> $slug,
				'screen_function' 	=> array( $this, 'dashboard_screens' ),
				'subnav_slug'		=> 'dashboard',
				'user_has_access'	=> $access
			);

			bp_core_new_nav_default( $args );

		}

	}

	public function dashboard_screens() {

		if( bp_core_can_edit_settings() ) {

			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

		}
		
	}


	public function account_screens() {

		if ( bp_core_can_edit_settings() ) {

			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

		}

	}


	public function woo_account_content() {

		// print( 'hdfhfghgfhfg' );

		// die;

		if ( is_user_logged_in() && is_wc_endpoint_url() ) {

			do_action( 'bp_before_member_dashboard_template' );

			wc_print_notices();
			do_action( 'woocommerce_account_content' );

		}

	}

	/**
	 * Point WooCommerce endpoints to BuddyPress My Account pages
	 */
	public function get_endpoint_url( $url = '', $endpoint = '', $value = '', $permalink = '' ) {

		if( is_user_logged_in() ) {

			if( bp_is_active( 'settings' ) ) {
	

				$slug = bp_get_settings_slug();
				$user_domain = bp_loggedin_user_domain();
				$account_url = trailingslashit( $user_domain . $slug );
				$endpoint_path = trailingslashit( $account_url . $endpoint );
				$endpoint_value_path = trailingslashit( $endpoint_path . $value );
				$secure_account_url = is_ssl() ? str_replace( 'http:', 'https:', $endpoint_value_path ) : $endpoint_value_path;	

					
				
				switch( $endpoint ) {
					case "orders":
					case "subscriptions":
					case "downloads":
					case "edit-address":
					case "payment-methods":
					case "add-payment-method":
					case "delete-payment-method":
					case "set-default-payment-method":
					case "bookings":
					case "view-order":
						if($value)
							return apply_filters( 'conductor_' . $endpoint . '_url', $endpoint_value_path );
						else
							return apply_filters( 'conductor_' . $endpoint . '_url', $endpoint_path );
						
					case "edit-account":
						return apply_filters( 'conductor_' . $endpoint . '_url', $this->conductor_account_url( 'general' ) );
						
					default:
						return $url;
				}
				
				//	if("/edit-address" == substr( $url, 0, 13 )) {
				//		return "/" . basename( get_permalink( get_option('woocommerce_myaccount_page_id') ) ) . $url;
				//	}
				return apply_filters( 'conductor_' . $endpoint . '_url', $url );

			}

		}

	}


	public function conductor_redirect_to_bp_profile() {

		if( is_user_logged_in() && conductor_buddypress_is_active() ) {


			wp_safe_redirect( bp_loggedin_user_domain() );

			exit;

		}

	}


	/**
	* bp_core_no_access() line 2313 
	* buddypress/bp-core/bp-core-catchuri
	**/
	// public function redirect_to_home( $args ) {


	// 	$args = array( 
	// 		'mode'			=> 1,
	// 		'redirect'		=> $args['redirect'],
	// 		'root'			=> bp_get_root_domain(),
	// 		'message'		=> $args['message']
	// 	 );
	// 	return $args;

	// }


}