<?php
add_action('admin_menu', 'ccg_menu');

function ccg_menu() {
	ccg_set_default();
	add_options_page(
		__( 'Creative Commons Generator Options', 'ccg-domain' ), 
		'CCG Options', 
		'manage_options', 
		'ccg-settings', 
		'ccg_settings_page'
	);
}

function ccg_set_default() {
	
	if ( !get_option( 'ccg_options' ) ) {
		
		$ccg_options = array(
			'active' => TRUE,
			'mod' => 'yes',
			'disallow_mon' => TRUE,
			'show_title' => FALSE,
			'title' => '',
			'show_author' => TRUE,
			'author_name' => "",
			'show_author_url' => TRUE,
			'author_url' => '',
			'source_url' => '',
			'more_url' => '',
			'format' => 'other'
		);
		
		update_option( 'ccg_options', $ccg_options );
	}
	
}

function ccg_get_banner( $ccg_options, $who_call ) {
	if ( !$ccg_options['active'] && !is_admin() ) return "";
	
	$banner = "";
	$islicensed ="";
	$title = "";
	$author ="";
	$source = "";
	$more = "";
	
	$attributes = "by";
	$attributes_text = __( 'Creative Commons Attribution', 'ccg-domain' );
	
	if ( $ccg_options['disallow_mon'] ) { 
		$attributes .= "-nc";
		$attributes_text .= __( '-NonCommercial', 'ccg-domain' );
	}
	if ( $ccg_options['mod'] == "but" ) {
		$attributes .= "-sa";
		$attributes_text .= __( '-ShareAlike', 'ccg-domain' );
	}
	if ( $ccg_options['mod'] == "no" ) {
		$attributes .= "-nd";
		$attributes_text .= __( '-NoDerivs', 'ccg-domain' );
	}

	$banner = "<a target='_blank' rel='license nofollow' href='http://creativecommons.org/licenses/" . $attributes . "/4.0/'><img alt='Creative Commons License' style='border-width:0' src='http://i.creativecommons.org/l/" . $attributes . "/4.0/88x31.png' /></a><br />";
	$islicensed =  ' is licensed under a <a target="_blank" rel="license nofollow" href="http://creativecommons.org/licenses/' . $attributes . '/4.0/">' . $attributes_text . ' 4.0 ' . __( 'International', 'ccg-domain' ) . '</a><br />';
	
	switch ( $who_call ) {
		case "admin":
			$title = __( 'Title', 'ccg-domain' );
			$author_name = __( 'Author-Name', 'ccg-domain' );
			$author_url = "#";
			break;
		case "publish": 
			$title = "";
			global $current_user;
			$author_name = esc_attr( $current_user->display_name );
			$author_url =  esc_attr ( esc_url( get_author_posts_url( $current_user->ID ) ) );
			break;
		case "meta_exist":
			$title = $ccg_options['title'];
			$author_name = $ccg_options['author_name'];
			$author_url = $ccg_options['author_url'];
			break;
		case "no_meta":
			$title = get_the_title();
			$author_name = get_the_author();
			$author_url = esc_url( get_author_posts_url(get_the_author_meta( 'ID' ) ) );
			break;
	}

	if( $ccg_options['show_title'] == FALSE || $title == "" )	$title = __( 'This work', 'ccg-domain' );
	
	//Format and Tittle 
	if ($ccg_options['format'] != "other")	$format_type = 'href="http://purl.org/dc/dcmitype/' . $ccg_options['format'] . '"  rel="dct:type"'; 
	else $format_type = "";
	$title = '<span xmlns:dct="http://purl.org/dc/terms/" property="dct:title" ' . $format_type . ' >' . $title . '</span>';
	
	//Show Author and URL
	if ( $ccg_options['show_author'] == TRUE && $author_name != "" ) {
		if ( $ccg_options['show_author_url'] == TRUE && $author_url != "") 
			$author = ' by <a target="_blank" xmlns:cc="http://creativecommons.org/ns#" href="' . $author_url . '" property="cc:attributionName" rel="cc:attributionURL nofollow">'. $author_name.'</a>';
		else
			$author = ' by <span xmlns:cc="http://creativecommons.org/ns#" property="cc:attributionName">' . $author_name . '</span>.';
	}

	//Show Source URL
	if ( $ccg_options['source_url'] != "" )	$source = 'Based on a work at <a target="_blank" xmlns:dct="http://purl.org/dc/terms/" href="' . $ccg_options["source_url"] . '" rel="dct:source nofollow">' . $ccg_options["source_url"] . '</a><br />';
	
	//Show More Permissions
	if ($ccg_options['more_url'] != "")	$more = 'Permissions beyond the scope of this license may be available at <a target="_blank" xmlns:cc="http://creativecommons.org/ns#" href="' . $ccg_options['more_url'] . '" rel="cc:morePermissions nofollow">' . $ccg_options['more_url'] . '</a>';
	
	$result = "<div class='ccg-banner'>" . $banner . $title . $author . $islicensed . $source . $more . "</div>";
	return $result;
}

