<?php

/*
Plugin Name: IgnitionDeck Crowdfunding
URI: http://IgnitionDeck.com
Description: A custom crowdfunding platform for WordPress. IgnitionDeck allows you to create unlimited and dynamic fundraising campaigns for physical and/or digital goods, integrates with a variety of email and ecommerce platforms, and is compatible with all WordPress themes 3.1+.
Version: 1.5.5
Author: Virtuous Giant
Author URI: http://VirtuousGiant.com
License: GPL2
*/

/*
This sections handles the following:

1. IgnitionDeck Pro Activation
2. WordPress Multisite Activation
3. Standard WordPress Activation
*/


global $ign_db_version;
global $ign_installed_ver;
$ign_db_version = "1.5.5";
$ign_installed_ver = get_option( "ign_db_version" );


function is_id_network_activated() {
	$active_plugins = get_site_option( 'active_sitewide_plugins');
	if (isset($active_plugins['ignitiondeck-crowdfunding/ignitiondeck.php'])) {
		if (is_multisite()) {
			return true;
		}
	}
	return false;
}

if (is_multisite() && is_id_pro()) {
	// we only run this if we're network activating
	if (is_network_admin()) {
		register_activation_hook(__FILE__,'install_id_for_blogs');
	}
	// we are not in network admin, so we run regular activation script
	else {
		register_activation_hook(__FILE__,'ign_pre_install');
	}
}

else {
	register_activation_hook(__FILE__,'ign_pre_install');
	register_activation_hook(__FILE__,'ign_set_defaults');
}

if (is_id_network_activated() && is_id_pro()) {
	add_action('wpmu_new_blog', 'ign_pre_install', 1, 1);
	add_action('wpmu_new_blog', 'ign_set_defaults');
}

function install_id_for_blogs() {
	global $wpdb;
	$sql = 'SELECT * FROM '.$wpdb->base_prefix.'blogs';
	$res = $wpdb->get_results($sql);
	foreach ($res as $blog) {
		ign_pre_install($blog->blog_id);
	}
}

function ign_pre_install ($blog_id = null) {
    global $wpdb;
    global $ign_db_version;
    global $charset_collate;
	
	if (!empty($blog_id) && is_id_network_activated() && is_id_pro()) {
		if ($blog_id == 1) {
			$wpdb->base_prefix = $wpdb->base_prefix;
		}
		else {
			$wpdb->base_prefix = $wpdb->base_prefix.$blog_id.'_';
		}
	}
	else if (!empty($blog_id) && is_id_pro()) {
		if ($blog_id == 1) {
			$wpdb->base_prefix = $wpdb->prefix;
		}
		else {
			$wpdb->base_prefix = $wpdb->prefix.$blog_id.'_';
		}
	}
	else {
		$wpdb->base_prefix = $wpdb->prefix;
	}

	$table_name = $wpdb->base_prefix . "ign_settings";
    
    $sql = "CREATE TABLE " . $table_name . " (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
	theme_value VARCHAR( 250 ) NOT NULL DEFAULT 'style1',
	theme_choices TEXT (65535),
	prod_page_fb TINYINT( 1 ) NOT NULL,
	prod_page_twitter TINYINT( 1 ) NOT NULL,
	prod_page_linkedin TINYINT( 1 ) NOT NULL,
	prod_page_google TINYINT( 1 ) NOT NULL,
	prod_page_pinterest TINYINT( 1 ) NOT NULL,
	id_widget_logo_on TINYINT( 1 ) NOT NULL DEFAULT '1',
	id_widget_link VARCHAR( 200 ) NOT NULL DEFAULT 'http://ignitiondeck.com',
	ask_a_question TINYINT( 1 ) NOT NULL,
	ask_email VARCHAR( 100 ) NOT NULL,
	UNIQUE KEY id (id)
	) $charset_collate;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    update_option("ign_db_version", $ign_db_version);
	
    $table_name = $wpdb->base_prefix . "ign_products";
    
    $sql = "CREATE TABLE " . $table_name . " (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    product_image VARCHAR( 250 ) NOT NULL ,
    product_name VARCHAR( 250 ) NOT NULL ,
    product_url VARCHAR( 250 ) NOT NULL ,
    ign_product_title VARCHAR ( 250 ) NOT NULL,
    ign_product_limit VARCHAR ( 250 ),
    product_details TEXT NOT NULL ,
    product_price DOUBLE NOT NULL ,
    goal DOUBLE NOT NULL ,
    created_at DATETIME, 
    UNIQUE KEY id (id)
    ) $charset_collate;";
    dbDelta($sql);
	
	$table_name = $wpdb->base_prefix . "ign_product_settings";

    $sql = "CREATE TABLE " . $table_name . " (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	product_id VARCHAR( 250 ) NOT NULL,
	mailchimp_api_key VARCHAR( 250 ) NOT NULL,
	mailchimp_list_id VARCHAR( 250 ) NOT NULL,
	aweber_email VARCHAR( 250 ) NOT NULL,
	active_mailtype enum('mailchimp','aweber') NOT NULL,
	form_settings TEXT NOT NULL,
	paypal_email VARCHAR( 250 ) NOT NULL,
	currency_code VARCHAR( 10 ) NOT NULL,
	UNIQUE KEY id (id)
	) $charset_collate;";
    dbDelta($sql);
	
    $pay_info = $wpdb->base_prefix . "ign_pay_info";

    $sql = "CREATE TABLE " . $pay_info . " (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	first_name VARCHAR( 250 ) NOT NULL ,
	last_name VARCHAR( 250 ) NOT NULL ,
	email VARCHAR( 250 ) NOT NULL ,
	address VARCHAR( 250 ) NOT NULL ,
	country VARCHAR( 250 ) NOT NULL ,
	state VARCHAR( 250 ) NOT NULL ,
	city VARCHAR( 250 ) NOT NULL ,
	zip VARCHAR( 250 ) NOT NULL ,
	product_id INT( 20 ) NOT NULL ,
	transaction_id varchar( 250 ) NOT NULL,
	preapproval_key varchar (250) NOT NULL,
	product_level INT( 2 ) NOT NULL,
	prod_price VARCHAR(200) NOT NULL,
	status VARCHAR( 250 ) NOT NULL DEFAULT 'P',
	created_at DATETIME, 
	UNIQUE KEY id (id)
	) $charset_collate;";
    dbDelta($sql);
    
	// Payment selection settings
	$pay_method_selection = $wpdb->base_prefix . "ign_pay_selection";
    
    $sql_pay_sett = "CREATE TABLE " . $pay_method_selection . " (
	id MEDIUMINT( 9 ) NOT NULL AUTO_INCREMENT,
	payment_gateway VARCHAR( 100 ) NOT NULL,
	modified_date DATETIME NOT NULL,
	UNIQUE KEY id (id)
	) $charset_collate;";
    dbDelta($sql_pay_sett);
	
	// Standard Payment settings
	$pay_settings = $wpdb->base_prefix . "ign_pay_settings";

    $sql_pay_sett = "CREATE TABLE " . $pay_settings . " (
	id MEDIUMINT( 9 ) NOT NULL AUTO_INCREMENT,
	identity_token VARCHAR( 250 ) NOT NULL,
	paypal_email VARCHAR( 250 ) NOT NULL,
	paypal_override TINYINT( 1 ) NOT NULL,
	paypal_mode ENUM('sandbox','production') NOT NULL,
	UNIQUE KEY id (id)
	) $charset_collate;";
    dbDelta($sql_pay_sett);
	
	// Payment settings for Adaptive payments
	$adaptive_pay_settings = $wpdb->base_prefix . "ign_adaptive_pay_settings";
    
    $sql_pay_sett = "CREATE TABLE " . $adaptive_pay_settings . " (
	id MEDIUMINT( 9 ) NOT NULL AUTO_INCREMENT,
	paypal_email VARCHAR( 100 ) NOT NULL,
	app_id VARCHAR( 100 ) NOT NULL,
	api_username VARCHAR( 100 ) NOT NULL,
	api_password VARCHAR( 100 ) NOT NULL,
	api_signature VARCHAR( 200 ) NOT NULL,
	pre_approval_key VARCHAR( 100 ) NOT NULL,
	paypal_mode ENUM('sandbox','production') NOT NULL,
	fund_type ENUM('standard', 'fixed') NOT NULL,
	UNIQUE KEY id (id)
	) $charset_collate;";
    dbDelta($sql_pay_sett);
	
	// Aweber Options
	$aweber_settings = $wpdb->base_prefix . "ign_aweber_settings";
	
    $sql_aweber = "CREATE TABLE " . $aweber_settings . " (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	list_email VARCHAR( 100 ) NOT NULL,
	is_active TINYINT( 2 ) NOT NULL,
	UNIQUE KEY id (id)
	) $charset_collate;";
    dbDelta($sql_aweber);

    // Mailchimp
    $mailchimp_subscription = $wpdb->base_prefix . "ign_mailchimp_subscription";

    $sql_pay_sett = "CREATE TABLE " . $mailchimp_subscription . " (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	api_key VARCHAR( 250 ) NOT NULL ,
	list_id VARCHAR( 250 ) NOT NULL,
	region VARCHAR( 50 ) NOT NULL ,
	is_active TINYINT( 2 ) NOT NULL,
	UNIQUE KEY id (id)
	) $charset_collate;";
    dbDelta($sql_pay_sett);

    $form_settings = $wpdb->base_prefix . "ign_form";

    $sql = "CREATE TABLE " . $form_settings . " (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    form_settings TEXT NOT NULL ,
    UNIQUE KEY id (id)
    ) $charset_collate;";
    dbDelta($sql);
	
    $ign_form_settings = $wpdb->base_prefix . "ign_prod_default_settings";

    $sql = "CREATE TABLE " . $ign_form_settings . " (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    form_settings TEXT NOT NULL,
	currency_code VARCHAR( 10 ) NOT NULL DEFAULT 'USD',
    UNIQUE KEY id (id)
    ) $charset_collate;";
    dbDelta($sql);

    $ign_deck_settings = $wpdb->base_prefix."ign_deck_settings";

    $sql = "CREATE TABLE ".$ign_deck_settings." (
    	id mediumint(9) NOT NULL AUTO_INCREMENT,
    	attributes LONGTEXT NOT NULL,
    	UNIQUE KEY id (id)
    ) $charset_collate;";
	dbDelta($sql);

    $url = dirname(dirname(dirname(dirname(__FILE__))));
	if (file_exists($url.'/log.txt')) {
		unlink($url.'/log.txt');
	}

	do_action('id_after_install');

}

