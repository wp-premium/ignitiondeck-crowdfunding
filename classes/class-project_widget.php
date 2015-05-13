<?php

/**
 * Product Widget
 */
class Product_Widget extends WP_Widget {

	protected $widget = array(
	    
		 
		'name' => 'IgnitionDeck Project Widget',
		'description' => 'Widget to show projects',
           
		// determines whether or not to use the sidebar _before and _after html
		'do_wrapper' => true, 

		'view' => false,
		
        	
		'fields' => array(
			array(
				'name' => 'Product',
				'desc' => '',
				'id' => 'product_id',
				'type' => 'select',
				'options' => array()
			),
			array(
			    
				'name' => 'Widget Options',
				'desc' => '',
				'id' => 'widget_options',
				'type' => 'radio',
				'options' => array(
					array('name' => 'Mini Deck', 'value' => 'miniwidget'),//translate
					array('name' => 'Full Deck', 'value' => 'fullwidget'),//translate
					array('name' => 'Mini Deck on all Pages except Project Page', 'value' => 'miniallpages')//translate
				)
			),
			array(
				'name' => 'Custom Deck ID',
				'desc' => '',
				'id' => 'deck_id',
				'type' => 'text'
				)
		)
		
	);

	function html($widget, $params, $sidebar)
	{
		global $post, $wpdb;
		$show_mini = false;
		$page_project_id = 0;
		$custom = false;
		// Condition to store the product_id, if the page currently being visited is the project page or some other
		if (isset($post)) {
			if ($post->post_type == 'ignition_product') {
				$post_id = $post->ID;
				$page_project_id = get_post_meta($post_id, 'ign_project_id', true);
				//$page_project_id = projectPageof($post->ID, $params['product_id']);
			}
		
			// We have three conditions now
			// 1.	Show Mini widget only, on all pages
			// 2.	Show Full widget on all pages
			// 3.	Show Mini widget on all pages except the project page
			if (isset($params['widget_options'])) {
				if ($params['widget_options'] == "miniwidget") {
					$show_mini = true;
				}
				else if ($params['widget_options'] == "fullwidget") {
					$show_mini = false;
				}
				else if ($params['widget_options'] == "miniallpages" && $page_project_id > 0) {	//2nd condition means it is a project page, containing some non-zero project_id
					$show_mini = false;
				}
				else {
					$show_mini = true;
				}
			}
			else {
				$show_mini = true;
			}
			if (isset($params['deck_id']) && $params['deck_id'] > 0) {
				$deck_id = $params['deck_id'];
				$settings = Deck::get_deck_attrs($deck_id);
				if (!empty($settings)) {
					$attrs = unserialize($settings->attributes);
					$custom = true;
				}
			}
		}
		// If it's a project page, use the post_id to get the product, else use the product_id in $params
		if ($page_project_id > 0) {
			$project_id = $page_project_id;
		} else {
			$project_id = $params['product_id'];
		}

		$deck = new Deck($project_id);
		if ($show_mini == true) {
			$mini_deck = $deck->mini_deck();
			$post_id = $mini_deck->post_id;
		}
		else {
			$the_deck = $deck->the_deck();
			$post_id = $deck->post_id;
		}
		$custom = apply_filters('idcf_custom_deck', $custom, $post_id);
		$attrs = apply_filters('idcf_deck_attrs', (isset($attrs) ? $attrs : null), $post_id);
		$widget_before = '';
		$widget_after = '';

		$mini_widget_before = '';
		$mini_widget_after = '';

		// Calling the HTML code
		ob_start();
		require ID_PATH.'languages/text_variables.php';
		include ID_PATH.'templates/_igWidget.php';
		$widget = ob_get_contents();
		ob_end_clean();
		if ($show_mini) {
			echo apply_filters('id_mini_widget', $widget);
		}
		else {
			echo apply_filters('id_widget', $widget);
		}
	}

	function Product_Widget()
	{
		//Initializing
		$classname = str_replace('_',' ', get_class($this));
		$widget_id = get_class($this);

		// widget actual processes
		parent::WP_Widget( 
			$id = $widget_id, 
			$name = (isset($this->widget['name'])?$this->widget['name']:$classname), 
			$options = array( 'description'=>$this->widget['description'] )
		);
	}

