<?php
add_filter('idc_protect_terms', 'add_project_category_protection');

function add_project_category_protection($terms) {
	$terms[] = 'project_category';
	return $terms;
}

function send_preauth_to_idc() {
	if (isset($_POST['POST_ID'])) {
		$post_id = absint($_POST['POST_ID']);
		if ($post_id > 0) {
			$project_id = get_post_meta($post_id, 'ign_project_id', true);
			$assignments = get_assignments_by_project($project_id);
			$products = array();
			if (!empty($assignments)) {
				foreach ($assignments as $assignment) {
					$product_id = $assignment->level_id;
					if ($product_id > 0) {
						$products[] = $product_id;
					}
				}
				print_r(json_encode($products));
			}
		}
	}
	exit;
}

add_action('wp_ajax_send_preauth_to_idc', 'send_preauth_to_idc');
add_action('wp_ajax_nopriv_send_preauth_to_idc', 'send_preauth_to_idc');
?>