function ccg_set_options( $form ) {
	$ccg_options = array();

	if ( isset( $form['ccg_all'] ) ) $ccg_options['active'] = TRUE; else $ccg_options['active'] = FALSE;
	if ( isset( $form['ccg_mod'] ) ) $ccg_options['mod'] = $form['ccg_mod']; else $ccg_options['mod'] = "yes";
	if ( isset( $form['ccg_mon'] ) ) $ccg_options['disallow_mon'] = TRUE; else $ccg_options['disallow_mon'] = FALSE;
	if ( isset( $form['ccg_show_title'] ) ) $ccg_options['show_title'] = TRUE; else $ccg_options['show_title'] = FALSE;
	if ( isset( $form['ccg_title'] ) ) $ccg_options['title'] = esc_attr( $form['ccg_title'] ); else $ccg_options['title'] = "";
	if ( isset( $form['ccg_show_author'] ) ) $ccg_options['show_author'] = TRUE; else $ccg_options['show_author'] = FALSE;
	if ( isset( $form['ccg_author_name'] ) ) $ccg_options['author_name'] = esc_attr( $form['ccg_author_name'] ); else $ccg_options['author_name'] = "";
	if ( isset( $form['ccg_show_author_url'] ) ) $ccg_options['show_author_url'] = TRUE; else $ccg_options['show_author_url'] = FALSE;
	if ( isset( $form['ccg_author_url'] ) ) $ccg_options['author_url'] = esc_attr( esc_url( $form['ccg_author_url'] ) );  else $ccg_options['author_url'] = "";
	if ( isset( $form['ccg_source'] ) ) $ccg_options['source_url'] = esc_attr( esc_url( $form['ccg_source'] ) ); else $ccg_options['source_url'] = "";
	$ccg_options['more_url'] = esc_attr( esc_url( $form['ccg_more'] ) );
	$ccg_options['format'] = $form['ccg_format'];

	return $ccg_options;
}

function ccg_get_table( $options ) {
	?>
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e( 'Options', 'ccg-domain' ); ?></th>
				<th><?php _e( 'Description', 'ccg-domain' ); ?></th>       
			</tr>
		</thead>
		<tbody>
			<tr>
			 <td>
				<p><label>
					<input name="ccg_all" type="checkbox" value="true" <?php checked( $options['active'] ); ?> /> <?php _e( 'Enable Creative Commons Generator', 'ccg-domain' ); ?>
				</p></label>
			 </td>
			 <td>
				<p><?php _e( 'Creative Commons Generator display the banner below the post if you check this.', 'ccg-domain' ); ?></p>
			 </td>
		   </tr>
		   <tr>
			 <td>
				<p><label>
					<input name="ccg_mod" type="radio" value="yes" <?php checked( $options['mod'], 'yes' ); ?> /> <?php _e( 'Allow modifications of your work', 'ccg-domain' ); ?>
				</p></label>
			 </td>
			 <td>
				<p><?php _e( 'The licensor permits others to copy, distribute, display and perform the work, as well as make derivative works based on it.', 'ccg-domain' ); ?></p>
			 </td>
		   </tr>
		   <tr>
			 <td>
				<p><label>
					<input name="ccg_mod" type="radio" value="but" <?php checked( $options['mod'], 'but' ); ?> /> <?php _e( 'Allow modifications as long as others share alike', 'ccg-domain' ); ?>
				</p></label>
			 </td>
			 <td>
				<p><?php _e( 'The licensor permits others to create and distribute derivative works but only under the same or a compatible license.', 'ccg-domain' ); ?></p>
			 </td>
		   </tr>
		   <tr>
			 <td>
				<p><label>
					<input name="ccg_mod" type="radio" value="no" <?php checked( $options['mod'], 'no' ); ?> /> <?php _e( 'Disallow modifications of the work.', 'ccg-domain' ); ?>
				</p></label>
			 </td>
			 <td>
				<p><?php _e( 'The licensor permits others to copy, distribute, display and perform only unaltered copies of the work, but not create and distribute derivative works based on it.', 'ccg-domain' ); ?></p>
			 </td>
		   </tr>
		   <tr>
			 <td>
				<p><label>
					<input type="checkbox" name="ccg_mon" value="true" <?php checked( $options['disallow_mon'] ); ?> /> <?php _e( 'Disallow commercial uses of your work', 'ccg-domain' ); ?>
				</p></label>
			 </td>
			 <td>
				<p><?php _e( 'The licensor permits others to copy, distribute, display, and perform the work for non-commercial purposes only.', 'ccg-domain' ); ?></p>
			</td>
		   </tr>
		   <tr>
			 <td>
				<p><label>
					<input type="checkbox" name="ccg_show_title" value="true" <?php checked( $options['show_title'] ); ?> /> <?php _e( 'Show title of work', 'ccg-domain' ); ?>
				</p></label>
			 </td>
			 <td>
				<p><?php _e( 'The title of the work you are licensing.', 'ccg-domain' ); ?></p>
			 </td>
		   </tr>
		   <tr>
			 <td>
				<p><label>
					<input type="checkbox" name="ccg_show_author" value="true" <?php checked( $options['show_author'] ); ?>  id="ccg_author1" onclick="if(!this.checked)document.getElementById('ccg_author2').checked = false"/> <?php _e( 'Show author of work', 'ccg-domain' ); ?>
				</p></label>
			 </td>
			 <td>
				<p><?php _e( 'The name of the person who should receive attribution for the work. Most often, this is the author.', 'ccg-domain' ); ?></p>
			 </td>
		   </tr>
		   <tr>
			 <td>
				<p><label>
					<input type="checkbox" name="ccg_show_author_url" value="true" <?php checked( $options['show_author_url'] ); ?> id="ccg_author2" onclick="if(this.checked)document.getElementById('ccg_author1').checked = true" /> <?php _e( 'Link to the author of the work', 'ccg-domain' ); ?>
				</p></label>
			 </td>
			 <td>
				<p><?php _e( "The URL to which the work should be attributed. For example, the work's page on the author's site.", 'ccg-domain' ); ?></p>
			 </td>
		   </tr>
		   <tr>
			 <td>
				<p><label>
					<?php _e( 'More permissions URL:', 'ccg-domain' ); ?>
					<input type="text" name="ccg_more" value="<?php echo $options['more_url']; ?>" />
				</p></label>
			 </td>
			 <td>
				<p><?php _e( 'A URL where a user can find information about obtaining rights that are not already permitted by the CC license.', 'ccg-domain' ); ?></p>
			 </td>
		   </tr>
		   <tr>
			 <td>
				<p><label>
					<?php _e( 'Format of work', 'ccg-domain' ); ?>
					<select name="ccg_format">
					  <option value="other" <?php selected( $options['format'], 'other' ); ?> ><?php _e( 'Other / Multiple formats', 'ccg-domain' );?></option>
					  <option value="Sound" <?php selected( $options['format'], 'Sound' ); ?> ><?php _e( 'Audio', 'ccg-domain' );?></option>
					  <option value="MovingImage" <?php selected( $options['format'], 'MovingImage' ); ?> ><?php _e( 'Video', 'ccg-domain' );?></option>
					  <option value="StillImage" <?php selected( $options['format'], 'StillImage' ); ?> ><?php _e( 'Image', 'ccg-domain' );?></option>
					  <option value="Text" <?php selected( $options['format'], 'Text' ); ?> ><?php _e( 'Text', 'ccg-domain' );?></option>
					  <option value="DataSet" <?php selected( $options['format'], 'DataSet' ); ?> ><?php _e( 'Dataset', 'ccg-domain' );?></option>
					  <option value="InteractiveResource" <?php selected( $options['format'], 'InteractiveResource' ); ?> ><?php _e( 'Interactive', 'ccg-domain' );?></option>
					</select>
				</p></label>
			 </td>
			 <td>
				<p><?php _e( 'This describes what kind of work is being licensed. For example, a photograph would have the "Image" format. If unsure, choose "Other / Multiple formats".', 'ccg-domain' ); ?></p>
			 </td>
		   </tr>
		</tbody>
	</table>
	<?php
}

