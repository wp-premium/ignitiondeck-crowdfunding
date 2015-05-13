<?php
class ID_Purchase_Form {
	var $project_id;
	function __construct(
		$project_id = null
		) {
		$this->project_id = $project_id;
	}

	function id_purchase_form() {
		$project = new ID_Project($this->project_id);
		$the_project = $project->the_project();
		$project_id = $this->project_id;
		$post_id = $project->get_project_postid();
		$project_settings = $project->get_project_settings();
		if (empty($prod_settings)) {
			$defaults = $project->get_project_defaults();
			$project_settings = $defaults;
		}
		$no_levels = get_post_meta( $post_id, "ign_product_level_count", true );
		$project_type = get_post_meta($post_id, 'ign_project_type', true);
		$project_disc = html_entity_decode(get_post_meta( $post_id, "ign_disclaimer", true ));
		$project_desc = get_post_meta( $post_id, "ign_product_level_1_desc", true );
		$level_data = $project->get_level_data($post_id, $no_levels);
		// level one stuff
		$level_one_data = new stdClass;
		$is_level_invalid = getLevelLimitReached($this->project_id, $post_id, 1);
		$meta_title = $the_project->ign_product_title;
		$meta_price = get_post_meta( $post_id, "ign_product_price", true );
		$meta_desc = $the_project->product_details;
		$meta_order = get_post_meta($post_id, 'ign_projectmeta_level_order', true);
		$level_one_data->is_level_invalid = $is_level_invalid;
		$level_one_data->meta_title = $meta_title;
		$level_one_data->meta_price = $meta_price;
		$level_one_data->meta_desc = $meta_desc;
		$level_one_data->meta_order = $meta_order;
		$level_one_data->id = 1;
		// add level one to object
		array_unshift($level_data, $level_one_data);
		$custom_level_order = get_post_meta($post_id, 'custom_level_order', true);
		if ($custom_level_order) {
			usort($level_data, 'ID_Project::level_sort');
		}
		//GETTING the currency symbols
		$currencyCodeValue = $project_settings->currency_code;	
		$cCode = setCurrencyCode($currencyCodeValue);

		//GETTING the form settings
		$form_settings = getProductFormSettings($project_id);

		if (!isset($form_settings)) {
			$form_settings = $project_settings->form_settings;
			$form_settings = unserialize($form_settings);
		}
		else {
			$form_settings = unserialize($form_settings);
		}

		$form_id = rand(21927391, 92817275);

		// Getting the Payment method first, to load the appropriate URLs if needed and
		// submit button name
		// this is paypal code that could be removed if disabled
		$payment_method = getDefaultPaymentMethod();

		if (isset($payment_method) && $payment_method->payment_gateway == "adaptive_paypal") {
			$adaptive_pay_settings = getAdaptivePayPalSettings();
			if (isset($adaptive_pay_settings)) {
			
				if ($adaptive_pay_settings->paypal_mode == "sandbox") {
					$paypal_address = "https://www.sandbox.paypal.com/webapps/adaptivepayment/flow/pay";
				}
				else {
					$paypal_address = "https://www.paypal.com/webapps/adaptivepayment/flow/pay";
				}
				if ($adaptive_pay_settings->fund_type == 'fixed') {
					$submit_btn_name = 'btnSubmitPreapproval';
				}
				else {
					$submit_btn_name = "submitPaymentAdaptive";
				}
			}
		} 

		else {
			$submit_btn_name = "submitPaymentPopup";		//will be caught by standard payments function for Paypal
		}

		$purchase_form = new stdClass;
		$purchase_form->project_id = $project_id;
		$purchase_form->post_id = $post_id;
		$purchase_form->the_project = $the_project;
		$purchase_form->project_settings = $project_settings;
		$purchase_form->no_levels = $no_levels;
		$purchase_form->level_data = $level_data;
		$purchase_form->project_type = $project_type;
		$purchase_form->project_desc = $project_desc;
		$purchase_form->project_disc = $project_disc;
		$purchase_form->currencyCodeValue = $currencyCodeValue;
		$purchase_form->cCode = $cCode;
		$purchase_form->form_settings = $form_settings;
		$purchase_form->form_id = $form_id;
		if (isset($paypal_address)) {
			$purchase_form->paypal_address = $paypal_address;
		}
		$purchase_form->submit_btn_name = $submit_btn_name;
		if (isset($adaptive_pay_settings)) {
			$purchase_form->adaptive_pay_settings = $adaptive_pay_settings;
		}
		return $purchase_form;
	}
}
?>