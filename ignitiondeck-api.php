<?php
function api_get_all_projects() {
	$projects = ID_Project::get_all_projects();
	print_r(json_encode($projects));
	exit;
}
add_action('wp_ajax_get_all_projects', 'api_get_all_projects');
add_action('wp_ajax_nopriv_get_all_projects', 'api_get_all_projects');

function api_get_project() {
	if (isset($_GET['project_id'])) {
		$project_id = absint($_GET['project_id']);
		$project = new ID_Project($project_id);
		$the_project = $project->the_project();
	}
	else {
		$the_project = null;
	}
	print_r(json_encode($the_project));
	exit;
}
add_action('wp_ajax_get_project', 'api_get_project');
add_action('wp_ajax_nopriv_get_project', 'api_get_project');
?>