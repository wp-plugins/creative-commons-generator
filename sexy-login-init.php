<?php
/*
Plugin Name: Sexy Login
Plugin URI: http://wordpress.org/extend/plugins/sexy-login/
Description: The sexiest widget login for Wordpress!
Version: 1.0
Author: OptimalDevs
Author URI: http://optimaldevs.com/
*/

require_once('sexy-login-widget.php');
require_once('sexy-login-functions.php');

function sexy_login_init() {
	register_widget( 'Sexy_Login_Widget' );
	add_action( 'wp_ajax_sexy_login_hook', 'sexy_login_ajax' );
	add_action( 'wp_ajax_nopriv_sexy_login_hook', 'sexy_login_ajax' );
	
	if(is_active_widget( false, false, 'sexy_login_widget', true) && !is_admin()){
		$ssl_plugins_url = (is_ssl()) ? str_replace('http://','https://', WP_PLUGIN_URL . '/sexy-login/') : WP_PLUGIN_URL . '/sexy-login/';
		
        wp_register_script('sexy-login-blockui',  $ssl_plugins_url . 'js/jquery.blockUI.js', array('jquery'), '2.53' );
		wp_register_script('sexy-login-ajax-id', $ssl_plugins_url . 'js/sexy-login-ajax.js', array( 'jquery' ), '1.0' );
		wp_register_style( 'sexy-login-style', $ssl_plugins_url . 'sexy-login-style.css', array(), '1.0', 'all' );
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'sexy-login-blockui' );
		wp_enqueue_script( 'sexy-login-ajax-id' );
		wp_enqueue_style( 'sexy-login-style' );
		wp_localize_script( 
			'sexy-login-ajax-id', 
			'sexy_ajax_script', 
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php', (is_ssl() ? 'https' : 'http') ), 
				'loadingurl' =>  $ssl_plugins_url . 'img/ajax-loader.gif'
			) 
		);
    }
}

add_action( 'widgets_init', 'sexy_login_init' );

?>