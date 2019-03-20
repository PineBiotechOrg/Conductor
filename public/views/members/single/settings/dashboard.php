<?php
/**
 * BuddyPress - Members Single Account Dashboard
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'bp_before_member_dashboard_template' ); ?>

<h2><?php
	/* translators: accessibility text */
	_e( 'Your Account', 'conductor' );
?></h2>

<div class="row">
	<div class="column col-xs-12">

			<?php 

				do_action( 'woocommerce_account_content' );

			 ?>

			<br>

			<ul class="list-group list-table">
				<?php if ( bp_core_can_edit_settings() ) : ?>

					<?php bp_get_options_nav(); ?>

				<?php endif; ?>
			</ul>
		
	</div>
</div>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_after_member_settings_template' );
