<?php
/**
 * Create Projects post type
 */
add_action( 'init', 'ign_create_post_type' );
function ign_create_post_type() {
	require 'languages/text_variables.php';
	$slug = apply_filters('idcf_archive_slug', __('projects', 'ignitiondeck'));
	register_post_type( 'ignition_product',
		array(
			'labels' => array(
				'name' => $tr_Projects,
				'singular_name' => $tr_Project,
				'add_new' => $tr_Add_New_Project,
				'add_new_item' => $tr_Add_New_Project,
				'edit' => $tr_Edit,
				'edit_item' => $tr_Edit_Project,
				'new_item' => $tr_New_Project,
				'view' => $tr_View_Project,
				'view_item' => $tr_View_Project,
				'search_items' =>$tr_Search_Project,
				'not_found' => $tr_No_Products_found ,
				'not_found_in_trash' => $tr_No_Product_in_Trash,
			),
                'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
			'hierarchical' => apply_filters('idcf_hierarchical', false),
			'menu_position' => 5,
			'capability_type' => 'post',
			'menu_icon' => plugins_url( '/images/ignitiondeck-menu.png', __FILE__ ),
			'query_var' => true,
			'rewrite' => array( 'slug' => $slug, 'with_front' => true ),
			'has_archive' => $slug,
			'supports' => array('title', 'editor', 'comments', 'author', 'thumbnail'),
			'taxonomies' => array('category', 'post_tag', 'project_category'),
		)
	);
}

add_action('init', 'ign_create_taxonomy');

function ign_create_taxonomy() {
	$labels = array(
		'name' => __('Project Categories', 'ignitiondeck'),
		'singular_name' => __('Project Category', 'ignitiondeck'),
	);
	$args = array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'project-category')
	);
	$args = apply_filters('project_category_args', $args);
	register_taxonomy('project_category', 'ignition_product', $args);

	$pt_labels = array(
		'name' => __('Project Types', 'ignitiondeck'),
		'singular_name' => __('Project Type', 'ignitiondeck'),
	);
	$pt_args = array(
		'hierarchical' => false,
		'labels' => $pt_labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'project-type')
	);
	$pt_args = apply_filters('project_type_args', $pt_args);
	register_taxonomy('project_type', 'ignition_product', $pt_args);
}

add_image_size('id_embed_image', 190, 127, true);
add_image_size('id_checkout_image', 500, 226, true);

/**
 * Enques Admin and Front End JS/CSS
 */

function enqueue_admin_js() {
	wp_register_script('ignitiondeck-admin', plugins_url('/js/ignitiondeck-admin.js', __FILE__));
	wp_enqueue_script('jQuery');
	wp_enqueue_script('ignitiondeck-admin');
	if (is_multisite() && is_id_network_activated()) {
		$id_ajaxurl = network_home_url('/').'wp-admin/admin-ajax.php';
	}
	else {
		$id_ajaxurl = site_url('/').'wp-admin/admin-ajax.php';
	}
	wp_localize_script('ignitiondeck-admin', 'id_homeurl', home_url());
	wp_localize_script('ignitiondeck-admin', 'id_ajaxurl', $id_ajaxurl);
	global $post;
	if (isset($post->post_type) && $post->post_type == 'ignition_product') {
	    wp_register_script( 'ignitiondeck', plugins_url('/js/ignitiondeck.js', __FILE__));
	    wp_enqueue_script( 'ignitiondeck' );
	    wp_localize_script('ignitiondeck', 'id_ajaxurl', $id_ajaxurl);
	    wp_dequeue_script('autosave');
	    //wp_enqueue_style('wp-pointer');
	    //wp_enqueue_script('wp-pointer');
	}
}

add_action('admin_enqueue_scripts', 'enqueue_admin_js');

function enqueue_admin_css() {
	wp_register_style( 'admin-css', plugins_url('/ignitiondeck-admin.css', __FILE__));
	wp_enqueue_style( 'admin-css');
}

add_action('admin_enqueue_scripts', 'enqueue_admin_css');

add_action('init', 'enqueue_styles_scripts_for_post_type');

function enqueue_styles_scripts_for_post_type() {
	global $post;
	if (isset($post->post_type) && $post->post_type == 'ignition_product') {
		add_action('admin_enqueue_scripts', 'enqueue_admin_css');
		add_action('admin_enqueue_scripts', 'enqueue_admin_js');
	}
}

// Change the columns for the edit CPT screen
function ign_change_columns( $cols ) {
	require 'languages/text_variables.php';		
  	$cols = array(
		'cb'		=> '<input type="checkbox" />',
		'title'		=> $tr_Product,
		'author' 	=> __('Author', 'ignitiondeck'),
		'type'		=> __('Type', 'ignitiondeck'),
		'goal'		=> $tr_Funding_Goal,
		'raised'	=> $tr_Pledged,	
		'enddate'	=> $tr_End_Date,		
		'daysleft'	=> $tr_Days_Remaining ,		
  	);
  	return apply_filters('id_project_columns', $cols);
}
add_filter( "manage_ignition_product_posts_columns", "ign_change_columns" );
add_action(	'manage_posts_custom_column', 'manage_ign_product_columns', 10, 2 );

function manage_ign_product_columns($column_name, $id) {
	global $post;
	require 'languages/text_variables.php';		
	$post_id = $post->ID;
	$project_id = get_post_meta($id, 'ign_project_id', true);
	$project = new ID_Project($project_id);
	$cCode = $project->currency_code();
	switch ($column_name) {
		// display goal amount with currency formatting
	case 'author':
		echo (!empty($post->post_author) ? $post->post_author : __('None', 'ignitiondeck'));
		break;

	case 'type':
		$type = get_post_meta($post_id, 'ign_project_type', true);
		if (isset($type)) {
			if ($type == 'pwyw') {
				$type = __('Pledge What You Want', 'Ignitiondeck');
			}
			else if ($type == 'level-based') {
				$type = __('Level-Based', 'ignitiondeck');
			}
		}
		$type = apply_filters('id_project_type', $type);
		echo (isset($type) ? $type : '');
		break;

	case 'goal':
		if (get_post_meta( $post->ID, 'ign_fund_goal', true)) {
			$goal_amt = number_format(get_post_meta( $post->ID, 'ign_fund_goal', true), 2, '.', ',');
			
			setlocale(LC_MONETARY, 'en_US');
			echo //money_format('%(#10n', $goal_amt);
				$cCode.$goal_amt;
		} else {
			echo '<em>'.$tr_No_Goal_set.'</em>';		
		}
		break;

	case 'raised':
		if (isset($project_id)) {
			$project = new ID_Project($project_id);
			$post_id = $project->get_project_postid();
			$raised = apply_filters('id_funds_raised', $project->get_project_raised(), $post_id);
			echo $raised;
		}
		break;
		// display end date
	case 'enddate':
		if (get_post_meta( $post->ID, 'ign_fund_end', true)) {
			echo get_post_meta( $post->ID, 'ign_fund_end', true);
		} else {
			echo '<em>'.$tr_No_Date_set.'</em>';		
		}
		break;
		
		// calculate days remaining
	case 'daysleft':
		if (get_post_meta( $post->ID, 'ign_fund_end', true)) {
			$days_left = $project->days_left();
			//$ending = get_post_meta( $post->ID, 'ign_fund_end', true);
			//$daysleft = ID_Project::days_left($ending);
			echo $days_left;
		} else {
			echo '<em>'.$tr_No_Date_set.'</em>';		
		}
		break;
		
		// return standard post columns
	default:
		break;
	} // end switch
}


// Make these columns sortable
function ign_sortable_columns() {
  $sortable_columns = array(
    'title'     => 'title',
    'author' 	=> 'author',
    'type'		=> 'type',
	'goal'      => 'goal',
	'raised'	=> 'raised',
    'enddate'	=> 'enddate',
	'daysleft'	=> 'daysleft',
  );
  return apply_filters('id_sortable_project_columns', $sortable_columns);
}

add_filter( "manage_edit-ignition_product_sortable_columns", "ign_sortable_columns" );

// This is the NEW Order Details Menu but appears to be unused

add_filter('manage-order_columns', 'order_detail_columns');

function order_detail_columns($columns) {
	require 'languages/text_variables.php';
	$columns = array(
		'name' => '<th scope="col" id="name" class="manage-column sortable desc"><b><?php echo $tr_Name; ?></b></th>',
		'project' => '<th scope="col" id="status" class="manage-column sortable desc"><b><?php echo $tr_Product_Name; ?></b></th>',
		'level' => '<th scope="col" id="action" class="manage-column sortable desc"><b><?php echo $tr_Level; ?></b></th>',
		'pledged' => '<th scope="col" id="action" class="manage-column sortable desc"><b><?php echo $tr_Pledged; ?></b></th>',
		'date' => '<th scope="col" id="action" class="manage-column sortable desc"><b><?php echo $tr_Date; ?></b></th>'
		);
	return apply_filters('id_order_columns', $columns);
}

// This is to make order details menu sortable but seems to be unused

add_filter ('edit-order_columns', 'order_details_sortable_columns');

function order_details_sortable_columns() {
	$columns = array(
		'name' => 'name',
		'project' => 'project',
		'level' => 'level',
		'pledged' => 'pledged',
		'date' => 'date'
	);
	return apply_filters('id_sortable_order_columns', $columns);
}

