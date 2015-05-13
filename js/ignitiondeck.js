jQuery(document).ready(function() {
	if (jQuery('.id-progress-raised').length > 0) {
		adjustHeights('.id-progress-raised');
	}
	jQuery('#share-embed').click(function() {
		jQuery('.social-button').toggle();
		jQuery('.embed-box').toggle();
	});
	jQuery('.hasvideo').click(function() {
		var vidSource = jQuery('.hasvideo iframe').attr('src');
		if (vidSource.indexOf('?rel=0&autoplay=1') == -1) {
	  		jQuery('.hasvideo iframe').attr('src', vidSource + '?rel=0&autoplay=1').show();
	  	}
	  	else {
	  		jQuery('.hasvideo iframe').show();
	  	}
	});
	// FAQ/UPDATES
	/*jQuery('.product-dashed-heading').click(function () {
        //console.log('clicked');
        jQuery('#prodfaq').slideToggle('fast', function () {
            if (jQuery('.product-dashed-heading').children('.sign').html() == "+")
                jQuery('.product-dashed-heading').children('.sign').html('-');
            else
                jQuery('.product-dashed-heading').children('.sign').html('+');
        });     
    });
    
    jQuery('.product-dashed-heading1').click(function () {      
        jQuery('#produpdates').slideToggle('fast', function () {
            if (jQuery('.product-dashed-heading1').children('.sign').html() == "+")
                jQuery('.product-dashed-heading1').children('.sign').html('-');
            else
                jQuery('.product-dashed-heading1').children('.sign').html('+');
        }); 
    });
    
    jQuery('.product-dashed-heading2').click(function () {
        jQuery('#prodbuyers').slideToggle('fast', function () {
            if (jQuery('.product-dashed-heading2').children('.sign').html() == "+")
                jQuery('.product-dashed-heading2').children('.sign').html('-');
            else
                jQuery('.product-dashed-heading2').children('.sign').html('+');
        });
    });*/
	// Purchase form scripts
    var btnName = jQuery("#button_pay_purchase").attr('name');
    var payChoices = jQuery("#payment-choices a").size();
    var level = jQuery('#form_pay').data('level');

    if (payChoices > 1) {
    	jQuery("#button_pay_purchase").attr('disabled', 'disabled');
        jQuery('.pay-choice').click(function(e) {
            e.preventDefault();
            if (jQuery(this).attr('id').indexOf('pay-with-paypal') !== -1) {
                jQuery('#pay-with-paypal').addClass("active");
                jQuery("input.main-btn").attr("name", btnName).val('Pay with Paypal');
                jQuery("#button_pay_purchase").removeAttr('disabled');
            }
            else {
                jQuery('#pay-with-paypal').removeClass("active");
            }
            jQuery(document).trigger('payChoiceSelect', jQuery(this).attr('id'));
        });
    }
    else {
        jQuery('#form_pay #pay-with-paypal').addClass("active");
        //jQuery("input.main-btn").attr("name", btnName).val('Pay with Paypal');
        jQuery("#form_pay #payment-choices").hide();
    }
    var proj_type = jQuery("#project_type").val();
    if (proj_type) {
	    if (proj_type == 'pwyw') {
	        var price_entry = jQuery("#price_entry").val();
	        jQuery('.preorder-form-product-price').html(price_entry);
	        jQuery('input[name="price"]').val(price_entry);
	        jQuery("#price_entry").change(function() {
	            price_entry = jQuery("#price_entry").val();
	            var cleanPrice = price_entry.replace(/[A-Za-z$-]/g, "");
	            jQuery(document).trigger('priceChange', cleanPrice);
	        });
	        jQuery(".main-btn").click(function(e) {
	            if (price_entry < 0.99) {
	                e.preventDefault();
	                jQuery("#price_entry").addClass("red-border");
	                alert('Please enter a contribution value greater than 0.99.');
	            }
	            else {
	                jQuery("#price_entry").removeClass("red-border");
	            }
	        });
	    }
	    else {
	        jQuery('#level_select').ddslick({
	            selectText: "Choose your level of support",
	            onSelected: function(selectedData){
	                //callback function: do something with selectedData;
	                //console.log(selectedData);
	                price = selectedData['selectedData']['price'];
	                desc = selectedData['selectedData']['description'];
	                selLevel = selectedData['selectedData']['value'];
	                jQuery(document).trigger('levelChange', price);
	                jQuery('.preorder-form-product-price').text(price);
	                jQuery('.id-checkout-level-desc').html(desc);
	                jQuery('input[name="price"]').val(price);
	                jQuery('input[name="desc"]').val(desc);
	                jQuery('input[name="level"]').val(selLevel);
	            }   
	        });
	        if (level && level > 0) {
	        	jQuery('#level_select').ddslick('select', {index: level - 1});
	        }
	        jQuery(".noclick").click(function(e) {
	            e.preventDefault();
	            jQuery("a.dd-option:first-child").select();
	        });
	    
	        /*jQuery('#level_select').change(function () {
	            var price = jQuery(this).find(':selected').attr('price');
	            //alert(price);
	            jQuery('input[name="price"]').val(price);
	            jQuery('.preorder-form-product-price').html(price);
	        });

	        jQuery('#level_select').change(function() {
	            var desc = jQuery(this).find(':selected').attr('desc');
	            //alert(desc);
	            jQuery('input[name="desc"]').val(desc);
	            jQuery('.id-checkout-level-desc').html(desc);
	        });*/
	    }
	}
	jQuery(document).bind('priceChange', function(e, cleanPrice) {
		jQuery('input[name="price"]').val(cleanPrice);
	    jQuery('.preorder-form-product-price').html(cleanPrice);
	    jQuery(document).trigger('priceChangeComplete', cleanPrice);
	});
    jQuery('#message-container .error .close-notification').click(function(e) {
        e.preventDefault();
        jQuery('#message-container').hide();
    });
    jQuery("#button_pay_purchase").click(function(e) {
    	if (btnName.indexOf('Paypal') == -1 && btnName.indexOf('Preapproval') == -1 && btnName.indexOf('Adaptive') == -1 && btnName.indexOf('Popup') == -1) {
        	e.preventDefault();
        }
        var price = parseInt(jQuery('input[name="price"]').val());
        if (price > 0) {
	        var type = jQuery('#form_pay').data('projectType');
	        var level = jQuery('input[name="level"]').val();
	        var post_id = jQuery('#form_pay').data('postid');
	        var project = jQuery('#form_pay').data('projectid');
	        var url = id_siteurl;
	        jQuery(".ign-checkout-button .main-btn").val('Processing...');
	        jQuery(this).attr('disabled', 'disabled');
	        var widgetClass = jQuery('.id-purchase-form div:first-child').attr('id');
	        var validate = checkIgnitionDeckForm(widgetClass, proj_type, level, post_id, project, url);
	        if (validate == true) {
	        	jQuery(this).removeAttr('disabled');
	            jQuery(document).trigger('validate', true);
	            if (jQuery("#button_pay_purchase").attr('name') == btnName) {
	                return true;
	            }
	        }
	        else {
	        	jQuery(this).removeAttr('disabled');
	            jQuery(".ign-checkout-button .main-btn").val('Continue Checkout');
	            jQuery(document).trigger('validate', false);
	        }
	    }
        return false;
    });
	/* Shortcode Grid Magic */

	if (jQuery('.grid_wrap').length > 0) {
		var wide = parseInt(jQuery('.grid_wrap').data('wide'));
		jQuery('.grid_item:nth-child(' + wide + 'n + ' + wide + ')').css('margin-right', 0);
		jQuery('.grid_item:nth-child(' + wide + 'n + ' + (wide + 1) + ')').css('clear', 'both');
	}
    /*jQuery(document).bind('validate', function(e, validated) {
        if (jQuery("#button_pay_purchase").attr('name') == btnName) {
            console.log('in here');
            jQuery("#form_pay").submit();
            return false;
        }
    });*/
});
function checkIgnitionDeckForm(formId, type, level, post_id, project, url){
	//first lets set price
	var keys = [{
		'level': level,
		'post_id': post_id,
		'project': project}];
	if (type !== 'pwyw') {
		jQuery.ajax({
			url: url + 'wp-admin/admin-ajax.php',
			type: 'POST',
			data: {action: 'id_validate_price', Keys: keys},
			success: function(res) {
				//console.log(res);
				jQuery('input[name="price"]').val(res);
			}
		});
	}
    
    //clear previous results
    jQuery('#'+ formId +' .required').removeClass('red-border');
    jQuery('#'+ formId +' .form-item-error-msg').remove();

    
    var result = true;
    jQuery('#'+ formId +' .required').each(function(){
        if(isEmpty(this)){
        	//console.log(this);
            jQuery(this).addClass('red-border');
            jQuery(this).after('<span class="form-item-error-msg"> required </span>');
            
            if(result){
               result= !result;
            }
        }
    });

    if(!isEmail(jQuery('#'+ formId +' .email').val())){
        jQuery('#'+ formId +' .email').addClass('red-border');
        jQuery('#'+ formId +' .email').after('<span class="form-item-error-msg"> invalid </span>');
        
        if(result){
           result= !result;
        }
    }
    //console.log(result);
    return result;
}

