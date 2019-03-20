<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/JoshuaMcKendall/Conductor-Plugin/includes/
 * @since      1.0.0
 *
 * @package    Conductor
 * @subpackage Conductor/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Conductor
 * @subpackage Conductor/includes
 * @author     Joshua McKendall <conductor@joshuamckendall.com>
 */
class Conductor_Activator {

	/**
	 *
	 *
	 * @since    1.0.0
	 */
	public static function activate(Conductor_Activator $conductor) {
		
		$conductor->create_options();
		
	}
	
	private function create_options() {
	

	}
	

}