// change post title box text
function change_ign_product_title_text( $title ){
	require 'languages/text_variables.php';		
	$screen = get_current_screen();
	if ( 'ignition_product' == $screen->post_type ) {
		$title = $tr_Enter_Project_Name_Here;		
	}
	return $title;
}
add_filter( 'enter_title_here', 'change_ign_product_title_text' );

//-------------------------Admin Side Add IgnitionDeck STARTS------------------------------

add_action('admin_menu', 'id_admin_menus', 12);
function id_admin_menus() {
	if (current_user_can('manage_options')) {
	    $settings = add_menu_page(__('IDCF Settings', 'ignitiondeck'), __('IDCF', 'ignitiondeck'), 'manage_options', 'ignitiondeck', 'id_main_menu', plugins_url( '/images/ignitiondeck-menu.png', __FILE__ ));
		$project_settings = add_submenu_page( 'ignitiondeck', 'Project Settings', 'Project Settings', 'manage_options', 'project-settings', 'product_settings');
		if (is_id_licensed()) {
			$custom_settings = add_submenu_page( 'ignitiondeck', __('Custom Settings', 'ignitiondeck'), __('Custom Settings', 'ignitiondeck'), 'manage_options', 'custom-settings', 'custom_settings');
	    	$payment_settings = add_submenu_page( 'ignitiondeck', __('Payment Settings', 'ignitiondeck'), __('Payment Settings', 'ignitiondeck'), 'manage_options', 'payment-options', 'paypal_payment_options');
	    	$deck_settings = add_submenu_page( 'ignitiondeck', __('Deck Builder', 'ignitiondeck'), __('Deck Builder', 'ignitiondeck'), 'manage_options', 'deck-builder', 'deck_builder');
	    }
	    $order_menu = add_submenu_page( 'ignitiondeck', __('Orders', 'ignitiondeck'), __('Orders', 'ignitiondeck'), 'manage_options', 'order_details', 'order_details');
		if (is_id_licensed()) {
			$email_settings = add_submenu_page( 'ignitiondeck', __('Email Settings', 'ignitiondeck'), __('Email Settings', 'ignitiondeck'), 'manage_options', 'email-settings', 'email_settings');
	    }
	    //add_submenu_page( 'ignitiondeck', 'Social Settings', 'Social Settings ', 'manage_options', 'social-settings', 'social_application');
	    //add_submenu_page( 'ignitiondeck', 'Payment Form Settings', 'Payment Form Settings', 'manage_options', 'form-settings', 'form_settings');
		//add_submenu_page( 'ignitiondeck', 'Asked Question', 'Asked Question', 'manage_options', 'asked_questions', 'asked_questions');
		$edit_order = add_submenu_page( $order_menu, __('Edit Order', 'ignitiondeck'), '', 'manage_options', 'edit_order', 'edit_order');
		$view_order = add_submenu_page( $order_menu, __('View order', 'ignitiondeck'), '', 'manage_options', 'view_order', 'view_order');
		$delete_order = add_submenu_page( $order_menu, __('Delete Order', 'ignitiondeck'), '', 'manage_options', 'delete_order', 'delete_order');
		$add_order = add_submenu_page( $order_menu, __('Add Order', 'ignitiondeck'), '', 'manage_options', 'add_order', 'add_order');	
		//add_submenu_page( $order_menu, 'Refund', '', 'manage_options', 'refund', 'refund_order');
		do_action('id_submenu');
		add_action('admin_print_styles-'.$settings, 'id_font_awesome');
		$menus = array($settings, $project_settings, $order_menu, $edit_order, $view_order, $delete_order, $add_order);
		if (is_id_licensed()) {
			$menus[] = $custom_settings;
			$menus[] = $payment_settings;
			$menus[] = $deck_settings;
			$menus[] = $email_settings;
		}
		$menus = apply_filters('id_menu_enqueue', $menus);
		if (is_array($menus)) {
			foreach ($menus as $menu) {
				add_action('admin_print_styles-'.$menu, 'enqueue_admin_css');
				add_action('admin_print_styles-'.$menu, 'enqueue_admin_js');
			}
		}
	}
}