function ign_set_defaults() {
	global $wpdb;
	global $ign_installed_ver;

	$check_db = 'SELECT id_widget_logo_on FROM '.$wpdb->prefix.'ign_settings WHERE id="1"';
	$return_check = $wpdb->get_row($check_db);

	if( !isset($return_check) ) {
		$sql = "INSERT INTO ".$wpdb->prefix."ign_settings (id_widget_logo_on) VALUES ('1')";
		$update = $wpdb->query($sql);
	}
	update_option('id_email_inactive', 1);
	do_action('id_set_defaults');

	// convert image1 to featured image
	$projects = ID_Project::get_project_posts();
	if (!empty($projects)) {
		global $wpdb;
		foreach ($projects as $project) {
			$post_id = $project->ID;
			$thumb = get_the_post_thumbnail($post_id);
			if (empty($thumb)) {
				$image1 = get_post_meta($post_id, 'ign_product_image1', true);
				$sql = $wpdb->prepare('SELECT ID FROM '.$wpdb->prefix.'posts WHERE guid = %s', $image1);
				$res = $wpdb->get_row($sql);
				if (!empty($res)) {
					$attachment_id = $res->ID;
					set_post_thumbnail($post_id, $attachment_id);
				}
			}
		}
	}
	if (empty($ign_installed_ver)) {
		update_option('idcf_auto_insert', 1);
	}
}

//register_deactivation_hook( __FILE__, 'ignitiondeck_deactivate' );

function ignitiondeck_deactivate(){
    global $wpdb;
    $check = 'SELECT * FROM '.$wpdb->prefix.'ign_pay_settings WHERE id = 1';
    $return_check = $wpdb->get_results($check);
    if (isset($return_check->charge_mode)) {
    	$sql = 'ALTER TABLE '.$wpdb->prefix.'ign_pay_settings DROP COLUMN charge_mode';
    	$wpdb->query($sql);
    }
    $check2 = 'SELECT * FROM '.$wpdb->prefix.'ign_adaptive_pay_settings WHERE id = 1';
    $return_check2 = $wpdb->get_results($check2);
    if (isset($return_check2->charge_mode)) {
    	$sql2 = 'ALTER TABLE '.$wpdb->prefix.'ign_adaptive_pay_settings DROP COLUMN charge_mode';
    	$wpdb->query($sql2);
    }
}

if (is_id_network_activated() && is_id_pro()) {
	add_action('delete_blog', 'ignitiondeck_uninstall', 1, 1);
	register_uninstall_hook(__FILE__,'id_remove_all_traces');
}
else {
	register_uninstall_hook(__FILE__, 'ignitiondeck_uninstall');
}

function id_remove_all_traces() {
	global $wpdb;
	$sql = 'SELECT * FROM '.$wpdb->base_prefix.'blogs';
	$res = $wpdb->get_results($sql);
	foreach ($res as $blog) {
		ignitiondeck_uninstall($blog->blog_id);
	}
}

function ignitiondeck_uninstall($blog_id = null) {
	global $wpdb;
	if (!empty($blog_id) && is_id_network_activated() && is_id_pro()) {
		if ($blog_id == 1) {
			$wpdb->base_prefix = $wpdb->base_prefix;
		}
		else {
			$wpdb->base_prefix = $wpdb->base_prefix.$blog_id.'_';
		}
	}
	else if (!empty($blog_id) && is_id_pro()) {
		if ($blog_id == 1) {
			$wpdb->base_prefix = $wpdb->prefix;
		}
		else {
			$wpdb->base_prefix = $wpdb->prefix.$blog_id.'_';
		}
	}
	else {
		$wpdb->base_prefix = $wpdb->prefix;
	}
	$sql = 'DROP TABLE IF EXISTS '.$wpdb->base_prefix.'ign_adaptive_pay_settings, '.$wpdb->base_prefix.'ign_aweber_settings, '.$wpdb->base_prefix
	.'ign_customers, '.$wpdb->base_prefix.'ign_facebookapp_settings, '.$wpdb->base_prefix.'ign_form, '.$wpdb->base_prefix.'ign_mailchimp_subscription, '.
	$wpdb->base_prefix.'ign_pay_info, '.$wpdb->base_prefix.'ign_pay_selection, '.$wpdb->base_prefix.'ign_pay_settings, '.$wpdb->base_prefix
	.'ign_products, '.$wpdb->base_prefix.'ign_product_settings, '.$wpdb->base_prefix.'ign_prod_default_settings, '.$wpdb->base_prefix.'ign_questions, '.
	$wpdb->base_prefix.'ign_settings, '.$wpdb->base_prefix.'ign_twitterapp_settings, '.$wpdb->base_prefix.'ign_deck_settings';
	$res = $wpdb->query($sql);

	$options = array(
		'id_license_key',
		'is_id_pro',
		'is_id_basic',
		'id_settings_option',
		'id_defaults_notice',
		'id_settings_notice',
		'id_products_notice',
		'id_purchase_default',
		'id_ty_default',
		'id_email_inactive',
		'ign_db_version',
		);
	foreach ($options as $option) {
		delete_option($option);
	}
	ID_Project::delete_project_posts();
}
/*
End Pro Activation, Multisite Activation, Standard Activation
*/

define( 'ID_PATH', plugin_dir_path(__FILE__) );

include_once 'classes/class-id_form.php';
include_once 'classes/class-project_widget.php';
include_once 'classes/class-id_project.php';
include_once 'classes/class-deck.php';
include_once 'classes/class-id_purchase_form.php';
if (is_id_pro()) {
	include_once 'classes/class-id_fes.php';
}
include_once 'classes/class-id_order.php';
include_once 'ignitiondeck-functions.php';
include_once 'ignitiondeck-admin.php';
include_once 'ignitiondeck-postmeta.php';
include_once 'ignitiondeck-shortcodes.php';
//include_once 'ignitiondeck-globals.php';
if (is_id_pro()) {
	include_once 'ignitiondeck-enterprise.php';
}
include_once 'ignitiondeck-update.php';
$active_plugins = get_option('active_plugins', true);
if (in_array('ignitiondeck/idf.php', $active_plugins) && is_id_licensed()) {
	include_once plugin_dir_path(dirname(__FILE__)).'/ignitiondeck/idf.php';
}
else if (is_multisite() && is_id_network_activated() && file_exists(plugin_dir_path(dirname(__FILE__)).'/ignitiondeck/idf.php')) {
	include_once plugin_dir_path(dirname(__FILE__)).'/ignitiondeck/idf.php';
}
if (idf_exists()) {
	include_once 'idf/ignitiondeck-idf.php';
}
if (idf_exists() && idf_platform() == 'idc') {
	include_once 'ignitiondeck-idc.php';
}
include_once 'ignitiondeck-api.php';
include_once 'ignitiondeck-filters.php';

