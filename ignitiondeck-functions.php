<?php
function is_id_project() {
	global $post;
	if (isset($post)) {
		$post_content = $post->post_content;
		if ($post->post_type == 'ignition_product') {
			$post_id = $post->ID;
			$project_id = get_post_meta($post_id, 'ign_project_id', true);
		}
		else if (strpos($post_content, 'project_')) {
			$pos = strpos($post_content, 'product=');
			$project_id = absint(substr($post_content, $pos + 9, 1));
		}
	}
	return (isset($project_id) ? $project_id : null);
}

/**
 * Get Form Settings
 */
function getFormSettings() {
    global $wpdb;
    $sql="SELECT * FROM ".$wpdb->prefix . "ign_form where id='1'";
    $res1 =  $wpdb->query( $sql );
    $rows = $wpdb->get_results($sql);
    $row = &$rows[0];
    $form = unserialize( $row->form_settings );
    return $form;
}

/*
/*  get currency symbol *
*/
function setCurrencyCode($cvalue){ 
		switch($cvalue){		
			case 'USD':
				$currencyCode = '$';
				break;
			case 'AUD':
				$currencyCode = '$';
				break;
			case 'CAD':
				$currencyCode = '$';
				break;
			case 'CZK':
				$currencyCode = 'Kč';
				break;
			case 'DKK':
				$currencyCode = 'Kr';
				break;
			case 'EUR':
				$currencyCode = '&euro;';
				break;
			case 'HKD':
				$currencyCode = '$';
				break;
			case 'HUF':
				$currencyCode = 'Ft';
				break;
			case 'ILS':
				$currencyCode = '₪';
				break;
			case 'JPY':
				$currencyCode = '&yen;';
				break;
			case 'MXN':
				$currencyCode = '$';
				break;
			case 'MYR':
				$currencyCode = 'RM';
				break;
			case 'NOK':
				$currencyCode = 'kr';
				break;
			case 'NZD':
				$currencyCode = '$';
				break;
			case 'PHP':
				$currencyCode = '₱';
				break;
			case 'PLN':
				$currencyCode = 'zł';
				break;
			case 'GBP':
				$currencyCode = '&pound;';
				break;
			case 'SGD':
				$currencyCode = '$';
				break;
			case 'SEK':
				$currencyCode = 'kr';
				break;
			case 'CHF':
				$currencyCode = 'Fr';
				break;
			case 'TWD':
				$currencyCode = 'NT$';
				break;
			case 'THB':
				$currencyCode = '&#3647;';
				break;
			case 'TRY':
				$currencyCode = '&#8356;';
				break;
			case 'BRL':
				$currencyCode = 'R$';
				break;
			default :
				$currencyCode = '$';
		}
		return $currencyCode;
	}

/**
 *  Get Payment Settings, paypal address
 * @global object $wpdb
 * @return object
 */
function getPaymentSettings(){
    global $wpdb;
    $query="SELECT * FROM ".$wpdb->prefix . "ign_pay_settings where id='1'";
    $res = $wpdb->query( $query );
    $settings = $wpdb->get_results($query);
    $result = $settings[0];
    if($result){
        $result->url = 'www.paypal.com'; //www.paypal.com or www.sandbox.paypal.com
        return $settings[0];
    }
}

/**
 *  Get Payment Settings for Adaptive Paypal
 * @global object $wpdb
 * @return object
 */
function getAdaptivePayPalSettings(){
    global $wpdb;
    $query="SELECT * FROM ".$wpdb->prefix."ign_adaptive_pay_settings WHERE id='1'";
    $res = $wpdb->get_row( $query );
    if ($res)
		return $res;
}

/**
 * isPaypalTransactionValid
 * Checks if paypal payment is valid
 * @param string $GLOBALS['transactionId']
 * @return bool
 */
function isPaypalTransactionValid($transactionid){
    return true;
    $paypalOptions = getPaymentSettings();
    $h = curl_init();

    curl_setopt($h, CURLOPT_URL, "https://".$paypalOptions->url."/cgi-bin/webscr");
    curl_setopt($h, CURLOPT_POST, true);
    curl_setopt($h, CURLOPT_POSTFIELDS, array(
        'cmd'   => '_notify-synch',
        'tx'    => $transactionid,
        'at'    => $paypalOptions->identity_token,
        'submit' => 'PDT'
    ));

    curl_setopt($h, CURLOPT_HEADER, false);
    curl_setopt($h, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($h);
    $lines = explode("\n", $result);
//    var_dump($lines);
    $keyarray = array();
    if (strcmp ($lines[0], "SUCCESS") == 0) {
        for ($i=1; $i<count($lines);$i++){
            list($key,$val) = explode("=", $lines[$i]);
            $keyarray[urldecode($key)] = urldecode($val);
        }
        // process payment
        $firstname = $keyarray['first_name'];
        $lastname = $keyarray['last_name'];
        $itemname = $keyarray['num_cart_items'];
        $amount = $keyarray['mc_gross'];
        return true;
    }
    else if (strcmp ($lines[0], "FAIL") == 0) {
        // log for manual investigation
        return false;
    }
}

/**
 * SetOrderStatus
 * @param string $status
 * @param int $paymentId
 */
function setOrderStatus($status, $paymentId){
    global $wpdb;
    $sql_update="update ".$wpdb->prefix . "ign_pay_info set status='$status' where id='$paymentId'";
    $res = $wpdb->query( $sql_update );
}

function addCustomerInfo($data){
    global $wpdb;
    $query="INSERT INTO ".$wpdb->prefix ."ign_customers (
        link,
        profile_pic,
        product_id)
        values ('".$data['link']."','"
            .$data['profile_pic']."','"
            .$data['product_id']."')";
    $res = $wpdb->query( $query );
}
/**
 * getProductByOrderId
 * Get product by given order id
 * @global object $wpdb
 * @param <type> $id
 * @return <type> 
 */
function getProductInfoByOrderId($id){
    global $wpdb;
    $query="SELECT `product_id` FROM ".$wpdb->prefix . "ign_pay_info where id=$id";
    $productId = $wpdb->get_row( $query );

    $productId = $productId->product_id;

    $sql="SELECT * FROM ".$wpdb->prefix . "ign_products WHERE id='". $productId ."' limit 0,1";
    $res = $wpdb->query( $sql );
    $items = $wpdb->get_results($sql);
    $product = $items[0];
    return $product;
}

/**
 * GetOrderById
 * @global object $wpdb
 * @param <type> $id
 * @return <type>
 */
function getOrderById($id){
    global $wpdb;
    $query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix . "ign_pay_info where id = %d", $id);
    $order = $wpdb->get_row( $query );
    return $order;
}

/**
 * GetMailchimpSettings
 * @return object
 */
function getMailchimpSettings(){
    global $wpdb;
    $sql = $wpdb->prepare('SELECT * from '.$wpdb->prefix.'ign_mailchimp_subscription where id = %d', '1');
    $res = $wpdb->get_row($sql);
    return $res;
}

function getProductMailchimpSettings($project_id) {
	global $wpdb;
	$sql = $wpdb->prepare('SELECT * from '.$wpdb->prefix.'ign_product_settings where product_id = %d ', $project_id);
    $res = $wpdb->get_row($sql);
    return $res;
}

function subscribeToMailchimp($email, $userData, $api_key, $list_id) {
    $merges = array(
        'FNAME' => $userData['first_name'],
        'LNAME' => $userData['last_name']
    );
	if (!empty($api_key) && !empty($list_id)) {
		require_once('inc/MCAPI.class.php');
		$api = new MCAPI($api_key);
		$added_to_mailchimp = $api->listSubscribe($list_id, $email, $merges);
		
	   /* $data = array(
	        'email_address'=> $email,
			'FNAME'=> $userData['first_name'],
			'LNAME'=> $userData['last_name'],
	        'apikey'=> $api_key,
			'merge_vars' => $merges,
			'id' => $list_id,
	        'double_optin' => true,
	        'update_existing' => false,
	        'replace_interests' => true,
	        'send_welcome' => false,
	        'email_type' => 'html'
	    );

	    $payload = json_encode($data);
	    $submit_url = "http://".$mailchimpSettings->region.".api.mailchimp.com/1.3/?method=listSubscribe";

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $submit_url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, urlencode($payload));
	    $result = curl_exec($ch);
	    curl_close ($ch);
	    $data = json_decode($result);
	    print_r($data);*/
	}
}

/*
 *	getPostbyProductID()
 *	Desc: Return the post_id of the given $prod_id (product id)
 */
function getPostbyProductID($project_id) {
	$project = new ID_Project($project_id);
	$post_id = $project->get_project_postid();	
	return $post_id;
}

function get_embed_image($project_id) {
	global $wpdb;
	$project = new ID_Project($project_id);
	$post_id = $project->get_project_postid();	
	$url = ID_Project::get_project_thumbnail($post_id, 'id_embed_image');
	return $url;	
}