function id_main_menu(){
	require 'languages/text_variables.php';		
	global $wpdb;
	$super = true;
	if (is_multisite()) {
		$super = is_super_admin();
	}

	$license_key = get_option('id_license_key');
	$is_pro = get_option('is_id_pro', 0);
	$is_basic = get_option('is_id_basic', 0);
	if (isset($_POST['license_key'])) {
		$is_pro = 0;
		$is_basic = 0;
		$license_key = esc_attr($_POST['license_key']);
		update_option('id_license_key', $license_key);
		$validate = id_validate_license($license_key);
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
	if ($is_pro) {
		$type_msg = __(' IgnitionDeck Enterprise', 'ignitiondeck');
	}
	else if ($is_basic) {
		$type_msg = __(' IgnitionDeck', 'ignitiondeck');
	}

	$skins = $wpdb->get_row('SELECT theme_choices FROM '.$wpdb->prefix.'ign_settings WHERE id="1"');
	if (isset($skins)) {
		$skins = unserialize($skins->theme_choices);
	}
	else {
		$skins = array();
	}

	$deleted_skin_list = deleted_skin_list($skins);

	if (isset($_POST['add-skin'])) {
		$skin = str_replace('.css', '', $_POST['skin-name']);
		if ($skin !== '') {
			$skins[] = $skin;
			$deleted_skin_list = deleted_skin_list($skins);
			$skins = serialize($skins);
			$sql = $wpdb->prepare('UPDATE '.$wpdb->prefix.'ign_settings SET theme_choices=%s WHERE id="1"', $skins);
			$res = $wpdb->query($sql);
		}
	}

	if (isset($_POST['delete-skin'])) {
		$deleted = $_POST['deleted-skin'];
		foreach ($skins as $key => $val) {
			if (strtolower(str_replace(' ', '', $val)) == strtolower(str_replace(' ', '', $deleted))) {
				unset($skins[$key]);
			}
		}
		$deleted_skin_list = deleted_skin_list($skins);
		$skins = serialize($skins);
		$sql = $wpdb->prepare('UPDATE '.$wpdb->prefix.'ign_settings SET theme_choices=%s WHERE id="1"', $skins);
		$res = $wpdb->query($sql);
	}

	if (isset($_POST['btnIgnSettings'])) {
		if ($_POST['btnIgnSettings'] == $tr_Add) {		
			$sql_insert = "	INSERT INTO ".$wpdb->prefix."ign_settings
							(
								theme_value,
								prod_page_fb,
								prod_page_twitter,
								prod_page_linkedin,
								prod_page_google,
								prod_page_pinterest,
								id_widget_logo_on,
								id_widget_link,
								theme_choices
							) VALUES (	
								'".(isset($_POST['theme_value']) ? $_POST['theme_value'] : 'style1')."',
								'".(isset($_POST['prod_page_fb']) ? $_POST['prod_page_fb'] : 0)."',
								'".(isset($_POST['prod_page_twitter']) ? $_POST['prod_page_twitter'] : 0)."',
								'".(isset($_POST['prod_page_linkedin']) ? $_POST['prod_page_linkedin'] : 0)."',
								'".(isset($_POST['prod_page_google']) ? $_POST['prod_page_google'] : 0)."',
								'".(isset($_POST['prod_page_pinterest']) ? $_POST['prod_page_pinterest'] : 0)."',
								'".(isset($_POST['id_widget_logo_on']) ? $_POST['id_widget_logo_on'] : 0)."',
								'".$_POST['id_widget_link']."',
								'".serialize($skins)."'
							)";
			$wpdb->query( $sql_insert );
			update_option('id_settings_notice', 'off');
		} else if ($_POST['btnIgnSettings'] == $tr_Update) {			
			$sql_update = "	UPDATE ".$wpdb->prefix."ign_settings SET
							theme_value = '".(isset($_POST['theme_value']) ? $_POST['theme_value'] : 'style1')."',
							prod_page_fb = '".(isset($_POST['prod_page_fb']) ? $_POST['prod_page_fb'] : 0)."',
							prod_page_twitter = '".(isset($_POST['prod_page_twitter']) ? $_POST['prod_page_twitter'] : 0)."',
							prod_page_linkedin = '".(isset($_POST['prod_page_linkedin']) ? $_POST['prod_page_linkedin'] : 0)."',
							prod_page_google = '".(isset($_POST['prod_page_google']) ? $_POST['prod_page_google'] : 0)."',
							prod_page_pinterest = '".(isset($_POST['prod_page_pinterest']) ? $_POST['prod_page_pinterest'] : 0)."',
							id_widget_logo_on = '".(isset($_POST['id_widget_logo_on']) ? $_POST['id_widget_logo_on'] : 0)."',
							id_widget_link = '".$_POST['id_widget_link']."'
							WHERE id = '1'";
			$wpdb->query( $sql_update );
			update_option('id_settings_notice', 'off');
		}
	}

	if (isset($_POST['btnIgnSettings'])) {
		echo '<div id="message" class="updated">Settings Saved</div>';
	}
	
	if (isset($_POST['btnActivateScriptPrice'])) {
		$sql_script = "ALTER TABLE `".$wpdb->prefix."ign_pay_info` ADD `prod_price` VARCHAR( 200 ) NOT NULL AFTER `product_level`";
		$wpdb->query( $sql_script );
		
		// Getting all the previous records to modify them one by one to update prod_price
		$sql_pay_info = "SELECT * FROM ".$wpdb->prefix."ign_pay_info";
		$rows_pay_info = $wpdb->get_results($sql_pay_info);
		
		// Looping through all the purchases and updating the new field accordingly
		foreach ($rows_pay_info as $row_pay_info)
		{
			// Updating the record
			$sql_update = "UPDATE ".$wpdb->prefix."ign_pay_info SET prod_price = '".getProductPrice($row_pay_info->product_level, $row_pay_info->product_id)."' WHERE id = '".$row_pay_info->id."'";
			$wpdb->query($sql_update);
		}
		$message = '<div class="updated fade below-h2" id="message" class="updated"><p>Activated Successfully. You can now change price of product safely.</p></div>';		
        echo $message;
	}
	
	
	$data = getSettings();
	if (isset($data)) {
		if ($data->id_widget_link == "") {
			$affiliate_link = "http://ignitiondeck.com";
		}
		else {
			$affiliate_link = $data->id_widget_link;
		}
	}
	else {
		$affiliate_link = "http://ignitiondeck.com";
	}
	echo '<div class="wrap">
		'.admin_menu_html()/*.'
	<div class="icon32" id="icon-options-general"><br></div><h2>'.$tr_IgnitionDeck_Control_Panel.'</h2>'*/;
	echo '<br />';
	
	$sql_products = "SELECT * FROM ".$wpdb->prefix."ign_products";
	$products = $wpdb->get_results($sql_products);
	
	$site_url = site_url();
	
	include_once 'templates/admin/_settingsIgnDeck.php';
}

/**
 * Form settings for Admin area
 * @global object $wpdb
 */
function form_settings(){
	require 'languages/text_variables.php';		
    global $wpdb;
    if(isset($_POST['submit'])){
        $serializedForm = serialize($_POST['ignitiondeck_form']);
        if($_POST['submit'] == $tr_Save_Settings){	
            $sql_insert="INSERT INTO ".$wpdb->prefix ."ign_form(form_settings) values ('".$serializedForm."')";
            $res = $wpdb->query( $sql_insert );
            $message = '<div class="updated fade below-h2" id="message" class="updated"><p>'.$tr_Settings_saved.'</p></div>';	
            echo $message;
        }
        if($_POST['submit'] == $tr_Update_Settings){	

            $sql_update="update ".$wpdb->prefix . "ign_form set form_settings='".$serializedForm."' where id='1'";
            $res = $wpdb->query( $sql_update );
            $message = '<div class="updated fade below-h2" id="message" class="updated"><p>'.$tr_Form_settings_successfully_updated.'</p></div>';	
            echo $message;
        }
    }

    $sql="select * from ".$wpdb->prefix . "ign_form where id='1'";
    $res1 =  $wpdb->query( $sql );
    $rows = $wpdb->get_results($sql);
    $row = &$rows[0];
    if($row != null){
        $submit = 'Update Settings';
        $form = unserialize( $row->form_settings );
    }else{
        $submit = 'Save Settings';
    }

	echo '<div class="wrap">
		'.admin_menu_html();
    include_once 'templates/admin/_formSettings.php';
	echo '</div>';
}

/*
 *  Email settings combining both Aweber and Mailchinmp settings
 */
function email_settings() {
	require 'languages/text_variables.php';		
	global $wpdb;
	$inactive = get_option('id_email_inactive');
	$aweber_check = 'SELECT * FROM '.$wpdb->prefix.'ign_aweber_settings WHERE id = "1"';
	$aweber_res = $wpdb->get_row($aweber_check);

	if (empty($aweber_res)) {
		$aweber_new = true;
		$aweber_res = new stdClass();
	}
	else {
		$aweber_new = false;
		$aweber_active = $aweber_res->is_active;
	}

	$mc_check = 'SELECT * FROM '.$wpdb->prefix.'ign_mailchimp_subscription WHERE id = "1"';
	$mc_res = $wpdb->get_row($mc_check);

	if (empty($mc_res)) {
		$mc_new = true;
		$mc_res = new stdClass();
	}
	else {
		$mc_new = false;
		$mc_active = $mc_res->is_active;
	}

	if (isset($_POST['submitEmailSettings'])) {
		if (isset($_POST['mc_active'])) {
			$mc_active = 1;
			$aweber_active = 0;
			$inactive = 0;
		}
		else if (isset($_POST['aweber_active'])) {
			$mc_active = 0;
			$aweber_active = 1;
			$inactive = 0;
		}
		else {
			$mc_active = 0;
			$aweber_active = 0;
			$inactive = 1;
		}
		$aweber_res->list_email = esc_attr($_POST['list_email']);
		$mc_res->api_key = esc_attr($_POST['apikey']);
		$mc_res->list_id = esc_attr($_POST['listid']);

		//Condition for submission of Aweber Settings
			
		if($aweber_new) {	
			$sql_insert = "	INSERT INTO ".$wpdb->prefix . "ign_aweber_settings (list_email, is_active) VALUES ('".$aweber_res->list_email."','".($aweber_active ? '1' : '0')."')";
	        $res = $wpdb->query( $sql_insert );
			//echo $sql_insert; exit;
		}
		
		else {
			$sql_update = "	UPDATE ".$wpdb->prefix . "ign_aweber_settings SET
							list_email = '".$aweber_res->list_email."',
							is_active = '".($aweber_active ? '1' : '0')."'
							WHERE id = '1'";
			$res = $wpdb->query( $sql_update );
		}
		
		//Condition for submission of Mailchimp Settings
	    $apiRegion = explode('-', $mc_res->api_key);
	    $apiRegion = (isset($apiRegion[1]))? $apiRegion[1] : '';

	    if($mc_new){	
	        $sql="INSERT INTO ".$wpdb->prefix . "ign_mailchimp_subscription (api_key, list_id, region, is_active) VALUES ('".$mc_res->api_key."', '".$mc_res->list_id."', '".$apiRegion."', '".($mc_active ? '1' : '0')."')";
	        $res = $wpdb->query( $sql );
	    }
	    else {
	        $sql_update="update ".$wpdb->prefix . "ign_mailchimp_subscription set api_key='".$mc_res->api_key."',list_id='".$mc_res->list_id."', region='".$apiRegion."', is_active = '".($mc_active ? '1' : '0')."' where id='1'";
			$res = $wpdb->query( $sql_update );
	    }
	    update_option('id_email_inactive', $inactive);
	    $message = '<div class="updated fade below-h2" id="message" class="updated"><p>'.$tr_Email_Settings_Saved.'</p></div>';
	}
	
	echo '<div class="wrap">
		'.admin_menu_html();
	
	include_once( "templates/admin/_emailSettings.php" );
	echo '</div>';
}

/**
 * Paypal Payment settings
 * @global object $wpdb
 */

function paypal_payment_options() {
	require 'languages/text_variables.php';
    global $wpdb;
	$tz = get_option('timezone_string');
	if (empty($tz)) {
		$tz = 'UTC';
	}
	date_default_timezone_set($tz);
    if(isset($_POST['btnSaveAdaptivePayment'])){
        if($_POST['btnSaveAdaptivePayment'] == $tr_Save_Settings) {
            // check if it is first time data entered
            $sql="SELECT * FROM ".$wpdb->prefix."ign_adaptive_pay_settings where id='1'";
            $res = $wpdb->query( $sql );
            $isFirstTime = ($res == 0)? true : false;
            if (isset($_POST['sandbox_mode'])) {
            	$sandbox_mode = $_POST['sandbox_mode'];
            }
            else {
            	$sandbox_mode = '';
            }
            if($isFirstTime){
                $sql="INSERT INTO ".$wpdb->prefix."ign_adaptive_pay_settings (id, paypal_email, app_id, api_username, api_password, api_signature, pre_approval_key, paypal_mode, fund_type)
						VALUES (
							1,
							'".$_POST['adaptive_email']."',
							'".$_POST['application_id']."',
							'".$_POST['api_username']."',
							'".$_POST['api_password']."',
							'".$_POST['api_signature']."',
							'',
							'".$sandbox_mode."',
							'".$_POST['fund_type']."'
						)";
                $res = $wpdb->query( $sql );
            } else {
                $sql="UPDATE ".$wpdb->prefix."ign_adaptive_pay_settings SET
						paypal_email = '".$_POST['adaptive_email']."',
						app_id='".$_POST['application_id']."',
						api_username='".$_POST['api_username']."',
						api_password='".$_POST['api_password']."',
						api_signature='".$_POST['api_signature']."',
						pre_approval_key = '',
						paypal_mode = '".$sandbox_mode."',
						fund_type = '".$_POST['fund_type']."'
						WHERE id='1'";
                $res = $wpdb->query( $sql );
            }
			
			// Check whether there is an entry in the selection table, if not make an entry\
			// else update the selection table
			// check if it is first time data entered
            $sql_selection_table = "SELECT * FROM ".$wpdb->prefix . "ign_pay_selection WHERE id='1'";
            $res = $wpdb->query( $sql_selection_table );
            $isFirstTimeSelection = ($res == 0)? true : false;
			
			if($isFirstTimeSelection) {
				$sql="INSERT INTO ".$wpdb->prefix."ign_pay_selection (id, payment_gateway, modified_date) VALUES (1, '".$_POST['payment_gateway']."',
                    '".date('Y-m-d H:i:s')."')";
                $res = $wpdb->query( $sql );
            } else {
                $sql="UPDATE ".$wpdb->prefix."ign_pay_selection SET payment_gateway = '".$_POST['payment_gateway']."', modified_date = '".date('Y-m-d H:i:s')."' WHERE id='1'";
                $res = $wpdb->query( $sql );
            }
			
            $message = '<div class="updated fade below-h2" id="message" class="updated"><p>'.$tr_Payment_settings_saved.'</p></div>';
        }
    }

    $sql="SELECT * FROM ".$wpdb->prefix . "ign_adaptive_pay_settings where id='1'";
    $payment_settings = $wpdb->get_row( $sql );
	//print_r($payment_settings);
	if(isset($_POST['btnSavePaymentSettings'])) {
        if($_POST['btnSavePaymentSettings'] == $tr_Save_Settings) {
            // check if it is first time data entered
            $sql="SELECT * FROM ".$wpdb->prefix . "ign_pay_settings where id='1'";
            $res = $wpdb->query( $sql );
            $isFirstTime = ($res == 0)? true : false;
            if (!isset($_POST['paypal_override'])) {
            	$paypal_override = "";
            }
            else {
            	$paypal_override = $_POST['paypal_override'];
            }
            if (isset($_POST['paypal_mode'])) {
            	$test_mode = $_POST['paypal_mode'];
            }
            else {
            	$test_mode = '';
            }

            if($isFirstTime){
                $sql="INSERT INTO ".$wpdb->prefix."ign_pay_settings (id, paypal_email, paypal_override, paypal_mode) VALUES (1, '".$_POST['paypal_email']."', '".$paypal_override."', '".$test_mode."')";
                //echo $sql;
                $res = $wpdb->query( $sql );
				
            }else{
                $sql="UPDATE ".$wpdb->prefix."ign_pay_settings set paypal_email='".$_POST['paypal_email']."', paypal_override='".$paypal_override."', paypal_mode='".$test_mode."' WHERE id='1'";
                //echo $sql;
                $res = $wpdb->query( $sql );
			
            }
			
			// Check whether there is an entry in the selection table, if not make an entry\
			// else update the selection table
			// check if it is first time data entered
            $sql_selection_table = "SELECT * FROM ".$wpdb->prefix . "ign_pay_selection WHERE id='1'";
            $res = $wpdb->query( $sql_selection_table );
            $isFirstTimeSelection = ($res == 0)? true : false;
			
			if($isFirstTimeSelection) {
				$sql="INSERT INTO ".$wpdb->prefix."ign_pay_selection (id, payment_gateway, modified_date) VALUES (1, '".$_POST['payment_gateway']."',
                    '".date('Y-m-d H:i:s')."')";
                $res = $wpdb->query( $sql );
            } else {
                $sql="UPDATE ".$wpdb->prefix."ign_pay_selection SET payment_gateway = '".$_POST['payment_gateway']."', modified_date = '".date('Y-m-d H:i:s')."' WHERE id='1'";
                $res = $wpdb->query( $sql );
            }
            $message = '<div class="updated fade below-h2" id="message" class="updated"><p>'.$tr_Payment_settings_saved.'</p></div>';
        }
    }

    $sql="SELECT * FROM ".$wpdb->prefix . "ign_pay_settings where id='1'";
    $res = $wpdb->query( $sql );
    $isFirstTime = ( $res ==  0 )? true: false;
    $items = $wpdb->get_results($sql);
    $item = &$items[0];
	
	
	// Selection the payment selection from the table 'ign_pay_selection'
	$sql = "SELECT * FROM ".$wpdb->prefix."ign_pay_selection WHERE id = '1'";

	$payment_select_data = $wpdb->get_row($sql);
	if (isset($payment_select_data) && $payment_select_data->payment_gateway == "standard_paypal") {
		$selected_standard = 'selected="selected"';
		$selected_adaptive = "";
	}

	else if (isset($payment_select_data) && $payment_select_data->payment_gateway == "adaptive_paypal") {
		$selected_standard = "";
		$selected_adaptive = 'selected="selected"';
	}

	else {
		$selected_standard = "";
		$selected_adaptive = "";
	}
	
	echo '<div class="wrap">
			'.admin_menu_html();
    include_once 'templates/admin/_paymentSelection.php';
    echo '<h3>Other Payment Settings</h3>';
    do_action('id_paysettings_links');
	echo '</div>';
}