/**
 * Register ignitiondeck domain for translation texts
 */
add_action( 'init', 'languageLoad' );
function languageLoad() {
	load_plugin_textdomain( 'ignitiondeck', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
}
require ('languages/text_variables.php');		

//Not currently in use
//add_action( 'init', 'ignitiondeck_init' );
function ignitiondeck_init(){

}

add_action('wp_head', 'id_fb_ogg');

function id_fb_ogg() {
	global $post;
	$show_ogg = false;
	if (isset($post)) {
		$post_content = $post->post_content;
		if ($post->post_type == 'ignition_product') {
			$post_id = $post->ID;
			$show_ogg = true;
		}
		else if (strpos($post_content, 'project_')) {
			$pos = strpos($post_content, 'product=');
			$project_id = absint(substr($post_content, $pos + 9, 1));
			if (isset($project_id) && $project_id > 0) {
				$project = new ID_Project($project_id);
				$post_id = $project->get_project_postid();
				$post = get_post($post_id);
				$show_ogg = true;
			}
		}
		// since we are now using a query, we don't need the following code
		/*else {
			$fh_ogg = false;
			$theme = wp_get_theme();
			if (strpos($theme->get('Name'), '500')) {
				// using 500 framework
				$fh_ogg = true;
			}
			else if ($theme->get('Template') == 'fivehundred') {
				// using 500 child theme
				$fh_ogg = true;
			}
			if ($fh_ogg) {
				$fh_options = get_option('fivehundred_theme_settings');
				if (!empty($fh_options)) {
					$home_project = $fh_options['home'];
					if (isset($home_project) && $home_project > 0) {
						echo $home_project;
						$project = new ID_Project($home_project);
						$post_id = $project->get_project_postid();
						$post = get_post($post_id);
						$show_ogg = true;
					}
				}
			}
		}*/
	}
	if ($show_ogg) {
		$current_site = get_option('blogname');
		$image = ID_Project::get_project_thumbnail($post->ID);
		$description = strip_tags(html_entity_decode(get_post_meta($post_id, 'ign_project_description', true)));
		$meta = '<meta property="og:image" content="'.$image.'" />';
		$meta .= '<meta property="og:title" content="'.$post->post_title.'" />';
		$meta .= '<meta property="og:url" content="'.get_permalink($post->ID).'" />';
		$meta .= '<meta property="og:site_name" content="'.$current_site.'" />';
		$meta .= '<meta property="og:description" content="'.$description.'" />';
		echo $meta;
	}
	wp_reset_query();
}

// Deregister Woo Media Uploader on Project Pages
function disable_woo_media($hook) {
	global $post_type;
	if ($post_type == 'ignition_product') {
		wp_dequeue_script('woo-medialibrary-uploader');
		wp_deregister_script('woo-medialibrary-uploader');
	}
}
add_action('wp_print_scripts', 'disable_woo_media', 20);

/**
 * Include stylesheets
 */
function enqueue_front_css(){

	$theme_name = getThemeFileName();
	
    wp_register_style('ignitiondeck-base', plugins_url('/ignitiondeck-base.css', __FILE__));
    if (file_exists(ID_PATH.'/ignitiondeck-custom.css')) {
    	wp_register_style('ignitiondeck-custom', plugins_url('/ignitiondeck-custom.css', __FILE__));
    	wp_enqueue_style('ignitiondeck-custom');
    }
    wp_register_style($theme_name, plugins_url('/skins/'.$theme_name.'.css', __FILE__));
    wp_enqueue_style('ignitiondeck-base');
    wp_enqueue_style($theme_name);
}
add_action('wp_enqueue_scripts', 'enqueue_front_css');

/**
 * includeJavascript files
 */
function enqueue_front_js() {
    wp_register_script( 'ignitiondeck', plugins_url('/js/ignitiondeck.js', __FILE__));
    wp_register_script('idlightbox', plugins_url('/js/idlightbox.js', __FILE__));
    wp_register_script( 'ddslick', plugins_url('/js/jquery.ddslick.min.js', __FILE__));
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'ddslick');
    wp_enqueue_script( 'ignitiondeck' );
    wp_enqueue_script('idlightbox');
    $settings = getSettings();
    if ($settings->prod_page_fb == 1) {
    	global $post;
    	global $wpdb;
    	if (isset($post)) {
    		$post_name = $post->post_name;

	    	$sql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'postmeta WHERE meta_key = %s AND meta_value=%s', 'ign_post_name', $post_name);
	    	$res = $wpdb->get_results($sql);
	    	if (isset($res) && !isset($_GET['purchaseform'])) {
	    		wp_register_script( 'facebook', plugins_url('/js/facebook.js', __FILE__));
	 			wp_enqueue_script( 'facebook');
	    	}
	    	
	    	else if (isset($post->post_type) && $post->post_type == 'ignition_product' && !isset($_GET['purchaseform'])) {
	    		wp_register_script( 'facebook', plugins_url('/js/facebook.js', __FILE__));
	 			wp_enqueue_script( 'facebook');
	    	}
    	}
	}
	if (is_multisite() && is_id_network_activated()) {
		$id_ajaxurl = network_home_url('/', 'relative').'wp-admin/admin-ajax.php';
	}
	else {
    	$id_ajaxurl = site_url('/', 'relative').'wp-admin/admin-ajax.php';
    }
    $id_siteurl = site_url('/');
    wp_localize_script('ignitiondeck', 'id_ajaxurl', $id_ajaxurl);
    wp_localize_script('ignitiondeck', 'id_siteurl', $id_siteurl);
}
add_action('wp_enqueue_scripts', 'enqueue_front_js');

function id_font_awesome() {
	wp_register_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');
	wp_enqueue_style('font-awesome');
}
add_action('wp_enqueue_scripts', 'id_font_awesome');

// Initializing our widget in the admin area
add_action( 'widgets_init', 'showproduct_load_widgets' );
function showproduct_load_widgets() {
    register_widget( 'Product_Widget' );
}

/**
 * Proccess Product buyout
 */
function productBuyout(){
	require ('languages/text_variables.php');
    if(isset($_POST['submitPaymentPopup'])){
    	$first_name = '';
    	$last_name = '';
    	$email = '';
    	$address = '';
    	$country = '';
    	$state = '';
    	$city = '';
    	$zip = '';
    	$product_id = 0;
    	$level = 0;
    	$price = '0.00';
		// Store all the required variables in SESSION to get them later
		session_start();
		if (isset($_POST['first_name'])) {
			$first_name = esc_attr($_POST['first_name']);
		}
		if (isset($_POST['last_name'])) {
			$last_name = esc_attr($_POST['last_name']);
		}
		if (isset($_POST['email'])) {
			$email = esc_attr($_POST['email']);
		}
		if (isset($_POST['address'])) {
			$address = esc_attr($_POST['address']);
		}
		if (isset($_POST['country'])) {
			$country = esc_attr($_POST['country']);
		}
		if (isset($_POST['state'])) {
			$state = esc_attr($_POST['state']);
		}
		if (isset($_POST['city'])) {
			$city = esc_attr($_POST['city']);
		}
		if (isset($_POST['zip'])) {
			$zip = esc_attr($_POST['zip']);
		}
		if (isset($_POST['project_id'])) {
			$product_id = absint($_POST['project_id']);
		}
		if (isset($_POST['level'])) {
			$level = absint($_POST['level']);
		}
		if (isset($_POST['price'])) {
			$price = esc_attr(str_replace(',', '', $_POST['price']));
		}
		$payment_variables = array(
			"first_name" => $first_name,
			"last_name" => $last_name,
			"email" => $email,
			"address" => $address,
			"country" => $country,
			"state" => $state,
			"city" => $city,
			"zip" => $zip,
			"product_id" => $product_id,
			"level_select" => $level,
			"price" => $price
		);
		$_SESSION['ig_payment_variables'] = serialize($payment_variables);
            $project_id = $_POST['project_id'];
            $project = new ID_Project($project_id);
            $post_id = $project->get_project_postid();
            $paymentSettings = getPaymentSettings();
            // use this for default
            $paypal_email = $paymentSettings->paypal_email;
			$productDetails = $project->the_project();
			
			//GETTING product default settings
			$default_prod_settings = getProductDefaultSettings();
			$prod_settings = $default_prod_settings;
			// see if we have custom settings
			$custom_settings = $project->get_project_settings();
			if (!empty($custom_settings)) {
				$prod_settings = $custom_settings;
				if (!empty($custom_settings->paypal_email)) {
					$paypal_email = $custom_settings->paypal_email;
				}
			}

			if ($paymentSettings->paypal_mode == "sandbox")
					$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			else 
					$url = "https://www.paypal.com/cgi-bin/webscr";
			
			$notifyURL = site_url(). "/?ipn_handler=1&payment_vars=".urlencode($_SESSION['ig_payment_variables']);

            include_once 'templates/_paypalForm.php';

    }
}
if (is_id_licensed()) {
	add_action('init', 'productBuyout');
}

