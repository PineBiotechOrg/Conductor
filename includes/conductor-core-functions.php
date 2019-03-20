<?php


function conductor_buddypress_is_active() {

	if ( class_exists( 'BuddyPress' ) ) { return true; } else { return false; }

}


function conductor_woocommerce_is_active() {

    if (!defined('WC_VERSION')) {

    	return false;
        
    } else {

    	return true;

        // var_dump("WooCommerce installed in version", WC_VERSION);

    }

}
add_action('plugins_loaded', 'conductor_woocommerce_is_active');

function conductor_bbpress_is_active() {

	if ( class_exists( 'bbpress' ) ) {

		return true; 

	} else { 

		return false;

	}

}


/**
 * Override WooCommerce default "is_add_payment_method_page" method so that it returns true if we're on the BuddyPress equivalent
 */
if( ! function_exists('is_add_payment_method_page') ) {
    function is_add_payment_method_page() {
        global $wp;
        
        if( isset( $wp->query_vars['add-payment-method'] ) ) 
            return true;
        return ( is_page( wc_get_page_id( 'myaccount' ) ) && isset( $wp->query_vars['add-payment-method'] ) );
    }
}


function conductor_loggedin_user_avatar( $args = '' ) {
	global $bp;

	if ( conductor_buddypress_is_active() ) {

		$r = wp_parse_args( $args, array(
			'item_id' => bp_loggedin_user_id(),
			'type'    => 'thumb',
			'width'   => false,
			'height'  => false,
			'html'    => true,
			'alt'     => sprintf( __( 'Profile picture of %s', 'buddypress' ), bp_get_loggedin_user_fullname() )
		) );

		/**
		 * Filters the logged in user's avatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value User avatar string.
		 * @param array  $r     Array of parsed arguments.
		 * @param array  $args  Array of initial arguments.
		 */
		return apply_filters( 'bp_get_loggedin_user_avatar', bp_core_fetch_avatar( $r ), $r, $args );

	} else {

		get_avatar(  );

	}

}

function conductor_get_signup_allowed() {

}

function conductor_get_signup_page() {

}

function conductor_get_login_page() {

}

function conductor_form_field_attributes($attr) {

}


function conductor_user_meta_loggedout_links() {

	$links = '';


	$log_in_icn = canvas_get_svg_icon( array(

		'icon'		=> 'log-in',
		'size'		=> 'sm'

	) );

	if( get_option( 'users_can_register' ) ) {

	}

	$user_plus_icn = canvas_get_svg_icon( array(

		'icon'		=> 'user-plus',
		'size'		=> 'sm'

	) );

	$log_in_link = '<a href="'. esc_url( wp_login_url( get_permalink() ) ) .'" class="link link-secondary" ><span class="icon icon-left">'.$log_in_icn.'</span>'. __('Log In', 'canvas') .'</a>';

	$register_link = '<a href="'. esc_url( wp_registration_url( get_permalink() ) ) .'" class="link link-secondary" ><span class="icon icon-left">'.$user_plus_icn.'</span>'. __('Register', 'canvas') .'</a>';

	$links .= '<span class="user-dropdown-item">'.$log_in_link.'</span>';

	$links .= '<span class="user-dropdown-item">'.$register_link.'</span>';


	return apply_filters( 'conductor_loggedout_links', $links );

}