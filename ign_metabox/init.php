<?php
/*
Script Name: 	Custom Metaboxes and Fields
Contributors: 	Andrew Norcross (@norcross / andrewnorcross.com)
				Jared Atchison (@jaredatch / jaredatchison.com)
				Bill Erickson (@billerickson / billerickson.net)
Description: 	This will create metaboxes with custom fields that will blow your mind.
Version: 		0.4
*/

/**
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

/************************************************************************
		You should not edit the code below or things might explode!
*************************************************************************/
//$meta_boxes = array();
$meta_boxes = apply_filters('ign_cmb_meta_boxes', array());
if (is_array($meta_boxes)) {
	foreach ( $meta_boxes as $meta_box ) {
		$my_box = new ign_cmb_Meta_Box($meta_box);
	}
}


/**
 * Validate value of meta fields
 * Define ALL validation methods inside this class and use the names of these 
 * methods in the definition of meta boxes (key 'validate_func' of each field)
 */

class ign_cmb_Meta_Box_Validate {
	function check_text( $text ) {
		if ($text != 'hello') {
			return false;
		}
		return true;
	}
}


/*
 * Script url to load local resources.
 */

//define( 'CMB_META_BOX_URL', trailingslashit( str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname(__FILE__) ) ) );
define( 'IDCF_CMB_META_BOX_URL', plugins_url('/ign_metabox/', dirname(__FILE__)));

/**
 * Create meta boxes
 */

class ign_cmb_Meta_Box {
	protected $_meta_box;

	function __construct( $meta_box ) {
		if ( !is_admin() ) return;

		$this->_meta_box = $meta_box;

		$upload = false;
		foreach ( $meta_box['fields'] as $field ) {
			if ( $field['type'] == 'file' || $field['type'] == 'file_list' || $field['type'] == 'wysiwyg') {
				$upload = true;
				break;
			}
		}
		
		$current_page = substr(strrchr($_SERVER['PHP_SELF'], '/'), 1, -4);
		
		if ( $upload && ( $current_page == 'page' || $current_page == 'page-new' || $current_page == 'post' || $current_page == 'post-new' ) ) {
			add_action('admin_head', array(&$this, 'add_post_enctype'));
		}

		add_action( 'admin_menu', array(&$this, 'add') );
		add_action( 'save_post', array(&$this, 'save'), 3, 2 );
	}