/*
 *	Function for catching the submission of popup, for using Paypal Adaptive Payments
 */
function projectPurchaseAdaptive() {
	if (isset($_POST)) {
		//exit;
	}
	if (isset($_POST['submitPaymentAdaptive'])) {
		// Store all the required variables in SESSION to get them later
		session_start();

		$project_id = $_POST['project_id'];
        $project = new ID_Project($project_id);
		
		$payment_variables = array(
			"fname" => (isset($_POST['first_name']) ? $_POST['first_name'] : ''),
			"lname" => (isset($_POST['last_name']) ? $_POST['last_name'] : ''),
			"email" => (isset($_POST['email']) ? $_POST['email'] : ''),
			"address" => (isset($_POST['address']) ? $_POST['address'] : ''),
			"country" => (isset($_POST['country']) ? $_POST['country'] : ''),
			"state" => (isset($_POST['state']) ? $_POST['state'] : ''),
			"city" => (isset($_POST['city']) ? $_POST['city'] : ''),
			"zip" => (isset($_POST['zip']) ? $_POST['zip'] : ''),
			"product_id" => (isset($_POST['project_id']) ? absint($_POST['project_id']) : ''),
			"level" => (isset($_POST['level']) ? absint($_POST['level']) : ''),
			"prod_price" => (isset($_POST['price']) ? str_replace(',', '', $_POST['price']) : '')
		);

		$_SESSION['ig_payment_variables'] = http_build_query($payment_variables);

		//print_r($payment_variables);
		//echo $_SESSION['ig_payment_variables'];

		// Getting the Adaptive payment settings
		$adaptive_pay_settings = getAdaptivePayPalSettings();

		require_once 'paypal/lib/AdaptivePayments.php';
		
		// GETTING product default settings
		$default_prod_settings = getProductDefaultSettings();
		
		// Getting product settings and if they are not present, set the default settings as product settings
		$prod_settings = $project->get_project_settings();
		if (empty($prod_settings))
			$prod_settings = $default_prod_settings;
		

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
			$app_id = $adaptive_pay_settings->app_id;
		}

		/***** 3token API credentials *****************/
		define('API_AUTHENTICATION_MODE','3token');
		define('API_USERNAME', $adaptive_pay_settings->api_username);
		define('API_PASSWORD', $adaptive_pay_settings->api_password);
		define('API_SIGNATURE', $adaptive_pay_settings->api_signature);
		define('X_PAYPAL_APPLICATION_ID', $app_id);
		require_once 'paypal/lib/Config/paypal_sdk_clientproperties.php';
		//print_r($adaptive_pay_settings);
		// Setting the necessary variables for the payment
		$returnURL = site_url()."/?payment_success=1&product_id=".$_POST['project_id'];
		$cancelURL = site_url(). "/?payment_cancel=1" ;
		$notifyURL = esc_url(site_url()). "/?ipn_handler=1&".$_SESSION['ig_payment_variables'];
		$currencyCode = $prod_settings->currency_code;
		$email = esc_attr($_POST['email']);
		$preapprovalKey = "";
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
		$payRequest->senderEmail = $email;
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
		$receiver1->amount = esc_attr(str_replace(',', '', $_POST['price']));
		
		$payRequest->receiverList = new ReceiverList();
		$payRequest->receiverList = array($receiver1);

		/* 	Make the call to PayPal to get the Pay token
		 *	If the API call succeded, then redirect the buyer to PayPal
		 *	to begin to authorize payment.  If an error occured, show the
		 *	resulting errors
		 */
		$ap = new AdaptivePayments();
		//print_r($ap);
		$response=$ap->Pay($payRequest);

		//echo "end of line<br/>";
		if(strtoupper($ap->isSuccess) == 'FAILURE')
		{
			//echo "inside failure<br/>";
			$fault = $ap->getLastError();

			if (isset($fault)) {
				
				$errors = $fault->error;
			}
			if (count($errors) > 1) {
				$errors_content = array();
				foreach ($errors as $error) {
					$errors_content[] = $error->message;
				}
			}
			else {
				$errors_content = $errors->message;
			}
			

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
			if ($_POST['price'] > 0) {
				$_SESSION['paypal_errors_content'] = $errors_content;
			}
			else {
				$_SESSION['paypal_errors_content'] = "Please enter an amount greater than 0.00";
			}
		}
		else
		{
			//echo "inside non-fail<br/>";
			$_SESSION['payKey'] = $response->payKey;
			if($response->paymentExecStatus == "COMPLETED")
			{
				//$location = "PaymentDetails.php";
				$success_url = site_url()."/?payment_success=1&product_id=".$_POST['project_id'];
				echo '<script type="text/javascript">window.location="'.$success_url.'";</script>';
			}
			else
			{
				$token = $response->payKey;
				$payPalURL = PAYPAL_REDIRECT_URL.'_ap-payment&paykey='.$token;
				echo '<script type="text/javascript">window.location="'.$payPalURL.'";</script>';
				//header("Location: ".$payPalURL);
			}
		}

	}
}
if (is_id_licensed()) {
	add_action('init', 'projectPurchaseAdaptive');
}