function idc_checkout_image($post_id) {
	global $wpdb;
	$image = ID_Project::get_project_thumbnail($post_id, 'id_checkout_image');
	return $image;
}

function getPostDetailbyProductID($project_id) {
	$project = new ID_Project($project_id);
	$post_id = $project->get_project_postid();	
	$post = get_post($post_id);
	return $post;
}

function getProductPrice($level, $productid) {
	global $wpdb;
	//echo "\$level: ".$level;
	$post_id = getPostbyProductID($productid);
	
	$level = (($level > 1) ? ($level - 1) : 0);
	
	if ($level == 0) {
		$sql_prod = "SELECT * FROM ".$wpdb->prefix."ign_products WHERE id = '".$productid."'";
		$product_data = $wpdb->get_row($sql_prod);
		
		return $product_data->product_price;
	}
	//echo "ign_product_level_".$level."_price<br />";
	//echo "post_oid: ".$post_id;
	$level_price = get_post_meta( $post_id, $name="ign_product_level_".$level."_price", true );
	//$level_desc = get_post_meta( $post->ID, $name="ign_product_level_".$order_data->product_level."_desc", true );
	return $level_price;
}

function getTotalProductFund($project_id) {
	global $wpdb;
	$sql = "Select SUM(prod_price) AS raise from ".$wpdb->prefix . "ign_pay_info where product_id='".$project_id."'";
	$result = $wpdb->get_row($sql);
	if ($result->raise != NULL || $result->raise != 0)
		return $result->raise;
	else
		return 0;
}

function getProjectGoal($project_id) {
	global $wpdb;

	$goal_query = $wpdb->prepare('SELECT goal FROM '.$wpdb->prefix.'ign_products WHERE id=%d', $project_id);
	$goal_return = $wpdb->get_row($goal_query);
	return $goal_return->goal;
}

function getPercentRaised($project_id) {
	global $wpdb;
	$total = getTotalProductFund($project_id);
	$goal = getProjectGoal($project_id);
	$percent = 0;
	if ($total > 0) {
		$percent = number_format($total/$goal*100, 2, '.', '');
	}
	return $percent;
}

/*
 *	getLevelLimitReached()
 *	Desc: To calculate the goal reached so for for a certain level
 */
function getLevelLimitReached($product_id, $post_id, $level) {
	global $wpdb;
	//$sql = "SELECT SUM(prod_price) AS LevelPurchaseTotal FROM ".$wpdb->prefix."ign_pay_info WHERE product_id = '".$product_id."' AND product_level = '".$level."'";
	$sql = "SELECT COUNT(*) AS TotalOrders FROM ".$wpdb->prefix."ign_pay_info WHERE product_id = '".$product_id."' AND product_level = '".$level."'";
	//echo $sql."<br />";
	$level_purchase_so_far = $wpdb->get_row($sql)->TotalOrders;
	
	if ($level_purchase_so_far == "")	//If there are no purchases $level_purchase_so_far will be empty, so putting condition
		$level_purchase_so_far = 0;
	
	//echo "level_purchase_so_far: ".$level_purchase_so_far."<br />";

	if ($level == 1) {
		$product_details = getProductDetails($product_id);
		if (isset($product_details)) {
			$meta_limit = $product_details->ign_product_limit;
		}
	}
	else {
		// getting the level limit set, from the wp_postmeta
		$meta_limit = get_post_meta( $post_id, "ign_product_level_".$level."_limit", true );
	}
	// Setting the level limit to non-formatted number
	$meta_limit = floatval(preg_replace('/[^\d.]/', '', $meta_limit));
	
	if (empty($meta_limit)) {
		return false;
	}
	
	if ($level_purchase_so_far < $meta_limit)
		return false;
	else if ($level_purchase_so_far >= $meta_limit)
		return true;
}

function getCurrentLevelTotal($product_id, $post_id, $level) {
	global $wpdb;
	$sql = "SELECT SUM(prod_price) AS LevelPurchaseTotal FROM ".$wpdb->prefix."ign_pay_info WHERE product_id = '".$product_id."' AND product_level = '".$level."'";
	//echo $sql."<br />";
	$level_purchase_so_far = $wpdb->get_row($sql)->LevelPurchaseTotal;
	
	if ($level_purchase_so_far == "") {
		//If there are no purchases $level_purchase_so_far will be empty, so putting condition
		$level_purchase_so_far = 0;
	}	
	return $level_purchase_so_far;
}

/*
 *	getUsersOrders()
 *	Desc: To get the number of orders placed for the $level
 */
function getCurrentLevelOrders($product_id, $post_id, $level) {
	global $wpdb;
	$sql = "SELECT COUNT(*) AS TotalOrders FROM ".$wpdb->prefix."ign_pay_info WHERE product_id = '".$product_id."' AND product_level = '".$level."'";
	//echo $sql."<br />";
	$level_purchase_so_far = $wpdb->get_row($sql)->TotalOrders;
	
	if ($level_purchase_so_far == "") {
		//If there are no purchases $level_purchase_so_far will be empty, so putting condition
		$level_purchase_so_far = 0;
	}	
	return $level_purchase_so_far;
}

function getProductDetails($product_id) {
	global $wpdb;
	
	$sql = "SELECT * FROM ".$wpdb->prefix."ign_products WHERE id = '".$product_id."'";
	$result = $wpdb->get_row($sql);

	return $result;
}

function getAweberSettings() {
	global $wpdb;
    $sql ="SELECT * from ".$wpdb->prefix."ign_aweber_settings where id='1'";
    $settings = $wpdb->get_row($sql);
    return $settings;
}

function getProductAweberSettings($productId) {
	global $wpdb;
	$sql ="SELECT * from ".$wpdb->prefix . "ign_product_settings where product_id='".$productId."'";
    $settings = $wpdb->get_row($sql);
	return $settings;
}

function subscribeToAweber($email, $name, $product_id) {
	$settings = getProductAweberSettings($product_id); 
	if (empty($settings)) {
		$settings = getAweberSettings();
		$aweber_email = $settings->list_email;
	} 
	else {
		$aweber_email = $settings->list_email;
	}
	//$to = $settings->list_email;
	$to = $aweber_email.'@aweber.com';
	$subject = "Add Email to Subscriber List";
	
	$header = "MIME-Version: 1.0" . "\r\n";
	$header .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$header .= 'From: '.$name['first_name'].' '.$name['last_name'].' <'.$email.'> com'."\r\n";
	//			Cc: '.$cc.'\r\n';
	
	$email_msg = mail($to, $subject, "", $header);
}

function currentPageURL() {
	
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
	 $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
	 $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	$thispage = $pageURL;
	
	return $thispage;
}

function getProductURL($prod_id, $paypal_page="") {
	$slug = apply_filters('idcf_archive_slug', __('projects', 'ignitiondeck'));
	$prod_id = urlencode($prod_id);
	$page = urlencode($page);
	if (get_option('permalink_structure') == "") {
		$post = getPostDetailbyProductID($prod_id);

		if ($paypal_page == "")
			$product_url = home_url()."/?ignition_product=".$post->post_name;
		else
			$product_url = home_url()."/?ignition_product=".$post->post_name."&paypa_passed=yes";
	} else {
		$post = getPostDetailbyProductID($prod_id);
		if ($paypal_page == "")
			$product_url = home_url()."/".$slug."/".$post->post_name;
		else
			$product_url = home_url()."/".$slug."/".$post->post_name."/?paypa_passed=yes";
	}
	
	return $product_url;
}

/*
 *	Desc: Based on the latest structure of the Project URL stored, we are using this funtion to get the Project URL
 *	Function: getProjectURLfromType()
 */
