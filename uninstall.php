<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-01-11
 * Time: 7:46 AM
 */

// if uninstall/delete is not called from WordPress, then exit
if ( ! defined( 'ABSPATH' ) && ! defined( ! 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete option from options table
delete_site_option( 'hne_options' );

// At this point, one would delete any other option, or custom tables and files