function adaptivePreapproval() {
	if (isset($_POST['btnSubmitPreapproval'])) {
		global $wpdb;
		$tz = get_option('timezone_string');
		if (empty($tz)) {
			$tz = 'UTC';
		}
		date_default_timezone_set($tz);
		//print_r($_POST);
		session_start();

		$payment_variables = array(
			"fname" => $_POST['first_name'],
			"lname" => $_POST['last_name'],
			"email" => $_POST['email'],
			"address" => $_POST['address'],
			"country" => $_POST['country'],
			"state" => $_POST['state'],
			"city" => $_POST['city'],
			"zip" => $_POST['zip'],
			"product_id" => absint($_POST['project_id']),
			"level" => absint($_POST['level']),
			"prod_price" => str_replace(',', '', $_POST['price'])
		);
		$preapproval_key = "";
		$project = new ID_Project($variables['product_id']);
		$post_id = $project->get_project_postid();
		$product_name = get_the_title($post_id);
		$_SESSION['ig_payment_variables'] = http_build_query($payment_variables);

		// Getting the Adaptive payment settings
		$adaptive_pay_settings = getAdaptivePayPalSettings();

		require_once 'paypal/lib/AdaptivePayments.php';
		
		// GETTING product default settings
		$default_prod_settings = getProductDefaultSettings();
		
		// Getting product settings and if they are not present, set the default settings as product settings
		$prod_settings = getProductSettings(absint(esc_attr($_POST['project_id'])));

		if (empty($prod_settings)) {
			$prod_settings = $default_prod_settings;
		}
		# Endpoint: this is the server URL which you have to connect for submitting your API request.
		//Chanege to https://svcs.paypal.com/  to go live */
		if ($adaptive_pay_settings->paypal_mode == "sandbox") {
			define('API_BASE_ENDPOINT', 'https://svcs.sandbox.paypal.com/AdaptivePayments/Preapproval/');
			define('PAYPAL_REDIRECT_URL', 'https://www.sandbox.paypal.com/webscr&cmd=_ap-preapproval&preapprovalkey='.$preapproval_key);
			$app_id = "APP-80W284485P519543T";
		}
		else {
			define('API_BASE_ENDPOINT', 'https://svcs.paypal.com/AdaptivePayments/Preapproval/');
			define('PAYPAL_REDIRECT_URL', 'https://www.paypal.com/webscr&cmd=_ap-preapproval&preapprovalkey='.$preapproval_key);
			$app_id = $adaptive_pay_settings->app_id;
		}

		/***** 3token API credentials *****************/
		define('API_AUTHENTICATION_MODE','3token');
		define('API_USERNAME', $adaptive_pay_settings->api_username);
		define('API_PASSWORD', $adaptive_pay_settings->api_password);
		define('API_SIGNATURE', $adaptive_pay_settings->api_signature);
		define('X_PAYPAL_APPLICATION_ID', $app_id);
		require_once 'paypal/lib/Config/paypal_sdk_clientproperties.php';

		$returnURL = site_url()."/?payment_success=1&product_id=".absint(esc_attr($_POST['project_id']));
		$cancelURL = site_url(). "/?payment_cancel=1" ;
		$notifyURL = esc_url(site_url()). "/?ipn_handler=1&type=paypal_preauth&".$_SESSION['ig_payment_variables'];
		$currencyCode = $prod_settings->currency_code;

		$senderEmail = esc_attr($_POST['email']);

		$preauth = new PreapprovalRequest();

		$preauth->cancelUrl = $cancelURL;
		$preauth->ipnNotificationUrl = $notifyURL;
		$preauth->returnUrl = $returnURL;
		$preauth->currencyCode = $currencyCode;
		$preauth->maxNumberOfPayments = "1";
		$preauth->maxNumberofPaymentsPerPeriod = 1;
		$preauth->endingDate = date("Y-m-d\Z", strtotime("+ 364 day"));
		$preauth->startingDate = date('Y-m-d\Z');
		$preauth->maxTotalAmountOfAllPayments = esc_attr(str_replace(',', '', $_POST['price']));
		$preauth->memo = $product_name.' pledge of '.number_format($_POST['price'], 2, '.', ',').' '.$currencyCode;

		$preauth->clientDetails = new ClientDetailsType();
		$preauth->clientDetails->applicationId = $app_id;
		//$preauth->clientDetails->deviceId = DEVICE_ID;
		//$preauth->clientDetails->ipAddress = "127.0.0.1";
		//$preapprovalRequest->maxNumberOfPayments = $maxNumberOfPayments;
		//$preapprovalRequest->maxTotalAmountOfAllPayments = $maxTotalAmountOfAllPayments;
		$preauth->requestEnvelope = new RequestEnvelope();
		$preauth->requestEnvelope->errorLanguage = "en_US";
		$preauth->senderEmail = $senderEmail;           

		$ap = new AdaptivePayments();
		$response=$ap->Preapproval($preauth);
		//print_r($preauth);
		//print_r($response);
		//print_r($ap);
		if(strtoupper($ap->isSuccess) == 'FAILURE')
		{
			$fault = $ap->getLastError();
			// For error handling
			if(is_array($fault->error))
			{
				$errors_content = '<table width =\"450px\" align=\"center\">';
				foreach($fault->error as $err) {
					$errors_content .= '<tr>';
					$errors_content .= '<td>';
					$errors_content .= 'Error ID: ' . $err->errorId . '<br />';
					$errors_content .= 'Domain: ' . $err->domain . '<br />';
					$errors_content .= 'Severity: ' . $err->severity . '<br />';
					$errors_content .= 'Category: ' . $err->category . '<br />';
					$errors_content .= 'Message: ' . $err->message . '<br />';
					if(empty($err->parameter)) {
						$errors_content .= '<br />';
					}
					else {
						$errors_content .= 'Parameter: ' . $err->parameter . '<br /><br />';
					}
						
					$errors_content .= '</td>';
					$errors_content .= '</tr>';
				}
				$errors_content .= '</table>';
			}
			else
			{
				$errors_content = 'Error ID: ' . $fault->error->errorId . '<br />';
				$errors_content .= 'Domain: ' . $fault->error->domain . '<br />';
				$errors_content .= 'Severity: ' . $fault->error->severity . '<br />';
				$errors_content .= 'Category: ' . $fault->error->category . '<br />';
				$errors_content .= 'Message: ' . $fault->error->message . '<br />';
				if(empty($fault->error->parameter)) {
					$errors_content .= '</br>';
				}
				else {
					$errors_content .= 'Parameter: ' . $fault->error->parameter . '<br /><br />';
				}
			}
			$_SESSION['paypal_errors_content'] = $errors_content;
		
		}
		else
		{
			// Redirect to paypal.com here
			$_SESSION['preapprovalKey'] = $response->preapprovalKey;
			$token = $response->preapprovalKey;
			$payPalURL = PAYPAL_REDIRECT_URL.'_ap-preapproval&preapprovalkey='.$token;
			echo '<script type="text/javascript">window.location="'.$payPalURL.'";</script>';
		}

	}
}
if (is_id_licensed()) {
	add_action('init', 'adaptivePreapproval');
}

/*
 * 	Function for adding the Order if the payment is made successfully
 */
function paymentSuccess() {
	if (isset($_GET['payment_success']) || isset($_GET['merchant_return_link'])) {
		do_action('id_payment_return');
		$prod_id = esc_attr($_GET['product_id']);
		$ty_url = getThankYouURLfromType($prod_id, "thank_you_url");
		echo '<script type="text/javascript">window.location="'.$ty_url.'";</script>';
	}
}
if (is_id_licensed()) {
	add_action('init', 'paymentSuccess');
}

/*
 *
 */
function id_query_vars($vars) {
	// add my_plugin to the valid list of variables
	$new_vars = array('ipn_handler', 'fname', 'lname', 'email', 'address', 'country', 'state', 'city', 'zip', 'product_id', 'level', 'prod_price');
	if (is_array($vars))
		$vars = array_merge($vars, $new_vars); //$vars = $new_vars + vars;
    return $vars;
}
add_filter('query_vars', 'id_query_vars');