function deck_builder() {
	require 'languages/text_variables.php';		
	if (isset($_POST['deck_submit'])) {
		$attrs = array();
		foreach ($_POST as $k=>$v) {
			if ($k !== 'deck_submit' && $k !== 'deck_select') {
				if ($k == 'deck_title') {
					$attrs[$k] = esc_attr($v);
				}
				else {
					$attrs[$k] = absint($v);
				}
			}
		}
		if ($_POST['deck_select'] > 0) {
			// update saved deck
			$deck_id = absint($_POST['deck_select']);
			Deck::update_deck($attrs, $deck_id);
		}
		else {
			// new deck, insert
			$new = Deck::create_deck($attrs);
		}
	}
	else if (isset($_POST['deck_delete'])) {
		$deck_id = absint($_POST['deck_select']);
		Deck::delete_deck($deck_id);
	}
	echo '<div class="wrap">';
	echo admin_menu_html();
	include 'templates/admin/_deckBuilder.php';
	echo '</div>';
}

/**
 * Order Details
 * @global object $wpdb
 */
function order_details(){
    require 'languages/text_variables.php';		
	global $wpdb;
	//$total_count = mysql_num_rows(mysql_query("SELECT * FROM ".$wpdb->prefix."ign_pay_info")); // number of total rows in the database
	$sql_products = "SELECT * FROM ".$wpdb->prefix."ign_products";
	$products = $wpdb->get_results($sql_products);
	
    echo '<div class="wrap">
			'.admin_menu_html();

    include_once 'templates/admin/_orderDetails.php';
	echo '</div>';
}

/*
 * Function for editing Order
 */
function edit_order() {
	require 'languages/text_variables.php';
	$orderid = $_GET['orderid'];
	
	global $wpdb;
    $sql = "SELECT * FROM ".$wpdb->prefix."ign_pay_info WHERE id = '".$orderid."'";
    $order_data = $wpdb->get_row( $sql );
	
	$sql_prods = "SELECT * FROM ".$wpdb->prefix."ign_products";
	$products = $wpdb->get_results( $sql_prods );
	
	echo '<div class="wrap">
			'.admin_menu_html();
	include_once 'templates/admin/_orderEdit.php';
	echo '</div>';
}

/*
 *  function for updating order on submission of form
 */
function update_order() {
	if ( isset($_POST['btnUpdateOrder']) ) {
		global $wpdb;
		$orderid = $_GET['orderid'];
		if (isset($_POST['manual-input']) && $_POST['manual-input'] !== "") {
			$price = $_POST['manual-input'];
		}
		else {
			$price = $_POST['prod_price'];
		}
		$sql = "UPDATE ".$wpdb->prefix."ign_pay_info SET
				first_name = '".$_POST['first_name']."',
				last_name = '".$_POST['last_name']."',
				email = '".$_POST['email']."',
				address = '".$_POST['address']."',
				country = '".$_POST['country']."',
				state = '".$_POST['state']."',
				city = '".$_POST['city']."',
				zip = '".$_POST['zip']."',
				status = '".$_POST['status']."',
				product_id = '".$_POST['product_id']."',
				product_level = '".$_POST['product_level']."',
				prod_price = '".$price."'
				WHERE id = '".$_GET['orderid']."'
				";
		$wpdb->query( $sql );
		
		wp_redirect( "admin.php?page=order_details" );
		do_action('id_order_update', $orderid);
		exit;
	}
}
add_action('init', 'update_order');

/*
 *  function for viewing order
 */
function view_order() {
	$orderid = $_GET['orderid'];

	$order = new ID_Order($orderid);
    $order_data = $order->get_order();
	
	$project = new ID_Project($order_data->product_id);
	$product_data = $project->the_project();
	$post_id = $project->get_project_postid();
	
	if ($order_data->product_level == 1) {
		$level_price = $product_data->product_price;
		$level_desc = $product_data->product_details;
	} else {
		$product_level = (int)($order_data->product_level);
		$level_price = get_post_meta( $post_id, $name="ign_product_level_".$product_level."_price", true );
		$level_desc = get_post_meta( $post_id, $name="ign_product_level_".$product_level."_desc", true );
	}
	require 'languages/text_variables.php';		
	echo '<div class="wrap">
			'.admin_menu_html();
	include_once 'templates/admin/_orderView.php';
	echo '</div>';
}

/*
 *  Function for deleting Order
 */
function delete_order() {
	global $wpdb;
	$orderid = $_GET['orderid'];
	do_action('id_pre_order_delete', $orderid);
    $sql = "DELETE FROM ".$wpdb->prefix."ign_pay_info WHERE id = '".$orderid."'";
    $wpdb->query( $sql );
	do_action('id_order_delete', $orderid);
	echo '<script type="text/javascript">window.location = "admin.php?page=order_details";</script>';
	exit;
}

/*
 *  Manually add order
 */
