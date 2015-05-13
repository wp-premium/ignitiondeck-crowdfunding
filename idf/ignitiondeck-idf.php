<?php

add_action('init', 'idf_verify_platform', 1);

function idf_verify_platform() {
	$platform = idf_platform();

	if ($platform !== 'legacy') {
		remove_shortcode('project_purchase_form');
	}

	if ($platform == 'wc' && class_exists('WooCommerce')) {
		add_action('add_meta_boxes', 'idwc_project_pairing');
		add_action('wp', 'idwc_level_links');
		add_action('wp', 'idwc_project_redirect');
		add_action('woocommerce_order_status_changed', 'idwc_insert_order', 1, 3);
		add_action('before_delete_post', 'idwc_delete_order', 1, 1);
	}
	else if ($platform == 'edd' && class_exists('Easy_Digital_Downloads')) {
		add_action( 'add_meta_boxes', 'idedd_project_pairing');
		remove_shortcode('project_purchase_form');
		add_action('wp', 'idedd_level_links');
		add_action('wp', 'idedd_project_redirect');
		add_shortcode('project_purchase_form', 'idedd_swap_forms', 1);
		add_action('edd_insert_payment', 'idedd_insert_order', 5, 2);
		add_action('edd_update_edited_purchase', 'idedd_update_order', 5, 1);
		add_action('edd_complete_purchase', 'idedd_complete_order', 5, 1);
		add_action('before_delete_post', 'idedd_delete_order', 5, 1);
		// can we de-register the scripts and links from the template?
	}
	else if ($platform == 'itexchange' && class_exists('IT_Exchange')) {
		add_filter('id_postmeta_boxes', 'iditexch_project_pairing');
		add_action('save_post', 'save_iditexch_project_pairing', 10, 3);
		add_action('load-post.php', 'iditexch_project_loaded');
		add_action('wp', 'iditexch_project_redirect');
		add_filter('id_product_levels_html_admin', 'iditexch_project_levels_filter', 10, 3);
		add_action('it_exchange_add_transaction_success', 'iditexch_insert_order');
		add_action('it_exchange_update_transaction', 'iditexch_update_order');
		add_action('it_exchange_update_transaction_status', 'iditexch_complete_order', 10, 4);
		add_action('before_delete_post', 'iditexch_delete_order', 5);
	}
	// now we load the general functions that apply to all frameworks
	add_action('id_widget_after', 'idcf_level_select_lb', 10, 2);
}

/*
IDF WC Integration
1. Update orders
*/



function idwc_project_pairing() {
	add_meta_box("idwc_project_pairing", __("WooCommerce Shortcode", "ignitiondeck"), "set_idwc_project_pairing", "ignition_product", "side", "default");
}

function set_idwc_project_pairing($post) {
	// Add an nonce field so we can check for it later.
  	wp_nonce_field( 'set_idwc_project_pairing', 'set_idwc_project_pairing_nonce' );
  	$value = get_post_meta($post->ID, 'idwc_project_pairing', true);
	$fields = array(
		array(
			'before' => '<p>'.__('If matching IgnitionDeck project to WooCommerce products, please enter the product ID here.', 'ignitiondeck').__('Otherwise, leave this field blank.', 'ignitiondeck').'</p>',
			'label' => __('Product ID', 'ignitiondeck'),
			'value' => (isset($value) ? $value : ''),
			'name' => 'idwc_project_pairing',
			'type' => 'number'
		)
	);
	$form = new ID_Form($fields);
	echo apply_filters('idwc_project_pairing_form', $form->build_form());
}

add_action('save_post', 'save_idwc_project_pairing');

