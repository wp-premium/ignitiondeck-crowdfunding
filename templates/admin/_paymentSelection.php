<div class="wrap">
	<div class="postbox-container" style="width:95%; margin-right: 5%">
		<div class="metabox-holder">
			<div class="meta-box-sortables" style="min-height:0;">
				<div class="postbox">
					<h3 class="hndle"><span><?php echo $tr_payment_method_select; ?></span><a href="javascript:toggleDiv('hPaymentset2');" class="idMoreinfo">[?]</a></h3>
					<div class="inside">
						<div id="hPaymentset2" class="idMoreinfofull">
							<?php echo $tr_PayPal_Payment_standard_help; ?> 
						</div>	
						<form method="post" action="" enctype="multipart/form-data" id="formSubmit" name="formSubmit">
							<?php echo (isset($message) ? $message : ''); ?>		
							<ul>
								<li>
									<label for="paypal_email"><?php echo $tr_payment_method ?></label>
									<select id="payment_gateway" name="payment_gateway">
									<div>
									<option>Choose Payment Gateway--</option>
									<option name="payment_gateway" value="standard_paypal" id="standard_paypal" <?php echo $selected_standard; ?>>Standard</option></div>
									<option name="payment_gateway" value="adaptive_paypal" id="adaptive_paypal" <?php echo $selected_adaptive; ?>>Adaptive</option></div>
									</select>
								</li>
							</ul>			
							<ul id="standard-settings-container" style="<?php echo (($payment_select_data->payment_gateway == "standard_paypal") ? 'display:block;' : 'display:none;') ?>">
								<input type="hidden" name="identity_token" size="50" value="<?php echo $item->identity_token;?>" />
								<li>
									<label for="paypal_email"><?php echo $tr_PayPal_Email; ?> :</label>
									<p><input type="text" name="paypal_email" size="50" value="<?php echo (isset($item) ? $item->paypal_email : ''); ?>" /></p>
								</li>
								<li>
									<label for="paypal_override"><?php echo $tr_PayPal_Override; ?> :</label>
									<p><input <?php echo (isset($item) && $item->paypal_override == 1 ? 'checked="checked"' : '')?> name="paypal_override" type="checkbox" id="paypal_override" value="1" />&nbsp;<span style="color: #666; font-size: 12px; font-weight: normal;"><?php echo $tr_paypal_override_help; ?> </span></p>
								</li>
								<li>
									<label for="paypal_mode_sandbox"><?php echo $tr_PayPal_mode; ?> :</label>
									<?php /*?><td><label for="paypal_mode_sandbox"><input type="radio" name="paypal_mode" id="paypal_mode_sandbox" value="sandbox" <?php echo (($item->paypal_mode == "sandbox") ? 'checked' : '') ?> />Sandbox</label>
										<label for="paypal_mode_production"><input type="radio" name="paypal_mode" id="paypal_mode_production" value="production" <?php echo (($item->paypal_mode == "production") ? 'checked' : '') ?> />Production</label></td><?php */?>
						             <p>
						             <input type="checkbox" name="paypal_mode" id="paypal_mode_sandbox" value="sandbox"  <?php echo (isset($item) && $item->paypal_mode == "sandbox" ? 'checked' : '') ?> />&nbsp;<span style="color: #666; font-size: 12px; font-weight: normal;"><?php echo $tr_PayPal_mode_help; ?> </span>
						             </p>
								</li>
								<li>
									
									<p>
									<input class="button-primary" type="submit" name="btnSavePaymentSettings" value="<?php echo $tr_Save_Settings; ?>" />
									</p>
								</li>
							</ul>
							<ul id="adaptive-settings-container" style="<?php echo (($payment_select_data->payment_gateway == "adaptive_paypal") ? 'display:block;' : 'display:none;') ?>">
								<li>
									<label for="app_id"><?php echo $tr_Application_Id ?></label>
									<div><input type="text" value="<?php echo (isset($payment_settings->app_id) ? $payment_settings->app_id : ''); ?>" id="application_id" name="application_id"></div>
								</li>
								<li>
									<label for="paypal_email"><?php echo $tr_PayPal_Email ?></label>
									<div><input type="text" value="<?php echo (isset($payment_settings->paypal_email) ? $payment_settings->paypal_email : ''); ?>" id="adaptive_email" name="adaptive_email"></div>
								</li>
								<li>
									<label for="api_username"><?php echo $tr_API_Username ?></label>
									<div><input type="text" value="<?php echo (isset($payment_settings->api_username) ? $payment_settings->api_username : ''); ?>" id="api_username" name="api_username"></div>
								</li>
								<li>
									<label for="api_password"><?php echo $tr_API_Password ?></label>
									<div><input type="text" value="<?php echo (isset($payment_settings->api_password) ? $payment_settings->api_password : ''); ?>" id="api_password" name="api_password"></div>
								</li>
								<li>
									<label for="api_signature"><?php echo $tr_API_Signature ?></label>
									<div><input type="text" value="<?php echo (isset($payment_settings->api_signature) ? $payment_settings->api_signature : ''); ?>" id="api_signature" name="api_signature"></div>
								</li>
								<li>
									<label for="sandbox_mode"><?php echo $tr_PayPal_mode ?>?</label>
									<div><input type="checkbox" name="sandbox_mode" id="sandbox_mode" value="sandbox" <?php echo (isset($payment_settings->paypal_mode) && $payment_settings->paypal_mode == "sandbox" ? 'checked' : ''); ?> /></div>
								</li>
								<li>
									<label for="fund_type"><?php echo $tr_Project_Type; ?></label>
									<div>
										<select name="fund_type" id="fund_type">
											<option><?php echo $tr_Choose_Project_Type; ?></option>
											<option value="standard" <?php echo (isset($payment_settings->fund_type) && $payment_settings->fund_type == 'standard' ? 'selected="selected"' : ''); ?>><?php echo $tr_Standard; ?></option>
											<option value="fixed" <?php echo (isset($payment_settings->fund_type) && $payment_settings->fund_type == 'fixed' ? 'selected="selected"' : ''); ?>><?php echo $tr_OH_Percent; ?></option>
										</select>
									</div>
								</li>
								<li>
									<div><input type="submit" name="btnSaveAdaptivePayment" id="btnSaveAdaptivePayment" value="<?php echo $tr_Save_Settings ?>" class="button-primary"/></div>
								</li>
							</ul>
						</form>
					</div>
				</div>
				<div id="charge-screen" style="display: none;" class="postbox">
					<h3 class="hndle"><span>Process Project Authorizations</span><a href="javascript:toggleDiv('hPPcharge');" class="idMoreinfo">[?]</a></h3>
					<div class="inside">
						<div>
							<div id="hPPcharge" class="idMoreinfofull">
								<p><?php echo $tr_PP_Charge; ?></p>
							</div>
							<div id="charge-confirm"></div>
							<p><span class="alert"><?php echo $tr_Warning; ?>:</span><?php echo $tr_Warning_Details; ?></p>
							<p><strong><?php echo $tr_Charged_Once; ?></strong></p>
							<p id="projects">
								<select id="project-list" name="project-list">
								</select>
							<div>
								<input type="submit" name="btnProcessPP" id="btnProcessPP" projid="btnProcessPP" value="<?php echo $tr_Process_Auth; ?>" class="button" />
							</div>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>