function id_parse_request($wp) {
    // only process requests with "ipn_handler=1"
    if (array_key_exists('ipn_handler', $wp->query_vars) 
            && $wp->query_vars['ipn_handler'] == '1') {
        IPNHandler($wp);
    }
}
if (is_id_licensed()) {
	add_action('parse_request', 'id_parse_request');
}

 
function id_rewrite_rules( $wp_rewrite ) {
  $new_rules = array('ipn_handler/1' => 'index.php?ipn_handler=1');
  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
add_action('generate_rewrite_rules', 'id_rewrite_rules');

function IPNHandler($wp) {
	//if (isset($_GET['ipn_handler'])) {
		global $wpdb;
		$tz = get_option('timezone_string');
		if (empty($tz)) {
			$tz = 'UTC';
		}
		date_default_timezone_set($tz);
		//unlink("log.txt");
		//================================================================================================================
		//		Adaptive Payment IPN for PayPal code
		//================================================================================================================
		//$filetest_handler = fopen("log.txt", 'a+');

        ini_set('post_max_size', '12M');
        //fwrite($filetest_handler, "ini is set so we are increasing post size \n");
		
		$data_array = array();
		
		$payment_complete = false;
		$approved = false;
		$status = null;
		$vars = array();
        foreach($_POST as $key=>$val) {
        	//fwrite($filetest_handler, $key."=".$val."\n");
            $data1 = explode("=", $key);
            $data2 = explode("=", $val);
            $key = $data1[0];
            $val = $data2[0];
            $vars[$key] = $val;
            
            if ($data1[0] == "payment_status" && strtoupper($data2[0]) == "COMPLETED") {
                $payment_complete = true;
                //fwrite($filetest_handler, $payment_complete);
            }
            else if ($data1[0] == "payment_status" && strtoupper($data2[0]) == "PENDING") {
                $payment_complete = true;
                $payment_pending = true;
                //fwrite($filetest_handler, $payment_complete);
            }
            else if ($data1[0] == "status") {
            	//fwrite($filetest_handler, 'it does equal status');
            	if (strtoupper($data2[0]) == "COMPLETED") {
            		if (isset($vars['preapproval_key'])) {
		            	
		            	$preauth_key = esc_attr($vars['preapproval_key']);
		            	$sender_email = esc_attr($vars['sender_email']);
		            	$status = 'C';
		            	$txn_id = esc_attr($vars['pay_key']);
		            	$sql = $wpdb->prepare('UPDATE '.$wpdb->prefix.'ign_pay_info SET status=%s, transaction_id=%s WHERE email = %s AND preapproval_key = %s', $status, $txn_id, $sender_email, $preauth_key);
		            	//fwrite($filetest_handler, $sql);
		            	$res = $wpdb->query($sql);
		            }
		            else {
	                	$payment_complete = true;
	                	//fwrite($filetest_handler, $payment_complete);
	                }
                }
                else if (strtoupper($data2[0]) == "ACTIVE") {
                	//fwrite($filetest_handler, 'it should equal active');
                	$status = 'active';
            		//fwrite($filetest_handler, $status);
                }
            }

            if ($data1[0] == "approved" && strtoupper($data2[0]) == "TRUE") {
            		//fwrite($filetest_handler, 'it should equal true');
            		$approved = true;
            		//fwrite($filetest_handler, $approved);
            }

            if ($approved == true && $status == 'active') {
            	$preauth_complete = true;
            	//fwrite($filetest_handler, 'preauthcomplete '. $preauth_complete);
            }
        }
        foreach ($vars as $key=>$val) {
        	//fwrite($filetest_handler, 'key->'.$key.'='.'val->'.$val);
        }
    		
		if ($payment_complete) {
            //fwrite($filetest_handler, 'payment is complete'."\n");
            if (isset($vars['txn_id'])) {
            	$txn_id = esc_attr($vars['txn_id']);
            }
            else {
            	$txn_id = esc_attr($vars['pay_key']);
            }

            $check = $wpdb->prepare('SELECT id FROM '.$wpdb->prefix.'ign_pay_info WHERE transaction_id = %s', $txn_id);
            //fwrite($filetest_handler, $check);
            $checkres = $wpdb->get_row($check);
            if (empty($checkres)) {
            
				$query="INSERT INTO ".$wpdb->prefix ."ign_pay_info (
							prod_price,
							first_name,
							last_name,
							email,
							address,
							country,
							state,
							city,
							zip,
							product_id,
							transaction_id,
							product_level,
							created_at
						)
						values (
							'".esc_attr($_GET['prod_price'])."',
							'".esc_attr($_GET['fname'])."',
							'".esc_attr($_GET['lname'])."',
							'".$_GET['email']."',
							'".esc_attr($_GET['address'])."',
							'".esc_attr($_GET['country'])."',
							'".esc_attr($_GET['state'])."',
							'".esc_attr($_GET['city'])."',
							'".esc_attr($_GET['zip'])."',
							'".absint($_GET['product_id'])."',
							'".$txn_id."',
							'".absint($_GET['level'])."',
							'".date('Y-m-d H:i:s')."'
						)";
	            //fwrite($filetest_handler, $query);
				
				//echo $query;exit;
				$res = $wpdb->query( $query );
				$pay_info_id = $wpdb->insert_id;
				do_action('id_payment_success', $pay_info_id);
				//fwrite($filetest_handler, "\n".$pay_info_id);
				
				$_SESSION['ig_payinfo_id_latest'] = $pay_info_id;
				$_SESSION['ig_product_id_latest'] = esc_attr($_GET['product_id']);
				
				//$product = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix ."ign_products WHERE id = '".absint(esc_attr($_GET['product_id']))."'");

				// set order status
				if (isset($payment_pending) && $payment_pending == true) {
					setOrderStatus('P', $pay_info_id);
				}
				else {
					setOrderStatus('C', $pay_info_id);
				}

				//store transaction info in session

				$_SESSION['id_order'] = array(
					'id' => $GLOBALS['transactionId'],

					'product_id' => esc_attr($_GET['product_id']),
					'shared-on-twitter' => false,
					'shared-on-facebook' => false,
				);
			}
			else {
				//fwrite($filetest_handler, $checkres);
			}
		} 

		else if (isset($preauth_complete)) {
			//fwrite($filetest_handler, 'preauth is complete \n');

			$num_payments = absint(esc_attr($_POST['current_number_of_payments']));
			$preauth_key = esc_attr($_POST['preapproval_key']);
			$current_attempts = absint(esc_attr($_POST['current_period_attempts']));

			$check = $wpdb->prepare('SELECT id FROM '.$wpdb->prefix.'ign_pay_info WHERE transaction_id = %s', $vars['preapproval_key']);
            $checkres = $wpdb->get_row($check);
            if (empty($checkres)) {
				$query="INSERT INTO ".$wpdb->prefix ."ign_pay_info (
							prod_price,
							first_name,
							last_name,
							email,
							address,
							country,
							state,
							city,
							zip,
							product_id,
							preapproval_key,
							product_level,
							status,
							created_at
						)
						values (
							'".esc_attr($_GET['prod_price'])."',
							'".esc_attr($_GET['fname'])."',
							'".esc_attr($_GET['lname'])."',
							'".esc_attr($_GET['email'])."',
							'".esc_attr($_GET['address'])."',
							'".esc_attr($_GET['country'])."',
							'".esc_attr($_GET['state'])."',
							'".esc_attr($_GET['city'])."',
							'".esc_attr($_GET['zip'])."',
							'".esc_attr($_GET['product_id'])."',
							'".esc_attr($vars['preapproval_key'])."',
							'".esc_attr($_GET['level'])."',
							'W',
							'".date('Y-m-d H:i:s')."'
						)";
				//fwrite($filetest, $query);
				//echo $query;exit;
				$res = $wpdb->query( $query );
				$pay_info_id = $wpdb->insert_id;
				do_action('id_payment_success', $pay_info_id);
			}
		}

		else {
			// read the post from PayPal system and add 'cmd'
			$req = 'cmd=_notify-validate';
			
			foreach ($_POST as $key => $value) {
				$value = urlencode(stripslashes($value));
				$req .= "&$key=$value";
			}
			
			// post back to PayPal system to validate
			$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
			$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
			
			if (!$fp) {
				//fwrite($filetest_handler, 'Paypal errored out');
			} else {
				fputs ($fp, $header . $req);
				//fwrite($filetest_handler, 'Paypal was successful');
				while (!feof($fp)) {
					$res = fgets ($fp, 1024);
					if (strcmp ($res, "VERIFIED") == 0) {
						// check the payment_status is Completed
						// check that txn_id has not been previously processed
						// check that receiver_email is your Primary PayPal email
						// check that payment_amount/payment_currency are correct
						// process payment
						//fwrite($filetest_handler, $res."\n");
						global $wpdb;
						
						// Check if a new payment is made using the session variable set before making that payment
						if (1/*isset($_GET['payment_vars'])*/) {
							// Deserialize the payment variables and get them to work
							//$payment_variables = urldecode($_GET['payment_vars']);
							//$payment_variables = str_replace("\\","",$payment_variables);
							//fwrite($filetest_handler, "Payment variables: ".$payment_variables."\n");
							//$payment_variables = unserialize($payment_variables);
							//fwrite($filetest_handler, "Payment variables array: ".$payment_variables."\n");
				
							$query="INSERT INTO ".$wpdb->prefix ."ign_pay_info (
										prod_price,
										first_name,
										last_name,
										email,
										address,
										country,
										state,
										city,
										zip,
										product_id,
										transaction_id,
										product_level,
										created_at
									)
									values (
										'".esc_attr($_GET['prod_price'])."',
										'".esc_attr($_GET['fname'])."',
										'".esc_attr($_GET['lname'])."',
										'".esc_attr($_GET['email'])."',
										'".esc_attr($_GET['address'])."',
										'".esc_attr($_GET['country'])."',
										'".esc_attr($_GET['state'])."',
										'".esc_attr($_GET['city'])."',
										'".esc_attr($_GET['zip'])."',
										'".esc_attr($_GET['product_id'])."',
										'".esc_attr($_GET['tx'])."',
										'".esc_attr($_GET['level'])."',
										'".date('Y-m-d H:i:s')."'
									)";
							//echo $query;exit;
							$res = $wpdb->query( $query );
							$pay_info_id = $wpdb->insert_id;
							$_SESSION['ig_payinfo_id_latest'] = $pay_info_id;
							$_SESSION['ig_product_id_latest'] = esc_attr($_GET['product_id']);
							
							$product = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix ."ign_products WHERE id = '".$_GET['product_id']."'");
				
							// set order status
							setOrderStatus('C', $pay_info_id);
				
							// subscribe user to mailchimp
							
							$order = getOrderById($pay_info_id);
							$mailchip_settings = getMailchimpSettings();
							$aweber_settings = getAweberSettings();
							$product_settings = getProductSettings($product->id);
							
							if ($product_settings != -1 && $product_settings->active_mailtype == "mailchimp"/*$mailchip_settings->is_active*/)
								subscribeToMailchimp($order->email, array('first_name' => $order->first_name, 'last_name' => $order->last_name ), $product->id);
				
							else if ($mailchip_settings->is_active)
								subscribeToMailchimp($order->email, array('first_name' => $order->first_name, 'last_name' => $order->last_name ), $product->id);
								
							if ($product_settings != -1 && $product_settings->active_mailtype == "aweber"/*$aweber_settings->is_active*/)
								subscribeToAweber($order->email, array('first_name' => $order->first_name, 'last_name' => $order->last_name ), $product->id);
							else if ($aweber_settings->is_active)
								subscribeToAweber($order->email, array('first_name' => $order->first_name, 'last_name' => $order->last_name ), $product->id);
							
				
							//store transaction info in session
				
							$_SESSION['id_order'] = array(
								'id' => $GLOBALS['transactionId'],
	
								'product_id' => esc_attr($_GET['product_id']),
								'shared-on-twitter' => false,
								'shared-on-facebook' => false,
							);
						}
						
					}
					else if (strcmp ($res, "INVALID") == 0) {
						// log for manual investigation
						//fwrite($filetest_handler, $res);
					}
				}
				fclose ($fp);
			}
		}

		//fclose($filetest_handler);
		return;
		//======================================== code ends here ========================================================
	//}
}
//add_action('init', 'IPNHandler');

/*
 *	Payment cancel
 *
 */
