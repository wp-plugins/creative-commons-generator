<?php
/*
Plugin Name: Creative Commons Generator
Plugin URI: http://wordpress.org/extend/plugins/creative-commons-generator/
Description: A Creative Commons banner for Wordpress!. 
Version: 1.4
Author: OptimalDevs
Author URI: https://profiles.wordpress.org/optimaldevs/
*/
load_plugin_textdomain( 'ccg-domain', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
require_once( 'ccg-admin.php' );
require_once( 'ccg-post-options.php' );
require_once( 'ccg-frontend.php' );
?>