jQuery(document).ready(function() {
	 
	// Datepicker
	jQuery('.cmb_datepicker').each(function (){
		jQuery('#' + jQuery(this).attr('id')).datepicker();
		// jQuery('#' + jQuery(this).attr('id')).datepicker({ dateFormat: 'yy-mm-dd' });
		// For more options see http://jqueryui.com/demos/datepicker/#option-dateFormat
	});
	
	var pID = jQuery('#post_ID').val();
	
	// File and image upload handling 
	//-------------------------------------------------------------------------------------------//
	var formfield;
	var uploadStatus = true;
	
	jQuery('.upload_button').click(function() {
		formfield = jQuery(this).prev('input').attr('name');
		tb_show('', 'media-upload.php?post_id=' + pID + 'type=image&cbm_setting=cbm_value&TB_iframe=true');
		return false;
	});
	
	jQuery('.remove_file_button').on('click', function() {
		formfield = jQuery(this).attr('rel');
		jQuery('input.' + formfield).val('');
		jQuery(this).parent().remove();
		return false;
	});
	var type = jQuery('input[name*="ign_project_type"]:checked').val();
		if (type == 'pwyw') {
			jQuery(".new_level").hide();
			jQuery(".new_levels").hide();
		}
		if (type == 'level-based') {
			jQuery(".new_level").show();
			jQuery(".new_levels").show();
		}
	jQuery('input[name*="ign_project_type"]').click(function() {
		type = jQuery('input[name*="ign_project_type"]:checked').val();
		if (type == 'pwyw') {
			jQuery(".new_level").hide();
			jQuery(".new_levels").hide();
		}
		if (type == 'level-based') {
			jQuery(".new_level").show();
			jQuery(".new_levels").show();
		}
	});
	
	/*
	jQuery( 'div#gallery-settings' ).hide();
	jQuery( '.savesend input.button[value*="Insert into Post"], .media-item #go_button' ).attr( 'value', 'Use this File' );
	jQuery( '.savesend a.wp-post-thumbnail' ).hide();
	jQuery( '#media-items .align' ).hide();
	jQuery( '#media-items .url' ).hide();
	*/
	
	window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function(html) {
		if (formfield) {
			
	        if ( jQuery(html).html(html).find('img').length > 0 ) {
	        	itemurl = jQuery(html).html(html).find('img').attr('src'); // Use the URL to the size selected.
	        } else {
	        	// It's not an image. Get the URL to the file instead.
	        	var htmlBits = html.split("'"); // jQuery seems to strip out XHTML when assigning the string to an object. Use alternate method.
	        	itemurl = htmlBits[1]; // Use the URL to the file.
	        	var itemtitle = htmlBits[2];
	        	itemtitle = itemtitle.replace( '>', '' );
	        	itemtitle = itemtitle.replace( '</a>', '' );
	        }
         
			var image = /(^.*\.jpg|jpeg|png|gif|ico*)/gi;
			var document = /(^.*\.pdf|doc|docx|ppt|pptx|odt|psd|eps|ai*)/gi;
			var audio = /(^.*\.mp3|m4a|ogg|wav*)/gi;
			var video = /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi;
        
			if (itemurl.match(image)) {
			 	uploadStatus = '<div class="img_status"><img src="'+itemurl+'" alt="" /><a href="#" class="remove_file_button" rel="' + formfield + '">Remove Image</a></div>';
			} else {
			// No output preview if it's not an image
			// Standard generic output if it's not an image.
				html = '<a href="'+itemurl+'" target="_blank" rel="external">View File</a>';
				uploadStatus = '<div class="no_image"><span class="file_link">'+html+'</span>&nbsp;&nbsp;&nbsp;<a href="#" class="remove_file_button" rel="' + formfield + '">Remove</a></div>';
			}

			jQuery('.' + formfield).val(itemurl);
			jQuery('.' + formfield).siblings('.cmb_upload_status').slideDown().html(uploadStatus);
			tb_remove();
        
		} else {
			window.original_send_to_editor(html);
		}
		// Clear the formfield value so the other media library popups can work as they are meant to. - 2010-11-11.
		formfield = '';
	}
});