<?php
class Sexy_Login_Widget extends WP_Widget {
	
	function Sexy_Login_Widget() {
		$widget_ops = array('classname' => 'sexy_login_widget', 'description' => 'A simple widget for login in Wordpress.' );
		$this->WP_Widget('sexy_login_widget', 'Sexy Login', $widget_ops);
	}
	
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		if ( $instance['sexy_login_style'] ) $sexyLoginClass = "class='sexy-login-wrap'";
		else $sexyLoginClass = '';
		?>
		<div class="sexy-login-show-error" id="sexy-login-show-error-<?php echo $this->id; ?>" ></div>
		<div <?php echo $sexyLoginClass; ?> id='sexy-login-wrap-<?php echo $this->id;?>'>
		<?php
		
		if ( is_user_logged_in() ) {
			global $current_user;
			switch ( $instance['name_to_show'] ) {
				case "nickname":
					$name_to_show =  $current_user->nickname;
					break;
				case "username":
					$name_to_show = $current_user->user_login;
					break;
				case "display":
					$name_to_show = $current_user->display_name;
					break;	
			}
			
			if ( $instance['show_avatar'] )	echo get_avatar( $current_user->ID, 300 );
			
			echo "<h2>".$name_to_show."</h2>";
			
			if ( $instance['show_dashboard'] ):
			?>	<button type="button" onClick="location.href='<?php  echo admin_url(); ?>'"><?php _e("Dashboard");?></button>	<?php 
			endif;
			if ( $instance['show_edit_my_profile'] ):	
			?>	<button type="button" onClick="location.href='<?php  echo admin_url(); ?>profile.php'"><?php _e("Edit My Profile");?></button>	<?php 
			endif;
			?>
			<button type="button" onClick="location.href='<?php echo wp_logout_url( sexy_login_current_url() ); ?>'"><?php _e("Log Out"); ?></button>		
			<?php	
		}		
		else {
			if ( force_ssl_admin() ) $redirect_to = str_replace( 'http:', 'https:', esc_url(sexy_login_current_url()) );			
			?>	<form id="sexy-login-form-<?php echo $this->id; ?>" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
					<p>
						<input type="text" name="log" size="20" placeholder="<?php _e('Username'); ?>" />
					</p>
					<p>
						<input type="password" name="pwd" size="20" placeholder="<?php _e('Password'); ?>" />
					</p>
					<p>
						<label>
							<input type="checkbox" name="rememberme" value="forever" /><?php _e("Remember Me");?>
						</label>
					</p>
					<p>
						<input name="action" type="hidden" value="sexy_login_hook" />
						<input name="id" type="hidden" value="<?php echo $this->id; ?>" />
						<input name="redirect_to" type="hidden" value="<?php echo $redirect_to; ?>" />
						<?php wp_nonce_field( 'sln-security', 'sexy-login-nonce' ); ?>
						<input class="submit-button" type="submit" value="<?php _e("Log In"); ?>" />
						<?php if ( get_option( 'users_can_register' ) ) : ?>
							<button type="button" onClick="location.href='<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login' ) ); ?>'"><?php _e( 'Register' ); ?></button>
						<?php endif; ?>
					</p>
				</form>
				<a href="<?php echo esc_url( wp_lostpassword_url( sexy_login_current_url() ) ); ?>" title="<?php _e("Lost your password?"); ?>"><?php _e("Lost your password?"); ?></a>
			<?php
		}
		echo "</div>";
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_avatar'] = $new_instance['show_avatar'];
		$instance['show_dashboard'] = $new_instance['show_dashboard'];
		$instance['show_edit_my_profile'] = $new_instance['show_edit_my_profile'];
		$instance['sexy_login_style'] = $new_instance['sexy_login_style'];
		$instance['name_to_show'] = strip_tags($new_instance['name_to_show']);
		return $instance;
	}
	
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Sexy Login', 'show_avatar' => '1', 'show_dashboard' => '1', 'show_edit_my_profile' => '1', 'sexy_login_style' => '1','name_to_show' => '' ) );
		$title = strip_tags($instance['title']);
		$name_to_show = strip_tags($instance['name_to_show']);
		
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title"); ?>: 
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php esc_attr_e($title); ?>" />
				</label>
			</p>
			<p> <?php echo __( "Show" ) . ":"; ?></p>
			<p>
				<label>
					<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_avatar'); ?>" name="<?php echo $this->get_field_name('show_avatar'); ?>" value="1" <?php checked( $instance['show_avatar'], "1" ); ?> />
					<?php _e( "Avatar" ); ?>
				</label>
			</p>
			
			<p>
				<label>
					<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_dashboard'); ?>" name="<?php echo $this->get_field_name('show_dashboard'); ?>" value="1" <?php checked( $instance['show_dashboard'], "1" ); ?> />
					<?php _e( "Dashboard" ); ?>
				</label>
			</p>
			
			<p>
				<label>
					<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_edit_my_profile'); ?>" name="<?php echo $this->get_field_name('show_edit_my_profile'); ?>" value="1" <?php checked( $instance['show_edit_my_profile'], "1" ); ?> />
					<?php _e("Edit My Profile" );?>
				</label>
			</p>
			
			<p>
				<label>
					<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('sexy_login_style'); ?>" name="<?php echo $this->get_field_name('sexy_login_style'); ?>" value="1" <?php checked( $instance['sexy_login_style'], "1" ); ?> />
					<?php _e("Sexy Login Style", "sl-domain"); ?>
				</label>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('name_to_show'); ?>"><?php _e("Select Name to Show", "sl-domain"); ?>:</label>
				<select class="widefat" name="<?php echo $this->get_field_name('name_to_show'); ?>" id="<?php echo $this->get_field_id('name_to_show'); ?>">
					<option value="username" <?php if ($name_to_show == "username") echo 'selected="selected"'; ?>><?php _e("Username"); ?></option>
					<option value="nickname" <?php if ($name_to_show == "nickname") echo 'selected="selected"'; ?>><?php _e("Nickname"); ?></option>
					<option value="display" <?php if ($name_to_show == "display") echo 'selected="selected"'; ?>><?php _e("Display Name"); ?></option>
				</select>
			</p>
		<?php
	}
}
?>