function getProjectURLfromType($project_id, $page="") {
	global $wpdb;
	$slug = apply_filters('idcf_archive_slug', __('projects', 'ignitiondeck'));
	$project_id = urlencode($project_id);
	if ($project_id > 0) {
		$project = new ID_Project($project_id);
		$post_id = $project->get_project_postid();
		if ($post_id > 0) {
			$product_url = get_permalink($post_id);
		}
		$page = urlencode($page);
		$post = getPostDetailbyProductID($project_id);
		if (!empty($post)) {
			$meta_url = get_post_meta($post->ID, 'id_project_URL', true);
			if (get_option('permalink_structure') == "") {
				if (get_post_meta($post->ID, 'ign_option_project_url', true) == "current_page") {		// If Project URL is the normal Project Page
					if ($page == "")
						$product_url = home_url()."/?ignition_product=".$post->post_name;
					else if ($page == "purchaseform")
						$product_url = home_url()."/?ignition_product=".$post->post_name."&purchaseform=1&prodid=".$project_id;
					else if ($page == "preapprovalkey")
						$product_url = home_url()."/?ignition_product=".$post->post_name."&generatepreapproval=1";
					else
						$product_url = home_url()."/?ignition_product=".$post->post_name."&paypa_passed=yes";
						
				}
				else if (get_post_meta($post->ID, 'ign_option_project_url', true) == "page_or_post") {		// If Project URL is another post or Project page
					$post_name = html_entity_decode(get_post_meta($post->ID, 'ign_post_name', true));
					$sql_project_post = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts WHERE post_name = %s AND post_type != 'ignition_product' LIMIT 1", $post_name);
					$project_post = $wpdb->get_row($sql_project_post);
					if (!empty($project_post)) {
						if ($page == "")
							$product_url = $project_post->guid;
						else if ($page == "purchaseform")
							$product_url = $project_post->guid."&purchaseform=1&prodid=".$project_id;
						else if ($page == "preapprovalkey")
							$product_url = $project_post->guid."&generatepreapproval=1";
						else
							$product_url = $project_post->guid."&paypa_passed=yes";
					}
				}
				else if (get_post_meta($post->ID, 'ign_option_project_url', true) == "external_url" && !empty($meta_url)) {		//If some external URL is set as Project page
					if ($page == "")
						$product_url = $meta_url;
					else if ($page == "purchaseform")
						$product_url = $meta_url."&purchaseform=1&prodid=".$project_id;
					else if ($page == "preapprovalkey")
						$product_url = $meta_url."&generatepreapproval=1";
					else
						$product_url = $meta_url."&paypa_passed=yes";
				}
			} 
			else {
				if (get_post_meta($post->ID, 'ign_option_project_url', true) == "current_page") {		// If Project URL is the normal Project Page
					if ($page == "")
						$product_url = home_url()."/".$slug."/".$post->post_name;
					else if ($page == "purchaseform")
						$product_url = home_url()."/".$slug."/".$post->post_name."/?purchaseform=1&prodid=".$project_id;
					else if ($page == "preapprovalkey")
						$product_url = home_url()."/".$slug."/".$post->post_name."/?generatepreapproval=1";
					else
						$product_url = home_url()."/".$slug."/".$post->post_name."/?paypa_passed=yes";
						
				}
				else if (get_post_meta($post->ID, 'ign_option_project_url', true) == "page_or_post") {		// If Project URL is another post or Project page
					$post_name = html_entity_decode(get_post_meta($post->ID, 'ign_post_name', true));
					$sql_project_post = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts WHERE post_name = %s AND post_type != 'ignition_product' LIMIT 1", $post_name);
					$project_post = $wpdb->get_row($sql_project_post);
					if (!empty($project_post)) {
						if ($page == "")
							//$product_url = $project_post->guid;
							$product_url = get_permalink($project_post->ID);
						else if ($page == "purchaseform")
							$product_url = get_permalink($project_post->ID)."?purchaseform=1&prodid=".$project_id;
						else if ($page == "preapprovalkey")
							$product_url = get_permalink($project_post->ID)."?generatepreapproval=1";
						else
							$product_url = get_permalink($project_post->ID)."?paypa_passed=yes";
					}
				}
				else if (get_post_meta($post->ID, 'ign_option_project_url', true) == "external_url" && !empty($meta_url)) {		//If some external URL is set as Project page
					if ($page == "")
						$product_url = $meta_url;
					else if ($page == "purchaseform")
						$product_url = $meta_url."?purchaseform=1&prodid=".$project_id;
					else if ($page == "preapprovalkey")
						$product_url = $meta_url."?generatepreapproval=1";
					else
						$product_url = $meta_url."?paypa_passed=yes";
				}
			}
		}
	}
	return $product_url;
}

function getPurchaseURLfromType($project_id, $page="") {
	$slug = apply_filters('idcf_archive_slug', __('projects', 'ignitiondeck'));
	global $wpdb;
	// Set default purchase url in the event we don't have one set
	$purchase_default = get_option('id_purchase_default');
	if (!empty($purchase_default)) {
		if (!empty($purchase_default['option'])) {
			$option = $purchase_default['option'];
			if ($option == 'page_or_post') {
				if (!empty($purchase_default['value'])) {
					$purchase_url = get_permalink($purchase_default['value']);
				}
			}
			else {
				if (isset($purchase_default['value'])) {
					$purchase_url = $purchase_default['value'];
				}
			}
		}
	}
	$project_id = absint($project_id);
	$page = urlencode($page);
	if ($project_id > 0) {
		$post = getPostDetailbyProductID($project_id);
		if (isset($post->ID)) {
			$post_page = get_post_meta($post->ID, 'ign_option_purchase_url', true);
			if (!empty($post_page)) {
				$permalink_structure = get_option('permalink_structure');
				if ($post_page !== 'default') {
					$meta_url = get_post_meta($post->ID, 'purchase_project_URL', true);
					if ($permalink_structure == "") {
						// we no longer set defaults here since they are set above
						if ($post_page == "current_page") {		// If Project URL is the normal Project Page
							if ($page == "purchaseform") {
								$purchase_url = home_url()."/?ignition_product=".$post->post_name."&purchaseform=1&prodid=".$project_id;
							}
						} 
						else if ($post_page == "page_or_post") {		// If Project URL is another post or Project page
							$post_name = html_entity_decode(get_post_meta($post->ID, 'ign_purchase_post_name', true));
							$sql_purchase_post = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts WHERE post_name = %s AND post_type != 'ignition_product' LIMIT 1", $post_name);
							$purchase_post = $wpdb->get_row($sql_purchase_post);
							if (!empty($purchase_post)) {
								if ($page == "purchaseform") {
									$purchase_url = $purchase_post->guid."&purchaseform=1&prodid=".$project_id;
								}
							}	
						} 
						else if ($post_page == "external_url") {		//If some external URL is set as Project page
							if ($page == "purchaseform" && !empty($meta_url)) {
								$purchase_url = $meta_url."&purchaseform=1&prodid=".$project_id;
							}	
						}
					} 
					else {
						if ($post_page == "current_page") {		// If Project URL is the normal Project Page
							if ($page == "purchaseform") {
								$purchase_url = home_url()."/".$slug."/".$post->post_name."/?purchaseform=1&prodid=".$project_id;
							}	
						} 
						else if ($post_page == "page_or_post") {		// If Project URL is another post or Project page

							$post_name = html_entity_decode(get_post_meta($post->ID, 'ign_purchase_post_name', true));

							$sql_purchase_post = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts WHERE post_name = %s AND post_type != 'ignition_product' LIMIT 1", $post_name);
							$purchase_post = $wpdb->get_row($sql_purchase_post);
							if (!empty($purchase_post)) {
								if ($page == "purchaseform") {
									$purchase_url = get_permalink($purchase_post->ID)."?purchaseform=1&prodid=".$project_id;
								}
							}
						} 
						else if ($post_page == "external_url") {		//If some external URL is set as Project page
							if ($page == "purchaseform" && !empty($meta_url)) {
								$purchase_url = $meta_url."?purchaseform=1&prodid=".$project_id;
							}	
						}
					}
				}
				else {
					if (empty($purchase_url)) {
						$purchase_url = get_permalink($post->ID);
					}
					if ($permalink_structure == "") {
						$purchase_url = $purchase_url.'&purchaseform=1&prodid='.$project_id;
					}
					else {
						$purchase_url = $purchase_url.'?purchaseform=1&prodid='.$project_id;
					}
				}
			}
		}
	}
	return $purchase_url;
}