	function add_post_enctype() {
		echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#post").attr("enctype", "multipart/form-data");
			jQuery("#post").attr("encoding", "multipart/form-data");
		});
		</script>';
	}

	/// Add metaboxes
	function add() {
		$this->_meta_box['context'] = empty($this->_meta_box['context']) ? 'normal' : $this->_meta_box['context'];
		$this->_meta_box['priority'] = empty($this->_meta_box['priority']) ? 'high' : $this->_meta_box['priority'];
		foreach ($this->_meta_box['pages'] as $page) {
			add_meta_box($this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']);
		}
	}

	// Show fields
	function show() {
		global $post;

		// Use nonce for verification
		echo '<input type="hidden" name="wp_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
		echo '<ul class="form-table cmb_metabox">';

		foreach ( $this->_meta_box['fields'] as $field ) {
			// Set up blank values for empty ones
			if ( !isset($field['desc']) ) $field['desc'] = '';
			if ( !isset($field['std']) ) $field['std'] = '';
			if ( !isset($field['id']) ) $field['id'] = '';
			if ( !isset($field['name']) ) $field['name'] = '';
			if ( !isset($field['show_help']) ) $field['show_help'] = false;
			$meta = get_post_meta( $post->ID, $field['id'], 'multicheck' != $field['type'] /* If multicheck this can be multiple values */ );
			
			if ( $field['type'] == "level1wraptop" ) {
				echo '<div level="1" class="projectmeta-levelbox" style="padding: 7px;"><h2>'.$GLOBALS["tr_Level"].' 1 </h2>'; 
			}
			if ( $field['type'] == "level1wrapbottom" ) {
				echo '<div class="clear"></div></div>'; 
			}
			
			echo '<li class="', $field['class'], '">';
	
			if ( $field['type'] == "title" ) {
				echo '<div class="idProjectsFields>';
			} 
			else if ($field['type'] == 'checkbox') {
				echo '<div id="', $field['id'], 'Help" class="idMoreinfofull">', $field['desc'], '</div>';
				echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
				echo ' ';
				echo '<label for="', $field['id'], '" style="font-weight: bold">', $field['name'], '</label> <a href="javascript:toggleDiv(\'', $field['id'], 'Help\');" class="idMoreinfo">[?]</a>';
			}
			else {
				if( $field['show_help'] == true ) {
						echo '<label for="', $field['id'], '" style="font-weight: bold">', $field['name'], '</label> <a href="javascript:toggleDiv(\'', $field['id'], 'Help\');" class="idMoreinfo">[?]</a>
						<div id="', $field['id'], 'Help" class="idMoreinfofull">', $field['desc'], '</div>
						';
				} 

				else {			
					echo '<label for="', $field['id'], '" style="font-weight: bold">', $field['name'], '</label>';
				}			
				echo '<div>';
			}		
			switch ( $field['type'] ) {
				case 'text':
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" />',
						' ';
					break;
				case 'text_small':
					echo '<input class="cmb_text_small" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" /> ';
					break;
				case 'text_medium':
					echo '<input class="cmb_text_medium" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" />';
					break;
				case 'text_date':
					echo '<input class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" /> ';
					break;
				case 'text_money':
					echo '<input class="cmb_text_money" type="text" name="', $field['id'], '" id="', $field['id'], '" value="'. (!empty($meta) ? number_format($meta, 2, '.', ',') : $field['std']). '" /> ';
					break;
				case 'textarea':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="10" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
						'';
					break;
				case 'textarea_code':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="10" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
						' ';
					break;					
				case 'textarea_small':
					echo '<textarea name="', $field['id'], '" class="', $field['class'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
						' ';
					break;
				case 'textarea_medium':
					echo '<textarea name="', $field['id'], '" class="', $field['class'], '" id="', $field['id'], '" cols="60" rows="7" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
						' ';
					break;					
				case 'select':
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					foreach ($field['options'] as $option) {
						echo '<option value="', $option['value'], '"', $meta == $option['value'] ? ' selected="selected"' : '', '>', $option['name'], '</option>';
					}
					echo '</select>';
					echo ' ';
					break;
				case 'radio_inline':
					echo '<div class="cmb_radio_inline">';
					foreach ($field['options'] as $option) {
						echo '<div class="cmb_radio_inline_option"><input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'], '</div>';
					}
					echo '</div>';
					echo ' ';
					break;
				case 'radio':
					foreach ($field['options'] as $option) {
						echo '<p><input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'].'</p>';
					}
					echo ' ';
					break;
				/*case 'checkbox':
					echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
					echo ' ';
					break;*/
				case 'multicheck':
					echo '<ul>';
					foreach ( $field['options'] as $value => $name ) {
						// Append `[]` to the name to get multiple values
						// Use in_array() to check whether the current option should be checked
						echo '<li><input type="checkbox" name="', $field['id'], '[]" id="', $field['id'], '" value="', $value, '"', in_array( $value, $meta ) ? ' checked="checked"' : '', ' /><label>', $name, '</label></li>';
					}
					echo '</ul>';
					echo ' ';					
					break;		
				case 'title':
					echo '<h5 class="cmb_metabox_title">', $field['name'], '</h5>';
					echo ' ';
					break;
				case 'wysiwyg':
					echo '<div id="poststuff" class="meta_mce">';
					echo '<div class="customEditor"><textarea class="mce-html" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="7" style="width:97%">', $meta ? $meta : '', '</textarea></div>';
                    echo '</div>';
			        echo ' ';
				break;
/*
				case 'wysiwyg':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" class="theEditor" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>';
					echo ' ';	
					break;
*/
				case 'file_list':
					echo '<input id="upload_file" type="text" size="36" name="', $field['id'], '" value="" />';
					echo '<input class="upload_button button" type="button" value="Upload File" />';
					echo ' ';
						$args = array(
								'post_type' => 'attachment',
								'numberposts' => null,
								'post_status' => null,
								'post_parent' => $post->ID
							);
							$attachments = get_posts($args);
							if ($attachments) {
								echo '<ul class="attach_list">';
								foreach ($attachments as $attachment) {
									echo '<li>'.wp_get_attachment_link($attachment->ID, 'thumbnail', 0, 0, 'Download');
									echo '<span>';
									echo apply_filters('the_title', '&nbsp;'.$attachment->post_title);
									echo '</span></li>';
								}
								echo '</ul>';
							}
						break;
				case 'file':
					echo '<div class="ign_file_upload"><input id="upload_file" type="text" size="45" class="', $field['id'], '" name="', $field['id'], '" value="', $meta, '" />';
					echo '<input class="upload_button button" type="button" value="Upload File" />';
					echo '</div>';
					echo '<div class="file_actions ign_file_upload_image">';	
						if ( $meta != '' ) { 
							$check_image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $meta );
							if ( $check_image ) {
								echo '<div class="img_status submitbox" id="delete-action">';
								echo '<div class="ign_image_crop"><a href="', $meta, '" target="_blank"><img class="ign_image_tiny" src="', $meta, '"></a></div>';
								echo '<a href="#" class="submitdelete deletion remove_file_button" rel="', $field['id'], '">Remove Image</a></div>';
								echo '<div class="clear"></div>';
							} else {
								$parts = explode( "/", $meta );
								for( $i = 0; $i < sizeof( $parts ); ++$i ) {
									$title = $parts[$i];
								} 
								echo 'File: <strong>', $title, '</strong>&nbsp;&nbsp;&nbsp; (<a href="', $meta, '" target="_blank" rel="external">Download</a> / <div class="submitbox" id="delete-action"><a href="?post_id='.$_GET["post"].'&meta_key='.$field['id'].'" class="submitdelete deletion" rel="', $field['id'], '">Remove</a></div>)';
							}	
						}
					echo '</div>'; 
				break;
				case 'product_levels':
					$meta_no_levels = get_post_meta( $post->ID, $name="ign_product_level_count", true );
					$levels_html = '';
					//echo $meta_no_levels;

					if ($meta_no_levels > 0 || $meta_no_levels != "") {
						$levels_html .= '<div levels="'.$meta_no_levels.'"><input id="levels" name="level-count" type="hidden" value="'.$meta_no_levels.'"/>';

						for ($i=2 ; $i <= $meta_no_levels ; $i++) {
							$meta_title = stripslashes(get_post_meta( $post->ID, $name="ign_product_level_".($i)."_title", true ));
							$meta_limit = get_post_meta( $post->ID, $name="ign_product_level_".($i)."_limit", true );
							$meta_order = get_post_meta( $post->ID, $name="ign_product_level_".($i)."_order", true );
							if (empty($meta_order)) {
								$meta_order = 0;
							}
							$meta_price = get_post_meta( $post->ID, $name="ign_product_level_".($i)."_price", true );
							$meta_short_desc = stripslashes(get_post_meta( $post->ID, $name="ign_product_level_".($i)."_short_desc", true ));
							$meta_desc = stripslashes(get_post_meta( $post->ID, $name="ign_product_level_".($i)."_desc", true ));
							$levels_html .= '<div'.(($i == 0) ? '' : ' level="'.($i).'"').' class="projectmeta-levelbox">'.
									'<h2>'.__('Level', 'ignitiondeck').' '.($i).' </h2>'.
									'<div class="ign_projectmeta_reward_title"><div><label style="font-weight: bold">'.__('Level Title', 'ignitiondeck').' </label></div><input class="cmb_text" type="text" name="levels['.$i.'][title]" id="ign_level_'.$i.'" cols="60" value="'.$meta_title.'" /></div>'.
									'<div class="ign_projectmeta_reward_left"><div class="ign_projectmeta_reward_price"><label style="font-weight: bold">'.__('Level Price', 'ignitiondeck').' </label><input class="cmb_text_money" type="text" name="levels['.$i.'][price]" id="ign_level_'.$i.'" value="'.$meta_price.'" /></div>'.
									'<div class="ign_projectmeta_reward_limit"><label style="font-weight: bold">'.__('Level Limit', 'ignitiondeck').' </label><input class="cmb_text_small" type="text" name="levels['.$i.'][limit]" id="ign_level_'.$i.'_limit" value="'.$meta_limit.'" /></div>'.
									'<div class="ign_projectmeta_reward_limit"><label style="font-weight: bold">'.__('Level Order', 'ignitiondeck').' </label><input class="cmb_text_small" type="text" name="levels['.$i.'][order]" id="ign_level_'.$i.'_order" value="'.$meta_order.'" /></div></div>' .
									'<div class="ign_projectmeta_reward_desc"><label style="font-weight: bold">'.__('Level Short Description', 'ignitiondeck').' </label><textarea name="levels['.$i.'][short_description]" id="ign_level'.$i.'short_desc" cols="60" rows="4" style="width:97%">'.$meta_short_desc.'</textarea></div>'.
									'<div class="ign_projectmeta_reward_desc"><label style="font-weight: bold">'.__('Level Long Descriptions', 'ignitiondeck').' </label><textarea name="levels['.$i.'][description]" class="tinymce" id="ign_level'.$i.'desc" cols="60" rows="4" style="width:97%">'.$meta_desc.'</textarea></div>'.
								'<div></div>';
								$levels_html .= '</div>';
						}
						
					} else {
						$levels_html .= '<div levels="1"><input id="levels" name="level-count" type="hidden" value="1"/>';
//						echo 	'<div id="delete-action"><span addlevel="1" class="ig-add-level button-primary">'.$GLOBALS[tr_Add_Level].'</span>&nbsp;&nbsp;<span deletelevel="1" class="ig-add-level deletion">'.$GLOBALS[tr_Delete_Last].'</span></div>
//								 <div></div>';
//						echo 	'<div class="projectmeta-levelbox">'.
//									'<h2>'.$GLOBALS[tr_Level].' '.($i).' </h2>'.
//										'<div class="ign_projectmeta_reward_title"><label style="font-weight: bold">'.$GLOBALS[tr_Title_For].' </label><input class="cmb_text" type="text" name="levels['.$i.'][title]" id="ign_level_'.$i.'" cols="60" value="'.$meta_title.'" /></div>'.
//										'<div class="ign_projectmeta_reward_left"><div class="ign_projectmeta_reward_price"><label style="font-weight: bold">'.$GLOBALS[tr_Price_For].' </label><input class="cmb_text_money" type="text" name="levels['.$i.'][price]" id="ign_level_'.$i.'" value="'.$meta_price.'" /></div>'.
//										'<div class="ign_projectmeta_reward_limit"><label style="font-weight: bold">'.$GLOBALS[tr_Limit_For].' </label><input class="cmb_text_small" type="text" name="levels['.$i.'][limit]" id="ign_level_'.$i.'" value="'.$meta_limit.'" /></div></div>'.
//										'<div class="ign_projectmeta_reward_desc"><label style="font-weight: bold">'.$GLOBALS[tr_Description_For].' </label><textarea name="levels['.$i.'][description]" id="ign_level'.$i.'desc" cols="60" rows="4" style="width:97%">'.$meta_desc.'</textarea></div>'.
//									'<div></div>';
						$levels_html .= '</div>';
					}
					echo apply_filters('id_product_levels_html_admin', $levels_html, $meta_no_levels, $post->ID);
				break;
				case 'add_levels':
					echo '	<div class="submitbox"> <span addlevel="1" class="ig-add-level" id="delete-action"><a class="button-primary">'.$GLOBALS["tr_Add_Level"].'</a></span> &nbsp;&nbsp; <span deletelevel="1" class="ig-add-level"> <a class="submitdelete deletion">'.$GLOBALS["tr_Delete_Last"].'</a></span> </div>';
				break;
				
				case 'short_code':
					echo '	<div class="shortcode-container">
								<div class="id-projectpage-short-codes"></div>
							</div>';
				break;
				case 'headline1':
					echo '<h4 class="ign_projectmeta_title">'.$GLOBALS["tr_Headline1"].'</h4>'; 
				break;
				case 'headline2':
					echo '<h4 class="ign_projectmeta_title">'.$GLOBALS["tr_Headline2"].'</h4>'; 
				break;
				case 'headline2':
					echo '<h4 class="ign_projectmeta_title">'.$GLOBALS["tr_Headline2"].'</h4>'; 
				break;
				}
			echo '','</li>';
		}
		echo '</ul><div style="clear: both"></div>';
	}

	// Save data from metabox
	function save($post_id, $post)  {
		global $post;

		// verify nonce
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || !wp_verify_nonce($_POST['wp_meta_box_nonce'], basename(__FILE__))) {
			return $post_id;
		}

		// check autosave
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		if (isset($_POST['wp-preview']) && $_POST['wp-preview'] == 'dopreview') {
			return $post_id;
		}

		// check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		}

		else if ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		//===================================================================
		//	Saving the post meta field in the product table as well
		//===================================================================
			//echo "referrer: ".$_SERVER['HTTP_REFERER']."<br />"; exit;
			global $wpdb;
			if (!empty($_POST['ign_product_price'])) {
				//$product_price = 0.00;
				$product_price = round(str_replace(",", "", esc_attr($_POST['ign_product_price'])), 2);		//replacing commas with empty
			}
			else {
				$product_price = '';
			}
			
			if ($post->post_type == 'ignition_product') {
				if ( stristr($_SERVER['HTTP_REFERER'], "action=edit") !== false) {

					$project_id = get_post_meta($post_id, 'ign_project_id', true);
					//$post_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE ID = '".$post_id."'");
				
					
					$product_goal = round(str_replace(",", "", esc_attr($_POST['ign_fund_goal'])), 2);		//replacing commas with empty
					update_post_meta($post_id, 'ign_project_id', $project_id);
					$sql_update = $wpdb->prepare("UPDATE ".$wpdb->prefix."ign_products SET product_name = %s,
						ign_product_title = %s,
						ign_product_limit = %s,
						product_details = %s,
						product_price = %s,
						product_url = %s,
						goal = %s WHERE id = %d", esc_attr($_POST['post_title']), esc_attr($_POST['ign_product_title']), esc_attr($_POST['ign_product_limit']), esc_attr($_POST['ign_product_details']), $product_price, esc_attr($_POST['id_project_URL']).'/', $product_goal, $project_id);
					$wpdb->query( $sql_update );
					do_action('id_update_project', $post_id, $project_id);
					update_option('id_preview_data', serialize($_POST));
					update_option('id_products_notice', 'off');
				} else {

					/*if ($_POST['ign_product_price'] == "") {
						$product_price = 0;
					}

					else {
						$product_price = round(str_replace(",", "", $_POST['ign_product_price']), 2);		//replacing commas with empty
					}*/
					
					$product_goal = round(str_replace(",", "", $_POST['ign_fund_goal']), 2);		//replacing commas with empty
					$sql_product = $wpdb->prepare("INSERT INTO ".$wpdb->prefix ."ign_products (
									product_image, 
									product_name, 
									product_url, 
									ign_product_title, 
									ign_product_limit, 
									product_details, 
									product_price, 
									goal, 
									created_at) VALUES (
										'product image',
										%s,
										%s,
										%s,
										%s,
										%s,
										%s,
										%s,
										'".date('Y-m-d H:i:s')."'
									)", esc_attr($_POST['post_title']),
									esc_attr($_POST['id_project_URL']).'/' ,
									esc_attr($_POST['ign_product_title']) , 
									esc_attr($_POST['ign_product_limit']), 
									esc_attr($_POST['ign_product_details']),
									$product_price,
									$product_goal);

					$res = $wpdb->query( $sql_product );
					$product_id = $wpdb->insert_id;
					update_post_meta($post_id, 'ign_project_id', $product_id);
					do_action('id_create_project', $post_id, $product_id);
					update_option('id_products_notice', 'off');
				}
			}
			
		//===================================================================

		foreach ( $this->_meta_box['fields'] as $field ) {
			if ( !isset($field['desc']) ) $field['desc'] = '';
			if ( !isset($field['std']) ) $field['std'] = '';
			if ( !isset($field['id']) ) $field['id'] = '';
			if ( !isset($field['name']) ) $field['name'] = '';
			if ( !isset($field['show_help']) ) $field['show_help'] = false;

			$name = $field['id'];
			$old = get_post_meta( $post_id, $name, 'multicheck' != $field['type'] /* If multicheck this can be multiple values */ );
			$new = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : null;

			if ( $field['type'] == 'wysiwyg' || $field['type'] == 'textarea_medium') {
				$new = wpautop($new);
			}

			if ( ($field['type'] == 'textarea') || ($field['type'] == 'textarea_small') ) {
				$new = htmlspecialchars($new);
			}
			
			if ( ($field['id'] == "ign_product_price") || ($field['id'] == "ign_fund_goal") ) {
				$new = str_replace(",", "", $new);
			}

			if ($field['id'] == "ign_product_name") {
				$new = htmlspecialchars($new);
			}

			// validate meta value
			if ( isset($field['validate_func']) ) {
				$ok = call_user_func(array('ign_cmb_Meta_Box_Validate', $field['validate_func']), $new);
				if ( $ok === false ) { // pass away when meta value is invalid
					continue;
				}
			} elseif ( 'multicheck' == $field['type'] ) {
				// Do the saving in two steps: first get everything we don't have yet
				// Then get everything we should not have anymore
				if ( empty( $new ) ) {
					$new = array();
				}
				$aNewToAdd = array_diff( $new, $old );
				$aOldToDelete = array_diff( $old, $new );
				foreach ( $aNewToAdd as $newToAdd ) {
					add_post_meta( $post_id, $name, $newToAdd, false );
				}
				foreach ( $aOldToDelete as $oldToDelete ) {
					delete_post_meta( $post_id, $name, $oldToDelete );
				}
			} elseif ($new && $new != $old) {
				update_post_meta($post_id, $name, $new);
			} elseif ('' == $new && $old && $field['type'] != 'file') {
				delete_post_meta($post_id, $name, $old);
			}
		}
		
		//===================================================================
		//	Saving the product levels
		//===================================================================
			//print_r($_POST[levels]); exit;
			
			/*if ( stristr($_SERVER['HTTP_REFERER'], "action=edit") !== false ) {
				$sql_delete_levels = "DELETE FROM ".$wpdb->prefix ."postmeta WHERE post_id = '".$post_id."' AND meta_key LIKE 'ign_product_level%'";
				$wpdb->query($sql_delete_levels);
			}*/
			if (isset($_POST['level-count'])) {
				update_post_meta($post_id, "ign_product_level_count", $_POST['level-count']);
			}
			$j = 2;
			//find a better way to declare this without using +1
			if (isset($_POST['levels'])) {
				$custom_order = false;
				$level_order = absint($_POST['ign_projectmeta_level_order']);
				if ($level_order > 0) {
					$custom_order = true;
				}
				if ($_POST['levels'] > 1 ) {
					foreach ( $_POST['levels'] as $level ) {
						update_post_meta($post_id, $meta_key="ign_product_level_".$j."_title", esc_attr($meta_value=$level['title']));
						update_post_meta($post_id, $meta_key="ign_product_level_".$j."_limit", $meta_value=$level['limit']);
						update_post_meta($post_id, $meta_key="ign_product_level_".$j."_order", $meta_value=$level['order']);
						update_post_meta($post_id, $meta_key="ign_product_level_".$j."_price", $meta_value=str_replace(",", "", $level['price']));
						update_post_meta($post_id, $meta_key="ign_product_level_".$j."_short_desc", esc_html($meta_value=$level['short_description']));
						update_post_meta($post_id, $meta_key="ign_product_level_".$j."_desc", esc_html($meta_value=$level['description']));
						if ($level['order'] > 0) {
							$custom_order = true;
						}
						$j++;
					}
				}
				update_post_meta($post_id, 'custom_level_order', $custom_order);
			}
			
		//===================================================================
	}
}