function paymentCancelled() {
	if (isset($_GET['adaptive_payment_cancel'])) {
		include ('templates/_purchaseCancel.php');
	}
}
if (isset($_GET['adaptive_payment_cancel'])) {
	add_action('the_content', 'paymentCancelled');
}

function embedWidget() {
	global $wpdb;
	$tz = get_option('timezone_string');
	if (empty($tz)) {
		$tz = 'UTC';
	}
	date_default_timezone_set($tz);
	$theme_name = getThemeFileName();
	
	echo "<link rel='stylesheet' id='ignitiondeck-iframe-css'  href='".plugins_url('/ignitiondeck-iframe.css?ver=3.1.3', __FILE__)."' type='text/css' media='all' />";
	if (isset($_GET['product_no'])) {
		$project_id = $_GET['product_no'];
	}

	if (!empty($project_id)) {
		$deck = new Deck($project_id);
		$the_deck = $deck->the_deck();
		$post_id = $deck->get_project_postid();

		$project_desc = get_post_meta( $post_id, "ign_project_description", true );
		$project_desc = get_post_meta( $post_id, "ign_project_description", true );
		
		//GETTING the main settings of ignitiondeck
		$settings = getSettings();
		$logo_on = true;
		if (is_id_pro() && $settings->id_widget_logo_on !== '1') {
			$logo_on = false;
		}
		
		//GETTING project URL
		$product_url = getProjectURLfromType($project_id);
		
		require 'languages/text_variables.php';
		include 'templates/_embedWidget.php';
	}
	exit;
}
if (isset($_GET['ig_embed_widget'])) {
	add_action('init', 'embedWidget');
}
	
/**
 *  for sending Ask A Question email
 */
function askAQuestion() {
	
	session_start();
	global $wpdb;
	
	if (isset($_POST['btnSubmitQuestion'])) {
		$tz = get_option('timezone_string');
		if (empty($tz)) {
			$tz = 'UTC';
		}
		date_default_timezone_set($tz);
		require 'languages/text_variables.php';
		$settings = getSettings();
		
		$to = $settings->ask_email;
		$from = $_POST['ask_sender_email'];
		$subject = sprintf($tr_IgnitionDeck_Question, $_POST['ask_sender_fullname']);
		$body = '<table border="0" width="100%">
					<tr>
						<td class="label">'.$tr_Name /*#change-languageVariables_20Jan2012*/.'</td>
						<td class="field">'.$_POST['ask_sender_fullname'].'</td>
					</tr>
					<tr>
						<td class="label">'.$tr_Email /*#change-languageVariables_20Jan2012*/.'</td>
						<td class="field">'.$_POST['ask_sender_email'].'</td>
					</tr>
					<tr>
						<td class="label">'.$tr_Subject /*#change-languageVariables_20Jan2012*/.'</td>
						<td class="field">'.$_POST['ask_sender_subject'].'</td>
					</tr>
					<tr>
						<td class="label">'.$tr_Message /*#change-languageVariables_20Jan2012*/.'</td>
						<td class="field">'.$_POST['ask_sender_comments'].'</td>
					</tr>
				</table>';
				//echo $_POST['secure'];
				//echo $_SESSION['security_number'];
				if($_POST['secure'] != $_SESSION['security_number'])
				{
					echo "<script type='text/javascript'>alert('Mail has not been sent due to the captcha code mismatch');</script>";
					
				}
				else
				{
					$header = "MIME-Version: 1.0" . "\r\n";
					$header .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
					$header .= 'From: '.$from."\r\n";
					
					$email_msg = mail($to, $subject, $body, $header);
					
					$sql_insert_question = "INSERT INTO ".$wpdb->prefix."ign_questions (
								full_name,
								email,
								subject,
								comments,
								created_date
							) VALUES (
								'".$_POST['ask_sender_fullname']."',
								'".$_POST['ask_sender_email']."',
								'".$_POST['ask_sender_subject']."',
								'".$_POST['ask_sender_comments']."',
								'".date('Y-m-d H:i:s')."'
							)";
					$wpdb->query($sql_insert_question);
					
				}
				
		
	}
}
//add_action('init', 'askAQuestion');

/*
 *  Adding METABoxes code for displaying widget short codes
 */

add_action( 'add_meta_boxes', 'add_project_url' );
if (is_id_licensed()) {
	add_action( 'add_meta_boxes', 'add_purchase_url' );
	add_action( 'add_meta_boxes', 'add_ty_url' );
	add_action( 'add_meta_boxes', 'shortcode_side_meta' );
	add_action( 'add_meta_boxes', 'shortcode_on_post' );
	add_action( 'add_meta_boxes', 'shortcode_on_page' );
	add_action( 'add_meta_boxes', 'add_project_parent' );
}

/* Adds a box to the main column on the Post and Page edit screens */
function shortcode_side_meta() {
	global $post;
	if (isset($post) && $post->filter == 'edit') {
    	add_meta_box("shortcode_meta", "Shortcodes", "add_shortcode_meta", "ignition_product", "side", "low");
    }
}
function shortcode_on_post() {
    add_meta_box("shortcode_meta", "IgnitionDeck Shortcodes", "shortcode_normal_post", "post", "side", "default");
}
function shortcode_on_page() {
    add_meta_box("shortcode_meta", "IgnitionDeck Shortcodes", "shortcode_normal_post", "page", "side", "default");
}
function add_project_url() {
	add_meta_box("add_project_url_box", "Project URL", "add_project_url_box", "ignition_product", "side", "default");
}
function add_purchase_url() {
	add_meta_box("add_purchase_url_box", "Purchase URL", "add_purchase_url_box", "ignition_product", "side", "default");
}
function add_ty_url() {
	add_meta_box("add_ty_url_box", "Thank You Project URL", "add_ty_url_box", "ignition_product", "side", "default");
}
function add_project_parent() {
	add_meta_box("add_project_parent_box", "Project Parent", "add_project_parent_box", "ignition_product", "side", "default");
}
/* Prints the box content */
function add_shortcode_meta( $post ) {
	// USE nonce for verification
  	wp_nonce_field( plugin_basename( __FILE__ ), 'ignitiondeck' );
	
  	// THE output
	getAllShortCodes();
}
/* TO print the shortcodes in the Post/Page adding screen in meta box */
function shortcode_normal_post ($post) {
	// USE nonce for verification
  	wp_nonce_field( plugin_basename( __FILE__ ), 'ignitiondeck' );
	
	// THE output
	getShortCodesPostPage();
}

// To place a box on the right sidebar of Add New Project page
function add_project_url_box ($post) {
	require ('languages/text_variables.php');	
	//echo $post->ID;
	
	echo '<input type="hidden" name="add_project_url_box_nonce" value="'. wp_create_nonce('add_project_url_box'). '" />';
	echo '<table width="100%" border="0">
			<tr>
				<td>&nbsp;</td>
				<td></td>
			</tr>
			<tr>
				<td>'.$tr_Project_Page_URL.'</td>
				<td>
					<select name="ign_option_project_url" id="select_pageurls" onchange=storeurladdress();>
						<option value="current_page" '.((get_post_meta($post->ID, 'ign_option_project_url', true) == "current_page") ? 'selected' : '').'>'.$tr_Current_Project_Page.'</option>
						<option value="page_or_post" '.((get_post_meta($post->ID, 'ign_option_project_url', true) == "page_or_post") ? 'selected' : '').'>'.$tr_Page_Post.'</option>
						<option value="external_url" '.((get_post_meta($post->ID, 'ign_option_project_url', true) == "external_url") ? 'selected' : '').'>'.$tr_External_URL.'</option>
					</select>
				</td>
			</tr>
			<tr>
			<td>
			</td>
			</tr>
			<tr>
			<td>
			</td>
			<td '.((get_post_meta($post->ID, 'ign_option_project_url', true) == "external_url") ? 'style="display:block;"' : 'style="display:none;"').' id="proj_url_cont" ><input class="product-url-container" name="id_project_URL" type="text" id="id_project_URL" value="'.get_post_meta($post->ID, 'id_project_URL', true).'"></td>
			</tr>
			<tr>
			<td>
			</td>';
			?>
            
			<td>
			<div id="proj_posts" <?php echo ((get_post_meta($post->ID, 'ign_option_project_url', true) == "page_or_post") ? 'style="display:block;"' : 'style="display:none;"') ?>>
			<?php
			global $wpdb;

			$sql = "SELECT * FROM ".$wpdb->prefix."posts WHERE (post_type = 'post' OR post_type = 'page') AND post_status = 'publish' ORDER BY post_title ASC";
			$results = $wpdb->get_results( $sql );
			?>
            <select name="ign_post_name" id="posts_pro">
            	<option value=""><?php echo $tr_Select; ?></option>
				<?php
				$post_name_value = get_post_meta($post->ID, 'ign_post_name', true);
				foreach( $results as $single_post ) {
					//setup_postdata($post);
					echo '<option value="'.$single_post->post_name.'" '.(($post_name_value == $single_post->post_name) ? 'selected' : '').'>'.$single_post->post_title.'</option>';
				}
				?>
            </select>
            </td>
          <?php
			echo '</div>
			</td>
			</tr>
		  </table>';
}

