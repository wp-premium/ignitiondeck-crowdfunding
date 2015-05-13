<?php 
	$columns = null;
	// seems to have no effect
	apply_filters('manage-order_columns', $columns);
		$p = new pagination;
		//use query to create ascending and descending variables for column sorting
		//this first section creates default values so that everything sorts asc at first
		$orderby = 'id';
		$sort = 'DESC';
		$name_sort = "asc";
		$project_sort = "asc";
		$level_sort = "asc";
		$pledged_sort = "asc";
		$date_sort = "ASC";
		
		//this next section checks to see if a header has been clicked, checks that header, and sets to desc for next query, or visa versa
		if (isset($_GET['orderby'])) {
			if ($_GET['orderby'] == "name") {
				
				if ($_GET['sort'] == "asc") {
					$name_sort = "desc";
				}

				else {
					$name_sort = "asc";
				}
				
			}
				
			elseif ($_GET['orderby'] == "project") {
				
					if ($_GET['sort'] == "asc") {
						$project_sort = "desc";
					}

					else {
						$project_sort = "asc";
					}
					
			}
					
			elseif ($_GET['orderby'] == "level") {
				
					if ($_GET['sort'] == "asc") {
						$level_sort = "desc";
					}

					else {
						$level_sort = "asc";
					}
					
			}
					
			elseif ($_GET['orderby'] == "pledged") {
				
					if ($_GET['sort'] == "asc") {
						$pledged_sort = "desc";
					}

					else {
						$pledged_sort = "asc";
					}
					
			}

			elseif ($_GET['orderby'] == "date") {

					if ($_GET['sort'] == "asc") {
						$date_sort = "desc";
					}

					else {
						$date_sort = "asc";
					}
					
			}
			else {
				$name_sort = "asc";
				$project_sort = "asc";
				$level_sort = "asc";
				$pledged_sort = "asc";
				$date_sort = "asc";
			}
			
			//next set orderby variables for database selection
			$orderby = "id";
			
			if ($_GET['orderby'] == "name") {
				$orderby = "last_name";
			}
			elseif ($_GET['orderby'] == "project") {
					$orderby = "product_id";
			}
			elseif ($_GET['orderby'] == "level") {
				$orderby = "product_level";
			}
			elseif ($_GET['orderby'] == "pledged") {
				$orderby = "prod_price";
			}
			elseif ($_GET['orderby'] == "date") {
				$orderby = "created_at";
			}
			
			//now, after we have check to see if orderby exists, we check to see if we're re-using current variable or clicking another header.
			if ($name_sort == "desc" && $_GET['orderby'] == "name") {
				$sort = "asc";
			}

			elseif ($name_sort == "asc" && $_GET['orderby'] == "name") {
				$sort = "desc";
			}

			elseif ($project_sort =="desc" && $_GET['orderby'] == "project") {
				$sort = "asc";
			}

			elseif ($project_sort =="asc" && $_GET['orderby'] == "project") {
				$sort = "desc";
			}

			elseif ($level_sort =="desc" && $_GET['orderby'] == "level") {
				$sort = "asc";
			}

			elseif ($level_sort =="asc" && $_GET['orderby'] == "level") {
				$sort = "desc";
			}

			elseif ($pledged_sort =="desc" && $_GET['orderby'] == "pledged") {
				$sort = "asc";
			}

			elseif ($pledged_sort =="asc" && $_GET['orderby'] == "pledged") {
				$sort = "desc";
			}
			elseif ($date_sort =="desc" && $_GET['orderby'] == "date") {
				$sort = "asc";
			}

			elseif ($date_sort =="asc" && $_GET['orderby'] == "date") {
				$sort = "desc";
			}
		}
		if (isset($_GET['project'])) {
			$project_filter = $_GET['project'];
		}
		else {
			$project_filter = null;
		}
		if (isset($_GET['s'])) {
			$s = $_GET['s'];
		}
		else {
			$s = null;
		}
		$query_args = array(
        	'orderby' => $orderby,
        	'sort' => $sort,
        	's' => $s,
        	'project' => $project_filter
        );

        if ($_SERVER['HTTP_HOST'] == "localhost") {
        	$query_string = http_build_query($query_args);
        }
        else {
        	$query_string = http_build_query($query_args);
        }

        $page_count = 20;

        if(isset($_GET['order_page'])) {
            $p->page = $_GET['order_page'];
        } else {
        	$p->page = 1;
        }
         
        //Query for limit paging
        $limit = "LIMIT " . ($p->page - 1) * $page_count  . "," . $page_count;

		$product_array = array();
		foreach ($products as $product) {
			$project = new ID_Project($product->id);
			$post_id = $project->get_project_postid();
			$product_array[$product->id] = get_the_title($post_id);
		}
		//Now run query to select data
		if (isset($_GET['s']) && $_GET['s'] !== "") {
			$search = $_GET['s'];
			if (isset($_GET['project']) && $_GET['project'] !== "0") {
				$project_filter = $_GET['project'];

				$sql = "SELECT * FROM ".$wpdb->prefix."ign_pay_info	WHERE (
				id LIKE  '%".$search."%'
				OR  first_name LIKE  '%".$search."%'
				OR  last_name LIKE  '%".$search."%'
				OR  email LIKE  '%".$search."%'
				OR  address LIKE  '%".$search."%'
				OR  country LIKE  '%".$search."%'
				OR  state LIKE  '%".$search."%'
				OR  city LIKE  '%".$search."%'
				OR  zip LIKE  '%".$search."%'
				OR  transaction_id LIKE  '%".$search."%'
				OR  product_level LIKE  '%".$search."%'
				OR  prod_price LIKE  '%".$search."%'
				OR  status LIKE  '%".$search."%'
				OR  created_at LIKE  '%".$search."%')
				AND  product_id =  '".$project_filter."'
				ORDER BY ".$orderby." ".$sort." ".$limit;

				$sql_count = "SELECT count(*) as count FROM ".$wpdb->prefix."ign_pay_info WHERE (
				id LIKE  '%".$search."%'
				OR  first_name LIKE  '%".$search."%'
				OR  last_name LIKE  '%".$search."%'
				OR  email LIKE  '%".$search."%'
				OR  address LIKE  '%".$search."%'
				OR  country LIKE  '%".$search."%'
				OR  state LIKE  '%".$search."%'
				OR  city LIKE  '%".$search."%'
				OR  zip LIKE  '%".$search."%'
				OR  transaction_id LIKE  '%".$search."%'
				OR  product_level LIKE  '%".$search."%'
				OR  prod_price LIKE  '%".$search."%'
				OR  status LIKE  '%".$search."%'
				OR  created_at LIKE  '%".$search."%')
				AND  product_id =  '".$project_filter."'";
			} 

			else {
			
				$sql = "SELECT * FROM ".$wpdb->prefix."ign_pay_info	WHERE (
				id LIKE  '%".$search."%'
				OR  first_name LIKE  '%".$search."%'
				OR  last_name LIKE  '%".$search."%'
				OR  email LIKE  '%".$search."%'
				OR  address LIKE  '%".$search."%'
				OR  country LIKE  '%".$search."%'
				OR  state LIKE  '%".$search."%'
				OR  city LIKE  '%".$search."%'
				OR  zip LIKE  '%".$search."%'
				OR  product_id LIKE  '%".$search."%'
				OR  transaction_id LIKE  '%".$search."%'
				OR  product_level LIKE  '%".$search."%'
				OR  prod_price LIKE  '%".$search."%'
				OR  status LIKE  '%".$search."%'
				OR  created_at LIKE  '%".$search."%')
				ORDER BY ".$orderby." ".$sort." ".$limit;

				$sql_count = "SELECT count(*) as count FROM ".$wpdb->prefix."ign_pay_info WHERE (
				id LIKE  '%".$search."%'
				OR  first_name LIKE  '%".$search."%'
				OR  last_name LIKE  '%".$search."%'
				OR  email LIKE  '%".$search."%'
				OR  address LIKE  '%".$search."%'
				OR  country LIKE  '%".$search."%'
				OR  state LIKE  '%".$search."%'
				OR  city LIKE  '%".$search."%'
				OR  zip LIKE  '%".$search."%'
				OR  product_id LIKE  '%".$search."%'
				OR  transaction_id LIKE  '%".$search."%'
				OR  product_level LIKE  '%".$search."%'
				OR  prod_price LIKE  '%".$search."%'
				OR  status LIKE  '%".$search."%'
				OR  created_at LIKE  '%".$search."%')";
			}
		}
		else if (isset($_GET['project'])) {
			if ($_GET['project'] !== "0") {
				$project_filter = esc_attr($_GET['project']);
				$sql = "SELECT * FROM ".$wpdb->prefix."ign_pay_info WHERE product_id = ".$project_filter." ORDER BY ".$orderby." ".$sort." ".$limit;
				$sql_count = $wpdb->prepare("SELECT count(*) as count FROM ".$wpdb->prefix."ign_pay_info WHERE product_id = %d", $project_filter);
			}
			else {
				$sql = "SELECT * FROM ".$wpdb->prefix."ign_pay_info ORDER BY ".$orderby." ".$sort." ".$limit;
				$sql_count = "SELECT count(*) as count FROM ".$wpdb->prefix."ign_pay_info";
			}
		}
		else {
			$sql = "SELECT * FROM ".$wpdb->prefix."ign_pay_info ORDER BY ".$orderby." ".$sort." ".$limit;
			$sql_count = "SELECT count(*) as count FROM ".$wpdb->prefix."ign_pay_info";
		}	
		$items = $wpdb->get_results($sql);
		$items_count = $wpdb->get_results($sql_count);

        $p->items($items_count[0]->count);
        $p->limit($page_count); // Limit entries per page
        if (isset($_GET['s'])) {
        	$p->target("admin.php?page=order_details&".$query_string); 
        }
        else {
        	$p->target("admin.php?page=order_details&".$query_string); 
        }
        if (isset($_GET['order_page'])) {
        	$cp = $_GET['order_page'];
        }
        else {
        	$cp = 1;
        }
        $p->parameterName('order_page');
        $p->currentPage($cp); // Gets and validates the current page
        $p->calculate(); // Calculates what to show
        $p->adjacents(1); //No. of page away from the current page
		if ($items_count > 0) {
			$prod_name_id_array = array();

			foreach ($products as $product) {
				$project = new ID_Project($product->id);
				$post_id = $project->get_project_postid();
				$prod_name_id_array[] = array(
					'id' => $product->id,
					'product_name' => get_the_title($post_id));
			}

		}
