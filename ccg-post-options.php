<?php
add_action( 'admin_init', 'ccg_add_meta_box', 1 );
add_action( 'save_post', 'ccg_save_post_data' );

function ccg_add_meta_box() {
	
	$args	= array(
        'public'	=> true,
        '_builtin'	=> false
    ); 

    $output 	= 'names'; // names or objects, note names is the default
    $operator 	= 'and'; // 'and' or 'or'
    $post_types = get_post_types($args,$output,$operator);
	$posttypes_array = array();

    foreach ($post_types  as $post_type ) {
        $posttypes_array[] = $post_type;
    }
	
	$posttypes_array[] = 'post';

	foreach ( $posttypes_array as $post_type ) {
		
		add_meta_box( 
			'ccg_metabox',
			__( 'Creative Commons Generator', 'ccg-domain' ),
			'ccg_meta_box',
			$post_type 
		);
		
	}
	
}

function ccg_save_post_data( $post_id ) {
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( !isset( $_POST['ccg_nonce'] ) ) return;
	if ( !wp_verify_nonce( $_POST['ccg_nonce'], plugin_basename( __FILE__ ) ) )	return;
	if ( !current_user_can( 'edit_post', $post_id ) )	return;

	$ccg_options = ccg_set_options( $_POST );	
	update_post_meta( $post_id, '_ccg_options', $ccg_options );
	
}

function ccg_meta_box( $post ) {
	
	wp_nonce_field( plugin_basename( __FILE__ ), 'ccg_nonce' );
	
	// Get the current options
	if ( get_post_meta( $post->ID, '_ccg_options' ) ) {
		
		$ccg_old_options = get_post_meta( $post->ID, '_ccg_options', true );
		$ccg_author_name = $ccg_old_options['author_name'];
		$ccg_author_url = $ccg_old_options['author_url'];
		echo ccg_get_banner( $ccg_old_options, 'meta_exist' ); 
		
	} else {
		
		$ccg_old_options = get_option('ccg_options');
		global $current_user;
		$ccg_author_name = esc_attr( $current_user->display_name );
		$ccg_author_url = esc_url( get_author_posts_url( $current_user->ID ) );
		echo ccg_get_banner( $ccg_old_options, 'publish' );
		
	}
	
?>
	<table class="widefat" style="margin: 10px 0;" >
		<tr>
			<td>
				<label for="ccg_title"><?php _e( 'Title of work:', 'ccg-domain' ); ?></label>
			</td>
			<td>
				<input type="text" id="ccg_title" name="ccg_title" value="<?php echo $ccg_old_options['title']; ?>" style="width: 260px;" />
			</td>
			<td>
				<?php _e( 'The title of the work you are licensing.', 'ccg-domain' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ccg_source"><?php _e( 'Source work URL:', 'ccg-domain' ); ?></label>
			</td>
			<td>
				<input type="text" id="ccg_source" name="ccg_source" value="<?php echo $ccg_old_options['source_url']; ?>" style="width: 260px;" />
			</td>
			<td>
				<?php _e( 'The URL of the work upon which this work is based or derived.', 'ccg-domain' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ccg_author_name"><?php _e( "Author's Name:", 'ccg-domain' ); ?></label>
			</td>
			<td>
				<input type="text" id="ccg_author_name" name="ccg_author_name" value="<?php echo $ccg_author_name; ?>" style="width: 260px;" />
			</td>
			<td>
				<?php _e( 'The name of the person who should receive attribution for the work. Most often, this is the author.', 'ccg-domain' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ccg_author_url"><?php _e( 'Link to the author of the work', 'ccg-domain' ); ?>:</label>
			</td>
			<td>
				<input type="text" id="ccg_author_url" name="ccg_author_url" value="<?php echo $ccg_author_url; ?>" style="width: 260px;" />
			</td>
			<td>
				<?php _e( "The URL to which the work should be attributed. For example, the work's page on the author's site.", 'ccg-domain' ); ?>
			</td>
		</tr>
	</table>
<?php
	ccg_get_table( $ccg_old_options );
}



?>