// To place a box on the right sidebar of Add New Project page
function add_purchase_url_box ($post) {
	require ('languages/text_variables.php');	
	//echo $post->ID;
	
	echo '<input type="hidden" name="add_purchase_url_box_nonce" value="'. wp_create_nonce('add_purchase_url_box'). '" />';
	echo '<table width="100%" border="0">
			<tr>
				<td>&nbsp;</td>
				<td></td>
			</tr>
			<tr>
				<td>Checkout Page</td>
				<td>
					<select name="ign_option_purchase_url" id="select_purchase_pageurls" onchange=storepurchaseurladdress();>
						<option value="default" '.((get_post_meta($post->ID, 'ign_option_purchase_url', true) == "default") ? 'selected' : '').'>'.__('Default', 'ignitiondeck').'</option>
						<option value="current_page" '.((get_post_meta($post->ID, 'ign_option_purchase_url', true) == "current_page") ? 'selected' : '').'>Current Project Page</option>
						<option value="page_or_post" '.((get_post_meta($post->ID, 'ign_option_purchase_url', true) == "page_or_post") ? 'selected' : '').'>Page/Post</option>
						<option value="external_url" '.((get_post_meta($post->ID, 'ign_option_purchase_url', true) == "external_url") ? 'selected' : '').'>External URL</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td '.((get_post_meta($post->ID, 'ign_option_purchase_url', true) == "external_url") ? 'style="display:block;"' : 'style="display:none;"').' id="purchase_url_cont" >
					<input class="purchase-url-container" name="purchase_project_URL" type="text" id="purchase_project_URL" value="'.get_post_meta($post->ID, 'purchase_project_URL', true).'">
				</td>
			</tr>
			<tr>
				<td>
				</td>';
			?>
            
			<td>
				<div id="purchase_posts" <?php echo ((get_post_meta($post->ID, 'ign_option_purchase_url', true) == "page_or_post") ? 'style="display:block;"' : 'style="display:none;"') ?>>
			<?php
			global $wpdb;

			$sql = "SELECT * FROM ".$wpdb->prefix."posts WHERE (post_type = 'ignition_product' OR post_type = 'post' OR post_type = 'page') AND post_status = 'publish' ORDER BY post_title ASC";
			$results = $wpdb->get_results( $sql );
			?>
            	<select name="ign_purchase_post_name" id="purchase_posts_pro">
            		<option value="">Select</option>
					<?php
					$post_name_value = get_post_meta($post->ID, 'ign_purchase_post_name', true);
					foreach( $results as $single_post ) {
						//setup_postdata($post);
						echo '<option value="'.$single_post->post_name.'" '.(($post_name_value == $single_post->post_name) ? 'selected' : '').'>'.$single_post->post_title.'</option>';
					}
					?>
	            </select>
            </td>
          <?php
			echo '</div>
			</td>
			</tr>
		  </table>';
}

// To place a box on the right sidebar of Add New Project page
function add_ty_url_box ($post) {
	require ('languages/text_variables.php');	
	//echo $post->ID;
	
	echo '<input type="hidden" name="add_ty_url_box_nonce" value="'. wp_create_nonce('add_ty_url_box'). '" />';
	echo '<table width="100%" border="0">
			<tr>
				<td>&nbsp;</td>
				<td></td>
			</tr>
			<tr>
				<td>Thank You Page</td>
				<td>
					<select name="ign_option_ty_url" id="select_ty_pageurls" onchange=storetyurladdress();>
						<option value="default" '.((get_post_meta($post->ID, 'ign_option_ty_url', true) == "default") ? 'selected' : '').'>'.__('Default', 'ignitiondeck').'</option>
						<option value="current_page" '.((get_post_meta($post->ID, 'ign_option_ty_url', true) == "current_page") ? 'selected' : '').'>Current Project Page</option>
						<option value="page_or_post" '.((get_post_meta($post->ID, 'ign_option_ty_url', true) == "page_or_post") ? 'selected' : '').'>Page/Post</option>
						<option value="external_url" '.((get_post_meta($post->ID, 'ign_option_ty_url', true) == "external_url") ? 'selected' : '').'>External URL</option>
					</select>
				</td>
				
			</tr>
			<tr>
				<td>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td '.((get_post_meta($post->ID, 'ign_option_ty_url', true) == "external_url") ? 'style="display:block;"' : 'style="display:none;"').' id="ty_url_cont" >
					<input class="ty-url-container" name="ty_project_URL" type="text" id="ty_project_URL" value="'.get_post_meta($post->ID, 'ty_project_URL', true).'">
				</td>
			</tr>
			<tr>
				<td>
				</td>';
			?>
            
				<td>
				<div id="ty_posts" <?php echo ((get_post_meta($post->ID, 'ign_option_ty_url', true) == "page_or_post") ? 'style="display:block;"' : 'style="display:none;"') ?>>
				<?php
				global $wpdb;

				$sql = "SELECT * FROM ".$wpdb->prefix."posts WHERE (post_type = 'ignition_product' OR post_type = 'post' OR post_type = 'page') AND post_status = 'publish' ORDER BY post_title ASC";
				$results = $wpdb->get_results( $sql );
				?>
	            <select name="ign_ty_post_name" id="ty_posts_pro">
	            	<option value="">Select</option>
					<?php
					$post_name_value = get_post_meta($post->ID, 'ign_ty_post_name', true);
					foreach( $results as $single_post ) {
						//setup_postdata($post);
						echo '<option value="'.$single_post->post_name.'" '.(($post_name_value == $single_post->post_name) ? 'selected' : '').'>'.$single_post->post_title.'</option>';
					}
					?>
	            </select>
            </td>
          <?php
			echo '</div>
			</td>
			</tr>
		  </table>';
}
function add_project_parent_box($post) {
	require ('languages/text_variables.php');
	// Getting the parent if any for auto selection
	$parent_id = get_post_meta( $post->ID, 'ign_project_parent', true );
	// Getting the list of ID projects
	$projects = ID_Project::get_project_posts();
	// If the screen is edit post, then don't show the current post id in dropdown
	if (isset($_GET['action']) && $_GET['action'] == 'edit') {
		$screen = 'edit';
	} else {
		$screen = 'add';
	}

	// Making the markup
	echo '<input type="hidden" name="add_project_parent_box" value="'. wp_create_nonce('add_project_parent_box'). '" />';
	echo '<table width="100%" border="0">
			<tr>
				<td>&nbsp;</td>
				<td></td>
			</tr>
			<tr>
				<td>Parent Project</td>
				<td>
					<select name="ign_option_project_parent" id="ign_option_project_parent">
						<option value="">'.__('No Parent', 'ignitiondeck').'</option>';
	if (!empty($projects)) {
		foreach ($projects as $project) {
			if ($screen == "add" || ($screen == 'edit' && $post->ID != $project->ID)) {
				echo '		<option value="'.$project->ID.'" '.(($parent_id == $project->ID) ? 'selected="selected"' : '').'>'.$project->post_title.'</option>';
			}
		}
	}
	echo '			</select>
				</td>
				
			</tr>
			<tr>
				<td>
				</td>
			</tr>
			
		</table>';

}

function idf_exists() {
	return (class_exists('IDF'));
}

function is_id_pro() {
	// do some validation here to check serial number
	$is_pro = get_option('is_id_pro', false);
	$was_pro = get_option('was_id_pro', false);
	if ($is_pro || $was_pro) {
		return true;
	}
	else {
		return false;
	}
}

function is_id_basic() {
	return get_option('is_id_basic', false);
}

function is_id_licensed() {
	$is_pro = is_id_pro();
	$is_basic = is_id_basic();
	$was_licensed = was_id_licensed();
	if ($is_pro || $is_basic || $was_licensed) {
		return true;
	}
	else {
		return false;
	}
}

function was_id_licensed() {
	return get_option('was_id_licensed', false);
}

add_action('activated_plugin','id_save_error');
function id_save_error(){
    update_option('id_plugin_error',  ob_get_contents());
}

//add_action('init', 'id_print_error');

function id_print_error() {
	echo get_option('id_plugin_error');
}

//add_action('init', 'id_debug');

function id_debug() {

}
?>