function save_idwc_project_pairing($post_id) {
	if (!isset($_POST['set_idwc_project_pairing_nonce'])) {
		return $post_id;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	$value = esc_attr($_POST['idwc_project_pairing']);
	update_post_meta($post_id, 'idwc_project_pairing', $value);
	$project_id = get_post_meta($post_id, 'ign_project_id', true);
	update_post_meta($value, '_wc_project_pairing', $project_id);
}

function remove_id_purchaseform() {
	return null;
}

function idwc_level_links() {
	$project_id = is_id_project();
	if (!empty($project_id)) {
		$project = new ID_Project($project_id);
		$post_id = $project->get_project_postid();
		$wc_product = get_post_meta($post_id, 'idwc_project_pairing', true);
		if (!empty($wc_product)) {
			$level_count = get_post_meta($post_id, 'ign_product_level_count', true);
			for ($i = 0; $i <= $level_count - 1; $i++) {
				$level_id = $i + 1;
				add_filter('id_level_'.$level_id.'_link', function($link, $project_id) use ($post_id, $wc_product, $i) {
					//$wc_product = get_post_meta($post_id, 'idwc_project_pairing', true);
					$product = new WC_Product_Variable($wc_product);
					if (!empty($product)) {
						if (!isset($variations)) {
							$variation_array = array();
							$variations = $product->get_available_variations();
							for ($j = 0; $j <= count($variations) - 1; $j++) {
								$variation_array[] = $variations[$j]['variation_id'];
							}
						}
						$link_id = $variation_array[$i];
					}
					if (isset($link_id)) {
						global $woocommerce;
						$checkout_url = $woocommerce->cart->get_checkout_url();
						$link = $checkout_url.'?add-to-cart='.$link_id;
					}
					return $link;
				} , 1, 2);
			}
		}
	}
}

function idwc_project_redirect() {
	global $post;
	if (isset($post) && $post->post_type == 'product' && is_singular()) {
		$post_id = $post->ID;
		$project_id = get_post_meta($post_id, '_wc_project_pairing', true);
		if (!empty($project_id)) {
			$project = new ID_Project($project_id);
			$post_id = $project->get_project_postid();
			if (!empty($post_id)) {
				$url = get_permalink($post_id);
				header('Location: '.$url);
			}
		}
	}
}

function idwc_insert_order($order_id, $old_status, $new_status) {
	// we need to run this on status update, edit, and delete
	// this used EDD payment_id as txn_id rather than gateway txn_id
	$items = idwc_order_items($order_id);
	$id_orders = array();
	foreach ($items as $item) {
		$qty = $item['qty'];
		for ($i = 1; $i <= $qty; $i++) {
			$vars = idwc_payment_vars($item, $new_status, $order_id, $i);
			$txn_id = $vars['transaction_id'];
			$existing_txn = ID_Order::get_order_by_txn($txn_id);
			if (empty($existing_txn)) {
				$rc = new ReflectionClass('ID_Order');
				$order = $rc->newInstanceArgs($vars);
				$pay_id = $order->insert_order();
				$id_orders[] = $pay_id;
			}
			else {
				if ($existing_txn->transaction_id !== $new_status) {
					$vars['id'] = $existing_txn->id;
					$rc = new ReflectionClass('ID_Order');
					$order = $rc->newInstanceArgs($vars);
					$update = $order->update_order();
				}
			}
		}
		update_post_meta($order_id, '_wc_order_pairing', $id_orders);
	}
}

function idwc_order_items($order_id) {
	$items = array();
	$order = new WC_Order($order_id);
	if (!empty($order)) {
		$items = $order->get_items();
	}
	return $items;
}

function idwc_payment_vars($item, $status, $order_id, $qty_num) {
	$order = new WC_Order($order_id);
	if (isset($item['product_id'])) {
		$product_id = $item['product_id'];
	}
	if (isset($item['variation_id'])) {
		$variation_id = $item['variation_id'];
	}
	if (isset($product_id)) {
		$project_id = get_post_meta($product_id, '_wc_project_pairing', true);
		if (isset($project_id)) {
			$product = new WC_Product_Variable($product_id);
			if (!empty($product)) {
				$variations = $product->get_available_variations();
				$v_array = array();
				foreach ($variations as $variant) {
					$v_array[] = $variant['variation_id'];
				}
				$level = array_search($variation_id, $v_array) + 1;
			}

			$first_name = get_post_meta($order_id, '_billing_first_name', true);
			$last_name = get_post_meta($order_id, '_billing_last_name', true);
			$email = get_post_meta($order_id, '_billing_email', true);
			$address = get_post_meta($order_id, '_billing_address_1', true);
			$city = get_post_meta($order_id, '_billing_city', true);
			$state = get_post_meta($order_id, '_billing_state', true);
			$zip = get_post_meta($order_id, '_billing_postcode', true);
			$country = get_post_meta($order_id, '_billing_country', true);
			$transaction_id = get_post_meta($order_id, '_order_key', true);
			$price = get_post_meta($variation_id, '_price', true);
			$date = $order->order_date;

			$vars = array(
				'id' => null,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'address' => $address,
				'state' => $state,
				'city' => $city,
				'zip' => $zip,
				'country' => $country,
				'product_id' => $project_id,
				'transaction_id' => $transaction_id.'-v'.$variation_id.'-'.$qty_num,
				'preapproval_key' => '',
				'product_level' => $level,
				'prod_price' => $price,
				'status' => $status,
				'created_at' => $date
			);
		}
	}
	return (isset($vars) ? $vars : array());
}

function idwc_delete_order($post_id) {
	$orders = get_post_meta($post_id, '_wc_order_pairing', true);
	if (!empty($orders)) {
		foreach ($orders as $order) {
		//$order = ID_Order::get_order_by_txn($payment_id);
			//if (!empty($order)) {
				//$pay_id = $order->id;
				$delete = ID_Order::delete_order($order);
			//}
		}
	}
}

function idedd_project_pairing() {
	add_meta_box("idedd_project_pairing", __("EDD Download ID", "ignitiondeck"), "set_idedd_project_pairing", "ignition_product", "side", "default");
}

function set_idedd_project_pairing($post) {
	// Add an nonce field so we can check for it later.
  	wp_nonce_field( 'set_idedd_project_pairing', 'set_idedd_project_pairing_nonce' );
  	$value = get_post_meta($post->ID, 'idedd_project_pairing', true);
	$fields = array(
		array(
			'before' => '<p>'.__('If matching IgnitionDeck project to Easy Digital Downloads, please enter the download ID here.', 'ignitiondeck').__('Otherwise, leave this field blank.', 'ignitiondeck').'</p>',
			'label' => __('Download ID', 'ignitiondeck'),
			'value' => (isset($value) ? $value : ''),
			'name' => 'idedd_project_pairing',
			'type' => 'number'
		)
	);
	$form = new ID_Form($fields);
	echo apply_filters('idedd_project_pairing_form', $form->build_form());
}

add_action('save_post', 'save_idedd_project_pairing');

function save_idedd_project_pairing($post_id) {
	if (!isset($_POST['set_idedd_project_pairing_nonce'])) {
		return $post_id;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	$value = esc_attr($_POST['idedd_project_pairing']);
	update_post_meta($post_id, 'idedd_project_pairing', $value);
	$project_id = get_post_meta($post_id, 'ign_project_id', true);
	update_post_meta($value, '_edd_project_pairing', $project_id);
}

function idedd_level_links() {
	$project_id = is_id_project();
	if (!empty($project_id)) {
		$project = new ID_Project($project_id);
		$post_id = $project->get_project_postid();
		$level_count = get_post_meta($post_id, 'ign_product_level_count', true);
		$edd = get_post_meta($post_id, 'idedd_project_pairing', true);
		if (!empty($edd)) {
			$edd_prices = edd_get_variable_prices($edd);
			$checkout_url = edd_get_checkout_uri();
			$start_value = (!empty($edd_prices[0]) ? 0 : 1);
			if ($start_value == 0) {
				$level_count = absint($level_count - 1);
			}
			for ($i = $start_value; $i <= $level_count; $i++) {
				$level_id = ($start_value == 0 ? absint($i + 1) : $i);
				add_filter('id_level_'.$level_id.'_link', function($link, $project_id) use ($checkout_url, $edd, $i) {
					// start here
					$link = $checkout_url.'?edd_action=add_to_cart&download_id='.$edd.'&edd_options[price_id]='.$i;
					return $link;
				} , 1, 2);
			}
		}
	}
}

function idedd_project_redirect() {
	global $post;
	if (isset($post) && $post->post_type == 'download' && is_singular()) {
		$post_id = $post->ID;
		$project_id = get_post_meta($post_id, '_edd_project_pairing', true);
		if (!empty($project_id)) {
			$project = new ID_Project($project_id);
			$post_id = $project->get_project_postid();
			if (!empty($post_id)) {
				$url = get_permalink($post_id);
				header('Location: '.$url);
			}
		}
	}
}

function idedd_swap_forms($attrs) {
	if (isset($attrs['product'])) {
		$project_id = absint($attrs['product']);
	}
	if (isset($_GET['prodid'])) {
		$project_id = absint($_GET['prodid']);	
	}
	if (isset($project_id) && $project_id > 0) {
		$project = new ID_Project($project_id);
		$post_id = $project->get_project_postid();
		if (isset($post_id) && $post_id > 0) {
			$download_id = get_post_meta($post_id, 'idedd_project_pairing', true);
			if (!empty($download_id) && $download_id > 0) {
				$text = __('Checkout', 'ignitiondeck');
				return do_shortcode('[purchase_link id="'.$download_id.'" text="'.$text.'"]');
			}
		}
	}
	return;
}

function idedd_insert_order($payment_id) {
	// we need to run this on status update, edit, and delete
	// this used EDD payment_id as txn_id rather than gateway txn_id
	$paymeta = get_post_meta($payment_id, '_edd_payment_meta', true);
	if (is_array($paymeta)) {
		$downloads = maybe_unserialize($paymeta['downloads']);
		if (is_array($downloads)) {
			$count = 1;
			$id_orders = array();
			foreach ($downloads as $download) {
				$download_id = $download['id'];
				$level = $download['options']['price_id'] + 1;
				$qty = $download['quantity'];
				for ($i = 1; $i <= $qty; $i++) {
					$vars = idedd_payment_vars($paymeta, $payment_id, $download_id, $level, $count, $i);
					$rc = new ReflectionClass('ID_Order');
					$order = $rc->newInstanceArgs($vars);
					$pay_id = $order->insert_order();
					$id_orders[] = $pay_id;
				}
				$count++;
			}
			update_post_meta($payment_id, '_edd_order_pairing', $id_orders);
		}
	}
}

function idedd_update_order($payment_id) {
	$paymeta = get_post_meta($payment_id, '_edd_payment_meta', true);
	if (is_array($paymeta)) {
		$downloads = unserialize($paymeta['downloads']);
		if (is_array($downloads)) {
			//print_r($downloads);
			$count = 1;
			foreach ($downloads as $download) {
				$download_id = $download['id'];
				$level = $download['options']['price_id'] + 1;
				$qty = $download['quantity'];
				for ($i = 1; $i <= $qty; $i++) {
					$vars = idedd_payment_vars($paymeta, $payment_id, $download_id, $level, $count, $i);
					if (!empty($vars)) {
						$rc = new ReflectionClass('ID_Order');
						$order = $rc->newInstanceArgs($vars);
						$check = $order->check_new_order($vars['transaction_id']);
						if (isset($check)) {
							$pay_id = $check->id;
							if (isset($pay_id) && $pay_id > 0) {
								$vars['id'] = $pay_id;
								$rc = new ReflectionClass('ID_Order');
	       						$order = $rc->newInstanceArgs($vars);
	        					$update = $order->update_order();
							}
						}
					}
				}
				$count++;
			}
		}
	}
}

function idedd_complete_order($payment_id) {
	$orders = get_post_meta($payment_id, '_edd_order_pairing', true);
	if (!empty($orders)) {
		foreach ($orders as $order) {
			$update = setOrderStatus('C', $order);
		}
	}
}

function idedd_delete_order($post_id) {
	$orders = get_post_meta($post_id, '_edd_order_pairing', true);
	if (!empty($orders)) {
		foreach ($orders as $order) {
		//$order = ID_Order::get_order_by_txn($payment_id);
			//if (!empty($order)) {
				//$pay_id = $order->id;
				$delete = ID_Order::delete_order($order);
			//}
		}
	}
}

function idedd_payment_vars($paymeta, $payment_id, $download_id, $level, $count, $qty_num) {
	$vars = array();
	$post = get_post($payment_id);
	if (isset($post)) {
		$status = strtoupper(substr($post->post_status, 0, 1));
		$date = $post->post_date;
	}
	else {
		$status = 'P';
	}
	if (is_array($paymeta['user_info'])) {
		// strange but seems that after editing, it saves as array and not serialized array
		$user_info = $paymeta['user_info'];
	}
	else {
		$user_info = unserialize($paymeta['user_info']);
	}
	if (isset($user_info['first_name'])) {
		$first_name = $user_info['first_name'];
	}
	else {
		$first_name = '';
	}
	if (isset($user_info['last_name'])) {
		$last_name = $user_info['last_name'];
	}
	else {
		$last_name = '';
	}
	if (isset($user_info['email'])) {
		$email = $user_info['email'];
	}
	else {
		$email = '';
	}
	$cart_details = maybe_unserialize($paymeta['cart_details']);
	$price_array = edd_get_variable_prices($download_id);
	$price = $price_array[$level - 1]['amount'];
	if (!isset($date)) {
		$date = null;
	}
	if (isset($download_id) && $download_id > 0) {
		$project_id = get_post_meta($download_id, '_edd_project_pairing', true);
		if (isset($project_id) && $project_id > 0) {
			$project = new ID_Project($project_id);
			$the_project = $project->the_project();
			$vars = array(
				'id' => null,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'address' => '',
				'state' => '',
				'city' => '',
				'zip' => '',
				'country' => '',
				'product_id' => $project_id,
				'transaction_id' => $payment_id.'-v'.$level.'-'.$count.'-'.$qty_num,
				'preapproval_key' => '',
				'product_level' => $level,
				'prod_price' => $price,
				'status' => $status,
				'created_at' => $date
			);
		}
	}
	return (isset($vars) ? $vars : array());
}

/********************************************************************************************************
 * Functions for iThemes Exchange integration
 ********************************************************************************************************/
/**
 * Filter function to add textbox in level for integrating with Exchange product
 */
function iditexch_project_pairing($meta_boxes) {
	$fields = $meta_boxes[0]['fields'];
	$new_fields = array();
	for ($i=0, $j=0 ; $i < count($fields) ; $i++, $j++) { 
		$new_fields[$j] = $fields[$i];
		// checking if the field is level title, then adding below it the textbox for exchange product
		if (isset($fields[$i]['id'])) {
			if ($fields[$i]['id'] == 'ign_product_title') {
				$j++;
				$new_fields[$j] = array(
					'name' => __('Exchange Product ID', 'ignitiondeck'),
					'id' => 'iditexch_level_pairing_1',
					'desc' => __('For integrating IgnitionDeck project level with iThemes Exchange product', 'ignitiondeck'),
					'class' => 'ign_projectmeta_reward_title',
					'show_help' => true,
					'type' => 'text'
				);
			}
		}
	}
	$meta_boxes[0]['fields'] = $new_fields;
	return $meta_boxes;
}

/**
 * Action called after ID Project saved to store the iT Exchange product with each level
 */
function save_iditexch_project_pairing($post_id, $post, $update) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// If this isn't a 'it_exchange_prod' post, don't update it.
    if ($post->post_type !== 'ignition_product') {
        return;
    }
    if (isset($_POST['level'])) {
		// Now saving all the levels' iT exchange product ids
		$levels = $_POST['level'];
		// Setting this variable to store level by level iditexch_level_pairing id
		$j = 2;
		$project_id = get_post_meta($post_id, 'ign_project_id', true);
		if (!empty($project_id)) {
			// storing 1st level's iT exchange product id
			update_post_meta($post_id, 'iditexch_level_pairing_1', absint($_POST['iditexch_level_pairing_1']));
			// Storing project_id against 1st level itExch product_id
			update_post_meta(absint($_POST['iditexch_level_pairing_1']), '_itexch_project_pairing', $project_id);
			// Now storing rest of the levels starting from level = 2
			foreach ($levels as $level) {
				// Storing pairing info in post meta
				update_post_meta($post_id, 'iditexch_level_pairing_' . $j, absint($level['iditexch_product_id']));
				// Storing project id as well against post_id of iT exhange product for later use
				update_post_meta(absint($level['iditexch_product_id']), '_itexch_project_pairing', $project_id);
				$j++;
			}
		}
	}
}

/**
 * Action called after WP object creation to redirect user to appropriate ID Project page
 */
function iditexch_project_redirect() {
	global $post;
	if (isset($post) && $post->post_type == 'it_exchange_prod' && is_singular()) {
		if (!isset($_GET['iditexch-solid'])) {
			$post_id = $post->ID;
			$project_id = get_post_meta($post_id, '_itexch_project_pairing', true);
			if (!empty($project_id)) {
				$project = new ID_Project($project_id);
				$post_id = $project->get_project_postid();
				if (!empty($post_id)) {
					$url = get_permalink($post_id);
					header('Location: '.$url);
				}
			}
		}
	}
}

/**
 * Action called when a post is edited
 */
function iditexch_project_loaded() {
	global $typenow;
	if ($typenow == "ignition_product") {
		wp_enqueue_script('idf-admin');
		wp_localize_script('idf-admin', 'idf_edit_project', '1');
		// echo '<script>alert("localizing variable")</script>';
	}
}

/**
 * Filter for project levels (more than 1) on project edit to add the field for iT Exch product
 */
function iditexch_project_levels_filter($levels_html, $levels_count, $post_id) {
	if ($levels_count > 0) {
		// Starting from level 2, as 1 is already there
		$field_index = 2;
		// Loop should run for all levels except level 1
		for ($i=0 ; $i < ($levels_count - 1) ; $i++) { 
			$iditexch_project_id = get_post_meta($post_id, 'iditexch_level_pairing_' . $field_index, true);
			$levels_html .= '<div class="iditexch-moveable-fields" level="'.$field_index.'">'.
								'<label for="iditexch_product_id_'.$field_index.'">Exchange Product ID</label>'.
								'<div><input type="text" id="iditexch_product_id_'.$field_index.'" name="level['.$field_index.'][iditexch_product_id]" value="'.$iditexch_project_id.'"> </div>'.
							'</div>';
			$field_index++;
		}
	}
	return $levels_html;
}

/**
 * Action called after successful transaction from iT Exchange
 */
function iditexch_insert_order($transaction_id) {
	// Getting the products involved in the transaction/payment
	$products = it_exchange_get_transaction_products( $transaction_id );

	if (is_array($products)) {
		$id_orders = array();
		$count = 1;
		foreach ($products as $cart_id => $data) {
			$product_id = $data['product_id'];
			$qty = $data['count'];
			$price = $data['product_subtotal'];
			$vars = iditexch_payment_vars($transaction_id, $product_id, $price, $count);
			for ($i=0 ; $i < $qty ; $i++) {
				// Adding order separately for each number of product quantity
				$rc = new ReflectionClass('ID_Order');
				// Adding quantity number to transaction_id
				$vars['transaction_id'] = $vars['transaction_id']."-".$i;
				$order = $rc->newInstanceArgs($vars);
				$pay_id = $order->insert_order();
				$id_orders[] = $pay_id;
			}
			$count++;
		}
		update_post_meta($transaction_id, '_itexch_order_pairing', $id_orders);
	}
}

/**
 * Action called when a transaction order is updated from iT Exchange
 */
function iditexch_update_order($args) {
	$transaction_id = $args['ID'];
	// Getting the products involved in the transaction/payment
	$products = it_exchange_get_transaction_products( $transaction_id );	

	if (is_array($products)) {
		$id_orders = array();
		$count = 1;

		foreach ($products as $cart_id => $data) {
			$product_id = $data['product_id'];
			$qty = $data['count'];
			$price = $data['product_subtotal'];
			$vars = iditexch_payment_vars($transaction_id, $product_id, $price, $count);
			for ($i=0 ; $i < $qty ; $i++) {
				// Adding order separately for each number of product quantity
				$rc = new ReflectionClass('ID_Order');
				$order = $rc->newInstanceArgs($vars);
				$check = $order->check_new_order($vars['transaction_id']."-".$i);
				if (isset($check)) {
					$pay_id = $check->id;
					if (isset($pay_id) && $pay_id > 0) {
						$vars['id'] = $pay_id;
						$rc = new ReflectionClass('ID_Order');
						// Adding quantity number to transaction_id
						$vars['transaction_id'] = $vars['transaction_id']."-".$i;
						$order = $rc->newInstanceArgs($vars);
						$update = $order->update_order();
					}
				}
			}
			$count++;
		}
	}
}

/**
 * Action called when a transaction is completed from iT Exchange
 */
function iditexch_complete_order($transaction, $old_status, $old_status_cleared, $status) {
	$transaction_id = $transaction->ID;
	// Getting ID orders belonging to the that iT Exchange transaction
	$orders = maybe_unserialize(get_post_meta($transaction_id, '_itexch_order_pairing', true));
	if (!empty($orders)) {
		foreach ($orders as $order) {
			if (!empty($order)) {
				// If transaction is set to clear for delivery
				if ($status == 'Completed') {
					$update = setOrderStatus('C', $order);
				}
				else {
					$update = setOrderStatus('P', $order);
				}
			}
		}
	}
}

/**
 * Default WP action called after a post is deleted, for deleting ID project order when a transaction is deleted
 */
function iditexch_delete_order($post_id) {
	$orders = get_post_meta($post_id, '_itexch_order_pairing', true);
	if (!empty($orders)) {
		foreach ($orders as $order) {
			$delete = ID_Order::delete_order($order);
		}
	}
}

/**
 * Function to return the vars for adding new order for iT Exchange, ID integration
 */
function iditexch_payment_vars($transaction_id, $product_id, $price, $count) {
	// Getting post for date and status
	$post = get_post($transaction_id);
	if (isset($post)) {
		$date = $post->post_date;
		$is_paid = it_exchange_transaction_is_cleared_for_delivery(new IT_Exchange_Transaction($post));
		if ($is_paid) {
			$status = 'C';
		} else {
			$status = strtoupper(substr($post->post_status, 0, 1));
		}
	}
	// else {
	// 	$status = 'P';
	// }
	// Getting the shipping of transaction
	$transaction = get_post( $transaction_id );
	$shipping_method = it_exchange_get_transaction_shipping_method($transaction);
	// If we are not getting the shipping method, get user details from his id
	if (empty($shipping_method)) {
		$user_id = get_post_meta($transaction_id, '_it_exchange_customer_id', true);
		$user_info = get_userdata($user_id);

		$first_name = $user_info->first_name;
		$last_name = $user_info->last_name;
		$email = $user_info->user_email;
	}
	else {
		$first_name = $shipping_method['first-name'];
		$last_name = $shipping_method['last-name'];
		$email = $shipping_method['email'];
	}
	// Getting the level and ID project id from iT exchange product_id
	list($project_id, $level) = get_paired_level_from_itexch_product($product_id);

	$vars = array(
		'id' => null,
		'first_name' => $first_name,
		'last_name' => $last_name,
		'email' => $email,
		'address' => '',
		'state' => '',
		'city' => '',
		'zip' => '',
		'country' => '',
		'product_id' => $project_id,
		'transaction_id' => $transaction_id.'-v'.$level.'-'.$count,
		'preapproval_key' => '',
		'product_level' => $level,
		'prod_price' => $price,
		'status' => $status,
		'created_at' => $date
	);
	return $vars;
}

/**
 * Function to get the level of a ID project from iT exchange product
 */
function get_paired_level_from_itexch_product($product_id) {
	$project_id = get_post_meta($product_id, '_itexch_project_pairing', true);
	
	// For level, getting the post id of ID Project
	$project = new ID_Project($project_id);
	$post_id = $project->get_project_postid();

	// Getting all meta of this post, and getting level against iT Exchange product_id
	$post_meta = get_post_meta($post_id);
	foreach ($post_meta as $meta_key => $meta_value) {
		// If we are getting $product_id as value, check if the meta_key is similar to 'iditexch_level_pairing'
		if ($meta_value[0] == $product_id) {
			// Checking meta_key, if it's like 'iditexch_level_pairing', the get the level
			if (stristr($meta_key, 'iditexch_level_pairing') != false) {
				$level = str_replace('iditexch_level_pairing_', '', $meta_key);
			}
		}
	}

	return array($project_id, $level);
}

function idcf_level_select_lb($project_id, $the_deck = null) {
	//ob_start();
	//$project = new ID_Project($project_id);
	global $pwyw;
	if (isset($the_deck) && $the_deck->project_type !== 'pwyw') {
		$post_id = $the_deck->post_id;
		$image = idc_checkout_image($post_id);
		if (isset($the_deck->level_data)) {
			$level_data = $the_deck->level_data;
		}
		else {
			$level_data = new stdClass;
		}
		$purchase_url = getPurchaseURLfromType($project_id, 'purchaseform');
		$action = apply_filters('idcf_purchase_url', $purchase_url, $project_id);
		include ID_PATH.'/templates/_lbLevelSelect.php';
		//$content = ob_get_contents();
		//ob_end_flush();
		//echo $content;
	}
	return;
}

//add_action('wp', 'catch_idcf_level_select', 1);

function catch_idcf_level_select() {
	// note: we really need to get this down to a single function that can be re-used
	if (isset($_POST['lb_level_submit'])) {
		$project_id = absint($_POST['project_id']);
		$level = absint($_POST['level_select']);
		$price = esc_attr($_POST['total']);
		if (isset($project_id) && $project_id > 0) {
			// which commerce system are we using?
			// test for IDC
			if (class_exists('ID_Member')) {
				$idc_owned = mdid_get_selected();
			}
			$purchase_url = getPurchaseURLfromType($project_id, 'purchaseform').'&level='.$level.'&price='.$price;
			header('Location: '.apply_filters('idcf_purchase_url', $purchase_url, $project_id));
		}
	}
	return;
}

?>