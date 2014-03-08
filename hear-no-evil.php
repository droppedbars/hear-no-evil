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

// file path for linking to external files
//require_once( dirname( __FILE__ ) . '/shared-globals.php' );

// function and global prefix: hne / HNE

/* WordPress Hooks */
define( "HNE_WP_PLUGIN_INIT", 'init' );
define( "HNE_WP_PLUGIN_ADMIN_MENU", 'admin_menu' );
define( "HNE_WP_PLUGIN_ADMIN_INIT", 'admin_init' );
define( "HNE_WP_USER_MANAGE_OPTS", 'manage_options' );

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
define( "HNE_FNC_SANITIZE_OPTS", 'hne_sanitize_options' );
define( "HNE_FNC_SETTINGS_PAGE", 'hne_page' );
define( "HNE_FNC_UPDATE_VALUE", 'hne_update_value' );
define( "HNE_FNC_ADMIN_SCRIPTS", 'hne_admin_scripts' );

register_activation_hook( __FILE__, 'hne_install' );
/*
	Called via the install hook.
	Ensure this plugin is compatible with the WordPress version.
	Set the default option values and store them into the database.
*/
function hne_install() {
	// check the install version
	global $wp_version;
	if ( version_compare( $wp_version, '3.7', '<' ) ) {
		wp_die( 'This plugin requires WordPress version 3.7 or higher.' );
	}

	if ( ! get_option( HNE_MARKS ) ) {
		$options_arr = array( 'site_block_comments' => 'false' );
		// update the database with the default option values
		update_option( HNE_MARKS, $options_arr );
	}
}

add_action( HNE_WP_PLUGIN_INIT, 'hne_init' );
/*
	Called via the init hook.
	Register javascript and CSS files.
*/
function hne_init() {
	//wp_register_script( 'hne_settings_handler', plugins_url( 'assets/settingsHandler.js', __FILE__ ) );
}

add_action( HNE_WP_PLUGIN_ADMIN_MENU, 'hne_menu' );
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
	$allSites    = null;
	$options_arr = get_option( HNE_MARKS );

	// create form
	echo '<h1>Hear No Evil Settings</h1>';
	echo '<div class="wrap">';
	echo '	<form method="post" action="options.php">';
	settings_fields( HNE_SETTINGS );


	/*$testState = ""; // TODO: test statement

	if ((is_multisite()) && (!wp_is_large_network())) { // current avoid sites with 10K+ sites, the wp_get_sites() will return empty in that case
		$testState .= "multisite ";
		if (current_user_can("manage_sites")) { // check if super-admin
			//     show apply to all
			$testState .= "super-admin ";
			/* can pass in $args to wp_get_sites() and limit the results based on args
			 * <?php $args = array(
						'network_id' => $wpdb->siteid,
						'public'     => null,
						'archived'   => null,
						'mature'     => null,
						'spam'       => null,
						'deleted'    => null,
						'limit'      => 100,
						'offset'     => 0,
				); ?>
			 */
	/*
				echo '<label><input type="checkbox" name="' . HNE_MARKS . '[master_block]" value="' . true . '" ' . checked( $options_arr['master_block'] ) . '>Globally Block Comments</label>';


				$allSites = wp_get_sites();
				if (!empty($allSites)) {
					/* What $allSites will look like
					 * Array(
						[0] => Array(
					[blog_id] => 1
					[site_id] => 1
					[domain] => example.com
					[path] => /sub-site
					[registered] => 2013-11-08 17:56:46
					[last_updated] => 2013-11-08 18:57:19
					[public] => 1
					[archived] => 0
					[mature] => 0
					[spam] => 0
					[deleted] => 0
					[lang_id] => 0
			)
					 */
	/*
					//     show all site
					foreach ($allSites as $site) {
						// TODO: if the array is empty, or the site setting doesn't exist, set it to false
						echo '<label><input type="checkbox" name="' . HNE_MARKS . '['.$site['site_id'].'_block]" value="' . true . '" ' . checked( $options_arr[$site['site_id'].'_block'] ) . '>'.$site['domain'].$site['path'].'</label>';
					}
					$testState .= "has sites ";
				} else {
					$testState .= "has no sites ";
					echo 'should never land here';
				}
			} else if (current_user_can("activate_plugins")) { // check if admin
			//     enable/disable for site
				$site = get_current_site();

				// TODO: if the array is empty, or the site setting doesn't exist, set it to false
				echo '<label><input type="checkbox" name="' . HNE_MARKS . '['.$site['site_id'].'_block]" value="' . true . '" ' . checked( $options_arr[$site['site_id'].'_block'] ) . '>'.$site['domain'].$site['path'].'</label>';
				$testState .= "admin ";
			} else {
			//     not allowed here
				echo 'should never land here';
				$testState .= "shouldn't happen! ";
			}
		}
		else {
			$testState .= "single site";
			if (current_user_can("activate_plugins")) { // check if admin
			//     enable/disable for site
				$site = get_current_site();

				// TODO: if the array is empty, or the site setting doesn't exist, set it to false
				echo '<label><input type="checkbox" name="' . HNE_MARKS . '['.$site['site_id'].'_block]" value="' . true . '" ' . checked( $options_arr[$site['site_id'].'_block'] ) . '>'.$site['domain'].$site['path'].'</label>';
				$testState .= "admin ";
			} else {
			//     not allowed here
				echo 'should never land here';
				$testState .= "shouldn't happen! ";
			}
		}

		echo $testState; //TODO test statement */
	$block_comments = false;

	if ( ( ! is_null( $options_arr ) ) && ( is_array( $options_arr ) ) ) {
		if (array_key_exists('site_block_comments', $options_arr)) {
			$block_comments = is_bool($options_arr['site_block_comments'])? $options_arr['site_block_comments'] : false;
		}
	}

	echo '<label><input type="checkbox" name="' . HNE_MARKS . '[site_block_comments]" value="' . true . '" ' . checked( $block_comments, true, false ) . '>Block comments</label><br>';

	echo '		<input type="submit" class="button-primary" value="';
	_e( 'Save Changes', HNE_PLUGIN_TAG );
	echo '" />';
	echo '	</form>';
	echo '</div>';
}