/**
 * Adding scripts and styles
 */

function ign_admin_scripts( $hook ) {
	global $post;
	if ($post) {
		if ( $post->post_type == 'ignition_product' ) {
			wp_register_script( 'cmb-scripts', IDCF_CMB_META_BOX_URL.'jquery.cmbScripts.js', array('jquery','media-upload','thickbox'));
			wp_register_script( 'datepicker', plugins_url('/ign_metabox/datepicker.js', dirname(__FILE__)));
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' ); // Make sure and use elements form the 1.7.3 UI - not 1.8.9
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'cmb-scripts' );
			wp_enqueue_script( 'datepicker' );
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'jquery-custom-ui' );
			//wp_register_script('product.levels');
	  		//wp_enqueue_script('product.levels');
			add_action( 'admin_head', 'id_cmb_styles_inline' );
  		}
	}
}

add_action( 'admin_enqueue_scripts', 'ign_admin_scripts',10,1 );

function ign_editor_admin_init() {
  global $post_type;
  if ($post_type == 'ignition_product') {
	  wp_enqueue_script('word-count');
	  wp_enqueue_script('post');
	  wp_enqueue_script('editor');
	}
}

function ign_editor_admin_head() {
	global $post_type;
	if ($post_type == 'ignition_product') {
  		//wp_tiny_mce();
  	}
}