function getThankYouURLfromType($project_id, $page="") {
	$slug = apply_filters('idcf_archive_slug', __('projects', 'ignitiondeck'));
	global $wpdb;
	// Set default ty url in the event we don't have one set
	$ty_default = get_option('id_ty_default');
	if (!empty($ty_default)) {
		if (!empty($ty_default['option'])) {
			$option = $ty_default['option'];
			if ($option == 'page_or_post') {
				if (!empty($ty_default['value'])) {
					$thank_you_url = get_permalink($ty_default['value']);
				}
			}
			else {
				if (isset($ty_default['value'])) {
					$thank_you_url = $ty_default['value'];
				}
			}
		}
	}
	$project_id = urlencode($project_id);
	$page = urlencode($page);
	if ($project_id > 0) {
		$post = getPostDetailbyProductID($project_id);
		if (isset($post->ID)) {
			$post_page = get_post_meta($post->ID, 'ign_option_ty_url', true);
			if (!empty($post_page)) {
				$permalink_structure = get_option('permalink_structure');
				if ($post_page !== 'default') {
					$meta_url = get_post_meta($post->ID, 'ty_project_URL', true);
					if ($permalink_structure == "") {
						// we no longer set defaults here since they are set above
						if ($post_page == "current_page") {		// If Project URL is the normal Project Page
							if ($page == "thank_you_url") {
								$thank_you_url = home_url()."/?ignition_product=".$post->post_name."&cc_success=1";
							}
							else {
								$thank_you_url = home_url()."/?ignition_product=".$post->post_name;
							}
								
						} else if ($post_page == "page_or_post") {		// If Project URL is another post or Project page

							$post_name = html_entity_decode(get_post_meta($post->ID, 'ign_ty_post_name', true));
							$sql_ty_post = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts WHERE post_name = %s AND post_type != 'ignition_product' LIMIT 1", $post_name);
							$ty_post = $wpdb->get_row($sql_ty_post);
							if (!empty($ty_post)) {
								if ($page == "thank_you_url") {
									$thank_you_url = $ty_post->guid."&cc_success=1";
								}

								else {
									$thank_you_url = $ty_post->guid;
								}
							}
								
						} else if ($post_page == "external_url" && !empty($meta_url)) {		//If some external URL is set as Project page
							if ($page == "thank_you_url") {
								$thank_you_url = $meta_url."&cc_success=1";
							}
							
							else {
								$thank_you_url = $meta_url;
							}
								
						}
					} else {
						
						if ($post_page == "current_page") {		// If Project URL is the normal Project Page
							
							if ($page == "thank_you_url") {
								$thank_you_url = home_url()."/".$slug."/".$post->post_name."/?cc_success=1";
							}
							
							else {
								$thank_you_url = home_url()."/".$slug."/".$post->post_name;
							}
								
								
						} else if ($post_page == "page_or_post") {		// If Project URL is another post or Project page
							
							$post_name = html_entity_decode(get_post_meta($post->ID, 'ign_ty_post_name', true));
							$sql_ty_post = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts WHERE post_name = %s AND post_type != 'ignition_product' LIMIT 1", $post_name);
							$ty_post = $wpdb->get_row($sql_ty_post);
							if (!empty($ty_post)) {
								if ($page == "thank_you_url") {
									$thank_you_url = get_permalink($ty_post->ID)."?cc_success=1";
								}
								
								else {
									$thank_you_url = get_permalink($ty_post->ID);
								}
							}
								
						} else if ($post_page == "external_url" && !empty($meta_url)) {		//If some external URL is set as Project page
							
							if ($page == "thank_you_url") {
								$thank_you_url = $meta_url."?cc_success=1";
							}
							
							else {
								$thank_you_url = $meta_url;
							}	
						}	
					}
				}
				else {
					if ($permalink_structure == "") {
						$thank_you_url = $thank_you_url.'&cc_success=1';
					}
					else {
						$thank_you_url = $thank_you_url.'?cc_success=1';
					}
				}
			}
		}
	}
	return $thank_you_url;
}

function getThemeFileName() {
	global $wpdb;
	
	$sql = "SELECT * FROM ".$wpdb->prefix."ign_settings WHERE id = '1'";
	$settings = $wpdb->get_row($sql);
	
	if (sizeof($settings) > 0) {
		if ($settings->theme_value == 'style1') {
			return 'ignitiondeck-style';
		} else {
			return 'ignitiondeck-'.$settings->theme_value;
		}
	}
	else
		return 'ignitiondeck-style';
}

function getSettings() {
	global $wpdb;
	
	$sql_settings = "SELECT * FROM ".$wpdb->prefix."ign_settings WHERE id = '1'";
	$settings = $wpdb->get_row( $sql_settings );
	
	return $settings;
}

function getProductFormSettings($product_id) {
	global $wpdb;
	
	$sql_settings = "SELECT form_settings FROM ".$wpdb->prefix."ign_product_settings WHERE product_id = '".$product_id."'";
	$settings = $wpdb->get_row( $sql_settings );
	
	if (count($settings) > 0) {
		$form = $settings->form_settings;
		return $form;
	}
	else {
		return null;
	}
}

function getProductSettings($product_id) {
	global $wpdb;
	$sql_settings = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."ign_product_settings WHERE product_id = %d", $product_id);
	$settings = $wpdb->get_row( $sql_settings );
	
	return $settings;
}

function getProductbyPostID($post_id) {
	$project_id = get_post_meta($post_id, 'ign_project_id', true);
	$project = new ID_Project($project_id);
	$the_project = $project->the_project();
	return $the_project;
}

function getProductNumberFromPostID($postid) {
	global $wpdb;
	//$product_details = getProductbyPostID($postid);

	$product_number = get_post_meta($postid, 'ign_project_id', true);
	return $product_number;
}

// This returns itself
function getProductNumberFromProductID($product_id) {
	global $wpdb;
	
	$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."ign_products WHERE id = %d", $product_id);
	$result = $wpdb->get_row($sql);
	
	$product_number = $result->id;
	return $product_number;
}

function postToURL($url, $data) {
	$fields = '';
	foreach($data as $key => $value) { 
	  	$fields .= $key . '=' . $value . '&'; 
	}
	rtrim($fields, '&');
	
	$post = curl_init();
	
	curl_setopt($post, CURLOPT_URL, $url);
	curl_setopt($post, CURLOPT_POST, count($data));
	curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($post);
	
	curl_close($post);
}

function getProductDefaultSettings() {
	global $wpdb;
	$sql_settings = "SELECT * FROM ".$wpdb->prefix."ign_prod_default_settings";
	$settings = $wpdb->get_row( $sql_settings );
	
	if (count($settings) > 0)
		return $settings;
	else
		return -1;
}

function action_id_payment_success($pay_info_id) {
	// This function handles all that happens after a successful order
	// 1. Lets set percent meta in case we need to fire the project success hook
	$percent = ID_Project::set_percent_meta();
	// 2. Let's send some mail
	$inactive = get_option('id_email_inactive');
	if (!$inactive) {
		$new_order = new ID_Order($pay_info_id);
		$order = $new_order->get_order();
		if (!empty($order)) {
			$project_id = $order->product_id;
		}
		if (isset($project_id) && $project_id > 0) {
			$mailchimp_settings = getMailchimpSettings();
			$aweber_settings = getAweberSettings();
			$product_settings = getProductSettings($order->product_id);
			if (isset($order->email)) {
				$email = $order->email;
			}
			else {
				$email = null;
			}
			if (isset($order->first_name)) {
				$first_name = stripslashes(html_entity_decode($order->first_name));
			}
			else {
				$first_name = '';
			}

			if (isset($order->last_name)) {
				$last_name = stripslashes(html_entity_decode($order->last_name));
			}
			else {
				$last_name = '';
			}
			if (!empty($email)) {
				if (!empty($product_settings)) {
					$active_mailtype = $product_settings->active_mailtype;
					if ($active_mailtype == 'mailchimp') {
						$api_key = $product_settings->mailchimp_api_key;
						$list_id = $product_settings->mailchimp_list_id;
						subscribeToMailchimp($email, array('first_name' => $first_name, 'last_name' => $last_name ), $api_key, $list_id);
					}
					else if ($active_mailtype == 'aweber') {
						subscribeToAweber($email, array('first_name' => $first_name, 'last_name' => $last_name ), $project_id);
					}
				}
				else {
					if (!empty($mailchimp_settings) && $mailchimp_settings->is_active) {
						$api_key = $mailchimp_settings->api_key;
						$list_id = $mailchimp_settings->list_id;
						subscribeToMailchimp($email, array('first_name' => $first_name, 'last_name' => $last_name ), $api_key, $list_id);
					}
					else if (!empty($aweber_settings) && $aweber_settings->is_active) {
						subscribeToAweber($email, array('first_name' => $first_name, 'last_name' => $last_name ), $project_id);
					}
				}
			}
		}
	}	
}

if (is_id_licensed()) {
	add_action('id_payment_success', 'action_id_payment_success', 1, 1);
}

/*
 *   Function to print all the short codes
 */
function getAllShortCodes() {
	echo '<div class="id-metabox-short-codes">
			<div class="shortcode-content">';
			$content = 
				'<div><strong>For Full Width Project Template:</strong><br>&#91;project_page_content product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&nbsp;</div>
				<div><strong>For Combination Project Template &amp; Project Widget:</strong><br>&#91;project_page_complete product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&nbsp;</div>
				<div><strong>To Use Project Template &amp; Widget Separately:</strong><br>&#91;project_page_content_left product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&#91;project_page_widget product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&#91;project_mini_widget product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&nbsp;</div>
				<div><strong>Project Grid:</strong><br>&#91;project_grid product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&nbsp;</div>
				<div><strong>For Pledge Form:</strong><br>&#91;project_purchase_form product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<!--<div><strong>For Thank You Page:</strong><br>&#91;project_thank_you product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>-->
				<div><strong>For Project Name:</strong><br>&#91;project_name product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Project Image:</strong><br>&#91;project_image product="<span data-product=&quot;&quot;></span>" image="&#60;image number&#62;"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Project Long Description:</strong><br>&#91;project_long_desc product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project Video:</strong><br>&#91;project_video product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Project Percentage Bar:</strong><br>&#91;project_percentage_bar product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Short Description:</strong><br>&#91;project_short_desc product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Goal Amount of the Project:</strong><br>&#91;project_goal product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Amount of Project Users:</strong><br>&#91;project_users product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Amount of Project Supporters:</strong><br>&#91;project_pledged product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project Days Left:</strong><br>&#91;project_daystogo product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project End:</strong><br>&#91;project_end product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project FAQ:</strong><br>&#91;project_faq product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project Updates:</strong><br>&#91;project_updates product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>';
				echo apply_filters('id_shortcode_list', $content);
		echo '</div>
		  </div>';
}

