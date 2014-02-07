<?php
/*
 * Plugin Name: Gravity Forms for Zurb Foundation 5
 * Plugin URI: https://github.com/pressoholics/prso-gravityforms-foundation-5
 * Description: Overrides Gravity Forms style/structure to use Zurb Foundation 5 Framework 
 * Author: Benjamin Moody
 * Version: 1.1
 * Author URI: http://www.benjaminmoody.com
 * License: GPL v3
 * Text Domain: prso-gravityforms-foundation-5-plugin
 * Domain Path: /languages/
 
	Gravity Forms for Zurb Foundation 5
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
 */
 
/**
 * @package Main
 */

//Define plugin constants
define( 'PRSOGFORMSFOUND5__MINIMUM_WP_VERSION', '3.0' );
define( 'PRSOGFORMSFOUND5__VERSION', '1.0' );
define( 'PRSOGFORMSFOUND5__DOMAIN', 'prso-gravityforms-foundation-5-plugin' );

//Plugin admin options will be available in global var with this name, also is database slug for options
define( 'PRSOGFORMSFOUND5__OPTIONS_NAME', 'prso-gravityforms-foundation-5-options' ); 

define( 'PRSOGFORMSFOUND5__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRSOGFORMSFOUND5__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

//Include plugin classes
require_once( PRSOGFORMSFOUND5__PLUGIN_DIR . 'class.prso-gravityforms-foundation-5.php'               );

//Set Activation/Deactivation hooks
register_activation_hook( __FILE__, array( 'PrsoGravityformsFoundation5', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'PrsoGravityformsFoundation5', 'plugin_deactivation' ) );

//Set plugin config
$config_options = array();

//Instatiate plugin class and pass config options array
new PrsoGravityformsFoundation5( $config_options );