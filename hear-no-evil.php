<?php
/*
Plugin Name: Hear No Evil
Plugin URI: http://github.com/droppedbars/Hear-No-Evil
Description: Hear No Evil is designed for Multisite right from the get go.  Each site administrator will have the ability to block or unblock the ability to comment, and comments themselves from their site's settings menu.
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
		$site_options_arr = array( 'site_block_commenting' => false, 'site_block_show_comments' => false );
		// update the database with the default option values
		update_site_option( HNE_MARKS, $site_options_arr );
	}

}

add_action( HNE_WP_PLUGIN_INIT, 'hne_init' );
/*
	Called via the init hook.
	Register javascript and CSS files.
*/
function hne_init() {
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
}

/*

*/
function hne_page() {
	if ( ! current_user_can( HNE_WP_USER_MANAGE_OPTS ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	if ( isset( $_POST['my_submit'] ) ) {
		if ( ! isset( $_POST[HNE_MARKS]['site_block_commenting'] ) ) {
			$_POST[HNE_MARKS]['site_block_commenting'] = false;
		} else {
			$_POST[HNE_MARKS]['site_block_commenting'] = true;
		}
		if ( ! isset( $_POST[HNE_MARKS]['site_block_show_comments'] ) ) {
			$_POST[HNE_MARKS]['site_block_show_comments'] = false;
		} else {
			$_POST[HNE_MARKS]['site_block_show_comments'] = true;
		}

		$site_options_arr    = Array();

		foreach ( (array) $_POST[HNE_MARKS] as $key => $value ) {
			// TODO Sanitize option saving
				$site_options_arr[$key] = (bool) $value;
		}

		update_site_option( HNE_MARKS, $site_options_arr );

	}
	$options_arr        = get_site_option( HNE_MARKS );

// create form
	echo '<h1>Hear No Evil Settings</h1>';
	echo '<div class="wrap">';
	// TODO fill out text
	echo 'content!!!';

	if ( isset( $_POST['my_submit'] ) ) {
		echo '<div id="message" class="updated fade">';
		echo '	<p>';
		_e( 'Settings Saved', 'my' );
		echo '	</p>';
		echo '</div>';
	}

	echo '	<form method="post" action="">';
	settings_fields( HNE_SETTINGS );

	$block_commenting           = false;
	$block_show_comments        = false;

	if ( ( ! is_null( $options_arr ) ) && ( is_array( $options_arr ) ) ) {
		if ( array_key_exists( 'site_block_commenting', $options_arr ) ) {
			$block_commenting = is_bool( $options_arr['site_block_commenting'] ) ? $options_arr['site_block_commenting'] : false;
		}
		if ( array_key_exists( 'site_block_show_comments', $options_arr ) ) {
			$block_show_comments = is_bool( $options_arr['site_block_show_comments'] ) ? $options_arr['site_block_show_comments'] : false;
		}
	}

	$disable_block_commenting    = false; // future global support: ( $global_block_commenting && is_multisite() ) ? 'disabled' : '';
	$disable_block_show_comments = false; // future global support: ( $global_block_show_comments && is_multisite() ) ? 'disabled' : '';

	echo '<label><input type="checkbox" name="' . HNE_MARKS . '[site_block_commenting]" value="' . true . '" ' . checked( $block_commenting, true, false ) . ' ' . $disable_block_commenting . '>Block commenting</label><br>';

	echo '<label><input type="checkbox" name="' . HNE_MARKS . '[site_block_show_comments]" value="' . true . '" ' . checked( $block_show_comments, true, false ) . ' ' . $disable_block_show_comments . '>Block showing comments</label><br>';

	echo '		<input name="my_submit" type="submit" class="button-primary" value="';
	_e( 'Save Changes', HNE_PLUGIN_TAG );
	echo '" />';
	echo '	</form>';
	echo '</div>';
}

/*
 * Sanitize the options set in the options page;
 * It copies the expected options into a second hash so as to remove any unexpected values
 * All options at this time are text, so just sanitizes the them as text fields.
 */
function hne_sanitize_options( $options ) {
	if ( ! is_null( $options ) ) {
		$sanitized_options = array();

		if ( ( is_multisite() && current_user_can( "manage_sites" ) ) || ( ! is_multisite() ) ) {
			if ( isset( $options['site_block_commenting'] ) ) {
				$sanitized_options['site_block_commenting'] = true;
			} else {
				$sanitized_options['site_block_commenting'] = false;
			}

			if ( isset( $options['site_block_show_comments'] ) ) {
				$sanitized_options['site_block_show_comments'] = true;
			} else {
				$sanitized_options['site_block_show_comments'] = false;
			}
		}

		return $sanitized_options;
	}
	return null;
}

// removes the links for adding comments
add_filter( 'comments_open', 'my_comments_open', 10, 2 );

/*
 *
 */
function my_comments_open( $open, $post_id ) {

	$options_arr = get_site_option( HNE_MARKS );

	if ( ( ! is_null( $options_arr ) ) && ( is_array( $options_arr ) ) ) {
		if ( array_key_exists( 'site_block_commenting', $options_arr ) ) {
			if ( $options_arr['site_block_commenting'] ) {
				return null;
			}
		}
	}

	return $open;
}

// remove the ability to view existing comments
add_filter( 'comments_array', 'my_comments_array', 10, 2 ); // should it be 2 args?

/*
 *
 */
function my_comments_array( $comments, $post_id ) {
	$options_arr = get_site_option( HNE_MARKS );

	if ( ( ! is_null( $options_arr ) ) && ( is_array( $options_arr ) ) ) {
		if ( array_key_exists( 'site_block_show_comments', $options_arr ) ) {
			if ( $options_arr['site_block_show_comments'] ) {
				return null;
			}
		}
	}

	return $comments;
}

// remove ability to view comment widget
add_action( 'widgets_init', 'custom_recent_comments' );

/*
 *
 */
function custom_recent_comments() {
	add_filter( 'comments_clauses', 'custom_comments_clauses' );
}

/*
 *
 */
function custom_comments_clauses( $clauses ) {
	$options_arr = get_site_option( HNE_MARKS );

	if ( ( ! is_null( $options_arr ) ) && ( is_array( $options_arr ) ) ) {
		if ( array_key_exists( 'site_block_show_comments', $options_arr ) ) {
			if ( $options_arr['site_block_show_comments'] ) {
				$clauses['limits'] = "LIMIT 0"; // change the SQL query to get 0 of them
			}
		}
	}

	return $clauses;
}