function add_order() {
	require 'languages/text_variables.php';		
	global $wpdb;
	$tz = get_option('timezone_string');
	if (empty($tz)) {
		$tz = 'UTC';
	}
	date_default_timezone_set($tz);
	$cancel_hook = false;
	if ( isset($_POST['btnAddOrder']) ) {
		if (isset($_POST['manual-input']) && $_POST['manual-input'] !== "") {
			$price = esc_attr(str_replace(',', '', $_POST['manual-input']));
		}
		else {
			$price = esc_attr($_POST['prod_price']);
		}
		if (isset($_POST['cancel-hook'])) {
			$cancel_hook = true;
		}

		$sql = "INSERT INTO ".$wpdb->prefix."ign_pay_info
					(first_name,last_name,email,address,country,state,city,zip,product_id,product_level,prod_price,status,created_at)
				VALUES (
					
					'".esc_attr($_POST['first_name'])."',
					'".esc_attr($_POST['last_name'])."',
					'".esc_attr($_POST['email'])."',
					'".esc_attr($_POST['address'])."',
					'".esc_attr($_POST['country'])."',
					'".esc_attr($_POST['state'])."',
					'".esc_attr($_POST['city'])."',
					'".esc_attr($_POST['zip'])."',
					'".absint($_POST['product_id'])."',
					'".absint($_POST['product_level'])."',
					'".esc_attr($price)."',
					'".esc_attr($_POST['status'])."',
					'".date('Y-m-d H:i:s')."'
				)";
		$wpdb->query( $sql );
		$pay_info_id = $wpdb->insert_id;
		if (!$cancel_hook) {
			do_action('id_payment_success', $pay_info_id);
		}
		
		$product_settings = getProductSettings($_POST['product_id']);
		$mailchip_settings = getMailchimpSettings();
		$aweber_settings = getAweberSettings();
		echo '<script type="text/javascript">window.location = "admin.php?page=order_details";</script>'; //wp_redirect( "admin.php?page=order_details" );
		exit;
	}
	
	$products = ID_Project::get_all_projects();

	//print_r($products);
	echo '<div class="wrap">
			'.admin_menu_html();
	include_once 'templates/admin/_orderAdd.php';
	echo '</div>';
}

/*
 *  Refund Paypal
 */
function refund_order() {
	global $wpdb;
	
	if (isset($_POST['btnRefundSubmit'])) {
		session_start();
		try {

			$currencyCode=$_REQUEST["currencyCode"];
	       	$payKey=$_REQUEST["payKey"];
			$email=$_REQUEST["receiveremail"];
			$amount = $_REQUEST["amount"];
			
	       /* Make the call to PayPal to get the Pay token
	        If the API call succeded, then redirect the buyer to PayPal
	        to begin to authorize payment.  If an error occured, show the
	        resulting errors
	        */
	       	$refundRequest = new RefundRequest();
	       	$refundRequest->currencyCode = $currencyCode;
	       	$refundRequest->payKey = $payKey;
			$refundRequest->requestEnvelope = new RequestEnvelope();
	        $refundRequest->requestEnvelope->errorLanguage = "en_US";
	        
	        $refundRequest->receiverList = new ReceiverList();
	        $receiver1 = new Receiver();
	        $receiver1->email = $email;
	        $receiver1->amount = $amount; 
	        $refundRequest->receiverList->receiver = $receiver1 ;
	        
	        $ap = new AdaptivePayments();
	        $response=$ap->Refund($refundRequest);
	           
	        if(strtoupper($ap->isSuccess) == 'FAILURE')
			{
				$_SESSION['FAULTMSG']=$ap->getLastError();
				$location = "APIError.php";
				header("Location: $location");
			
			}
		}
		catch(Exception $ex) {
			$fault = new FaultMessage();
			$errorData = new ErrorData();
			$errorData->errorId = $ex->getFile() ;
  			$errorData->message = $ex->getMessage();
	  		$fault->error = $errorData;
			$_SESSION['FAULTMSG']=$fault;
			$location = "APIError.php";
			//header("Location: $location");
		}
	}//end if
	
	$order_pay_info = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."ign_pay_info WHERE id = '".$_GET['orderid']."'");
	$product_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."ign_products WHERE product_id = '".$order_pay_info->product_id."'");
	$paypal_settings = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."ign_pay_settings WHERE product_id = '".$order_pay_info->product_id."'");
	
	require 'languages/text_variables.php';		
	echo '<div class="wrap">
			'.admin_menu_html();
	include_once ('templates/admin/_orderRefund.php');
	echo '</div>';
}

function main_settings() {
	require 'languages/text_variables.php';		
	global $wpdb;
	if ($_POST['btnIgnSettings'] == $tr_Add) {	
		$sql_insert = "	INSERT INTO ".$wpdb->prefix."ign_settings
						(
							theme_value,
							prod_page_fb,
							prod_page_twitter,
							prod_page_linkedin,
							prod_page_google,
							prod_page_pinterest,
							id_widget_logo_on,
							id_widget_link,
							ask_a_question,
							ask_email
						) VALUES (	
							'".$_POST['theme_value']."',
							'".$_POST['prod_page_fb']."',
							'".$_POST['prod_page_twitter']."',
							'".$_POST['prod_page_linkedin']."',
							'".$_POST['prod_page_google']."',
							'".$_POST['prod_page_pinterest']."',
							'".$_POST['id_widget_logo_on']."',
							'".$_POST['id_widget_link']."',
							'".$_POST['ask_a_question']."',
							'".$_POST['ask_email']."'
						)";

		$wpdb->query( $sql_insert );
	} elseif ($_POST['btnIgnSettings'] == $tr_Update) {	
		$sql_update = "	UPDATE ".$wpdb->prefix."ign_settings SET
						theme_value = '".$_POST['theme_value']."',
						prod_page_fb = '".$_POST['prod_page_fb']."',
						prod_page_twitter = '".$_POST['prod_page_twitter']."',
						prod_page_linkedin = '".$_POST['prod_page_linkedin']."',
						prod_page_google = '".$_POST['prod_page_google']."',
						prod_page_pinterest = '".$_POST['prod_page_pinterest']."',
						id_widget_logo_on = '".$_POST['id_widget_logo_on']."',
						id_widget_link = '".$_POST['id_widget_link']."',
						ask_a_question = '".$_POST['ask_a_question']."',
						ask_email = '".$_POST['ask_email']."'
						WHERE id = '1'";
		$wpdb->query( $sql_update );
	}
	$data = getSettings();
	
	include_once ('templates/admin/_settingsIgnDeck.php');
}

function generate_embed_code() {
	global $wpdb;
	$site_url = site_url();
	
	$sql_products = "SELECT * FROM ".$wpdb->prefix."ign_products";
	$products = $wpdb->get_results($sql_products);
	
	require 'languages/text_variables.php';		
	include_once ('templates/admin/_embedWidget.php');
}

function product_settings() {
	require 'languages/text_variables.php';		
	global $wpdb;
	if (is_id_pro()) {
		$project_default = get_option('id_project_default');
	}
	if (function_exists('idf_platform')) {
		$platform = idf_platform();
	}
	else {
		$platform = 'legacy';
	}
	$purchase_default = get_option('id_purchase_default');
	$ty_default = get_option('id_ty_default');
	$auto_insert = get_option('idcf_auto_insert');
	//============================================================================================================================================
	//	DEFAULT settings
	//============================================================================================================================================
		
		$sql_currency = "SELECT * FROM ".$wpdb->prefix."ign_prod_default_settings WHERE id = '1'";
		$default_currency = $wpdb->get_row($sql_currency);
		
		if(isset($_POST['btnSubmitDefaultSettings'])){
			if (!empty($_POST['ignitiondeck_form_default'])) {
				$serializedFormDefault = serialize($_POST['ignitiondeck_form_default']);
			}
			else {
				$serializedFormDefault = serialize(array());
			}
			if($_POST['btnSubmitDefaultSettings'] == $tr_Save_Settings){	
				$default_currency->currency_code = $_POST['currency_code_default'];
				$sql_insert = "	INSERT INTO ".$wpdb->prefix."ign_prod_default_settings
								(
									form_settings,
									currency_code
								) values (
									'".$serializedFormDefault."',
									'".$default_currency->currency_code."'
								)";
				$res = $wpdb->query( $sql_insert );
				// first time we are setting defaults, so we're updating option to avoid future nags
				update_option('id_defaults_notice', 'off');
				$message = '<div class="updated fade below-h2" id="message" class="updated"><p>'.$tr_Save_Settings.'</p></div>';	
			}
			if($_POST['btnSubmitDefaultSettings'] == $tr_Update_Settings){	
				$default_currency->currency_code = (isset($_POST['currency_code_default']) ? $_POST['currency_code_default'] : 'USD');
				$sql_update = "	UPDATE ".$wpdb->prefix."ign_prod_default_settings SET
								form_settings='".$serializedFormDefault."',
								currency_code = '".$default_currency->currency_code."'
								WHERE id='1'";
				$res = $wpdb->query( $sql_update );
				
				update_option('id_defaults_notice', 'off');
				$message = '<div class="updated fade below-h2" id="message" class="updated"><p>'.$tr_Settings_updated.'</p></div>';	
			}
			// purchase url
			$purl_sel = esc_attr($_POST['ign_option_purchase_url']);
			if ($purl_sel == 'page_or_post') {
				$purl = absint($_POST['ign_purchase_post_name']);
			}
			else {
				$purl = esc_attr($_POST['id_purchase_URL']);
			}
			$purchase_default = array('option' => $purl_sel, 'value' => $purl);
			update_option('id_purchase_default', $purchase_default);
			if (idf_exists() && idf_platform() == 'legacy') {
				// ty url
				$tyurl_sel = esc_attr($_POST['ign_option_ty_url']);
				if ($tyurl_sel == 'page_or_post') {
					$tyurl = absint($_POST['ign_ty_post_name']);
				}
				else {
					$tyurl = esc_attr($_POST['id_project_URL']);
				}
				$ty_default = array('option' => $tyurl_sel, 'value' => $tyurl);
				update_option('id_ty_default', $ty_default);
			}
			if (isset($_POST['auto_insert'])) {
				$auto_insert = absint($_POST['auto_insert']);
			}
			else {
				$auto_insert = 0;
			}
			update_option('idcf_auto_insert', $auto_insert);
		}
	
		$sql="SELECT * FROM ".$wpdb->prefix."ign_prod_default_settings WHERE id='1'";
		$res1 =  $wpdb->query( $sql );
		$rows = $wpdb->get_results($sql);
		$row = &$rows[0];
		if($row != null){
			$submit_default = $tr_Update_Settings;
			$form_default = unserialize( $row->form_settings );
		}else{
			$submit_default = $tr_Save_Settings;
		}
	
	$products = ID_Project::get_all_projects();

	$args = array('orderby' => 'title', 'order' => 'ASC', 'post_type' => array('post', 'page'), 'posts_per_page' => -1);
	$list = new WP_Query($args);
	echo '<div class="wrap">
			'.admin_menu_html();
	include_once ('templates/admin/_productSettings.php');
	echo '</div>';
}

