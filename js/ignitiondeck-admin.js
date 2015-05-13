jQuery(document).ready(function() {
	// FOR getting the project number, is called for the ignitiondeck-functions.php when Product
	// is changed in Add Post/Add Page page
	jQuery('#project_id_shortcodes').change(function () {
		var prod_num = jQuery(this).val();
		jQuery.ajax({
			type: "POST",
			url: 'admin-ajax.php',
			data: {action: 'get_product_number', product_id: prod_num},
			success: function(html) {						
				//alert(jQuery.trim(html));
				jQuery('.id-metabox-short-codes .shortcode-content span').html(jQuery.trim(html));
			}
		});
	});
	// edit project page
	var short_desc = 'ign_project_description';
	var long_desc = 'ign_project_long_description';
	var updates = 'ign_updates';
	var disclaimer = 'ign_disclaimer';
	var faq = 'ign_faqs';

	if (jQuery("#product_meta").length > 0) {
		evalTinyMCE();
		//adding a function to enable/disable tiny mce in postmeta
		jQuery('#content-html').click(function() {
			//console.log('html');
			//tinyMCE.execCommand('mceRemoveEditor', false, short_desc);
			/*tinyMCE.execCommand('mceRemoveEditor', false, long_desc);
			tinyMCE.execCommand('mceRemoveEditor', false, updates);
			tinyMCE.execCommand('mceRemoveEditor', false, disclaimer);
			tinyMCE.execCommand('mceRemoveEditor', false, faq);*/
			removeTinyMCE();
		});
		jQuery('#content-tmce').click(function() {
			//console.log('mce');
			//tinyMCE.execCommand('mceAddEditor', false, short_desc);
			/*tinyMCE.execCommand('mceAddEditor', false, long_desc);
			tinyMCE.execCommand('mceAddEditor', false, updates);
			tinyMCE.execCommand('mceAddEditor', false, disclaimer);
			tinyMCE.execCommand('mceAddEditor', false, faq);*/
			addTinyMCE();
		});
	}
	var pwywSetting = jQuery('input[name="ign_project_type"]:checked').val();
	if (pwywSetting == undefined) {
		jQuery('input[name="ign_project_type"]').eq(1).attr('checked', 'checked');
	}

	var endTypeSetting = jQuery('input[name="ign_end_type"]:checked').val();
	if (endTypeSetting == undefined) {
		jQuery('input[name="ign_end_type"]').eq(1).attr('checked', 'checked');
	}

	var enableLevels = jQuery('input[name="ign_disable_levels"]').attr('checked');
	jQuery('input[name="ign_disable_levels"]').click(function() {
		if (jQuery(this).attr('checked') == 'checked') {
			jQuery('.new_levels, .new_level, .ign_projectmeta_headline1, .projectmeta-levelbox').hide();
		}
		else {
			jQuery('.new_levels, .new_level, .ign_projectmeta_headline1, .projectmeta-levelbox').show();
		}
	});

	/* ------ */
	jQuery('#payment_gateway').change(function() {
		var option2 = jQuery('#payment_gateway').val();

		if (option2 == 'standard_paypal') {
			jQuery('#standard-settings-container').slideDown('fast');
			jQuery('#adaptive-settings-container').slideUp('fast');
			jQuery("#charge-screen").hide();
		}

		else if (option2 == 'adaptive_paypal') {
			jQuery('#standard-settings-container').slideUp('fast');
			jQuery('#adaptive-settings-container').slideDown('fast');
			jQuery("#charge-screen").show();
		}

		else {
			jQuery('#standard-settings-container').slideUp('fast');
			jQuery('#adaptive-settings-container').slideUp('fast');
		}
	});
	var option1 = jQuery('#payment_gateway').val();
	var url = id_ajaxurl;
	if (jQuery('#charge-screen #project-list').length > 0) {
		jQuery.ajax( {
			async: false,
			url: url,
			type: 'POST',
			data: {action: 'idpp_products_handler'},
			success: function(response) {
				//console.log(response);
				json = JSON.parse(response);
				jQuery.each(json, function() {
					jQuery("#project-list").append("<option value=\"" + this.id + "\" data-projid=\"" + this.id + "\">" + this.product_name.replace(/\\/g, '') + "</option>");
				});
			}
		});
	}
	var project = jQuery("#project-list").find(':selected').attr('data-projid');
	jQuery("#project-list").change(function() {
		project = jQuery("#project-list").find(':selected').attr('data-projid');
		//console.log(project);
	});
	//console.log(project);
	jQuery("#btnProcessPP").click(function(e) {
		e.preventDefault();
		jQuery("#btnProcessPP").attr("disabled", "disabled");
		jQuery.ajax( {
	    	url: url,
	    	type: 'POST',
	    	data: {action: 'idpp_process_handler', Project: project},
	    	success: function(response) {
	    		//console.log(response);
	    		json = JSON.parse(response);
	    		//console.log(json);
	    		jQuery("#charge-confirm").html('<div id="charge-notice" class="updated fade below-h2" id="message"><p>' + json.counts.success + ' Successful Transactions Processed, ' + json.counts.failures + ' Failed Transactions.</p><a id="close-notice" href="#">Close</a></div>');
	    		jQuery("#close-notice").click(function(event) {
	    			if (jQuery("#charge-notice").is(":visible")) {
	    				jQuery("#charge-notice").hide();
	    			}
	    		});
	    		jQuery("#btnProcessPP").removeAttr("disabled");
	    	}
	    });
	    return false;
	});

	if (option1 == 'standard_paypal') {
		jQuery('#standard-settings-container').slideDown('fast');
		jQuery('#adaptive-settings-container').slideUp('fast');
	}

	else if (option1 == 'adaptive_paypal') {
		jQuery('#standard-settings-container').slideUp('fast');
		jQuery('#adaptive-settings-container').slideDown('fast');
		jQuery("#charge-screen").show();
	}

	else {
		jQuery('#standard-settings-container').slideUp('fast');
		jQuery('#adaptive-settings-container').slideUp('fast');
	}
	/* Deck Builder */
	if (jQuery('select[name="deck_select"]').length > 0) {
		jQuery.ajax({
			url: url,
			type: 'POST',
			data: {action: 'get_deck_list'},
			success: function(res) {
				//console.log(res);
				json = JSON.parse(res);
				if (json) {
					//console.log(json);
					jQuery.each(json, function(k,v) {
						jQuery('select[name="deck_select"]').append('<option value="' + this.id + '">' + this.id + ': ' + this.attrs.deck_title + '</option>');
					});
					jQuery('select[name="deck_select"]').change(function() {
						var selected = jQuery(this).val();
						if (selected > 0) {
							jQuery.ajax({
								url: url,
								type: 'POST',
								data: {action: 'get_deck_attrs', Deck: selected},
								success: function(res) {
									//console.log(res);
									jQuery('input[name="deck_delete"]').show();
									if (res.length > 0) {
										json = JSON.parse(res);
										if (json) {
											jQuery.each(json, function(k,v) {
												if (v == 1) {
													jQuery('input[name="' + k + '"]').attr('checked', 'checked');
												}
												else {
													jQuery('input[name="' + k + '"]').val(v);
												}
											});
										}
									}
								}
							});
						}
						else {
							jQuery('.deck-attr-text').val('');
							jQuery('.deck-attr').removeAttr('checked');
							jQuery('input[name="deck_delete"]').hide();
						}
					});
				}
			}
		});
	}
	jQuery("#manual-switch").click(function(e) {
		e.preventDefault();
		//jQuery("#level-select").toggle();
		jQuery("#manual-select").toggle();
		// Remove the value in manual-input if manual-select is hidden
		if (jQuery("#manual-select").css('display') == "none") {
			jQuery("#manual-input").val('');
		}
	});
	if (jQuery('select[name="product_id"]').length > 0) {
		jQuery('select[name="product_id"]').change(function() {
			get_levels();
		});
		jQuery('#product_level').change(function() {
			change_price();
		});
		get_levels();
	}
	function change_price() {
		var lvlPrice = jQuery('#product_level option:selected').data('price');
		if (lvlPrice == '' || lvlPrice == undefined) {
			lvlPrice = 0;
		}
		jQuery('input[name="prod_price"]').val(lvlPrice);
		if (jQuery('#formAddOrder').length > 0) {
			jQuery('#manual-input').val(lvlPrice);
		} else if (jQuery('#formEditOrder').length > 0) {
			if (jQuery('#manual-input').val() == "" || jQuery('#manual-input').data('firstedit') == "no") {
				jQuery('#manual-input').val(lvlPrice);
			} else {
				jQuery('#manual-input').data('firstedit', 'no');
			}
		}
	}
	function get_levels() {
		var pid = jQuery('#product_id').val();
		jQuery.ajax({
			type: "POST",
			url: id_ajaxurl,
			data: {action: 'get_product_levels', Project: pid},
			success: function(res) {		
				//console.log(res);				
				jQuery('#product_level').html(jQuery.trim(res));
				var orderID = jQuery('#formEditOrder').data('orderid');
				if (orderID) {
					select_level(orderID);
				}
				change_price();
			}
		});
	};
	function select_level(orderID) {
		// used to select order level when editing/viewing an order
		jQuery.ajax({
			type: 'POST',
			url: id_ajaxurl,
			async: false,
			data: {action: 'get_order_level', Order: orderID},
			success: function(res) {
				console.log(res);
				if (res) {
					jQuery('#product_level').val(res);
					if (jQuery('#formEditOrder').data('order-price')) {
						var level_price = jQuery('#product_level option[value="'+ res +'"]').data('price');
						var order_price = jQuery('#formEditOrder').data('order-price');
						if (order_price !== level_price) {
							//console.log('its here, order_price: ', order_price);
							jQuery('#manual-input').val(order_price);
						}
					}
				}
			}
		});
	}
	jQuery('input[name="btn_generate_code"]').click(function(e) {
		product_number = document.getElementById('product_number').value;
		document.getElementById('embed_code').innerHTML = '<iframe frameBorder="0" scrolling="no" src="'+id_homeurl+'/?ig_embed_widget=1&product_no='+product_number+'" width="214" height="366"></iframe>';
	});
	jQuery(".hide-notice").click(function(e) {
		e.preventDefault();
		var notice = jQuery(this).attr('id');
		//console.log(notice);
		jQuery(this).parents('.updated').hide();
		jQuery.ajax({
			url: id_ajaxurl,
			type: 'POST',
			data: {action: 'id_hide_notice', Notice: notice},
			success: function(res) {
				//console.log(res);
			}
		});
	});
	jQuery('#emailSettingsForm input[type="radio"]').click(function() {
		jQuery('#emailSettingsForm input[type="radio"]').removeAttr('checked');
		jQuery(this).attr('checked', 'checked');
	});

	/* This is for IDC Add-Ons, Also Known as MDID */

	if (jQuery('#project-charge-screen').length > 0) {
		jQuery.ajax({
			url: id_ajaxurl,
			type: 'POST',
			data: {action: 'project_posts_list_ajax'},
			success: function(res) {
				if (res.length > 0) {
					var json = JSON.parse(res);
					//console.log(json);
					jQuery.each(json, function() {
						jQuery('#project-charge-screen select#project-list').append('<option value="' + this.ID + '">' + this.post_title + '</option>');
					});
				}
			}
		});
		jQuery('#btnProcessProjectPreauth').click(function(e) {
			e.preventDefault();
			jQuery('#charge-notice').hide();
			jQuery('#charge-notice .success-count, #charge-notice .fail-count').text('0');
			jQuery(this).attr('disabled', 'disabled');
			var postID = jQuery('select#project-list').val();
			jQuery.ajax({
				url: id_ajaxurl,
				type: 'POST',
				data: {action: 'send_preauth_to_idc', POST_ID: postID},
				success: function(res) {
					if (res.length > 0) {
						var json = JSON.parse(res);
					}
					if (jQuery(json).size() > 0) {
						//console.log(json);
						jQuery.each(json, function(k, v) {
							jQuery.ajax({
								url: md_ajaxurl,
								type: 'POST',
								data: {action: 'md_process_preauth', Level: this},
								success: function(res) {
									//console.log(res);
									new_json = JSON.parse(res);
									if (jQuery('#charge-notice').length > 0) {
										var successCount = jQuery('#charge-notice .success-count').text();
										successCount = parseInt(new_json.counts.success) + parseInt(successCount);
										jQuery('#charge-notice .success-count').text(successCount);
										var failCount = jQuery('#charge-notice .fail-count').text();
										failCount = parseInt(new_json.counts.failures) + parseInt(failCount);
										//console.log(failCount);
										jQuery('#charge-notice .fail-count').text(failCount);
									}
									else {
										jQuery("#project-charge-confirm").html('<div id="charge-notice" class="updated fade below-h2" id="message"><p><span class="success-count">' + new_json.counts.success + '</span> Successful Transactions Processed, <span class="fail-count">' + new_json.counts.failures + '</span> Failed Transactions.</p><a id="close-notice" href="#">Close</a></div>');
						    		}
						    		jQuery('#charge-notice').show();
						    		jQuery("#close-notice").click(function(event) {
						    			if (jQuery("#charge-notice").is(":visible")) {
						    				jQuery("#charge-notice").hide();
						    			}
						    		});
									jQuery('#btnProcessProjectPreauth').removeAttr('disabled');
								}
							});
						});
					}
				}
			});
		});
	}
});

