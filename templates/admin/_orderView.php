<div class="wrap ignOrders">
<a href="admin.php?page=order_details"> &lt; Back to <?php echo $tr_Order_Details; ?> List</a>
	<h3><?php echo $tr_Order_Details; ?>
		<span><a href="?page=edit_order&orderid=<?php echo $order_data->id ?>"><?php echo $tr_Edit; ?></a></span>
	</h3>
	<ul>
		<li class="first">
			<label><?php echo $tr_First_Name; ?></label>
			<div><?php echo stripslashes(html_entity_decode($order_data->first_name)); ?></div>
		</li>
		<li class="second">
			<label><?php echo $tr_Last_Name; ?></label>
			<div><?php echo stripslashes(html_entity_decode($order_data->last_name)); ?></div>
		</li>
		<li>
			<label><?php echo $tr_Email_Address; ?></label>
			<div><a href="mailto:<?php echo stripslashes(html_entity_decode($order_data->email)); ?>"><?php echo stripslashes(html_entity_decode($order_data->email)); ?></a></div>
		</li>
		<li>
			<label><?php echo $tr_Address; ?></label>
			<div><?php echo stripslashes(html_entity_decode($order_data->address)); ?></div>
		</li>
		<li class="first">
			<label><?php echo $tr_City; ?></label>
			<div><?php echo stripslashes(html_entity_decode($order_data->city)); ?></div>
		</li>
		<li class="second">
			<label><?php echo $tr_State; ?></label>
			<div><?php echo stripslashes(html_entity_decode($order_data->state)); ?></div>
		</li>
		<li class="first">
			<label><?php echo $tr_Zip_Code; ?></label>
			<div><?php echo stripslashes(html_entity_decode($order_data->zip)); ?></div>
		</li>
		<li class="second">
			<label><?php echo $tr_Country; ?></label>
			<div><?php echo stripslashes(html_entity_decode($order_data->country)); ?></div>
		</li>
	</ul>
	<h3><?php echo $tr_Product; ?></h3>
	<ul>
		<li>
			<label><?php echo $tr_Product_Name; ?></label>
			<div><?php echo stripslashes(html_entity_decode(get_the_title($post_id))); ?></div>
		</li>
		<li>
			<label><?php echo $tr_Level; ?></label>
			<div><?php echo absint($order_data->product_level); ?></div>
		</li>
		<li>
			<label><?php echo $tr_Level_Price; ?></label>
			<div><?php echo $level_price; ?></div>
		</li>
		<?php if (number_format($order_data->prod_price, 2) != number_format($level_price, 2)) { ?>
		<li>
			<label><?php _e('Manual Amount', 'ignitiondeck'); ?></label>
			<div><?php echo $order_data->prod_price; ?></div>
		</li>
		<?php } ?>
		<li>
			<label><?php echo $tr_Level_Description; ?></label>
			<div><?php echo stripslashes(html_entity_decode($level_desc)); ?></div>
		</li>
		<li>
			<label><?php echo $tr_Status; ?></label>
			<div><?php echo $order_data->status; ?></div>
		</li>
	</ul>
</div>