add_action('admin_init', 'ign_editor_admin_init');
add_action('admin_head', 'ign_editor_admin_head');


function ign_cmb_editor_footer_scripts() { 
	global $post;
	if (isset($post->post_type) && $post->post_type == 'ignition_product') {
	?>
		<script type="text/javascript">/* <![CDATA[ */
		
		jQuery(document).ready(function(e) {
			jQuery('span[addlevel]').click(function () {
				evalTinyMCE();
				var element_number = parseInt(jQuery('div[levels]').attr('levels')) + 1;
				var pre_element_number = element_number - 1;
				jQuery('div[levels]').attr('levels', element_number);
				jQuery('#levels').val(element_number);
				jQuery('div[levels]').append('<div level="'+element_number+'" class="projectmeta-levelbox">' +
										'<h2><?php _e('Level', 'ignitiondeck'); ?> '+(element_number)+' </h2><div class="ign_projectmeta_reward_title"><label><?php _e('Level Title', 'ignitiondeck'); ?> </label><div><input class="cmb_text" type="text" name="levels['+element_number+'][title]" id="ign_level'+element_number+'title" value="" /></div></div>'+
										'<div class="ign_projectmeta_reward_left"><div class="ign_projectmeta_reward_price"><label class="cmb_metabox_description"><?php _e('Level Price', 'ignitiondeck'); ?> </label><input class="cmb_text_money" type="text" name="levels['+element_number+'][price]" id="ign_level'+element_number+'" value="" /></div>' +
										'<div class="ign_projectmeta_reward_limit"><label class="cmb_metabox_description"><?php _e('Level Limit', 'ignitiondeck'); ?> </label><input class="cmb_text_small" type="text" name="levels['+element_number+'][limit]" id="ign_level'+element_number+'limit" value="" /></div>' +
										'<div class="ign_projectmeta_reward_limit"><label class="cmb_metabox_description"><?php _e('Level Order', 'ignitiondeck'); ?> </label><input class="cmb_text_small" type="text" name="levels['+element_number+'][order]" id="ign_level'+element_number+'order" value="0" /></div></div>' +
										'<div class="ign_projectmeta_reward_desc"><label style="font-weight: bold"><?php __('Level', 'ignitiondeck'); ?> <?php _e('Level Short Description', 'ignitiondeck'); ?> </label><textarea name="levels['+element_number+'][short_description]" id="ign_level'+element_number+'short_desc" cols="60" rows="4" style="width:97%"></textarea></div>' +
									'<div class="ign_projectmeta_reward_desc"><label style="font-weight: bold"><?php _e('Level Long Description', 'ignitiondeck'); ?> </label><textarea name="levels['+element_number+'][description]" class="tinymce" id="ign_level'+element_number+'desc" cols="60" rows="4" style="width:97%"></textarea></div>' +
									'<div class="clear"></div></div>');
				jQuery('.cmb_text_money').change(function () {
				var num = jQuery(this).val();
					var price = cmb_format_price(num);
					//console.log(price);
					jQuery(this).val(price);
				});
				
				if (jQuery("#wp-content-wrap").hasClass('tmce-active')) {
					tinyMCE.execCommand('mceAddEditor', false, 'ign_level'+element_number+'desc');
				}
			});
		});
		
		jQuery('span[deletelevel]').click(function () {
			var element_number = parseInt(jQuery('div[levels]').attr('levels'));
			var new_number = element_number - 1;
			jQuery('div[level="'+element_number+'"]').remove();

			if (element_number == 1) {
				jQuery('#ign_level_0').val('');
				jQuery('#ign_level0desc').html('');
			} else {
				jQuery('div[levels]').attr('levels', --element_number);
				jQuery('#levels').val(new_number);
			}
		});
		
		/*jQuery('.id-projectpage-short-codes').html('<div class="shortcode-content"><strong>For Full Width Project Template:</strong>&nbsp;[project_page_content product="<span product></span>"],&nbsp;<strong>For Combination Project Template &amp; Project Widget:</strong>&nbsp;[project_page_complete product="<span product></span>"],&nbsp;<strong>To Use Project Template &amp; Widget Separately:</strong>&nbsp;[project_page_content_left product="<span product></span>"]</div>' +
													'<div class="shortcode-widget">[project_page_widget product="<span product></span>"]&nbsp;</div>');*/
													
		jQuery.ajax({
			type: "POST",
			url: '<?php echo site_url(); ?>/wp-admin/admin-ajax.php',
			data: "action=" + 'get_new_product'
			+ "&action_type=<?php echo ((isset($_GET['action']) && $_GET['action'] == "edit") ? absint(esc_attr($_GET['post'])) : ''); ?>"
			,
			success: function(html) {	
			//console.log(html);				
				//alert(jQuery.trim(html));
				//jQuery('.id-projectpage-short-codes .shortcode-content span[product]').html(jQuery.trim(html));
				//jQuery('.id-projectpage-short-codes .shortcode-widget span[product]').html(jQuery.trim(html));
				jQuery('.id-metabox-short-codes .shortcode-content span[data-product]').html(jQuery.trim(html));
			}
		});
		
		// Ajax call for deleting an image, it's a listener to a link which calls ajax afterwards
		jQuery('.ign_file_upload_image a.submitdelete').click(function (e) {
			e.preventDefault();
			var thisDiv = this;
			var div_status_id = jQuery(this).attr('rel') + "_status";	//ign_product_image1_status
			var image_field_id = jQuery(this).attr('rel');		//ign_product_image1
			jQuery.ajax({
				type: "POST",
				url: id_ajaxurl,
				data: "action=" + 'remove_product_image'
				+ "&image=" + jQuery(this).attr('rel')
				+ "&post_id=" + "<?php echo $post->ID; ?>"
				,
				success: function(res) {	
					//console.log(res);					
					//alert(jQuery.trim(html));
					//alert("image_field_id: "+image_field_id);
					jQuery(thisDiv).closest('.ign_file_upload_image').remove();
					jQuery('#'+div_status_id).remove();		//emptying the div in which Remove link and image is contained
					jQuery('input[name="'+image_field_id + '"]').val(null);
				}
			});
		});
		
		jQuery('.cmb_text_money').change(function () {
			var num = jQuery(this).val();
			var price = cmb_format_price(num);
			//console.log(price);
			jQuery(this).val(price);
		});
		function cmb_format_price(num) {
			console.log(num);
			//console.log(num);
			if (num !== '') {
				num = num.toString().replace(/\$|\,/g,'');
				if(isNaN(num))
					num = "0";
				sign = (num == (num = Math.abs(num)));
				num = Math.floor(num*100+0.50000000001);
				cents = num%100;
				num = Math.floor(num/100).toString();
				if(cents<10)
					cents = "0" + cents;
				for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
					num = num.substring(0,num.length-(4*i+3))+','+
				
				num.substring(num.length-(4*i+3));
				return (((sign)?'':'-') + num + '.' + cents);
				//return (((sign)?'':'-') + '$' + num + '.' + cents);
			}
		}
		
		jQuery('#ign_fund_goal').change(function () {
			var num = jQuery(this).val();
			num = num.toString().replace(/\$|\,/g,'');
			if(isNaN(num))
				num = "0";
			sign = (num == (num = Math.abs(num)));
			num = Math.floor(num*100+0.50000000001);
			cents = num%100;
			num = Math.floor(num/100).toString();
			if(cents<10)
				cents = "0" + cents;
			for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
				num = num.substring(0,num.length-(4*i+3))+','+
			
			num.substring(num.length-(4*i+3));
			jQuery(this).val((((sign)?'':'-') + num + '.' + cents));

		});
		
	/* ]]> */</script>
	<?php 
	}
}

