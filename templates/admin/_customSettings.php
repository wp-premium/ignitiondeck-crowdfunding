<div class="wrap">
	<div class="postbox-container" style="width:95%; margin-right: 5%">
		<div class="metabox-holder">
			<div class="meta-box-sortables" style="min-height:0;">
				<div class="postbox">
					<?php 
					if (isset($_GET['pid'])) { 
						$project_id = absint($_GET['pid']);
						$project = new ID_Project($project_id);
						$post_id = $project->get_project_postid();
					?>
					<h3 class="hndle"><a href="#" class="back">&lt; <?php echo $tr_Back; ?></a>&nbsp;<span><?php echo stripslashes(get_the_title($post_id)); ?></span></h3>
					<div class="inside">
						<form method="post" action="" enctype="multipart/form-data" id="formSubmit" name="formSubmit">
							<table border="0">
								<tr>
									<td><label for="mailchimp_api_key"><strong><?php echo $tr_Mailchimp_API_Key; ?></strong></label></td>
									<td><input type="text" value="<?php echo (isset($mc_api) ? $mc_api : ''); ?>" id="mailchimp_api_key" name="mailchimp_api_key"></td>
								</tr>
								<tr>
									<td><label for="mailchimp_list_id"><strong><?php echo $tr_Mailchimp_List_ID; ?></strong></label></td>
									<td><input type="text" value="<?php echo (isset($mc_list) ? $mc_list : ''); ?>" id="mailchimp_list_id" name="mailchimp_list_id"></td>
								</tr>
								<tr>
									<td><label for="aweber_email"><strong><?php echo $tr_Aweber_Email; ?></strong></label></td>
									<td><input type="text" value="<?php echo (isset($aweber_email) ? $aweber_email : ''); ?>" id="aweber_email" name="aweber_email"></td>
								</tr>
								<tr>
									<td><strong><?php echo $tr_Active_Settings; ?></strong></td>
									<td><select id="active_mailtype" name="active_mailtype">
											<option <?php echo (isset($mailtype) && $mailtype == "mailchimp" ? 'selected="selected"' : '') ?> value="mailchimp">Mailchimp</option>
											<option <?php echo (isset($mailtype) && $mailtype == "aweber" ? 'selected="selected"' : '') ?> value="aweber">Aweber Email</option>
										</select></td>
								</tr>
								<?php if (isset($pay_selection) && $pay_selection->payment_gateway == 'standard_paypal') { ?>
								<tr>
									<td><strong><?php echo $tr_PayPal_Email; ?> :</strong></td>
									<td><input type="text" name="paypal_email" placeholder="<?php echo $tr_Standard_Only; ?>" size="30" value="<?php echo (isset($paypal_email) ? $paypal_email : ''); ?>" /></td>
								</tr>
								<?php } ?>
								<tr>
									<td><strong><?php echo $tr_Currency_Code; ?></strong></td>
									<td><select name="currency_code" id="currency_code">
										<option <?php echo(isset($currency_code) && $currency_code == "USD" ? 'selected="selected"' : '')?> value="USD">U.S. Dollar</option>
										<option <?php echo(isset($currency_code) && $currency_code == "AUD" ? 'selected="selected"' : '')?> value="AUD">Australian Dollar</option>
										<option <?php echo(isset($currency_code) && $currency_code == "CAD" ? 'selected="selected"' : '')?> value="CAD">Canadian Dollar</option>
										<option <?php echo(isset($currency_code) && $currency_code == "CZK" ? 'selected="selected"' : '')?> value="CZK">Czech Koruna</option>
										<option <?php echo(isset($currency_code) && $currency_code == "DKK" ? 'selected="selected"' : '')?> value="DKK">Danish Krone</option>
										<option <?php echo(isset($currency_code) && $currency_code == "EUR" ? 'selected="selected"' : '')?> value="EUR">Euro</option>
										<option <?php echo(isset($currency_code) && $currency_code == "HKD" ? 'selected="selected"' : '')?> value="HKD">Hong Kong Dollar</option>
										<option <?php echo(isset($currency_code) && $currency_code == "HUF" ? 'selected="selected"' : '')?> value="HUF">Hungarian Forint</option>
										<option <?php echo(isset($currency_code) && $currency_code == "ILS" ? 'selected="selected"' : '')?> value="ILS">Israeli New Sheqel</option>
										<option <?php echo(isset($currency_code) && $currency_code == "JPY" ? 'selected="selected"' : '')?> value="JPY">Japanese Yen</option>
										<option <?php echo(isset($currency_code) && $currency_code == "MXN" ? 'selected="selected"' : '')?> value="MXN">Mexican Peso</option>
										<option <?php echo(isset($currency_code) && $currency_code == "MYR" ? 'selected="selected"' : '')?> value="MYR">Malaysian Ringgit</option>
										<option <?php echo(isset($currency_code) && $currency_code == "NOK" ? 'selected="selected"' : '')?> value="NOK">Norwegian Krone</option>
										<option <?php echo(isset($currency_code) && $currency_code == "NZD" ? 'selected="selected"' : '')?> value="NZD">New Zealand Dollar</option>
										<option <?php echo(isset($currency_code) && $currency_code == "PHP" ? 'selected="selected"' : '')?> value="PHP">Philippine Peso</option>
										<option <?php echo(isset($currency_code) && $currency_code == "PLN" ? 'selected="selected"' : '')?> value="PLN">Polish Zloty</option>
										<option <?php echo(isset($currency_code) && $currency_code == "GBP" ? 'selected="selected"' : '')?> value="GBP">Pound Sterling</option>
										<option <?php echo(isset($currency_code) && $currency_code == "SGD" ? 'selected="selected"' : '')?> value="SGD">Singapore Dollar</option>
										<option <?php echo(isset($currency_code) && $currency_code == "SEK" ? 'selected="selected"' : '')?> value="SEK">Swedish Krona</option>
										<option <?php echo(isset($currency_code) && $currency_code == "CHF" ? 'selected="selected"' : '')?> value="CHF">Swiss Franc</option>
										<option <?php echo(isset($currency_code) && $currency_code == "TWD" ? 'selected="selected"' : '')?> value="TWD">New Taiwan Dollar</option>
										<option <?php echo(isset($currency_code) && $currency_code == "THB" ? 'selected="selected"' : '')?> value="THB">Thai Baht</option>
										<option <?php echo(isset($currency_code) && $currency_code == "TRY" ? 'selected="selected"' : '')?> value="TRY">Turkish Lira</option>
										<option <?php echo(isset($currency_code) && $currency_code == "BRL" ? 'selected="selected"' : '')?> value="BRL">Brazilian Real</option>
									</select></td>
								</tr>
								<tr>
									<td colspan="2"><div>
										<table width="100%">
											<tr>
												<td><?php echo $tr_Field_name; ?></td>
												<td><?php echo $tr_Enabled; ?></td>
												<td><?php echo $tr_Mandatory; ?></td>
											</tr>
											<tr>
												<td><?php echo $tr_First_name; ?></td>
												<td><input type="checkbox" <?php echo (isset($form['first_name']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[first_name][status]"/></td>
												<td><input type="checkbox" <?php echo (isset($form['first_name']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[first_name][mandatory]"/></td>
											</tr>
											<tr>
												<td><?php echo $tr_Last_name; ?></td>
												<td><input type="checkbox" <?php echo (isset($form['last_name']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[last_name][status]"/></td>
												<td><input type="checkbox" <?php echo (isset($form['last_name']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[last_name][mandatory]"/></td>
											</tr>
											<tr>
												<td><?php echo $tr_Email; ?></td>
												<td><input type="checkbox" <?php echo (isset($form['email']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[email][status]"/></td>
												<td><input type="checkbox" <?php echo (isset($form['email']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[email][mandatory]"/></td>
											</tr>
											<tr>
												<td><?php echo $tr_Address; ?></td>
												<td><input type="checkbox" <?php echo (isset($form['address']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[address][status]"/></td>
												<td><input type="checkbox" <?php echo (isset($form['address']['mandatory']))?'checked="checked"':''; ?>name="ignitiondeck_form[address][mandatory]"/></td>
											</tr>
											<tr>
												<td><?php echo $tr_Country; ?></td>
												<td><input type="checkbox" <?php echo (isset($form['country']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[country][status]"/></td>
												<td><input type="checkbox" <?php echo (isset($form['country']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[country][mandatory]"/></td>
											</tr>
											<tr>
												<td><?php echo $tr_State; ?></td>
												<td><input type="checkbox" <?php echo (isset($form['state']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[state][status]"/></td>
												<td><input type="checkbox" <?php echo (isset($form['state']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[state][mandatory]"/></td>
											</tr>
											<tr>
												<td><?php echo $tr_City; ?></td>
												<td><input type="checkbox" <?php echo (isset($form['city']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[city][status]"/></td>
												<td><input type="checkbox" <?php echo (isset($form['city']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[city][mandatory]"/></td>
											</tr>
											<tr>
												<td><?php echo $tr_Zip; ?></td>
												<td><input type="checkbox" <?php echo (isset($form['zip']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form[zip][status]"/></td>
												<td><input type="checkbox" <?php echo (isset($form['zip']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form[zip][mandatory]"/></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
										<td>
										<?php if (count($product_settings) > 0): ?>
										<input class="submitbtn button-primary" type="submit" name="btnSubmitProdSettings" id="btnSubmitProdSettings" value="<?php echo $tr_Update; ?>" />
										<?php else: ?>
										<input class="submitbtn button-primary" type="submit" name="btnSubmitProdSettings" id="btnSubmitProdSettings" value="<?php echo $tr_Save; ?>" />
										<?php endif; ?>
										<input class="submitbtn button-secondary" type="submit" name="btnClearProdSettings" id="btnClearProdSettings" value="<?php echo $tr_Clear_ignitiondeck; ?>" />
									</td>
								</tr>
							</table>
						</form>
					<?php } else { ?>
					<h3 class="hndle"><span><?php echo $tr_Custom_Pre_Product_Settings; ?><a href="javascript:toggleDiv('hCustomset');" class="idMoreinfo">[?]</a></h3>
					<div class="inside">
						<div id="hCustomset" class="idMoreinfofull">
							This is where you set the custom settings on a per project basis.  <br>
							IF YOU SAVE THIS SCREEN, it will now use these settings for this project.  If you wish to cancel out this, and use the default settings, choose 'Clear All' and save again.<br>
							We don’t recommend asking for address information unless you’ll be shipping something.  Even then - best to get their shipping information at the time when you’re actually ready to ship!
						</div>
						<table width="100%" border="0">
							<tr>
								<td width="20%"><?php _e('Project Name', 'ignitiondeck'); ?></td>
								<td width="5%"><?php _e('Project ID', 'ignitiondeck'); ?></td>
								<td><?php echo $tr_Action; ?></td>
							</tr>
							<?php
							foreach ($products as $product) {
								$project = new ID_project($product->id);
								$post_id = $project->get_project_postid();
								$post = get_post($post_id);
								if (!empty($post)) {
								?>
							<tr>
								<td width="30%"><a href="?page=custom-settings&amp;pid=<?php echo $product->id ?>"><?php echo stripslashes(get_the_title($post_id)); ?></a></td>
								<td width="15%"><?php echo $product->id; ?></td>			
								<td><a href="?page=custom-settings&amp;pid=<?php echo $product->id ?>"><?php echo $tr_Action; ?></a></td>
							</tr>
							<?php }
							}
							?>
						</table>
						<form action="" method="post" id="clearAll" name="clearAll">
							<input class="button-secondary" type="submit" name="btnClearAllSettings" id="btnClearAllSettings" value="<?php echo $tr_Clear_all; ?>"/>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<script>
jQuery(".back").click(function(e) {
	e.preventDefault();
	window.location="admin.php?page=custom-settings";
});
</script>