function getShortCodesPostPage() {
	global $wpdb;
	
	$products = ID_Project::get_all_projects();
	echo '<div class="id-metabox-short-codes">
			<div>
				<div>Select Project: <select name="project_id_shortcodes" id="project_id_shortcodes">
					 <option> --- </option>';
	foreach ($products as $product) {
		$project = new ID_Project($product->id);
		$post_id = $project->get_project_postid();
		echo '<option value="'.$product->id.'">'.get_the_title($post_id).'</option>';
	}
	echo '</select>
				</div>
			</div>
			<div class="shortcode-content">';
			$content = 
				'<div><strong>For Full Width Project Template:</strong><br>&#91;project_page_content product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&nbsp;</div>
				<div><strong>For Combination Project Template &amp; Project Widget:</strong><br>&#91;project_page_complete product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&nbsp;</div>
				<div><strong>To Use Project Template &amp; Widget Separately:</strong><br>&#91;project_page_content_left product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&#91;project_page_widget product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&#91;project_mini_widget product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&nbsp;</div>
				<div><strong>Project Grid:</strong><br>&#91;project_grid product="<span data-product=&quot;&quot;></span>"]</div>
				<div>&nbsp;</div>
				<div><strong>For Pledge Form:</strong><br>&#91;project_purchase_form product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<!--<div><strong>For Thank You Page:</strong><br>&#91;project_thank_you product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>-->
				<div><strong>For Project Name:</strong><br>&#91;project_name product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Project Image:</strong><br>&#91;project_image product="<span data-product=&quot;&quot;></span>" image="&#60;image number&#62;"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Project Long Description:</strong><br>&#91;project_long_desc product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project Video:</strong><br>&#91;project_video product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Project Percentage Bar:</strong><br>&#91;project_percentage_bar product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Short Description:</strong><br>&#91;project_short_desc product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Goal Amount of the Project:</strong><br>&#91;project_goal product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<!--<div><strong>For Project Levels:</strong><br>&#91;project_price_levels product="<span data-product=&quot;&quot;></span>"&#93;</div>-->
				<div>&nbsp;</div>
				<div><strong>For Amount of Project Users:</strong><br>&#91;project_users product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Amount of Project Supporters:</strong><br>&#91;project_pledged product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project Days Left:</strong><br>&#91;project_daystogo product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project End:</strong><br>&#91;project_end product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project FAQ:</strong><br>&#91;project_faq product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>
				<div><strong>For Displaying Project Updates:</strong><br>&#91;project_updates product="<span data-product=&quot;&quot;></span>"&#93;</div>
				<div>&nbsp;</div>';
				echo apply_filters('id_shortcode_list', $content);
		echo '</div>
		  </div>';
}

function projectPageof($current_post_id, $prod_id) {
	if (isset($_GET['preview']) && $_GET['preview'] == true) {
		$current_post_id = $_GET['preview_id'];
	}
	global $wpdb;
	// First check whether the current Page/Post is the Project page of the Product_id set in the widget options
	$product_post_details = getPostDetailbyProductID($prod_id);
	
	$current_post = get_post( $current_post_id );
	if (get_post_meta($product_post_details->ID, 'ign_post_name', true) == $current_post->post_name)
		return $prod_id;
	
	// If current page is a project page, and option selected for that project is also the Current_page as project page then
	if ($current_post->post_type == "ignition_product" && get_post_meta($current_post_id, 'ign_option_project_url', true) == 'current_page') {
		$product_details = getProductbyPostID($current_post_id);
		return $product_details->id;
	}
		
	// Check if this post is project page of any project
	// $current_post->post_name is name of any ign_post_name in the post meta, if yes, get post id and check if it is same as $product_post_details->ID
	$sql_check_post_name = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'ign_post_name' AND meta_value = '".$current_post->post_name."' LIMIT 1";
	$postmeta_from_name = $wpdb->get_row($sql_check_post_name);
	if (count($postmeta_from_name) > 0) {
		$product_details = getProductbyPostID($postmeta_from_name->post_id);
		return $product_details->id;
	}
	
	// Nothing found yet, the last search
	$sql_check_post_address = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'id_project_URL' AND meta_value = '".$current_post->guid."' LIMIT 1";
	$postmeta_post_address = $wpdb->get_row($sql_check_post_address);
	if (count($postmeta_post_address) > 0) {
		$product_details = getProductbyPostID($postmeta_post_address->post_id);
		return $product_details->id;
	}
		
	return 0;
}

function getDefaultPaymentMethod() {
	global $wpdb;
	
	$sql = "SELECT * FROM ".$wpdb->prefix."ign_pay_selection WHERE id = '1'";
	$payment_method = $wpdb->get_row($sql);
	return $payment_method;
}

if (!wp_next_scheduled('schedule_hourly_id_cron')) {
	wp_schedule_event(time(), 'hourly', 'schedule_hourly_id_cron');
}

if (!wp_next_scheduled('schedule_twicedaily_id_cron')) {
	wp_schedule_event(time(), 'twicedaily', 'schedule_twicedaily_id_cron');
}

function schedule_hourly_id_cron() {
	$raised = ID_Project::set_raised_meta();
	$percent = ID_Project::set_percent_meta();
	$days = ID_Project::set_days_meta();
	$closed = ID_Project::set_closed_meta();
}

add_action('schedule_hourly_id_cron', 'schedule_hourly_id_cron');

function schedule_twicedaily_id_cron() {
	$is_pro = false;
	$is_basic = false;
	$key = get_option('id_license_key');
	$validate = id_validate_license($key);
	if (isset($validate['response'])) {
		if ($validate['response']) {
			if (isset($validate['download'])) {
				if ($validate['download'] == '30') {
					$is_pro = 1;
				}
				else if ($validate['download'] == '1') {
					$is_basic = 1;
				}
			}
		}
	}
	update_option('is_id_pro', $is_pro);
	update_option('is_id_basic', $is_basic);
	if ($is_pro || $is_basic) {
		update_option('was_id_licensed', 1);
	}
	if ($is_pro) {
		update_option('was_id_pro', 1);
	}
}

add_action('schedule_twicedaily_id_cron', 'schedule_twicedaily_id_cron');

function id_create_order($order) {
	$fname = $order['fname'];
	$lname = $order['lname'];
	$email = $order['email'];
	$address = $order['address'];
	$city = $order['city'];
	$state = $order['state'];
	$zip = $order['zip'];
	$country = $order['country'];
	$project_id = $order['project_id'];
	$txn_id = $order['txn_id'];
	$preapproval_key = $order['preapproval_key'];
	$level = $order['level'];
	$price = $order['price'];
	$status = $order['status'];
	$date = $order['date'];

	$new_order = new ID_Order(null,
		$fname,
		$lname,
		$email,
		$address,
		$city,
		$state,
		$zip,
		$country,
		$project_id,
		$txn_id,
		$preapproval_key,
		$level,
		$price,
		$status,
		$date);
	$order_id = $new_order->insert_order;
	return $order_id;
}

function id_validate_license($key) {
	$ch = curl_init('http://ignitiondeck.com/id/?action=md_validate_license&key='.$key);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if (!$response) {
    	echo 'Curl error: '.curl_error($ch);
    	$valid = false;
    	$download = null;
    }
    else {
	    $data = json_decode($response);
	    $valid = $data->valid;
	    if (isset($data->download_id)) {
	    	$download = $data->download_id;
	    }
	    else {
	    	$download = null;
	    }
	}
	curl_close($ch);
    return array('response' => $valid, 'download' => $download);
}

//AJAX for getting the product number from product id
function get_product_number_callback() {
	global $wpdb;
	
	//GET product number by product id
	$prod_no = getProductNumberFromProductID($_POST['product_id']);
	
	echo $prod_no;
	exit;
			
}
add_action('wp_ajax_get_product_number', 'get_product_number_callback');
add_action('wp_ajax_nopriv_get_product_number', 'get_product_number_callback');

// AJAX call for deleting product image coming as an argument
function remove_product_image_callback() {
	global $wpdb;
	$post_id = absint($_POST['post_id']);
	$image = esc_attr($_POST['image']);
	$del = delete_post_meta($post_id, $image);
	exit;
}
add_action('wp_ajax_remove_product_image', 'remove_product_image_callback');
add_action('wp_ajax_nopriv_remove_product_image', 'remove_product_image_callback');

// AJAX call for deleting product image coming as an argument
// probably unused
function get_pages_links_callback() {
	global $wpdb, $post;
	
	$sql = "SELECT * FROM ".$wpdb->prefix."posts WHERE post_title LIKE '%".$_POST['page_title']."%' AND (post_type = 'ignition_product' OR post_type = 'post' OR post_type = 'page') AND post_status = 'publish'";
	$results = $wpdb->get_results( $sql );
	foreach( $results as $single_post ) {
		//setup_postdata($post);
		echo '<div class="post-link-container"><a class="post-link-filler" href="'.$single_post->guid.'">'.$single_post->post_title.'</a></div>';
	}
	exit;
}
add_action('wp_ajax_get_pages_links', 'get_pages_links_callback');
add_action('wp_ajax_nopriv_get_pages_links', 'get_pages_links_callback');

