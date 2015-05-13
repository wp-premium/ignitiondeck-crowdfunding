jQuery(document).ready(function() {
	jQuery(document).bind('idc_lightbox_general', function(e) {
		var selLevel = jQuery('.idc_lightbox:visible select[name="level_select"]').val();
		var levelDesc = jQuery('.idc_lightbox:visible select[name="level_select"] :selected').data('desc');
		var levelPrice = jQuery('.idc_lightbox:visible select[name="level_select"] :selected').data('price');
		jQuery('.idc_lightbox:visible .text p').text(levelDesc);
		jQuery('.idc_lightbox input[name="total"]').val(levelPrice);
		jQuery('.idc_lightbox:visible span.total').data('value', levelPrice).text(levelPrice);
	});
	jQuery('.idc_lightbox select[name="level_select"]').change(function(e) {
		if (jQuery(this).has(':visible')) {
			//console.log(e);
			selLevel = jQuery(this).val();
			levelDesc = jQuery('.idc_lightbox:visible select[name="level_select"] :selected').data('desc');
			levelPrice = jQuery('.idc_lightbox:visible select[name="level_select"] :selected').data('price');
			jQuery('.idc_lightbox:visible .text p').text(levelDesc);
			jQuery('.idc_lightbox input[name="total"]').val(levelPrice);
			jQuery('.idc_lightbox:visible span.total').data('value', levelPrice).text(levelPrice);
		}
	});
	jQuery(document).bind('idc_lightbox_level_select', function(e, clickLevel) {
		selLevel = jQuery('.idc_lightbox:visible select[name="level_select"] option').eq(clickLevel).val();
		levelDesc = jQuery('.idc_lightbox:visible select[name="level_select"] option').eq(clickLevel).data('desc');
		levelPrice = jQuery('.idc_lightbox:visible select[name="level_select"] option').eq(clickLevel).data('price');
		jQuery('.idc_lightbox:visible .text p').text(levelDesc);
		jQuery('.idc_lightbox input[name="total"]').val(levelPrice);
		jQuery('.idc_lightbox:visible span.total').data('value', levelPrice).text(levelPrice);
	});
});