function ccg_settings_page() {
?>
	<div class='wrap'>
		<div id="icon-tools" class="icon32"></div>
		<h2><?php _e( 'Creative Commons Generator - Options Page', 'ccg-domain' ); ?></h2>
<?php
	// Update options.
	if (isset($_POST['submit'])) {
		$ccg_options = ccg_set_options( $_POST );
		update_option( 'ccg_options', $ccg_options );
		
		echo "<div id='message' class='updated'><p>" . __( 'Successfully saved configuration.', 'ccg-domain' ) . "</p></div>";
	}	
?>		
		<h3><?php _e( 'Preview', 'ccg-domain' ); ?></h3>
		<?php echo ccg_get_banner( get_option('ccg_options'), 'admin' ); ?>
		
		<h3><?php _e( 'General Settings', 'ccg-domain' ); ?></h3>
		<p><?php _e( 'This setting is general and is used by default for all entries (old and new). However <strong>can be modified specifically for each entry</strong> from the Add/Edit section.', 'ccg-domain' ); ?></p>
		<form method="post">
			<?php ccg_get_table( get_option('ccg_options') ); ?>
			<p><input type="submit" class="button-primary" value="<?php esc_attr_e( __( 'Save Changes' ) ); ?>"/></p>
			<input type="hidden" name="submit" value="true" />
		</form>
		<p>* <?php _e( "Author's Name:", 'ccg-domain' ); ?> <?php _e( "The author's Display Name will be used by default.", 'ccg-domain' ); ?></p>
		<p>* <?php _e( 'Link to the author of the work', 'ccg-domain' ); ?>: <?php _e( "The author's link in WordPress will be used by default.", 'ccg-domain' ); ?></p>
		<p>* <?php _e( 'Title of work:', 'ccg-domain' ); ?> <?php _e( 'For entries created with disabled CCG will use the title of the post by default. For those that are published with the CCG enabled, the title must be entered manually.', 'ccg-domain' ); ?></p>
	</div>
<?php
}
?>