function custom_settings() {
	require 'languages/text_variables.php';	
	global $wpdb;

	if (isset($_GET['pid']) && $_GET['pid'] !== "") {
		$pid = $_GET['pid'];
		$pay_selection = getDefaultPaymentMethod();
		$project = new ID_Project($pid);
		$product_settings = $project->get_project_settings();
		$products = $project->the_project();

		if (!empty($product_settings)) {
			$form = unserialize($product_settings->form_settings);
			$serializedForm = $product_settings->form_settings;
			$mc_api = $product_settings->mailchimp_api_key;
			$mc_list = $product_settings->mailchimp_list_id;
			$aweber_email = $product_settings->aweber_email;
			$mailtype = $product_settings->active_mailtype;
			$paypal_email = $product_settings->paypal_email;
			$currency_code = $product_settings->currency_code;
		}
		else {
			$mc_api = '';
			$mc_list = '';
			$aweber_email = '';
			$mailtype = '';
			$paypal_email = '';
			$currency_code = 'USD';
			$form = '';
			$serializedForm = serialize($form);
		}
	}
	else {
		$products = ID_Project::get_all_projects();
	}

	if (isset($_POST['btnSubmitProdSettings'])) {
		if (isset($_POST['mailchimp_api_key'])) {
			$mc_api = $_POST['mailchimp_api_key'];
		}
		if (isset($_POST['mailchimp_list_id'])) {
			$mc_list = $_POST['mailchimp_list_id'];
		}
		if (isset($_POST['aweber_email'])) {
			$aweber_email = $_POST['aweber_email'];
		}
		if (isset($_POST['active_mailtype'])) {
			$mailtype = $_POST['active_mailtype'];
		}
		if (isset($_POST['paypal_email'])) {
			$paypal_email = $_POST['paypal_email'];
		}
		if (isset($_POST['currency_code'])) {
			$currency_code = $_POST['currency_code'];
		}
		if (isset($_POST['ignitiondeck_form'])) {
			$form = $_POST['ignitiondeck_form'];
			$serializedForm = serialize($_POST['ignitiondeck_form']);
		}

		if (empty($product_settings)) {
			$sql_insert_product = "INSERT INTO ".$wpdb->prefix."ign_product_settings (
										product_id,
										mailchimp_api_key,
										mailchimp_list_id,
										aweber_email,
										active_mailtype,
										form_settings,
										paypal_email,
										currency_code
									) VALUES (
										'".$_GET['pid']."',
										'".$mc_api."',
										'".$mc_list."',
										'".$aweber_email."',
										'".$mailtype."',
										'".$serializedForm."',
										'".$paypal_email."',
										'".$currency_code."'
									)";
			$wpdb->query($sql_insert_product);
			$message = '<div class="updated fade below-h2" id="message" class="updated"><p>'.$tr_Save_Settings.'</p></div>';	
		}
		else {
			$sql_update_product = "UPDATE ".$wpdb->prefix."ign_product_settings SET
									mailchimp_api_key = '".$mc_api."',
									mailchimp_list_id = '".$mc_list."',
									aweber_email = '".$aweber_email."',
									active_mailtype = '".$mailtype."',
									form_settings = '".$serializedForm."',
									paypal_email = '".$paypal_email."',
									currency_code = '".$currency_code."'								
									WHERE product_id = '".$_GET['pid']."'
									";
			$wpdb->query($sql_update_product);						
			$message = '<div class="updated fade below-h2" id="message" class="updated"><p>'.$tr_Settings_updated.'</p></div>';	
		}
	}

	else if (isset($_POST['btnClearProdSettings']) && $_POST['btnClearProdSettings'] !="") {
		$pid = $_GET['pid'];
		$project = new ID_Project($pid);
		$clear = $project->clear_project_settings();
		echo '<script>window.location="";</script>';
	}

	else if (isset($_POST['btnClearAllSettings']) && $_POST['btnClearAllSettings'] !="") {
		$sql = "DELETE FROM ".$wpdb->prefix."ign_product_settings";
		$clear_all_product_settings = $wpdb->query($sql);
	}
	
	echo '<div class="wrap">
			'.admin_menu_html();
	include_once ('templates/admin/_customSettings.php');
	echo '</div>';
}

function asked_questions() {
	require 'languages/text_variables.php';		
	global $wpdb;
	echo '<div class="wrap">
    	'.admin_menu_html().'
    <h2>'.$tr_Asked_Questions.'</h2>';	
    echo '<br />';
	
	if (isset($_GET['action']) && $_GET['action'] == "delete") {
		$sql_delete = "DELETE FROM ".$wpdb->prefix."ign_questions WHERE id = '".$_GET['quesid']."'";
		$wpdb->query($sql_delete);
		echo '<script type="text/javascript">window.location = "admin.php?page=asked_questions";</script>';
	}
	
	if (isset($_GET['quesid'])) {
		$sql_question = "SELECT * FROM ".$wpdb->prefix."ign_questions WHERE id = '".$_GET['quesid']."'";
		$question_data = $wpdb->get_row($sql_question);
		$question_view = true;
	} else {
		$sql_questions = "SELECT * FROM ".$wpdb->prefix."ign_questions";
		$questions = $wpdb->get_results($sql_questions);

		$total_pages = count($questions);
		/* Setup vars for query. */
		$targetpage = "?page=asked_questions";
		//your file name  (the name of this file)
		$limit = 10;
		//how many items to show per page
		$page = $_GET['page_no'];
		if($page){
			$start = ($page - 1) * $limit;
			//first item to display on this page
		}else{
			$start = 0;
			//if no page var is given, set start to 0/* Get data. */
		}
	
		$sql = "SELECT * FROM ".$wpdb->prefix."ign_questions ORDER BY id DESC LIMIT $start, $limit";
		$res = $wpdb->query( $sql );$items = $wpdb->get_results($sql);
		/* Setup page vars for display. */
	
		if ($page == 0){
			$page = 1;
		}
		//if no page var is given, default to 1.
		$prev = $page - 1;
		//previous page is page - 1
		$next = $page + 1;
		//next page is page + 1
		$lastpage = ceil($total_pages/$limit);
		//lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;
		//last page minus 1/* Now we apply our rules and draw the pagination object. We're actually saving the code to a variable in case we want to draw it more than once.*/
		$pagination = "";
		if($lastpage > 1){
			$pagination .= "<div class=\"pagination\">";
			//previous button
			if ($page > 1){
				$pagination.= "<a href=\"$targetpage&page_no=$prev\">&laquo; ".$tr_previous."</a>";	
			}else{
				$pagination.= "<span class=\"disabled\">&laquo; ".$tr_previous."</span>";	
			}
	
			//pages
			if ($lastpage < 7 + ($adjacents * 2)){
				for ($counter = 1; $counter <= $lastpage; $counter++){
					if ($counter == $page){
						$pagination.= "<span class=\"current\">$counter</span>";}
					else{
						$pagination.= "<a href=\"$targetpage&page_no=$counter\">$counter</a>";
					}
				}
			}elseif( $lastpage > 5 + ($adjacents * 2) ){
				//close to beginning; only hide later pages
				if($page < 1 + ($adjacents * 2)){
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
						if ($counter == $page){
							$pagination.= "<span class=\"current\">$counter</span>";
						}else{
							$pagination.= "<a href=\"$targetpage&page_no=$counter\">$counter</a>";
						}
					}
	
					$pagination.= "...";
					$pagination.= "<a href=\"$targetpage&page_no=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"$targetpage&page_no=$lastpage\">$lastpage</a>";
	
				}elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)){
					$pagination.= "<a href=\"$targetpage&page_no=1\">1</a>";
					$pagination.= "<a href=\"$targetpage&page_no=2\">2</a>";
					$pagination.= "...";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++){
						if ($counter == $page){
							$pagination.= "<span class=\"current\">$counter</span>";
						}else{
							$pagination.= "<a href=\"$targetpage&page_no=$counter\">$counter</a>";
						}
					}
	
					$pagination.= "...";
					$pagination.= "<a href=\"$targetpage&page_no=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"$targetpage&page_no=$lastpage\">$lastpage</a>";
	
				}else{
					$pagination.= "<a href=\"$targetpage&page_no=1\">1</a>";
					$pagination.= "<a href=\"$targetpage&page_no=2\">2</a>";
					$pagination.= "...";
	
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++){
						if ($counter == $page){
							$pagination.= "<span class=\"current\">$counter</span>";
						}else{
							$pagination.= "<a href=\"$targetpage&page_no=$counter\">$counter</a>";
						}
					}
				}
			}//next button
	
			if ($page < $counter - 1){
				$pagination.= "<a href=\"$targetpage&page_no=$next\">".$tr_next." &raquo;</a>";	
			}else{
				$pagination.= "<span class=\"disabled\">".$tr_next." &raquo;</span>";	
			}
			$pagination.= "</div>\n";
		}
	}
	
	include_once ('templates/admin/_questionView.php');
	
}