function isEmpty(element){
    if(element.value == ''){
        return true;
    }
    return false;
}

function isEmail(email){
    var emailfilter=/^\w+[\+\.\w-]*@([\w-]+\.)*\w+[\w-]*\.([a-z]{2,4}|\d+)$/i;
    return emailfilter.test(email);
}

var app = {
    log: function(mixed){
//        console.log(mixed);
    }
}

app.popupwindow = function(url, title, w, h){
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}

//ONLOAD function to call after the window has been loaded
window.onload = function () {
	
	//================================================================================================
	// Add New Project page popup for inserting a Project URL
	//================================================================================================
	// For Add New Project page to fill the text box with a link of the post, page or ID project page
	// probably unused
	jQuery('.post-link-filler').click(function () {
		var page_link = jQuery(this).attr('href');
		//alert("page_link: " + page_link);
		jQuery('[name=link-insert-box]').val(page_link);
		return false;
	});
	
	jQuery('#insert-project-link #search_page').keypress(function () {
		var ajax_url = jQuery('#insert-project-link span[url]').attr('url');
		jQuery.ajax({
			type: "POST",
			url: ajax_url,
			data: "action=" + 'get_pages_links'
			+ "&page_title=" + jQuery(this).val()
			,
			success: function(html) {						
				//alert(jQuery.trim(html));
				jQuery('#insert-project-link .pages-links').html(jQuery.trim(html));
				jQuery('.post-link-filler').click(function () {
					var page_link = jQuery(this).attr('href');
					jQuery('[name=link-insert-box]').val(page_link);
					return false;
				});
			}
		});
	});
	
	jQuery('#insert-project-link #btnTransferLink').click(function () {
		jQuery('.product-url-container').val(jQuery('#link-insert-box').val());
		jQuery.fancybox.close();
	});
	//================================================================================================
};

