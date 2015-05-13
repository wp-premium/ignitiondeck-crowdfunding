<div class="wrap">
	<form action="" method="post" name="formAddOrder" id="formAddOrder">
		<table width="100%" border="0" cellspacing="1" cellpadding="1">
			<thead>
				<tr>
					<th colspan="2" style="text-align: left"><?php echo $tr_Order_Details; ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="21%"><?php echo $tr_First_Name; ?></td>
					<td width="79%"><input type="text" name="first_name" id="first_name" /></td>
				</tr>
				<tr>
					<td><?php echo $tr_Last_Name; ?></td>
					<td><input type="text" name="last_name" id="last_name" /></td>
				</tr>
				<tr>
					<td><?php echo $tr_Email_Address; ?></td>
					<td><input type="text" name="email" id="email" /></td>
				</tr>
				<tr>
					<td><?php echo $tr_Address; ?></td>
					<td><textarea name="address" cols="40" rows="4" id="address"></textarea></td>
				</tr>
				<tr>
					<td><?php echo $tr_Country; ?></td>
					<td><input type="text" name="country" id="country" /></td>
				</tr>
				<tr>
					<td><?php echo $tr_State; ?></td>
					<td><input type="text" name="state" id="state" /></td>
				</tr>
				<tr>
					<td><?php echo $tr_City; ?></td>
					<td><input type="text" name="city" id="city" /></td>
				</tr>
				<tr>
					<td><?php echo $tr_Zip_Code; ?></td>
					<td><input type="text" name="zip" id="zip" /></td>
				</tr>
				<tr>
					<td><?php echo $tr_Status; ?></td>
					<td><select name="status" id="status">
							<option value="P" selected="selected"><?php echo $tr_Pending; ?></option>
							<option value="C"><?php echo $tr_Complete; ?></option>
						</select></td>
				</tr>
				<tr>
					<td><?php echo $tr_Cancel_Success_Hook; ?></td>
					<td><input type="checkbox" name="cancel-hook" id="cancel-hook"/></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
			<thead>
				<tr>
					<th colspan="2" style="text-align: left"><strong><?php echo $tr_Product; ?></strong></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php _e('Project Name', 'ignitiondeck'); ?></td>
					<td><select name="product_id" id="product_id">
						<?php
						foreach ($products as $product) {
							$project = new ID_project($product->id);
							$post_id = $project->get_project_postid();
							?>
							<option value="<?php echo $product->id; ?>"><?php echo get_the_title($post_id); ?></option>
							<?php
						}
						?>
						</select></td>
				</tr>
				<tr id="level-select">
					<td><?php echo $tr_Product_Level; ?></td>
					<td><select name="product_level" id="product_level">
							<option value="0"><?php echo $tr_Select_Product; ?></option>
						</select></td>
				</tr>
				<tr id="manual-select">
					<td><?php echo $tr_Manual_Amount; ?></td>
					<td><input type="text" class="textbox" name="manual-input" id="manual-input" /></td>
				</tr>
				<tr>
					
					<td><input type="hidden" name="prod_price" id="prod_price" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input class="button button-primary submitbtn" type="submit" name="btnAddOrder" id="btnAddOrder" onclick="prodpricefn();" value="<?php echo $tr_Add; ?>" /></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>