function addTinyMCE() {
	jQuery.each(jQuery('textarea.tinymce'), function() {
		var id = jQuery(this).attr('id');
		tinyMCE.execCommand('mceAddEditor', false, id);
	});
}

function removeTinyMCE() {
	jQuery.each(jQuery('textarea.tinymce'), function() {
		var id = jQuery(this).attr('id');
		tinyMCE.execCommand('mceRemoveEditor', false, id);
	});
}

function evalTinyMCE() {
	if (jQuery("#wp-content-wrap").hasClass('tmce-active')) {
		addTinyMCE();
	}
	else {
		removeTinyMCE();
	}
}

function storeurladdress() {
   	if(document.getElementById('select_pageurls').value == 'external_url')
   	{
	   	document.getElementById('proj_url_cont').style.display = 'block';
	   	document.getElementById('proj_posts').style.display = 'none';
   	}
   	else if(document.getElementById('select_pageurls').value == 'page_or_post')
   	{
	   	document.getElementById('proj_posts').style.display = 'block';
	   	document.getElementById('proj_url_cont').style.display = 'none';
   	}
   	else if(document.getElementById('select_pageurls').value == 'current_page')
	{
		document.getElementById('proj_posts').style.display = 'none';
	   	document.getElementById('proj_url_cont').style.display = 'none';
	}
}

