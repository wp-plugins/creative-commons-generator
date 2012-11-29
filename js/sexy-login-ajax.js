jQuery(
	function(){
		jQuery('.sexy_login_widget form').submit(
			function(){

				var thisform = this;
				var id = jQuery('input[name="id"]', thisform).val();

				jQuery('#sexy-login-show-error-'+id).slideUp('fast');
				jQuery('div#sexy-login-wrap-'+id).block({ 
					message: '<img src="' + sexy_ajax_script.loadingurl + '" />', 
					overlayCSS: { 
						backgroundColor: '#fff', 
						opacity:         0.6 
					},
					css: {
						padding:    0,
						margin:     0,
						width:      '30%',
						left:       '35%',
						textAlign:  'center',
						color:      '#000',
						border:     'none',
						backgroundColor:'none',
						cursor:     'wait'
					},
				}); 

				jQuery.post(
					sexy_ajax_script.ajaxurl, 
					jQuery('#sexy-login-form-'+id).serialize(),
					function(result){
						if (result.success == 1) {
							window.location = result.redirect;
						} else {
							jQuery('div#sexy-login-wrap-'+id).unblock()
							jQuery('#sexy-login-show-error-'+id).html(result.error);
							jQuery('#sexy-login-show-error-'+id).slideDown('fast');
						}
					}
				);			
				return false;
			}
		);
	}
);