add_action( HNE_WP_PLUGIN_ADMIN_INIT, 'hne_register_settings' );
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

		if ( isset( $options['site_block_comments'] ) ) {
			$sanitized_options['site_block_comments'] = true;
		} else {
			$sanitized_options['site_block_comments'] = false;
		}

		return $sanitized_options;
	} else {
		return null;
	}
}

//check here for hooks:http://codex.wordpress.org/Function_Reference/comment_form
// need to read wordpress code probably
//  looks like want the filter comment_form_default_fields

/*
 * 		global $current_site;
			if($current_site->id == 1) {
   		echo $current_site->path;
}
 */

// TODO: handle these based on settings

// removes the links for adding comments
add_filter( 'comments_open', 'my_comments_open', 10, 2 );

function my_comments_open( $open, $post_id ) {

	$options_arr = get_option( HNE_MARKS );

	if ( ( ! is_null( $options_arr ) ) && ( is_array( $options_arr ) ) ) {
		if (array_key_exists('site_block_comments', $options_arr)) {
			if ($options_arr['site_block_comments']) {
				return null;
			}
		}
	}
	return $open;
}

// remove the ability to edit existing comments
// if existing one should still be shown

// remove the ability to view existing comments
add_filter( 'comments_array', 'my_comments_array', 10, 2 ); // should it be 2 args?

function my_comments_array( $comments, $post_id ) {
	$options_arr = get_option( HNE_MARKS );

	if ( ( ! is_null( $options_arr ) ) && ( is_array( $options_arr ) ) ) {
		if (array_key_exists('site_block_comments', $options_arr)) {
			if ($options_arr['site_block_comments']) {
				return null;
			}
		}
	}
	return $comments;
}

// remove ability to view comment widget
add_action( 'widgets_init', 'custom_recent_comments' );
function custom_recent_comments() {
	add_filter( 'comments_clauses', 'custom_comments_clauses' );
}

function custom_comments_clauses( $clauses ) {
	$options_arr = get_option( HNE_MARKS );

	if ( ( ! is_null( $options_arr ) ) && ( is_array( $options_arr ) ) ) {
		if (array_key_exists('site_block_comments', $options_arr)) {
			if ($options_arr['site_block_comments']) {
				$clauses['limits'] = "LIMIT 0"; // change the SQL query to get 0 of them
			}
		}
	}
	return $clauses;
}