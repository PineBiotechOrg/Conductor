<?php
/**
 * BuddyPress - Users Settings
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
	
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

switch ( bp_current_action() ) :
	case 'notifications'  :
		bp_get_template_part( 'members/single/settings/notifications'  );
		break;
	case 'capabilities'   :
		bp_get_template_part( 'members/single/settings/capabilities'   );
		break;
	case 'delete-account' :
		bp_get_template_part( 'members/single/settings/delete-account' );
		break;
	case 'general'        :
		bp_get_template_part( 'members/single/settings/general'        );
		break;
	case 'profile'        :
		bp_get_template_part( 'members/single/settings/profile'        );
		break;
	default:
		bp_get_template_part( 'members/single/plugins'                 );
		break;
endswitch;