	function widget($sidebar, $params)
	{
		//initializing variables
		$this->widget['number'] = $this->number;
		$title = apply_filters( 'Product_Widget_title', '' );
		$do_wrapper = (!isset($this->widget['do_wrapper']) || $this->widget['do_wrapper']);

		if ( $do_wrapper ) 
			echo $sidebar['before_widget'];

		//loading a file that is isolated from other variables
		if (file_exists($this->widget['view']))
			$this->getViewFile($widget, $params, $sidebar);

		if ($this->widget['view'])
			echo $this->widget['view'];

		else $this->html($this->widget, $params, $sidebar);

		if ( $do_wrapper ) 
			echo $sidebar['after_widget'];
	}

	function getViewFile($widget, $params, $sidebar) {
		require $this->widget['view'];
	}

	function form($instance)
	{
		//reasons to fail
		if (empty($this->widget['fields'])) return false;

		$defaults = array(
			'id' => '',
			'name' => '',
			'desc' => '',
			'type' => '',
			'options' => '',
			'std' => '',
		);

		foreach ($this->widget['fields'] as $field)
		{
			//making sure we don't throw strict errors
			$field = wp_parse_args($field, $defaults);

			$meta = false;
			if (isset($field['id']) && array_key_exists($field['id'], $instance))
				@$meta = attribute_escape($instance[$field['id']]);

			if ($field['type'] != 'custom' && $field['type'] != 'metabox') 
			{
				echo '<p><label for="',$this->get_field_id($field['id']),'">';
			}
			if (isset($field['name']) && $field['name']) echo $field['name'],':<br />';

			switch ($field['type'])
			{
				case 'text':
					echo '<input type="text" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '" value="', (isset($instance[$field['id']]) ? $instance[$field['id']] : ''), '" class="vibe_text" />', 
					'<br/><span class="description">', @$field['desc'], '</span>';
					break;
				case 'textarea':
					echo '<textarea class="vibe_textarea" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '" cols="60" rows="4" style="width:97%">', $meta ? $meta : @$field['std'], '</textarea>', 
					'<br/><span class="description">', @$field['desc'], '</span>';
					break;
				case 'select':
					echo '<select class="vibe_select" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '">';
					
                    global $wpdb;
        			//$allproducts = ID_Project::get_all_projects();
        			$allproducts = ID_Project::get_project_posts();
        			foreach($allproducts as $prod)
					{
						$project_id = get_post_meta($prod->ID, 'ign_project_id', true);
						//$project = new ID_Project($prod->id);
						//$post_id = $project->get_project_postid();
						$selected_option = isset($value) ? $value : get_the_title($prod->ID);
						//echo "selected_option: ".$selected_option."<br />";
						//echo "meta: ".$meta."<br />";
					    echo '<option', (isset($project_id) ? ' value="' . $project_id . '"' : ''), ($meta == $project_id ? ' selected="selected"' : ''), '>', get_the_title($prod->ID), '</option>';
					}

					echo '</select>', 
					'<br/><span class="description">', @$field['desc'], '</span>';
					break;
				case 'radio':
					foreach ($field['options'] as $option)
					{
						
						echo '<input class="vibe_radio" type="radio" name="', $this->get_field_name($field['id']), '" value="', $option['value'], '"', ($meta == $option['value'] ? ' checked="checked"' : ''), ' />', 
						$option['name'].'<br />';
					}
					echo '<br/><span class="description">', @$field['desc'], '</span>';
					break;
				case 'checkbox':
					echo '<input type="hidden" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '" /> ', 
						 '<input class="vibe_checkbox" type="checkbox" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '"', $meta ? ' checked="checked"' : '', ' /> ', 
					'<br/><span class="description">', @$field['desc'], '</span>';
					break;
				case 'custom':
					echo $field['std'];
					break;
			}

			if ($field['type'] != 'custom' && $field['type'] != 'metabox') 
			{
				echo '</label></p>';
			}
		}
		return true;
	}

	function update($new_instance, $old_instance)
	{
		// processes widget options to be saved
		$instance = wp_parse_args($new_instance, $old_instance);
		return $instance;
	}

}
?>