// Submit form function for Purchasing product
// probably unused
function submitPurchaseForm(ajax_url) {
	//var dgFlow = new PAYPAL.apps.DGFlow({ trigger: 'submitBtn' });
	//jQuery('#submitBtn').trigger('click');
	//return false;
	//jQuery('#btnPayKey').attr('style','background: url("../images/loading.gif") no-repeat scroll top right transparent;');
	jQuery('#btnPayKey').val('Processing Payment...');
	jQuery.ajax({
		type: "POST",
		url: ajax_url,
		data: "action=" + 'get_paypal_paykey'
		+ "&" + jQuery('#form_pay').serialize()
		,
		success: function(html) {						
			//alert(jQuery.trim(html));
			//console.log(jQuery.trim(html));
			reply = jQuery.trim(html).split("|");
			if (reply[0] == "success") {
				//console.log(reply[1]);
				//jQuery('#pay_form_embedded').attr("action",reply[2]);
				jQuery('#paykey').val(reply[1]);
				var dgFlow = new PAYPAL.apps.DGFlow({ trigger: 'submitBtn' });
				jQuery('#submitBtn').trigger('click');
			} else {
				//console.log("Error: "+reply[1]);
				jQuery('#btnPayKey').val('Make Payment');
				jQuery('#message-container').html(	'<div class="notification error">' +
									'<a href="#" class="close-notification" title="Hide Notification" rel="tooltip">x</a>' +
									'<p><strong>Payment Error</strong> '+ reply[1] +'</p>' +
								'</div>');
				jQuery('#message-container').show();
			}
		}
	});
	
	return false;
}