<?php
function sexy_login_ajax() {
	if ( !wp_verify_nonce( $_POST['sexy-login-nonce'], 'sln-security' ) ) die( 'Wrong security token!' );

	$creds = array();
	$creds['user_login'] 	= $_POST['log'];
	$creds['user_password'] = $_POST['pwd'];
	if ( isset( $_POST['rememberme'] ) ) $creds['remember'] = $_POST['rememberme'];
	else $creds['rememberme'] = false;
	$redirect_to 			= esc_url( $_REQUEST['redirect_to'] );
 
	$secure_cookie = '';
	if ( ! force_ssl_admin() ) {
		$user_name = sanitize_user( $_POST['log'] );
		if ( $user = get_user_by('login',  $user_name ) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
	}
	if ( force_ssl_admin() ) $secure_cookie = true;
	if ( $secure_cookie=='' && force_ssl_login() ) $secure_cookie = false;
	
	if ( $secure_cookie && strstr($redirect_to, 'wp-admin') ) $redirect_to = str_replace('http:', 'https:', $redirect_to);

	$login = wp_signon($creds, $secure_cookie);
	$result = array();
	if (!is_wp_error($login)){
		$result['success'] = 1;
		$result['redirect'] = $redirect_to;
	} else {
		$result['success'] = 0;
		if ($login->errors)	$result['error'] = $login->get_error_message();
		else $result['error'] = __("<strong>ERROR</strong>: Please enter your username and password to login.", "sl-domain");
	}
	header('content-type: application/json; charset=utf-8');
	echo json_encode($result);
	die();
}

function sexy_login_current_url() {
	$current_url  = force_ssl_admin() ? 'https://' : 'http://';
	$current_url .= esc_attr( $_SERVER['HTTP_HOST'] );
	$current_url .= esc_attr( $_SERVER['REQUEST_URI'] );
	return strip_tags( $current_url );
}
?>