// AJAX call to get the PayKey from paypal using the given credentials
function get_paypal_paykey_callback() {
	global $wpdb;
	//==================================================================================================================================
	//		PayKey creation code
	//==================================================================================================================================
	// Getting the Adaptive payment settings
	$adaptive_pay_settings = getAdaptivePayPalSettings();
	
	// GETTING product default settings
	$default_prod_settings = getProductDefaultSettings();

	// Getting product settings and if they are not present, set the default settings as product settings
	$prod_settings = getProductSettings($_REQUEST['product_id']);
	if (empty($prod_settings))
		$prod_settings = $default_prod_settings;
		
	/***** 3token API credentials *****************/
	define('API_AUTHENTICATION_MODE','3token');
	define('API_USERNAME', $adaptive_pay_settings->api_username);
	define('API_PASSWORD', $adaptive_pay_settings->api_password);
	define('API_SIGNATURE', $adaptive_pay_settings->api_signature);
	require_once 'paypal/lib/Config/paypal_sdk_clientproperties.php';
	
	if ($adaptive_pay_settings->paypal_mode == "sandbox") {
		$paypal_address = "https://www.sandbox.paypal.com/webapps/adaptivepayment/flow/pay";
	}
	else {
		$paypal_address = "https://www.paypal.com/webapps/adaptivepayment/flow/pay";
	}
	
	// Setting the necessary variables for the payment
	$returnURL = site_url()."/?payment_success=1";
	$cancelURL = site_url(). "/?adaptive_payment_cancel=1" ;
	$notifyURL = site_url(). "/?ipn_handler=1";
	$currencyCode = $prod_settings->currency_code;
	
	$email = $_REQUEST["email"];
	$preapprovalKey = $_REQUEST["preapprovalKey"];
	$actionType = "CREATE";
	$requested='';
	$receiverEmail='';
	$amount='';
	
	$payRequest = new PayRequest();
	$payRequest->actionType = $actionType;
	$payRequest->cancelUrl = $cancelURL ;
	$payRequest->returnUrl = $returnURL;
	$payRequest->ipnNotificationUrl = $notifyURL;
	$payRequest->clientDetails = new ClientDetailsType();
	$payRequest->clientDetails->applicationId = $adaptive_pay_settings->app_id;
	//$payRequest->clientDetails->deviceId = DEVICE_ID;
	$payRequest->clientDetails->ipAddress = $_SERVER['REMOTE_ADDR'];
	$payRequest->currencyCode = $currencyCode;
	$payRequest->senderEmail = $email;
	$payRequest->requestEnvelope = new RequestEnvelope();
	$payRequest->requestEnvelope->errorLanguage = "en_US";
	if($preapprovalKey != "")
	{
		$payRequest->preapprovalKey = $preapprovalKey ;
	}
	$receiver1 = new receiver();
	$receiver1->email = $adaptive_pay_settings->paypal_email;
	$receiver1->phoneNumber = "012838636275";
	$receiver1->countryCode = "US";
	$receiver1->amount = $_REQUEST['price'];
	
	$payRequest->receiverList = array($receiver1);
	
	// Make a call to the API
	$ap = new AdaptivePayments();
	$response=$ap->Pay($payRequest);
	
	if(strtoupper($ap->isSuccess) == 'FAILURE')
	{
		$fault = $ap->getLastError();
		// For error handling
		if(is_array($fault->error))
		{
			$errors_content = '';
			foreach($fault->error as $err) {
				$errors_content .= $err->message . '<br />';
			}
		}
		else
		{
			$errors_content = "";
			$errors_content .= $fault->error->message;
		}
		echo "error|".$errors_content; exit;
			
	}
	else
	{
		$_SESSION['payKey'] = $response->payKey;
		$payKey = $response->payKey;
		if($response->paymentExecStatus == "CREATED")
		{
			if(isset($_GET['cs'])) {
				$_SESSION['payKey'] = '';
			}
			try {
				if(isset($_REQUEST["payKey"])) {
					$payKey = $_REQUEST["payKey"];
				}
				if(empty($payKey))
				{
					$payKey = $_SESSION['payKey'];
				}

				$pdRequest = new PaymentDetailsRequest();
				$pdRequest->payKey = $payKey;
				$rEnvelope = new RequestEnvelope();
				$rEnvelope->errorLanguage = "en_US";
				$pdRequest->requestEnvelope = $rEnvelope;

				$ap = new AdaptivePayments();
				$response=$ap->PaymentDetails($pdRequest);
				echo "success|".$payKey."|".$paypal_address;
				
				/* Display the API response back to the browser.
				If the response from PayPal was a success, display the response parameters'
				If the response was an error, display the errors received using APIError.php.
				*/
				if(strtoupper($ap->isSuccess) == 'FAILURE')
				{
					$fault = $ap->getLastError();
					// For error handling
					if(is_array($fault->error))
					{
						$errors_content = '';
						foreach($fault->error as $err) {				
							$errors_content .= $err->message . '<br />';
						}
					}
					else
					{
						$errors_content = "";
						$errors_content .= $fault->error->message;
					}
					echo "error|".$errors_content; exit;
				}/* else {		//Execute payment
					$executePaymentRequest = new ExecutePaymentRequest();
					$executePaymentRequest->payKey = $payKey;
				
					$executePaymentRequest->requestEnvelope = new RequestEnvelope();
					$executePaymentRequest->requestEnvelope->errorLanguage = "en_US";
					$ap_execute = new AdaptivePayments();
					$response=$ap_execute->ExecutePayment($executePaymentRequest);
					
					if(strtoupper($ap_execute->isSuccess) == 'FAILURE')
					{
						$fault = $ap_execute->getLastError();
						// For error handling
						if(is_array($fault->error))
						{
							$errors_content = '';
							foreach($fault->error as $err) {				
								$errors_content .= $err->message . '<br />';
							}
						}
						else
						{
							$errors_content = "";
							$errors_content .= $fault->error->message;
						}
						echo "error|".$errors_content; exit;
					}
					else
					{
						if($response->paymentExecStatus == "COMPLETED")
						{
							if($response->responseEnvelope->ack == "Success")
							{
								echo "error|Execute Payment Successful";
							}
						}
					}
				} */
			}
			catch(Exception $ex) {
	
				$fault = new FaultMessage();
				$errorData = new ErrorData();
				$errorData->errorId = $ex->getFile() ;
				$errorData->message = $ex->getMessage();
				$fault->error = $errorData;
				$fault = $ap->getLastError();
				// For error handling
				if(is_array($fault->error))
				{
					$errors_content = '';
					foreach($fault->error as $err) {				
						$errors_content .= $err->message . '<br />';
					}
				}
				else
				{
					$errors_content = "";
					$errors_content .= $fault->error->message;
				}
				echo "error|".$errors_content; exit;
			}
		}
			
	}
	//==================================================================================================================================
}
add_action('wp_ajax_get_paypal_paykey', 'get_paypal_paykey_callback');
add_action('wp_ajax_nopriv_get_paypal_paykey', 'get_paypal_paykey_callback');

class pagination{
/*
Script Name: *Digg Style Paginator Class
Script URI: http://www.mis-algoritmos.com/2007/05/27/digg-style-pagination-class/
Description: Class in PHP that allows to use a pagination like a digg or sabrosus style.
Script Version: 0.4
Author: Victor De la Rocha
Author URI: http://www.mis-algoritmos.com
*/
	/*Default values*/
	var $total_pages = -1;//items
	var $limit = null;
	var $target = ""; 
	var $page = 1;
	var $adjacents = 2;
	var $showCounter = false;
	var $className = "pagination";
	var $parameterName = "page";
	var $urlF = false;//urlFriendly

	/*Buttons next and previous*/
	var $nextT = "Next";
	var $nextI = "&#187;"; //&#9658;
	var $prevT = "Previous";
	var $prevI = "&#171;"; //&#9668;

	/*****/
	var $calculate = false;
	
	#Total items
	function items($value){$this->total_pages = (int) $value;}
	
	#how many items to show per page
	function limit($value){$this->limit = (int) $value;}
	
	#Page to sent the page value
	function target($value){$this->target = $value;}
	
	#Current page
	function currentPage($value){$this->page = (int) $value;}
	
	#How many adjacent pages should be shown on each side of the current page?
	function adjacents($value){$this->adjacents = (int) $value;}
	
	#show counter?
	function showCounter($value=""){$this->showCounter=($value===true)?true:false;}

	#to change the class name of the pagination div
	function changeClass($value=""){$this->className=$value;}

