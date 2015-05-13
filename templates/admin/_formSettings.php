<div class="wrap">
	<h2><?php echo $tr_Payment_Form_Settings; ?></h2>
	<form name="form_settings" action="" method="post">
		<table>
			<tr>
				<td><?php echo $tr_Field_name; ?></td>
				<td><?php echo $tr_Enabled; ?></td>
				<td><?php echo $tr_Mandatory; ?></td>
			</tr>
			<tr>
				<th><?php echo $tr_First_name; ?></th>
				<td><input type="checkbox" <?php echo (isset($form['first_name']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[first_name][status]"/></td>
				<td><input type="checkbox" <?php echo (isset($form['first_name']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[first_name][mandatory]"/></td>
			</tr>
			<tr>
				<th><?php echo $tr_Last_name; ?>/th>
				<td><input type="checkbox" <?php echo (isset($form['last_name']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[last_name][status]"/></td>
				<td><input type="checkbox" <?php echo (isset($form['last_name']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[last_name][mandatory]"/></td>
			</tr>
			<tr>
				<th><?php echo $tr_Email_Address; ?></th>
				<td><input type="checkbox" <?php echo (isset($form['email']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[email][status]"/></td>
				<td><input type="checkbox" <?php echo (isset($form['email']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[email][mandatory]"/></td>
			</tr>
			<tr>
				<th><?php echo $tr_Address; ?></th>
				<td><input type="checkbox" <?php echo (isset($form['address']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[address][status]"/></td>
				<td><input type="checkbox" <?php echo (isset($form['address']['mandatory']))?'checked="checked"':''; ?>name="ignitiondeck_form[address][mandatory]"/></td>
			</tr>
			<tr>
				<th><?php echo $tr_Country; ?></th>
				<td><input type="checkbox" <?php echo (isset($form['country']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[country][status]"/></td>
				<td><input type="checkbox" <?php echo (isset($form['country']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[country][mandatory]"/></td>
			</tr>
			<tr>
				<th><?php echo $tr_State; ?></th>
				<td><input type="checkbox" <?php echo (isset($form['state']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[state][status]"/></td>
				<td><input type="checkbox" <?php echo (isset($form['state']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[state][mandatory]"/></td>
			</tr>
			<tr>
				<th><?php echo $tr_City; ?></th>
				<td><input type="checkbox" <?php echo (isset($form['city']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[city][status]"/></td>
				<td><input type="checkbox" <?php echo (isset($form['city']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[city][mandatory]"/></td>
			</tr>
			<tr>
				<th><?php echo $tr_Zip; ?></th>
				<td><input type="checkbox" <?php echo (isset($form['zip']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[zip][status]"/></td>
				<td><input type="checkbox" <?php echo (isset($form['zip']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[zip][mandatory]"/></td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<input class="submitbtn" type="submit" name="submit" value="<?php echo $submit ?>"/>
	</form>
</div>