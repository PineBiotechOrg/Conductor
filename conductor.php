<?php
/**
 * @link              https://joshuamckendall.github.io/conductor
 * @since             1.0.0
 * @package           Conductor
 *
 * @wordpress-plugin
 * Plugin Name:       Conductor
 * Plugin URI:        https://joshuamckendall.github.io/conductor
 * Description:       Conductor serves as an abstraction plugin for unifying BuddyPress, bbPress, and WooCommerce.
 * Version:           1.0.0
 * Author:            Joshua McKendall
 * Author URI:        https://joshuamckendall.github.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       conductor
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function activate_conductor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-conductor-activator.php';
	$conductor = new Conductor_Activator;
	$conductor::activate($conductor);
}

function deactivate_conductor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-conductor-deactivator.php';
	Conductor_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_conductor' );
register_deactivation_hook( __FILE__, 'deactivate_conductor' );

require plugin_dir_path( __FILE__ ) . 'includes/class-conductor.php';

/**
 * Begin execution of the plugin.
 *
 * @since    1.0.0
 */
function run_conductor() {

	$plugin = new Conductor();
	$plugin->run();

}

run_conductor();