	function nextLabel($value){$this->nextT = $value;}
	function nextIcon($value){$this->nextI = $value;}
	function prevLabel($value){$this->prevT = $value;}
	function prevIcon($value){$this->prevI = $value;}

	#to change the class name of the pagination div
	function parameterName($value=""){$this->parameterName=$value;}

	#to change urlFriendly
	function urlFriendly($value="%"){
			if(eregi('^ *$',$value)){
					$this->urlF=false;
					return false;
				}
			$this->urlF=$value;
		}
	
	var $pagination;

	function pagination(){}
	function show(){
			if(!$this->calculate)
				if($this->calculate())
					echo "<div class=\"$this->className\">$this->pagination</div>\n";
		}
	function getOutput(){
			if(!$this->calculate)
				if($this->calculate())
					return "<div class=\"$this->className\">$this->pagination</div>\n";
		}
	function get_pagenum_link($id){
			if(strpos($this->target,'?')===false)
					if($this->urlF)
							return str_replace($this->urlF,$id,$this->target);
						else
							return "$this->target?$this->parameterName=$id";
				else
					return "$this->target&$this->parameterName=$id";
		}
	
	function calculate(){
			$this->pagination = "";
			$this->calculate == true;
			$error = false;
			if($this->urlF and $this->urlF != '%' and strpos($this->target,$this->urlF)===false){
					//Es necesario especificar el comodin para sustituir
					echo "Especificaste un wildcard para sustituir, pero no existe en el target<br />";
					$error = true;
				}elseif($this->urlF and $this->urlF == '%' and strpos($this->target,$this->urlF)===false){
					echo "Es necesario especificar en el target el comodin % para sustituir el número de página<br />";
					$error = true;
				}

			if($this->total_pages < 0){
					echo "It is necessary to specify the <strong>number of pages</strong> (\$class->items(1000))<br />";
					$error = true;
				}
			if($this->limit == null){
					echo "It is necessary to specify the <strong>limit of items</strong> to show per page (\$class->limit(10))<br />";
					$error = true;
				}
			if($error)return false;
			
			$n = trim($this->nextT.' '.$this->nextI);
			$p = trim($this->prevI.' '.$this->prevT);
			
			/* Setup vars for query. */
			if($this->page) 
				$start = ($this->page - 1) * $this->limit;             //first item to display on this page
			else
				$start = 0;                                //if no page var is given, set start to 0
		
			/* Setup page vars for display. */
			$prev = $this->page - 1;                            //previous page is page - 1
			$next = $this->page + 1;                            //next page is page + 1
			$lastpage = ceil($this->total_pages/$this->limit);        //lastpage is = total pages / items per page, rounded up.
			$lpm1 = $lastpage - 1;                        //last page minus 1
			
			/* 
				Now we apply our rules and draw the pagination object. 
				We're actually saving the code to a variable in case we want to draw it more than once.
			*/
			
			if($lastpage > 1){
					if($this->page){
							//anterior button
							if($this->page > 1)
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($prev)."\" class=\"prev\">$p</a>";
								else
									$this->pagination .= "<span class=\"disabled\">$p</span>";
						}
					//pages	
					if ($lastpage < 7 + ($this->adjacents * 2)){//not enough pages to bother breaking it up
							for ($counter = 1; $counter <= $lastpage; $counter++){
									if ($counter == $this->page)
											$this->pagination .= "<span class=\"current\">$counter</span>";
										else
											$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
								}
						}
					elseif($lastpage > 5 + ($this->adjacents * 2)){//enough pages to hide some
							//close to beginning; only hide later pages
							if($this->page < 1 + ($this->adjacents * 2)){
									for ($counter = 1; $counter < 4 + ($this->adjacents * 2); $counter++){
											if ($counter == $this->page)
													$this->pagination .= "<span class=\"current\">$counter</span>";
												else
													$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
										}
									$this->pagination .= "...";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
								}
							//in middle; hide some front and some back
							elseif($lastpage - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2)){
									$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
									$this->pagination .= "...";
									for ($counter = $this->page - $this->adjacents; $counter <= $this->page + $this->adjacents; $counter++)
										if ($counter == $this->page)
												$this->pagination .= "<span class=\"current\">$counter</span>";
											else
												$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
									$this->pagination .= "...";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
								}
							//close to end; only hide early pages
							else{
									$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
									$this->pagination .= "...";
									for ($counter = $lastpage - (2 + ($this->adjacents * 2)); $counter <= $lastpage; $counter++)
										if ($counter == $this->page)
												$this->pagination .= "<span class=\"current\">$counter</span>";
											else
												$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
								}
						}
					if($this->page){
							//siguiente button
							if ($this->page < $counter - 1)
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($next)."\" class=\"next\">$n</a>";
								else
									$this->pagination .= "<span class=\"disabled\">$n</span>";
								if($this->showCounter)$this->pagination .= "<div class=\"pagination_data\">($this->total_pages Pages)</div>";
						}
				}

			return true;
		}
}
function get_id_skins() {
	global $wpdb;
	$sql = 'SELECT * FROM '.$wpdb->prefix.'ign_settings WHERE id="1"';
	$res = $wpdb->get_row($sql);
	//$skins = $sql->theme_choices;
	$skins = unserialize($res->theme_choices);
	$content = '';
	if ($skins) {
		foreach ($skins as $skin) {
			$content .= '<option '. ($res->theme_value == $skin ? 'selected="selected"' : '').' value="'.$skin.'">'.$skin.'</option>';
		}
	}
	return $content;
}

add_filter('id_skin', 'get_id_skins', 10, 1);

function deleted_skin_list($skins) {
	$content = '';
	if ($skins) {
		foreach ($skins as $skin) {
			$content .= '<option name="'.$skin.'" id="'.$skin.'" value="'.$skin.'">'.$skin.'</option>';
		}
	}
	return $content;
}

function id_validate_price() {
	global $wpdb;
	if ($_POST) {
		if($_POST['Keys']) {
			$data = $_POST['Keys'][0];
		}
	}
	$level = $data['level'];
	$project = $data['project'];
	$post_id = $data['post_id'];
	if ($level) {
		if ($level == 1) {
			$sql = $wpdb->prepare('SELECT product_price FROM '.$wpdb->prefix.'ign_products WHERE id=%d', $project);
			$res = $wpdb->get_row($sql);
			$price = $res->product_price;
		}
		else {
			$price = get_post_meta($post_id, 'ign_product_level_'.$level.'_price', true);
		}
		echo $price;
		exit;
	}
	else {
		return false;
	}
}

if (is_id_licensed()) {
	add_action('wp_ajax_id_validate_price', 'id_validate_price');
	add_action('wp_ajax_nopriv_id_validate_price', 'id_validate_price');
}

function idpp_products_handler() {
	$projects = ID_Project::get_all_projects();
	print_r(json_encode($projects));
	exit;
}

add_action('wp_ajax_idpp_products_handler', 'idpp_products_handler');
add_action('wp_ajax_nopriv_idpp_products_handler', 'idpp_products_handler');

function project_posts_list_ajax() {
	$projects = ID_Project::get_project_posts();
	print_r(json_encode($projects));
	exit;
}

add_action('wp_ajax_project_posts_list_ajax', 'project_posts_list_ajax');
add_action('wp_ajax_nopriv_project_posts_list_ajax', 'project_posts_list_ajax');