?>
<div class="wrap">
	<h2><a class="button add-new-h2" href="admin.php?page=add_order"><?php echo $tr_Add_Order; ?></a> <?php echo (isset($message) ? $message : '');?></h2>
	<form id="posts-filter" action="" method="get">
	<p class="search-box">
		<label class="screen-reader-text" for="post-search-input">Search Orders:</label>
		<input type="search" id="post-search-input" name="s" value="<?php echo (isset($_GET['s']) ? $_GET['s'] : ''); ?>"/>
		<input type="hidden" name="page" value="order_details"/>
		<input type="submit" name="" id="search-submit" class="button" value="Search Orders">
	</p>
	<div class="tablenav">
	    <div class='tablenav-pages'>
	        <?php if ($items_count[0]->count < $p->limit) {
	        	echo '<span class="displaying-num">'.$items_count[0]->count.' items</span>';
	        }
	        echo $p->show();  // Echo out the list of paging. 
	        ?>
	    </div>
	    	<div class="alignleft actions">
	    		<select name="project">
	    			<option value="0"><?php _e('Show all projects', 'ignitiondeck'); ?></option>
	    			<?php
	    			foreach ($prod_name_id_array as $prod_combo) {
	    				$project = new ID_Project($prod_combo['id']);
						$post_id = $project->get_project_postid();
						$post = get_post($post_id);
						if (!empty($post)) {
	    					echo '<option value="'.$prod_combo['id'].'" name="prod'.$prod_combo['id'].'" '.(isset($_GET['project']) && $prod_combo['id'] == $_GET['project'] ? 'selected="selected"' : "").'>'.stripslashes(get_the_title($post_id)).'</option>';
	    				}
	    			}
	    			?>
	    		</select>
	    		<input type="submit" id="project-query-submit" class="button-secondary" value="Filter"/>
			</div>
		</form>
	</div>
	<table class="wp-list-table widefat fixed posts idOrders">
		<thead>
			<tr>
				<th scope="col" id="number" class="manage-column" width="60"><b><?php _e('ID', 'ignitiondeck'); ?></b></th>
				<th scope="col" id="name" class="manage-column sortable desc"><a href="?page=order_details&amp;orderby=name&amp;sort=<?php echo $name_sort; ?><?php echo (isset($_GET['s']) ? '&amp;s='.$_GET['s'] : ""); ?><?php echo (isset($_GET['project']) ? '&amp;project='.$_GET['project'] : ""); ?><?php echo (isset($_GET['order_page']) ? '&amp;order_page='.$_GET['order_page'] : ""); ?>"><b><?php echo $tr_Name; ?></b></a></th>
				<!--<th scope="col" id="last-name" class="manage-column sortable desc"><b><?php echo $tr_Last_Name; ?></b></th>-->
				<th scope="col" id="project" class="manage-column sortable desc"><a href="?page=order_details&amp;orderby=project&amp;sort=<?php echo $project_sort; ?><?php echo (isset($_GET['s']) ? '&amp;s='.$_GET['s'] : ""); ?><?php echo (isset($_GET['project']) ? '&amp;project='.$_GET['project'] : ""); ?><?php echo (isset($_GET['order_page']) ? '&amp;order_page='.$_GET['order_page'] : ""); ?>"><b><?php echo $tr_Product_Name; ?></b></a></th>
				<!--<th scope="col" id="status" class="manage-column sortable desc"><b><?php echo $tr_Status; ?></b></th>-->
				<!--<th scope="col" id="action" class="manage-column sortable desc"><b><?php echo $tr_Action; ?></b></th>-->
				<th scope="col" id="level" class="manage-column sortable desc"><a href="?page=order_details&amp;orderby=level&amp;sort=<?php echo $level_sort; ?><?php echo (isset($_GET['s']) ? '&amp;s='.$_GET['s'] : ""); ?><?php echo (isset($_GET['project']) ? '&amp;project='.$_GET['project'] : ""); ?><?php echo (isset($_GET['order_page']) ? '&amp;order_page='.$_GET['order_page'] : ""); ?>"><b><?php echo $tr_Level; ?></b></a></th>
				<th scope="col" id="pledged" class="manage-column sortable desc"><a href="?page=order_details&amp;orderby=pledged&amp;sort=<?php echo $pledged_sort; ?><?php echo (isset($_GET['s']) ? '&amp;s='.$_GET['s'] : ""); ?><?php echo (isset($_GET['project']) ? '&amp;project='.$_GET['project'] : ""); ?><?php echo (isset($_GET['order_page']) ? '&amp;order_page='.$_GET['order_page'] : ""); ?>"><b><?php echo $tr_Pledged; ?></b></a></th>
				<th scope="col" id="date" class="manage-column sortable desc"><a href="?page=order_details&amp;orderby=date&amp;sort=<?php echo $date_sort; ?><?php echo (isset($_GET['s']) ? '&amp;s='.$_GET['s'] : ""); ?><?php echo (isset($_GET['project']) ? '&amp;project='.$_GET['project'] : ""); ?><?php echo (isset($_GET['order_page']) ? '&amp;order_page='.$_GET['order_page'] : ""); ?>"><b><?php echo $tr_Date; ?></b></a></th>
			</tr>
		</thead>
		<?php
		if (count($items) > 0) {
			$item_array = array();
			$i=0;

			foreach ($items as $item) {
				$item_array[$i] = $item;
				$i++;
			}
			for ($i=0; $i < count($item_array); $i++) {
				$product_id = $item_array[$i]->product_id;
				$post_id = getPostbyProductID($product_id);
				$project = new ID_Project($product_id);
				$cCode = $project->currency_code();
				?>
				<tr>
					<td valign="top"><?php echo $item_array[$i]->id;?></td>
					<td valign="top" class="alternatetd"><?php echo stripslashes(html_entity_decode($item_array[$i]->first_name))." ".stripslashes(html_entity_decode($item_array[$i]->last_name)); ?></td>
					<td valign="top"><?php echo (isset($product_array[$product_id]) ? stripslashes($product_array[$product_id]) : ''); ?></td>
					<td valign="top" class="alternatetd">
						<?php if ($item_array[$i]->product_level > 1) {
							echo strip_tags(html_entity_decode(get_post_meta($post_id, 'ign_product_level_'.$item_array[$i]->product_level.'_title', true))); 
						}
						else if ($item_array[$i]->product_level == 1) {
							$level_name = strip_tags($project->get_lvl1_name());
							echo $level_name;
						}
						else {
							echo "&nbsp;";
						}
						?>
					</td>
					<td valign="top" class="alternatetd"><?php echo (isset($item_array[$i]->prod_price) && $item_array[$i]->prod_price > 0 ? $cCode.number_format($item_array[$i]->prod_price, 2, '.', ',') : ''); ?></td>
		            <td valign="top"><?php echo $item_array[$i]->created_at;?></td>
				</tr>
				<tr>
					<td colspan="6" alignt="left" class="orderoptions"><a href="?page=view_order&orderid=<?php echo $item_array[$i]->id ?>"><?php echo $tr_View; ?></a> | <a href="?page=edit_order&orderid=<?php echo $item_array[$i]->id ?>"><?php echo $tr_Edit; ?></a> | <a href="?page=delete_order&orderid=<?php echo $item_array[$i]->id ?>" class="delete" onclick="return confirm('<?php echo $tr_sure_want_delete; ?> <?php echo $item_array[$i]->first_name." ".$item_array[$i]->last_name ?>?')"><?php echo $tr_Delete; ?></a></td>
				</tr>
				<?php
			}
		}
	?>
	</table>
</div>
