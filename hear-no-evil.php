<?php
/*
Plugin Name: Hear No Evil
Plugin URI: http://github.com/droppedbars/Hear-No-Evil
Description: description
Version: 0.1.0
Author: Patrick Mauro
Author URI: http://patrick.mauro.ca
License: GPLv2
*/

/*	Copyright 2014 Patrick Mauro (email : patrick@mauro.ca)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should receive a copy of the GNU General Public License
	along with this program; if not, write to the Free Software 
	Foundation, Inc., 51 Franklin St., Fifth Floor, Boston, MA 02110-1301 USA
*/

//require_once( dirname( __FILE__ ) . '/shared-globals.php' );

// function and global prefix: hne / HNE

/* WordPress Hooks */
define( "HNE_WP_PLUGIN_INIT", 'init' );
define( "HNE_WP_PLUGIN_ADMIN_MENU", 'admin_menu' );
define( "HNE_WP_PLUGIN_ADMIN_INIT", 'admin_init' );
define( "HNE_WP_PLUGIN_PUBLISH_POST", 'publish_post' );
define( "HNE_WP_THE_POST", 'the_post' );
define( "HNE_WP_USER_MANAGE_OPTS", 'manage_options' );
define( "HNE_WP_THE_CONTENT", 'the_content' );
define( "HNE_WP_THE_EXCERPT", 'the_excerpt' );
define( "HNE_WP_THE_TITLE", 'the_title' );

/* Plugin Variables and Attributes */
define( "HNE_PLUGIN_TAG", 'brand_marker' );
define( "HNE_MARKS", 'hne_options' );
define( "HNE_DB_VERS", 'hne_db_version' );
define( "HNE_SETTINGS", 'hne-settings-group' );
define( "HNE_SETTINGS_PAGE_NAME", 'Hear No Evil' );
define( "HNE_SETTINGS_NAME", 'Hear No Evil' );
define( "HNE_SETTINGS_PAGE_URL", 'hear-no-evil' );

define( "HNE_DB_VERSION", '1.0' );

/* Function Names */
define( "HNE_FNC_INSTALL",       'hne_install' );
define( "HNE_FNC_INIT",          'hne_init' );
define( "HNE_FNC_ADMIN_MENU",    'hne_menu' );
define( "HNE_FNC_REG_SETTINGS",  'hne_register_settings' );
define( "HNE_FNC_SANITIZE_OPTS", 'hne_sanitize_options' );
define( "HNE_FNC_SETTINGS_PAGE", 'hne_page' );
define( "HNE_FNC_UPDATE_VALUE",  'hne_update_value' );
define( "HNE_FNC_ADMIN_SCRIPTS", 'hne_admin_scripts' );

/* Associate WordPress hooks with functions */
register_activation_hook( __FILE__,   HNE_FNC_INSTALL );
add_action( HNE_WP_PLUGIN_INIT,       HNE_FNC_INIT );
add_action( HNE_WP_PLUGIN_ADMIN_MENU, HNE_FNC_ADMIN_MENU );
add_action( HNE_WP_PLUGIN_ADMIN_INIT, HNE_FNC_REG_SETTINGS );
//add_filter( HNE_WP_THE_CONTENT,       HNE_FNC_UPDATE_VALUE );
//add_filter( HNE_WP_THE_EXCERPT,       HNE_FNC_UPDATE_VALUE );
//add_filter( HNE_WP_THE_TITLE,         HNE_FNC_UPDATE_VALUE );

/*
	Called via the install hook.
	Ensure this plugin is compatible with the WordPress version.
	Set the default option values and store them into the database.
*/
function hne_install() {
	// check the install version
	global $wp_version;
	if ( version_compare( $wp_version, '3.5', '<' ) ) {
//		wp_die( 'This plugin requires WordPress version 3.5 or higher.' );
	}

	if ( ! get_option( HNE_MARKS ) ) {
		$options_arr = array( 'key' => 'value' );
		// update the database with the default option values
		update_option( HNE_MARKS, $options_arr );
	}
}

/*
	Called via the init hook.
	Register javascript and CSS files.
*/
function hne_init() {
	//wp_register_script( 'hne_settings_handler', plugins_url( 'assets/settingsHandler.js', __FILE__ ) );
}

/*
	Called via the admin menu hook.
	Define and create the sub-menu item for the plugin under options menu
*/
function hne_menu() {
	$page_hook_suffix = add_options_page( __( HNE_SETTINGS_PAGE_NAME, HNE_PLUGIN_TAG ),
			__( HNE_SETTINGS_NAME, HNE_PLUGIN_TAG ),
			HNE_WP_USER_MANAGE_OPTS, HNE_SETTINGS_PAGE_URL, HNE_FNC_SETTINGS_PAGE );

	/*
   * Use the retrieved $page_hook_suffix to hook the function that links our script.
   * This hook invokes the function only on our plugin administration screen,
   * see: http://codex.wordpress.org/Administration_Menus#Page_Hook_Suffix
   */
	//add_action( 'admin_print_scripts-' . $page_hook_suffix, HNE_FNC_ADMIN_SCRIPTS );
}

/*
 * Load any already registered CSS or Javascript files
 */
function hne_admin_scripts() {
	/* Link our already registered script to a page */
	//wp_enqueue_script( 'hne_settings_handler' );
}

/*
	Called via the appropriate sub-menu hook.
	Create the settings page for the plugin
		Escapes the branding since it goes into a text field.
		No escaping is done on the trademark since it is only compared and not printed.
*/
function hne_page() {
	if ( ! current_user_can( HNE_WP_USER_MANAGE_OPTS ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}


	// load options


	// create form
	echo '<h1>Hear No Evil Settings</h1>';
	echo '<div class="wrap">';
	echo '	<form method="post" action="options.php">';
	settings_fields( HNE_SETTINGS );

	echo '		<input type="submit" class="button-primary" value="';
	_e( 'Save Changes', HNE_PLUGIN_TAG );
	echo '" />';
	echo '	</form>';
	echo '</div>';
}

/*
	Store the settings after the user has submitted the settings form.
*/
function hne_register_settings() {
	// register settings
	register_setting( HNE_SETTINGS, HNE_MARKS, HNE_FNC_SANITIZE_OPTS );
}

/*
 * Sanitize the options set in the options page;
 * It copies the expected options into a second hash so as to remove any unexpected values
 * All options at this time are text, so just sanitizes the them as text fields.
 */
function hne_sanitize_options( $options ) {
	if ( ! is_null( $options ) ) {
		$sanitized_options = array();
		$iterator          = 0;
		foreach ( $options as $key => $value ) {

		}

		return $sanitized_options;
	} else {
		return null;
	}
}

/*
 * Used as a hook for content, excerpt and title.  Update the content with the appropriate brand markings
 */
function hne_update_value( $value ) {
	$options_arr = get_option( HNE_MARKS );
	// set options to variables
	// do something
	return $value;
}
