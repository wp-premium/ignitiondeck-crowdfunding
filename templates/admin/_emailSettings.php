<div class="wrap">	<div class="postbox-container" style="width:95%; margin-right: 5%">
		<div class="metabox-holder">
			<div class="meta-box-sortables" style="min-height:0;">
				<div class="postbox">
					<h3 class="hndle"><span><?php echo $tr_Email_Settings; ?></span></h3>
					<div class="inside">
						<form name="emailSettingsForm" id="emailSettingsForm" method="post" action="">
							<?php echo (isset($message) ? $message : ''); ?>
							<input <?php echo (isset($inactive) && $inactive == 1 ? 'checked="checked"' : '') ?> name="inactive" type="radio" id="inactive" value="1" />
							&nbsp;<?php echo $tr_None; ?>
							<p><strong><?php echo $tr_Aweber_Settings; ?></strong></p>
							<p>
								<a href="http://www.aweber.com/?394033" alt="aweber sign-up" title="Sign Up for Aweber" target="_blank">Click here</a> to create an Aweber account for just $1.
							</p>
							<input <?php echo (isset($aweber_active) && $aweber_active == 1 ? 'checked="checked"' : '') ?> name="aweber_active" type="radio" id="aweber_active" value="1" />
							&nbsp;<?php echo $tr_Make_Active; ?>
							<div>&nbsp;</div>
							<table>
								<tr>
									<td><?php echo $tr_List_Email_Id; ?></td>
									<td><input type="text" name="list_email" size="40" value="<?php echo (isset($aweber_res->list_email) ? $aweber_res->list_email : ''); ?>" /></td>
								</tr>
							</table>
							<p>
								<strong><?php echo $tr_Mailchimp_Settings; ?></strong>
							</p>
							<p id="hMailchimp" class="idMoreinfofull">
								<a href="http://eepurl.com/DqCdz" alt="Mailchimp sign-up" title="Sign Up for Mailchimp" target="_blank">Click here</a> to create a free Mailchimp account.
							</p>
							<input <?php echo (isset($mc_active) && $mc_active == 1 ? 'checked="checked"' : '') ?> name="mc_active" type="radio" id="mc_active" value="1" />
							&nbsp;<?php echo $tr_Make_Active; ?>
							<div>&nbsp;</div>
							<table>
								<tr>
									<td><?php echo $tr_API_key; ?></td>
									<td><input type="text" name="apikey" size="40" value="<?php echo (isset($mc_res->api_key) ? $mc_res->api_key : ''); ?>"/></td>
								</tr>
								<tr>
									<td><?php echo $tr_List_id; ?></td>
									<td><input type="text" name="listid" size="40" value="<?php echo (isset($mc_res->list_id) ? $mc_res->list_id : '');?>"/></td>
								</tr>
								<tr>
									<td>
										<input class="button-primary" type="submit" name="submitEmailSettings" value="<?php echo $tr_Save; ?>"/>
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