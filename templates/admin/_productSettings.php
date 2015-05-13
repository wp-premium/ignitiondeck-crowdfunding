<div class="wrap">
	<?php echo (isset($message) ? $message : '');?>
	<div class="postbox-container" style="width:95%; margin-right: 5%">
		<div class="metabox-holder">
			<div class="meta-box-sortables" style="min-height:0;">
				<div class="postbox">
					<h3 class="hndle"><span><?php echo $tr_Default_Product_Settings; ?></span><a href="javascript:toggleDiv('hDefaultset');" class="idMoreinfo">[?]</a></h3>
					<div class="inside">
						<div id="hDefaultset" class="idMoreinfofull">
							<?php _e('This is where you set the defaults for whenever you create a new project on your website.  Whatever you set here, is what each project will default to, unless you set its custom settings below.', 'ignitiondeck'); ?><br><br>
							<?php if ($platform == 'legacy') { ?>
							<?php _e('Currency Code: this is the currency PayPal will collect funds in, as well as the currency that will be displayed publicly.', 'ignitiondeck'); ?><br><br>
							
							<?php _e('Address information: Ask for this if some of your reward levels involve shipping an item.  We highly suggest however, that you email all of your supporters once it’s actually time to ship, to get up to date shipping information from them at that time.', 'ignitiondeck'); ?>
							<?php } ?>
						</div>
						<div>
							<form name="formdefaultsettings" id="formdefaultsettings" action="" method="post">
								<table>
									<?php if ($platform == 'legacy') { ?>
									<tr>
										<td><strong><?php echo $tr_Currency_Code; ?></strong></td>
										<td colspan="2"><select name="currency_code_default" id="currency_code_default">
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "USD") ? 'selected="selected"' : '')?> value="USD"><?php _e('U.S. Dollar', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "AUD") ? 'selected="selected"' : '')?> value="AUD"><?php _e('Australian Dollar', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "CAD") ? 'selected="selected"' : '')?> value="CAD"><?php _e('Canadian Dollar', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "CZK") ? 'selected="selected"' : '')?> value="CZK"><?php _e('Czech Koruna', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "DKK") ? 'selected="selected"' : '')?> value="DKK"><?php _e('Danish Krone', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "EUR") ? 'selected="selected"' : '')?> value="EUR"><?php _e('Euro', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "HKD") ? 'selected="selected"' : '')?> value="HKD"><?php _e('Hong Kong Dollar<', 'ignitinodeck'); ?>/option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "HUF") ? 'selected="selected"' : '')?> value="HUF"><?php _e('Hungarian Forint', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "ILS") ? 'selected="selected"' : '')?> value="ILS"><?php _e('Israeli New Sheqel', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "JPY") ? 'selected="selected"' : '')?> value="JPY"><?php _e('Japanese Yen', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "MXN") ? 'selected="selected"' : '')?> value="MXN"><?php _e('Mexican Peso', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "MYR") ? 'selected="selected"' : '')?> value="MYR"><?php _e('Malaysian Ringgit', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "NOK") ? 'selected="selected"' : '')?> value="NOK"><?php _e('Norwegian Krone', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "NZD") ? 'selected="selected"' : '')?> value="NZD"><?php _e('New Zealand Dollar', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "PHP") ? 'selected="selected"' : '')?> value="PHP"><?php _e('Philippine Peso', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "PLN") ? 'selected="selected"' : '')?> value="PLN"><?php _e('Polish Zloty', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "GBP") ? 'selected="selected"' : '')?> value="GBP"><?php _e('Pound Sterling', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "SGD") ? 'selected="selected"' : '')?> value="SGD"><?php _e('Singapore Dollar', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "SEK") ? 'selected="selected"' : '')?> value="SEK"><?php _e('Swedish Krona', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "CHF") ? 'selected="selected"' : '')?> value="CHF"><?php _e('Swiss Franc', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "TWD") ? 'selected="selected"' : '')?> value="TWD"><?php _e('New Taiwan Dollar', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "THB") ? 'selected="selected"' : '')?> value="THB"><?php _e('Thai Baht', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "TRY") ? 'selected="selected"' : '')?> value="TRY"><?php _e('Turkish Lira', 'ignitinodeck'); ?></option>
											<option <?php echo ((isset($default_currency) && $default_currency->currency_code == "BRL") ? 'selected="selected"' : '')?> value="BRL"><?php _e('Brazilian Real', 'ignitinodeck'); ?></option>
										</select></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td><a href="#" id="check-all-settings"><?php _e('Check All', 'ignitinodeck'); ?></a></td>
										<td><a href="#" id="clear-all-settings"><?php _e('Clear All', 'ignitinodeck'); ?></a></td>
									</tr>
									<tr>
										<td><strong><?php echo $tr_Field_name; ?></strong></td>
										<td><strong><?php echo $tr_Enabled; ?></strong></td>
										<td><strong><?php echo $tr_Mandatory; ?></strong></td>
									</tr>
									<tr>
										<td><?php echo $tr_First_name; ?></td>
										<td><input type="checkbox" <?php echo (isset($form_default['first_name']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[first_name][status]" class="main-setting"/></td>
										<td><input type="checkbox" <?php echo (isset($form_default['first_name']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[first_name][mandatory]" class="main-setting"/></td>
									</tr>
									<tr>
										<td><?php echo $tr_Last_name; ?></td>
										<td><input type="checkbox" <?php echo (isset($form_default['last_name']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[last_name][status]" class="main-setting"/></td>
										<td><input type="checkbox" <?php echo (isset($form_default['last_name']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[last_name][mandatory]" class="main-setting"/></td>
									</tr>
									<tr>
										<td><?php echo $tr_Email; ?></td>
										<td><input type="checkbox" <?php echo (isset($form_default['email']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[email][status]" class="main-setting"/></td>
										<td><input type="checkbox" <?php echo (isset($form_default['email']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[email][mandatory]" class="main-setting"/></td>
									</tr>
									<tr>
										<td><?php echo $tr_Address; ?></td>
										<td><input type="checkbox" <?php echo (isset($form_default['address']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[address][status]" class="main-setting"/></td>
										<td><input type="checkbox" <?php echo (isset($form_default['address']['mandatory']))?'checked="checked"':''; ?>name="ignitiondeck_form_default[address][mandatory]" class="main-setting"/></td>
									</tr>
									<tr>
										<td><?php echo $tr_Country; ?></td>
										<td><input type="checkbox" <?php echo (isset($form_default['country']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[country][status]" class="main-setting"/></td>
										<td><input type="checkbox" <?php echo (isset($form_default['country']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[country][mandatory]" class="main-setting"/></td>
									</tr>
									<tr>
										<td><?php echo $tr_State; ?></td>
										<td><input type="checkbox" <?php echo 
										(isset($form_default['state']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[state][status]" class="main-setting"/></td>
										<td><input type="checkbox" <?php echo (isset($form_default['state']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[state][mandatory]" class="main-setting"/></td>
									</tr>
									<tr>
										<td><?php echo $tr_City; ?></td>
										<td><input type="checkbox" <?php echo (isset($form_default['city']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[city][status]" class="main-setting"/></td>
										<td><input type="checkbox" <?php echo (isset($form_default['city']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[city][mandatory]" class="main-setting"/></td>
									</tr>
									<tr>
										<td><?php echo $tr_Zip; ?></td>
										<td><input type="checkbox" <?php echo (isset($form_default['zip']['status']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[zip][status]" class="main-setting"/></td>
										<td><input type="checkbox" <?php echo (isset($form_default['zip']['mandatory']))?'checked="checked"':''; ?> name="ignitiondeck_form_default[zip][mandatory]" class="main-setting"/></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<?php } ?>
									<tr>
										<td><strong><?php echo $tr_Default_Purchase_Page; ?></strong></td>
									</tr>
									<tr>
										<td>
											<select name="ign_option_purchase_url" id="select_purchase_pageurls" onchange=storepurchaseurladdress();>
												<option value="page_or_post" <?php echo (!empty($purchase_default['option']) && $purchase_default['option'] == 'page_or_post' ? 'selected="selected"' : ''); ?>><?php echo $tr_Page_Post; ?></option>
												<option value="external_url" <?php echo (!empty($purchase_default['option']) && $purchase_default['option'] == 'external_url' ? 'selected="selected"' : ''); ?>><?php echo $tr_External_URL; ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<td id="purchase_url_cont" <?php echo (empty($purchase_default['option']) || $purchase_default['option'] !== 'external_url' ? 'style="display: none;"' : ''); ?>>
											<input class="purchase-url-container" name="id_purchase_URL" type="text" id="id_purchase_URL" value="<?php echo (isset($purchase_default['option']) && $purchase_default['option'] == 'external_url' && isset($purchase_default['value']) ? $purchase_default['value'] : ''); ?>">
										</td>
									</tr>
									<tr>
										<td id="purchase_posts" <?php echo (!empty($purchase_default['option']) && $purchase_default['option'] == 'external_url' ? 'style="display: none;"' : ''); ?>>
								            <select name="ign_purchase_post_name" id="">
								            	<option value="0"><?php echo $tr_Select; ?></option>
												<?php if ($list->have_posts()) {
													while ($list->have_posts()) {
														$list->the_post();
														$post_id = get_the_ID();
														echo '<option value="'.$post_id.'" '.(!empty($purchase_default['option']) && $purchase_default['option'] == 'page_or_post' && isset($purchase_default['value']) && $purchase_default['value'] == $post_id ? 'selected="selected"' : '').'>'.get_the_title().'</option>';
													}
												} ?>
								            </select>
								        </td>
									</tr>
									<?php if (idf_exists() && idf_platform() == 'legacy') { ?>
									<tr>
										<td><strong><?php echo $tr_Default_Thank_You_Page; ?></strong></td>
									</tr>
									<tr>
										<td>
											<select name="ign_option_ty_url" id="select_ty_pageurls" onchange=storetyurladdress();>
												<option value="page_or_post" <?php echo (!empty($ty_default['option']) && $ty_default['option'] == 'page_or_post' ? 'selected="selected"' : ''); ?>><?php echo $tr_Page_Post; ?></option>
												<option value="external_url" <?php echo (!empty($ty_default['option']) && $ty_default['option'] == 'external_url' ? 'selected="selected"' : ''); ?>><?php echo $tr_External_URL; ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<td id="ty_url_cont" <?php echo (empty($ty_default['option']) || $ty_default['option'] !== 'external_url' ? 'style="display: none;"' : ''); ?>>
											<input class="ty-url-container" name="id_ty_URL" type="text" id="id_ty_URL" value="<?php echo (isset($ty_default['option']) && $ty_default['option'] == 'external_url' && isset($ty_default['value']) ? $ty_default['value'] : ''); ?>">
										</td>
									</tr>
									<tr>
										<td>
											<div id="ty_posts">
								            <select name="ign_ty_post_name" id="">
								            	<option value="0"><?php echo $tr_Select; ?></option>
												<?php if ($list->have_posts()) {
													while ($list->have_posts()) {
														$list->the_post();
														$post_id = get_the_ID();
														echo '<option value="'.$post_id.'" '.(!empty($ty_default['option']) && $ty_default['option'] == 'page_or_post' && isset($ty_default['value']) && $ty_default['value'] == $post_id ? 'selected="selected"' : '').'>'.get_the_title().'</option>';
													}
												} ?>
								            </select>
								        </td>
									</tr>
									<?php } ?>
									<?php if (!is_id_pro()) { ?>
									<tr>
										<td>
											<input type="checkbox" name="auto_insert" id="auto_insert" value="1" <?php echo (isset($auto_insert) && $auto_insert ? 'checked="checked"' : ''); ?> /> <label for="auto_insert"><?php _e('Automatically insert project template', 'ignitiondeck'); ?></label>
										</td>
									</tr>
									<?php } ?>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td colspan="3">
											<input class="button-primary" type="submit" name="btnSubmitDefaultSettings" id="btnAddOrder" value="<?php echo $submit_default?>" />
										</td>
									</tr>
								</table>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