add_action('admin_print_footer_scripts','ign_cmb_editor_footer_scripts',99);
function id_cmb_styles_inline() { 
	echo '<link rel="stylesheet" type="text/css" href="' . IDCF_CMB_META_BOX_URL.'style.css" />';
	// For some reason this script doesn't like to register
	?>	
	<style type="text/css">
		table.cmb_metabox td, table.cmb_metabox th { border-bottom: 1px solid #f5f5f5; /* Optional borders between fields */ }
		table.cmb_metabox th { text-align: right; font-weight:bold;}
		table.cmb_metabox th label { margin-top:6px; display:block;}
		p.cmb_metabox_description { color: #AAA; font-style: italic; margin: 2px 0 !important;}
		span.cmb_metabox_description { color: #AAA; font-style: italic;}
		input.cmb_text_small { width: 100px; margin-right: 15px;}
		input.cmb_text_money { width: 90px; margin-right: 15px;}
		input.cmb_text_medium { width: 230px; margin-right: 15px;}
		table.cmb_metabox input, table.cmb_metabox textarea { font-size:11px; padding: 5px;}
		table.cmb_metabox li { font-size:11px; }
		table.cmb_metabox ul { padding-top:5px; }
		table.cmb_metabox select { font-size:11px; padding: 5px 10px;}
		table.cmb_metabox input:focus, table.cmb_metabox textarea:focus { background: #fffff8;}
		.cmb_metabox_title { margin: 0 0 5px 0; padding: 5px 0 0 0; font: italic 24px/35px Georgia,"Times New Roman","Bitstream Charter",Times,serif;}
		.cmb_radio_inline { padding: 4px 0 0 0;}
		.cmb_radio_inline_option {display: inline; padding-right: 18px;}
		table.cmb_metabox input[type="radio"] { margin-right:3px;}
		table.cmb_metabox input[type="checkbox"] { margin-right:6px;}
		table.cmb_metabox .mceLayout {border:1px solid #DFDFDF !important;}
		table.cmb_metabox .meta_mce {width:97%;}
		table.cmb_metabox .meta_mce textarea {width:100%;}
		table.cmb_metabox .cmb_upload_status {  margin: 10px 0 0 0;}
		table.cmb_metabox .cmb_upload_status .img_status {  position: relative; }
		table.cmb_metabox .cmb_upload_status .img_status img { border:1px solid #DFDFDF; background: #FAFAFA; max-width:350px; padding: 5px; -moz-border-radius: 2px; border-radius: 2px;}
		table.cmb_metabox .cmb_upload_status .img_status .remove_file_button {
		background: url("<?php echo plugins_url('/ign_metabox/images/ico-delete.png', __FILE__); ?>") no-repeat scroll 0 0 transparent;
		display: block;
		height: 20px;
		margin-top: 8px;
		padding-left: 20px;
		position: relative;
		}
	</style>
	<?php
}

// End. That's it, folks! //
?>