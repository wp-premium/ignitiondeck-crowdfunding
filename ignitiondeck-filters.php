<?php
/**
Project Filters
*/

/**
 * Automatically adds complete project shortcode to the content when enabled via IDCF Project Settings
 * @param string $content passing through the filter
 */
function idcf_auto_insert($content) {
	global $post;
	global $theme_base;
	if ($post->post_type == 'ignition_product' && !in_array($theme_base, array('fivehundred', 'fundify', 'crowdpress'))) {
		$auto_insert = get_option('idcf_auto_insert');
		if ($auto_insert) {
			if (!is_id_pro()) {
				$post_id = $post->ID;
				$project_id = get_post_meta($post_id, 'ign_project_id', true);
				$content = do_shortcode('[project_page_complete product="'.$project_id.'"]');
			}
		}
	}
	return $content;
}

add_filter('the_content', 'idcf_auto_insert');

/**
 * The filter to format the currency display anywhere for project
 * @param integer $amount The amount to be formatted
 * @param integer $post_id The post id of the project
 */
function id_funds_raised($amount, $post_id, $noformat = false) {
	if ($noformat) {
		return $amount;
	}
	else {
		return apply_filters('id_price_format', $amount, $post_id);
	}
}
add_filter('id_funds_raised', 'id_funds_raised', 10, 3);

/**
 * Filter for Percentage pledged for a project
 * @param double  $percentage The percentage value of the project goal
 * @param double  $pledged 		  Pledged of project
 * @param integer $post_id 		  Post ID of the project
 * @param double  $goal 		  Total Goal for the project
 */
function id_percentage_raised($percentage, $pledged, $post_id, $goal) {
	return apply_filters('id_percentage_format', $percentage);
}
add_filter('id_percentage_raised', 'id_percentage_raised', 10, 4);

/**
 * The filter to format the currency display anywhere for project
 * @param integer $goal The amount to be formatted
 * @param integer $post_id The post id of the project
 */
function id_project_goal($goal, $post_id, $noformat = false) {
	if ($noformat) {
		return $goal;
	}
	else {
		return apply_filters('id_price_format', $goal, $post_id);
	}
}
add_filter('id_project_goal', 'id_project_goal', 10, 3);

/**
 * The filter to format the currency display anywhere for project
 * @param integer $pledges The amount to be formatted
 * @param integer $post_id The post id of the project
 */
function id_number_pledges($pledges, $post_id) {
	return apply_filters('id_number_format', $pledges);
}
add_filter('id_number_pledges', 'id_number_pledges', 10, 2);

/**
 * The filter to format the currency display anywhere for project
 * @param integer $amount The amount to be formatted
 * @param integer $post_id The post id of the project
 */
function id_price_selection($amount, $post_id) {
	return apply_filters('id_price_format', $amount, $post_id);
}
add_filter('id_price_selection', 'id_price_selection', 10, 2);

/**
General Filters
*/

/**
 * The filter to format the currency display anywhere for project
 * @param integer $amount  The amount to be formatted
 * @param integer $post_id The post id of the project
 */
function id_price_format($amount, $post_id) {
	// Getting the currency of the project, first getting project id if currency code is not coming in the arguments
	$project_id = get_post_meta($post_id, 'ign_project_id', true);
	// Now getting currency
	$project = new ID_Project($project_id);
	$currency_code = apply_filters('id_display_currency', $project->currency_code());

	// Formatting the amount with currency code
	if ($amount > 0) {
		$amount = number_format($amount, 2, '.', ',');
	}
	//$amount = apply_filters('id_price_total_selection', $amount, $post_id);
	return $currency_code.$amount;
}
add_filter('id_price_format', 'id_price_format', 10, 2);

function id_number_format($number) {
	if ($number > 0) {
		$number = number_format($number);
	}
	return $number;
}
add_filter('id_number_format', 'id_number_format');

/**
 * Filter for Percentage pledged for a project
 * @param double  $percentage The percentage value of the project goal
 */
function id_percentage_format($percentage) {
	return ($percentage > 0 ? number_format($percentage, 2) : '0');
}
add_filter('id_percentage_format', 'id_percentage_format');

/**
Parent/Child Filters
*/

/**
 * The filter to format the currency display anywhere for project
 * @param integer $amount  The amount of the project
 * @param integer $post_id The post id of the project
 */
function id_funds_raised_parent($amount, $post_id) {
	$project_children = get_post_meta($post_id, 'ign_project_children', true);
	if (!empty($project_children)) {
		foreach ($project_children as $child_project) {
			$child_project_id = get_post_meta($child_project, 'ign_project_id', true);
			$project = new ID_Project($child_project_id);
			$raised = $project->get_project_raised();
			$amount = $amount + $raised;
			$sub_children = get_post_meta($child_project, 'ign_project_children', true);
			if (!empty($sub_children)) {
				foreach ($sub_children as $subchild_id) {
					$subchild_project_id = get_post_meta($subchild_id, 'ign_project_id', true);
					$subproject = new ID_Project($subchild_project_id);
					$raised = $subproject->get_project_raised();
					$amount = $amount + $raised;
				}
			}
		}
	}
	return $amount;
}
add_filter('id_funds_raised', 'id_funds_raised_parent', 2, 2);

/**
 * Filter to show the number of pledgers of a project and its children
 */
function id_number_pledges_parent($pledgers, $post_id) {
	// Getting the children projects if any to add the total in $amount
	$project_children = get_post_meta($post_id, 'ign_project_children', true);
	if (!empty($project_children)) {
		foreach ($project_children as $child_project) {
			$child_project_id = get_post_meta($child_project, 'ign_project_id', true);
			$project = new ID_Project($child_project_id);
			$orders = $project->get_project_orders();
			$pledgers = $pledgers + $orders;
			$sub_children = get_post_meta($child_project, 'ign_project_children', true);
			if (!empty($sub_children)) {
				foreach ($sub_children as $subchild_id) {
					$subchild_project_id = get_post_meta($subchild_id, 'ign_project_id', true);
					$subproject = new ID_Project($subchild_project_id);
					$orders = $subproject->get_project_orders();
					$pledgers = $pledgers + $orders;
				}
			}
		}
	}
	return $pledgers;
}
add_filter('id_number_pledges', 'id_number_pledges_parent', 2, 2);

/**
 * Filter for Percentage pledged for a project
 * @param double  $rating_percent The percentage value of the project goal
 * @param double  $pledged 		  Pledged of project
 * @param integer $post_id 		  Post ID of the project
 * @param double  $goal 		  Total Goal for the project
 */
function id_percentage_raised_parent($percentage, $pledged, $post_id, $goal) {
	// Calculating the new percentage with children
	if ($goal > 0) {
		$percentage = (float) $pledged / $goal * 100;
	}
	return $percentage;
}
add_filter('id_percentage_raised', 'id_percentage_raised_parent', 2, 4);
?>