function storepurchaseurladdress() {
   	if(document.getElementById('select_purchase_pageurls').value == 'external_url')
   	{
	   	document.getElementById('purchase_url_cont').style.display = 'block';
	   	document.getElementById('purchase_posts').style.display = 'none';
   	}
   	else if(document.getElementById('select_purchase_pageurls').value == 'page_or_post')
   	{
	   	document.getElementById('purchase_posts').style.display = 'block';
	   	document.getElementById('purchase_url_cont').style.display = 'none';
   	}
   	else if(document.getElementById('select_purchase_pageurls').value == 'current_page')
	{
		document.getElementById('purchase_posts').style.display = 'none';
	   	document.getElementById('purchase_url_cont').style.display = 'none';
	}
}

function storetyurladdress()
{
   	if(document.getElementById('select_ty_pageurls').value == 'external_url')
   	{
	   	document.getElementById('ty_url_cont').style.display = 'block';
	   	document.getElementById('ty_posts').style.display = 'none';
   	}
   	else if(document.getElementById('select_ty_pageurls').value == 'page_or_post')
   	{
	   	document.getElementById('ty_posts').style.display = 'block';
	   	document.getElementById('ty_url_cont').style.display = 'none';
   	}
   	else if(document.getElementById('select_ty_pageurls').value == 'current_page')
	{
		document.getElementById('ty_posts').style.display = 'none';
	   	document.getElementById('ty_url_cont').style.display = 'none';
	}
}
function toggleDiv(divId) {
	jQuery("#"+divId).slideToggle();
}