function admin_menu_html() {
	require 'languages/text_variables.php';		
	 //All the lines, with #GLOBALS['<variable name>']; replace with $<variable name>
	$menu = '
		<div class="sidebar ignitiondeck">
			<div class="icon32"></div><h2 class="title">'.__("IgnitionDeck Crowdfunding", "ignitiondeck").'</h2>
			<div class="help">
				<a href="http://forums.ignitiondeck.com" alt="IgnitionDeck Support" title="IgnitionDeck Support" target="_blank"><button class="button button-large">'.__('Support', 'memberdeck').'</button></a>
				<a href="http://docs.ignitiondeck.com" alt="IgnitionDeck Documentation" title="IgnitionDeck Documentation" target="_blank"><button class="button button-large">'.__('Documentation', 'memberdeck').'</button></a>
			</div>
			<br style="clear: both;"/>
			<h3 class="nav-tab-wrapper">';
	$menu .='<a'.(($_GET['page'] == "ignitiondeck") ? ' class="nav-tab nav-tab-active"' : ' class="nav-tab"').' href="admin.php?page=ignitiondeck">'.$tr_Settings.'</a>';
	$menu .= apply_filters('idcf_project_settings_tab', '<a '.(($_GET['page'] == "project-settings") ? ' class="nav-tab nav-tab-active"' : ' class="nav-tab"').' href="admin.php?page=project-settings">'.$tr_Product_Settings.'</a>');
				if (is_id_licensed()) {
				$menu .= apply_filters('idcf_custom_settings_tab', '<a '.(($_GET['page'] == "custom-settings") ? ' class="nav-tab nav-tab-active"' : ' class="nav-tab"').' href="admin.php?page=custom-settings">'.$tr_Custom_Pre_Product_Settings.'</a>');
				$menu .= apply_filters('idcf_payment_settings_tab', '<a '.(($_GET['page'] == "payment-options") ? ' class="nav-tab nav-tab-active"' : ' class="nav-tab"').'href="admin.php?page=payment-options">'.$tr_Payment_Settings.'</a>');
				$menu .= '<a '.(($_GET['page'] == "deck-builder") ? ' class="nav-tab nav-tab-active"' : ' class="nav-tab"').'href="admin.php?page=deck-builder">'.$tr_Deck_Builder.'</a>';
				}
				$menu .= '<a '.(($_GET['page'] == "order_details") ? ' class="nav-tab nav-tab-active"' : ' class="nav-tab"').' href="admin.php?page=order_details">'.$tr_Order_Details.'</a>';
				if (is_id_licensed()) {
					'<a '.(($_GET['page'] == "email-settings") ? ' class="nav-tab nav-tab-active"' : ' class="nav-tab"').' href="admin.php?page=email-settings">'.$tr_Email_List_Settings.'</a>';
				}	
	$menu_sub = '</h3></div>';
		
	return apply_filters('id_submenu_tab', $menu).$menu_sub;
}

function getProductFromPostID($postid) {
	$project_id = get_post_meta($post_id, 'ign_project_id', true);
	if ($project_id > 0) {
		$project = new ID_Project($project_id);
		$the_project = $project->the_project();
	}
	return (!empty($the_project) ? $the_project : null);
}
function delete_image() {
	//{
	global $wpdb;
	echo "hi";
	exit;
	$sql = "DELETE FROM ".$wpdb->prefix."postmeta WHERE post_id = '".$_GET['post_id']."' and meta_key='".$_GET['meta_key']."'";
	$wpdb->query($sql);	
}

function delete_img() {
	global $wpdb;
	global $post;
	if ($post) {
		$post_id = $post->ID;
		$meta_key = $_GET['meta_key'];
		$sql = $wpdb->prepare("DELETE FROM ".$wpdb->prefix."postmeta WHERE post_id = %d and meta_key=%s", $post_id, $_GET['meta_key']);
		if ($meta_key) {
			$wpdb->query($sql);
		}
	}
}

/*
 *	Desc: function to save the project URL that is stored in metabox for project url
 */
