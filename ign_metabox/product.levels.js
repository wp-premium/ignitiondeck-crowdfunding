//jQuery.noConflict();
jQuery(document).ready(function(e) {
	jQuery('span[addlevel]').click(function () {
		var element_number = parseInt(jQuery('div[levels]').attr('levels')) + 1;
		jQuery('div[levels]').attr('levels', element_number);
		jQuery('div[levels]').append('<div level="'+element_number+'">' +
										'$ <input class="cmb_text_money" type="text" name="levels[][price]" id="ign_level'+element_number+'" value="" /><span class="cmb_metabox_description">Goal for level 1</span>' +
										'<textarea name="levels[][description]" id="ign_level'+element_number+'desc" cols="60" rows="4" style="width:97%"></textarea><span class="cmb_metabox_description">Description for level 1</span>' +
									'<div>');
	});
	
	jQuery('span[deletelevel]').click(function () {
		var element_number = parseInt(jQuery('div[levels]').attr('levels'));
		jQuery('div[level="'+element_number+'"]').remove();
	});
});