function idpp_process_handler() {
	global $wpdb;

	$product_id = $_POST['Project'];
	$sql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'ign_pay_info WHERE product_id = %s AND status=%s', $product_id, 'W');
	//echo $sql;
	$res = $wpdb->get_results($sql);
	//print_r($res);
	$adaptive_pay_settings = getAdaptivePayPalSettings();

	// GETTING product default settings
	$default_prod_settings = getProductDefaultSettings();

	// Getting product settings and if they are not present, set the default settings as product settings
	$prod_settings = getProductSettings($product_id);
	if (empty($prod_settings))
		$prod_settings = $default_prod_settings;
	require_once 'paypal/lib/AdaptivePayments.php';
	
	# Endpoint: this is the server URL which you have to connect for submitting your API request.
	//Chanege to https://svcs.paypal.com/  to go live */
	if ($adaptive_pay_settings->paypal_mode == "sandbox") {
		define('API_BASE_ENDPOINT', 'https://svcs.sandbox.paypal.com/');
		define('PAYPAL_REDIRECT_URL', 'https://www.sandbox.paypal.com/webscr&cmd=');
		$app_id = "APP-80W284485P519543T";
	}
	else {
		define('API_BASE_ENDPOINT', 'https://svcs.paypal.com/');
		define('PAYPAL_REDIRECT_URL', 'https://www.paypal.com/webscr&cmd=');
		$app_id = $adaptive_pay_setings->app_id;
	}
	
	/***** 3token API credentials *****************/
	define('API_AUTHENTICATION_MODE','3token');
	define('API_USERNAME', $adaptive_pay_settings->api_username);
	define('API_PASSWORD', $adaptive_pay_settings->api_password);
	define('API_SIGNATURE', $adaptive_pay_settings->api_signature);
	require_once 'paypal/lib/Config/paypal_sdk_clientproperties.php';
	$no_success = array();
	$no_failures = array();
	foreach ($res as $payment) {
		if ($payment->preapproval_key !== '') {
			// Setting the necessary variables for the payment
			$returnURL = site_url('/');
			$cancelURL = site_url('/');
			$notifyURL = site_url('/').'?ipn_handler=1';
			$currencyCode = $prod_settings->currency_code;
			$email = $payment->email;
			$preapprovalKey = $payment->preapproval_key;
			$requested = '';

			$payRequest = new PayRequest();
			$payRequest->actionType = "PAY";
			$payRequest->cancelUrl = $cancelURL ;
			$payRequest->returnUrl = $returnURL;
			$payRequest->ipnNotificationUrl = $notifyURL;
			$payRequest->clientDetails = new ClientDetailsType();
			$payRequest->clientDetails->applicationId = $app_id;		//"APP-1JE4291016473214C";
			//$payRequest->clientDetails->deviceId = DEVICE_ID;
			$payRequest->clientDetails->ipAddress = $_SERVER['REMOTE_ADDR'];
			$payRequest->currencyCode = $currencyCode;
			$payRequest->senderEmail = html_entity_decode($email);
			$payRequest->requestEnvelope = new RequestEnvelope();
			$payRequest->requestEnvelope->errorLanguage = "en_US";
			//$payRequest->preapprovalKey = "PA-16707604HP296522Y";
			//print_r($payRequest);
			if($preapprovalKey !== "")
			{
				$payRequest->preapprovalKey = $preapprovalKey ;
				//echo $preapprovalKey."keyhere";
			}          	
			$receiver1 = new receiver();
			$receiver1->email = $adaptive_pay_settings->paypal_email;
			$receiver1->amount = $payment->prod_price;
			
			$payRequest->receiverList = new ReceiverList();
			$payRequest->receiverList = array($receiver1);

			/* 	Make the call to PayPal to get the Pay token
			 *	If the API call succeded, then redirect the buyer to PayPal
			 *	to begin to authorize payment.  If an error occured, show the
			 *	resulting errors
			 */
			$ap = new AdaptivePayments();
			$response=$ap->Pay($payRequest);

			//echo "end of line<br/>";
			if (strtoupper($ap->isSuccess)  == 'SUCCESS') {
			
				$no_success[] = 'success';
				
			}
			else if(strtoupper($ap->isSuccess) == 'FAILURE')
			{
				$no_failures[] = 'failure';
				//echo "inside failure<br/>";
				$fault = $ap->getLastError();
				$errors_content = $fault->error->message;
				//echo $errors_content; 
				// For error handling
				/*if (is_object($fault->error))
				{ 

					//$errors_content = '<table width =\"450px\" align=\"center\">';
					$errors_content;
					$errors_content = '';
					foreach($fault->error as $err) {

						//$errors_content .= '<tr>';
						//$errors_content .= '<td>';
						//$errors_content .= 'Error ID: ' . $err->errorId . '<br />';
						//$errors_content .= 'Domain: ' . $err->domain . '<br />';
						//$errors_content .= 'Severity: ' . $err->severity . '<br />';
						//$errors_content .= 'Category: ' . $err->category . '<br />';
						$errors_content .= $err . "<br />";

						if(empty($err->parameter)) {
							//$errors_content .= '<br />';
						}
						else {
							//$errors_content .= 'Parameter: ' . $err->parameter . '<br /><br />';
						}
							
						//$errors_content .= '</td>';
						//$errors_content .= '</tr>';
					}
					//$errors_content .= '</table>';
				}
				else
				{

					$errors_content = "";
					//$errors_content .= 'Error ID: ' . $fault->error->errorId . '<br />';
					//$errors_content .= 'Domain: ' . $fault->error->domain . '<br />';
					//$errors_content .= 'Severity: ' . $fault->error->severity . '<br />';
					//$errors_content .= 'Category: ' . $fault->error->category . '<br />';
					$errors_content .= $fault->error->message . '<br />';
					if(empty($fault->error->parameter)) {
						//$errors_content .= '</br>';
					}
					else {
						//$errors_content .= 'Parameter: ' . $fault->error->parameter . '<br /><br />';
					}
				}*/
			}
		}
	}
	$response_array['counts'] = array(
				'success' => count($no_success),
				'failures' => count($no_failures));
	print_r(json_encode($response_array));
	exit;
}

add_action('wp_ajax_idpp_process_handler', 'idpp_process_handler');
add_action('wp_ajax_nopriv_idpp_process_handler', 'idpp_process_handler');

//AJAX for product levels
function get_product_levels_callback() {
	global $wpdb;
	require 'languages/text_variables.php';
	if (isset($_POST['Project'])) {
		$project_id = absint($_POST['Project']);
		$project = new ID_Project($project_id);
		
		$product_settings = $project->get_project_settings();
		if (empty($product_settings)) {
			$product_settings = $project->get_project_defaults();
		}
		//GETTING the currency symbol
		if (isset($product_settings)) {
			$currencyCodeValue = $product_settings->currency_code;	
			$cCode = setCurrencyCode($currencyCodeValue);
		}
		else {
			$currencyCodeValue = 'USD';
			$cCode = '$';
		}
		
		$post_id = $project->get_project_postid();
		
		$level_count = get_post_meta($post_id, 'ign_product_level_count', true);
		$meta_price_1 = get_post_meta( $post_id, "ign_product_price", true );
		$options = "<option data-price='".number_format($meta_price_1, 2, '.', '')."' value=\"1\">".$tr_Level." 1: ".$tr_Price." ".$cCode.number_format(absint($meta_price_1), 2, '.', ',')."</option>";
		if (isset($level_count) && $level_count > 0) {
			
			for ($i=1 ; $i <= $level_count ; $i++) {
				$meta_price = get_post_meta( $post_id, $name="ign_product_level_".($i)."_price", true );
				if ($meta_price !== "") {
					$options .= "<option data-price='".number_format($meta_price, 2, '.', '')."' value=\"".($i)."\">".$tr_Level." ".($i).": ".$tr_Price." ".$cCode.number_format($meta_price, 2, '.', '')."</option>";
				}
			}
		}
		echo $options;
	}
	exit;
			
}
add_action('wp_ajax_get_product_levels', 'get_product_levels_callback');
add_action('wp_ajax_nopriv_get_product_levels', 'get_product_levels_callback');

function get_order_level() {
	$order_id = absint($_POST['Order']);
	if (!empty($order_id)) {
		$order = new ID_Order($order_id);
		$get_order = $order->get_order();
		if (isset($get_order)) {
			echo $get_order->product_level;
		}
	}
	exit;
}

add_action('wp_ajax_get_order_level', 'get_order_level');
add_action('wp_ajax_nopriv_get_order_level', 'get_order_level');

//AJAX for getting the new product number automatically
function get_new_product_callback() {
	global $wpdb;

	//$sql = 'SELECT id FROM '.$wpdb->prefix.'ign_products ORDER BY id DESC';
	//$res = $wpdb->get_results($sql);
	if (isset($_POST['action_type'])) {
		$post_id = $_POST['action_type'];
		$prod_no = get_post_meta($post_id, 'ign_project_id', true);
	}
	else {
		$prod_no = '';
	}
	
	echo $prod_no;
	exit;			
}
add_action('wp_ajax_get_new_product', 'get_new_product_callback');
add_action('wp_ajax_nopriv_get_new_product', 'get_new_product_callback');

function get_deck_list() {
	$list = Deck::get_deck_list();
	$decks = array();
	foreach ($list as $item) {
		$deck = array();
		$deck['id'] = $item->id;
		$deck['attrs'] = unserialize($item->attributes);
		$decks[] = $deck;
	}
	print_r(json_encode($decks));
	exit;
}

add_action('wp_ajax_get_deck_list', 'get_deck_list');
add_action('wp_ajax_nopriv_get_deck_list', 'get_deck_list');

function get_deck_attrs() {
	global $wpdb;
	if (isset($_POST['Deck'])) {
		$deck_id = absint($_POST['Deck']);
		if ($deck_id > 0) {
			$settings = Deck::get_deck_attrs($deck_id);
			$attrs = unserialize($settings->attributes);
			print_r(json_encode($attrs));
		}
	}
	exit;
}

add_action('wp_ajax_get_deck_attrs', 'get_deck_attrs');
add_action('wp_ajax_nopriv_get_deck_attrs', 'get_deck_attrs');

function id_hide_notice() {
	if (isset($_POST['Notice'])) {
		$notice = $_POST['Notice'];
		//echo $notice;
		update_option($notice, 'off');
	}
	exit;
}

add_action('wp_ajax_id_hide_notice', 'id_hide_notice');
add_action('wp_ajax_nopriv_id_hide_notice', 'id_hide_notice');
?>