function save_project_url($post_id) {
	// check nonce
	if (!isset($_POST['add_project_url_box_nonce']) || !wp_verify_nonce($_POST['add_project_url_box_nonce'], 'add_project_url_box')) {
		return $post_id;
	}
	
	// check capabilities
	if ('post' == $_POST['post_type']) {
		
		if (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

	}
	elseif (!current_user_can('edit_page', $post_id)) {
		return $post_id;
	}

	// exit on autosave	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	
	if(isset($_POST['ign_option_project_url']))
	{
		update_post_meta($post_id, 'ign_option_project_url', esc_attr($_POST['ign_option_project_url']));
	}
	
	if ($_POST['ign_option_project_url'] == "external_url") {		// If the Project URL is selected as external URL, that is the popup box is used to insert link
		if(isset($_POST['id_project_URL'])) {
			update_post_meta($post_id, 'id_project_URL', esc_attr($_POST['id_project_URL']));
			//update_post_meta($post_id, 'id_project_URL', $_POST['id_project_URL']);
		} else {
			delete_post_meta($post_id, 'id_project_URL');
		}
		delete_post_meta($post_id, 'ign_post_name');
	} else if ($_POST['ign_option_project_url'] == "page_or_post") {		// If project URL is some other Project page or Post page, then save its name
		if(isset($_POST['ign_post_name']))
		{
		
			if($_POST['ign_post_name'] != '')
			{
				update_post_meta($post_id, 'ign_post_name', esc_attr($_POST['ign_post_name']));
			}
		}
		delete_post_meta($post_id, 'id_project_URL');
	} else if ($_POST['ign_option_project_url'] == "current_page") {		// If it is the current page that is used as Project page, do nothing
		// Do nothing as the project page is the ignition_project page itself
		
		// Deleting the Meta data for other types of $_POST['ign_option_project_url'] if it was previously stored
		delete_post_meta($post_id, 'ign_post_name');
		delete_post_meta($post_id, 'id_project_URL');
	}
	
	
	
}
add_action('save_post', 'save_project_url', 10, 2);

function save_purchase_url($post_id) {
	// check nonce
	if (!isset($_POST['add_purchase_url_box_nonce']) || !wp_verify_nonce($_POST['add_purchase_url_box_nonce'], 'add_purchase_url_box')) {
		return $post_id;
	}
	
	// check capabilities
	if ('post' == $_POST['post_type']) {
		
		if (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

	}
	elseif (!current_user_can('edit_page', $post_id)) {
		return $post_id;
	}

	// exit on autosave	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	
	if(isset($_POST['ign_option_purchase_url']))
	{
		update_post_meta($post_id, 'ign_option_purchase_url', esc_attr($_POST['ign_option_purchase_url']));
	}
	
	if ($_POST['ign_option_purchase_url'] == "external_url") {		// If the Project URL is selected as external URL, that is the popup box is used to insert link
		if(isset($_POST['purchase_project_URL'])) {
			update_post_meta($post_id, 'purchase_project_URL', esc_attr($_POST['purchase_project_URL']));
			//update_post_meta($post_id, 'id_project_URL', $_POST['id_project_URL']);
		} else {
			delete_post_meta($post_id, 'purchase_project_URL');
		}
		delete_post_meta($post_id, 'ign_purchase_post_name');
	} else if ($_POST['ign_option_purchase_url'] == "page_or_post") {		// If project URL is some other Project page or Post page, then save its name
		if(isset($_POST['ign_purchase_post_name']))
		{
		
			if($_POST['ign_purchase_post_name'] != '')
			{
				update_post_meta($post_id, 'ign_purchase_post_name', esc_attr($_POST['ign_purchase_post_name']));
			}
		}
		delete_post_meta($post_id, 'purchase_project_URL');
	} else if ($_POST['ign_option_purchase_url'] == "current_page") {		// If it is the current page that is used as Project page, do nothing
		// Do nothing as the project page is the ignition_project page itself
		
		// Deleting the Meta data for other types of $_POST['ign_option_project_url'] if it was previously stored
		delete_post_meta($post_id, 'ign_purchase_post_name');
		delete_post_meta($post_id, 'purchase_project_URL');
	}
	
	
	
}
add_action('save_post', 'save_purchase_url', 10, 2);

function save_ty_url($post_id) {
	// check nonce
	if (!isset($_POST['add_ty_url_box_nonce']) || !wp_verify_nonce($_POST['add_ty_url_box_nonce'], 'add_ty_url_box')) {
		return $post_id;
	}
	
	// check capabilities
	if ('post' == $_POST['post_type']) {
		
		if (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

	}
	elseif (!current_user_can('edit_page', $post_id)) {
		return $post_id;
	}

	// exit on autosave	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	
	if(isset($_POST['ign_option_ty_url']))
	{
		update_post_meta($post_id, 'ign_option_ty_url', esc_attr($_POST['ign_option_ty_url']));
	}
	
	if ($_POST['ign_option_ty_url'] == "external_url") {		// If the Project URL is selected as external URL, that is the popup box is used to insert link
		if(isset($_POST['ty_project_URL'])) {
			update_post_meta($post_id, 'ty_project_URL', esc_attr($_POST['ty_project_URL']));
			//update_post_meta($post_id, 'id_project_URL', $_POST['id_project_URL']);
		} else {
			delete_post_meta($post_id, 'ty_project_URL');
		}
		delete_post_meta($post_id, 'ign_ty_post_name');
	} else if ($_POST['ign_option_ty_url'] == "page_or_post") {		// If project URL is some other Project page or Post page, then save its name
		if(isset($_POST['ign_ty_post_name']))
		{
		
			if($_POST['ign_ty_post_name'] != '')
			{
				update_post_meta($post_id, 'ign_ty_post_name', esc_attr($_POST['ign_ty_post_name']));
			}
		}
		delete_post_meta($post_id, 'ty_project_URL');
	} else if ($_POST['ign_option_ty_url'] == "current_page") {		// If it is the current page that is used as Project page, do nothing
		// Do nothing as the project page is the ignition_project page itself
		
		// Deleting the Meta data for other types of $_POST['ign_option_project_url'] if it was previously stored
		delete_post_meta($post_id, 'ign_ty_post_name');
		delete_post_meta($post_id, 'ty_project_URL');
	}
}
add_action('save_post', 'save_ty_url', 10, 2);

/**
 * Function to save project parent in post meta using the meta_box
 */
function save_project_parent($post_id) {
	// Get it's parent id for using below
	$parent_id = get_post_meta($post_id, 'ign_project_parent', true);

	// Saving parent if it's not empty
	if(isset($_POST['ign_option_project_parent']) && !empty($_POST['ign_option_project_parent'])) {
		// updating post meta for parent id of $post_id if it's changed
		if ($parent_id != $_POST['ign_option_project_parent']) {
			update_post_meta($post_id, 'ign_project_parent', esc_attr($_POST['ign_option_project_parent']));

			// Removing the $post_id from older parent's children
			$old_parent_children = get_post_meta($parent_id, 'ign_project_children', true);
			// If nothing is in post meta field ign_project_children then don't do anything
			if (!empty($old_parent_children)) {
				$index_post_id = array_search($post_id, $old_parent_children);
				if (!($index_post_id === false)) {
					unset($old_parent_children[$index_post_id]);
				}
				// Saving back the array into post meta in ign_project_children
				update_post_meta($parent_id, 'ign_project_children', $old_parent_children);
			}
		}
		
		// Now saving this into post meta of parent post id, but first getting the post meta of parent
		$parent_children = get_post_meta($_POST['ign_option_project_parent'], 'ign_project_children', true);
		// If nothing is in post meta field ign_project_children, then start it from an empty array
		if (empty($parent_children)) {
			$parent_children = array();
		}

		// Adding the current post_id into children if needed
		if (array_search($post_id, $parent_children) === false) {
			array_push($parent_children, $post_id);
			update_post_meta($_POST['ign_option_project_parent'], 'ign_project_children', $parent_children);
		}
	}
	// removing the parent if value is null
	else {
		delete_post_meta($post_id, 'ign_project_parent');
		
		// getting the parent's children and removing $post_id from them
		$parent_children = get_post_meta($parent_id, 'ign_project_children', true);

		// If nothing is in post meta field then don't do anything
		if (!empty($parent_children)) {
			$index_post_id = array_search($post_id, $parent_children);
			if (!($index_post_id === false)) {
				unset($parent_children[$index_post_id]);
			}
			// Saving back the array into post meta in ign_project_children
			update_post_meta($parent_id, 'ign_project_children', $parent_children);
		}
	}
}
add_action('save_post', 'save_project_parent', 10, 2);

function set_project_meta($post_id) {
	$post = get_post($post_id);
	if (!empty($post)) {
		if ($post->post_type == 'ignition_product') {
			$raised = ID_Project::set_raised_meta();
			$percent = ID_Project::set_percent_meta();
			$days = ID_Project::set_days_meta();
			$closed = ID_Project::set_closed_meta();
		}
	}
}

add_action('save_post', 'set_project_meta');

function delete_project($post_id) {
	global $wpdb;
    $post = get_post($post_id);
    if ($post->post_type == 'ignition_product') {
        $product = getProductbyPostID($post->ID);
        if (!empty($product)) {
        	$project_id = get_post_meta($post_id, 'ign_project_id', true);
        	do_action('idcf_before_delete_project', $post_id, $project_id);
	        $remove_query = $wpdb->prepare('DELETE FROM '.$wpdb->prefix.'ign_products WHERE id = %d', $product->id);
	        $remove_res = $wpdb->query($remove_query);
	        $sql_prod_settings = "DELETE FROM ".$wpdb->prefix."ign_product_settings WHERE product_id = '".$product->id."'";
			$wpdb->query($sql_prod_settings);
			do_action('idcf_delete_project', $post_id, $project_id);
	    }
    }
}
add_action('before_delete_post', 'delete_project');

function id_setup_nags() {
	$settings = getSettings();
	// Let's check if the notices have been cleared before
	$settings_notice = get_option('id_settings_notice');
	$defaults_notice = get_option('id_defaults_notice');
	$products_notice = get_option('id_products_notice');
	$idf_notice = get_option('id_idf_notice');

	if (empty($settings) && empty($settings_notice)) {
		// add settings nag
		add_action('admin_notices', 'id_settings_notice');
	}
	$defaults = getProductDefaultSettings();
	if ((empty($defaults) || !is_object($defaults)) && empty($defaults_notice)) {
		// add defaults nag
		add_action('admin_notices', 'id_defaults_notice');
	}
	$products = ID_Project::get_all_projects();
	if (empty($products) && empty($products_notice)) {
		// add products nag
		add_action('admin_notices', 'id_products_notice');
	}
	if (!idf_exists() && empty($idf_notice)) {
		add_action('admin_notices', 'id_idf_notice');
	}
}

add_action('admin_init', 'id_setup_nags', 100);

function id_settings_notice() {
	echo '<div class="updated">
	       <p>IgnitionDeck Crowdfunding is active. Please <a href="wp-admin/admin.php?page=ignitiondeck">save settings</a> before creating your first project. | <a href="#" id="id_settings_notice" class="hide-notice">Hide Notice</a></p>
	    </div>';
}

function id_defaults_notice() {
	echo '<div class="updated">
	       <p>Please <a href="admin.php?page=project-settings">save default project settings</a> before creating your first project. | <a href="#" id="id_defaults_notice" class="hide-notice">Hide Notice</a></p>
	    </div>';
}

function id_products_notice() {
	echo '<div class="updated">
	       <p>IgnitionDeck Crowdfunding is active. Now it&rsquo;s time to <a href="post-new.php?post_type=ignition_product">create your first project</a>. | <a href="#" id="id_products_notice" class="hide-notice">Hide Notice</a></p>
	    </div>';
}

function id_idf_notice() {
	if (file_exists(plugin_dir_path(dirname(__FILE__)).'/ignitiondeck/idf.php')) {
		$url = wp_nonce_url(network_admin_url('plugins.php'));
	}
	else {
		$url = wp_nonce_url(network_admin_url( 'plugin-install.php?tab=search&s=ignitiondeck' ));
	}
	echo '<div class="updated">
	       <p>This plugin requires the <a href="'.$url.'">IgnitionDeck Framework</a> in order to function properly.</p>
	    </div>';
}

class ID_MCE_Buttons {
	function ID_MCE_Buttons() {
		if(is_admin()) {
			if ( current_user_can('edit_posts') && current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
        		add_filter('tiny_mce_version', array(&$this, 'tiny_mce_version') );
        		add_filter("mce_external_plugins", array(&$this, "id_mce_plugin"));
        		add_filter('mce_buttons_2', 'id_mce_shortcodes');
        	}
		}
	}
	function id_mce_shortcodes($buttons) {
		$buttons[] = 'idshortcodes';
		return $buttons;
	}
	function id_mce_plugin($plugin_array) {
		$plugin_array['idshortcodes']  =  plugins_url('/js/idmce.js', __FILE__);
		return $plugin_array;
	}
	function tiny_mce_version($version) {
		return ++$version;
	}

}

//add_action('init', 'ID_MCE_Buttons');

function ID_MCE_Buttons() {
	global $ID_MCE_Buttons;
	$ID_MCE_Buttons = new ID_MCE_Buttons();
}
?>