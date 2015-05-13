<?php
/**
 * The Template for displaying purchase form.
 */
?>
<?php // this is paypal code that could be removed if disabled ?>
<?php 
	if (isset($level)) {
		$level_invalid = getLevelLimitReached($project_id, $post_id, $level);
		if ($level_invalid) {
			$level = 0;
		}
	}
?>

<script src="https://www.paypalobjects.com/js/external/dg.js"></script>
<div class="ignitiondeck id-purchase-form-full">
<div class="id-checkout-description">
	<p>
			<?php echo $tr_ThankYouText; ?> <?php echo (isset($purchase_form->the_project) ? stripslashes(get_the_title($purchase_form->post_id)) : '');?>.
		</p>
</div>
<div class="id-purchase-form-wrapper">
<div class="id-purchase-form">
	<div id="<?php echo $purchase_form->form_id; ?>-pay-form">
		<form action="" method="post" name="form_pay" id="form_pay" data-postid="<?php echo (isset($purchase_form->post_id) ? absint($purchase_form->post_id) : ''); ?>" data-projectid="<?php echo (isset($_GET['prodid']) ? absint($_GET['prodid']) : ''); ?>" data-level="<?php echo (isset($level) ? absint($level) : ''); ?>" data-projectType="<?php echo (isset($purchase_form->project_type) ? $purchase_form->project_type : ''); ?>" data-currency="<?php echo (isset($purchase_form->currencyCodeValue) ? $purchase_form->currencyCodeValue : ''); ?>">
			<input type="hidden" name="project_id" value="<?php echo ($purchase_form->project_id); ?>" />
			<ul>
				<li>
					<h4><?php echo $tr_Payment_Information; ?></h4>
				</li>
				<li id="id-notifications"><div class="notification"></div></li>
				<li id="message-container" <?php echo (!isset($_SESSION['paypal_errors_content']) || $_SESSION['paypal_errors_content'] == "" ? 'style="display: none;"' : ''); ?>>
					<div class="notification error">
						<a href="#" class="close-notification" title="Hide Notification" rel="tooltip">x</a>
						<p><strong><?php echo $tr_Payment_Error; ?>: </strong><span id="paypal-error-message"><?php echo (isset($_SESSION['paypal_errors_content']) ? $_SESSION['paypal_errors_content'] : ''); ?></span></p>
					</div>
				</li>
				<?php
				if (isset($_SESSION['paypal_errors_content'])) {
					unset($_SESSION['paypal_errors_content']);
				}
				?>
				<?php if(isset($purchase_form->form_settings['first_name']['status'])):?>
				<li class="form-row third left idinput">
					<label class="idfield_label" for="first_name"><?php echo $tr_First_Name;  ?>:
						<?php if(isset($purchase_form->form_settings['first_name']['mandatory'])): ?>
						<span class="required-mark"><?php echo $tr_Required;  ?></span>
						<?php endif; ?></label>
					<div class="idfield"><input type="text" name="first_name" class="<?php echo (isset($purchase_form->form_settings['first_name']['mandatory']))?'required':''; ?>" id="first_name" /></div>
				</li>
				<?php endif; ?>
				<?php if(isset($purchase_form->form_settings['last_name']['status'])):?>
				<li class="form-row twothird idinput">
					<label class="idfield_label" for="last_name"><?php echo $tr_Last_Name; ?>:
						<?php if(isset($purchase_form->form_settings['last_name']['mandatory'])): ?>
						<span class="required-mark"><?php echo $tr_Required;  ?></span>
						<?php endif; ?></label>
					<div class="idfield"><input type="text" name="last_name" class="<?php echo (isset($purchase_form->form_settings['last_name']['mandatory']))?'required':''; ?>" id="last_name" /></div>
				</li>
				<?php endif; ?>
				<?php if(isset($purchase_form->form_settings['email']['status'])):?>
				<li class="form-row idinput">
					<label class="idfield_label" for="email"><?php echo $tr_Email; ?>:
						<?php if(isset($purchase_form->form_settings['email']['mandatory'])): ?>
						<span class="required-mark"><?php echo $tr_Required;  ?></span>
						<?php endif; ?></label>
					<div class="idfield"><input type="email" name="email" class="<?php echo (isset($purchase_form->form_settings['email']['mandatory']))?'required':''; ?> email" id="email" /></div>
				</li>
				<?php endif; ?>
				<?php if(isset($purchase_form->form_settings['address']['status'])):?>
				<li class="form-row idinput">
					<label class="idfield_label" for="address"><?php echo $tr_Address; ?>:
						<?php if(isset($purchase_form->form_settings['address']['mandatory'])): ?>
						<span class="required-mark"><?php echo $tr_Required;  ?></span>
						<?php endif; ?></label>
					<div class="idfield"><textarea name="address" class="<?php echo (isset($purchase_form->form_settings['address']['mandatory']))?'required':''; ?>" id="address"></textarea></div>
				</li>
				<?php endif; ?>
				<?php if(isset($purchase_form->form_settings['city']['status'])):?>
				<li class="form-row half left idinput">
					<label class="idfield_label" for="city"><?php echo $tr_City; ?>:
						<?php if(isset($purchase_form->form_settings['city']['mandatory'])): ?>
						<span class="required-mark"><?php echo $tr_Required;  ?></span>
						<?php endif; ?></label>
					<div class="idfield"><input type="text" name="city" id="city" class="<?php echo (isset($purchase_form->form_settings['city']['mandatory']))?'required':''; ?>"/></div>
				</li>
				<?php endif; ?>
				<?php if(isset($purchase_form->form_settings['state']['status'])):?>
				<li class="form-row half idinput">
					<label class="idfield_label" for="state"><?php echo $tr_State; ?>:
						<?php if(isset($purchase_form->form_settings['state']['mandatory'])): ?>
						<span class="required-mark"><?php echo $tr_Required;  ?></span>
						<?php endif; ?></label>
					<div class="idfield"><input type="text" name="state" id="state" class="<?php echo (isset($purchase_form->form_settings['state']['mandatory']))?'required':''; ?>" /></div>
				</li>
				<?php endif; ?>
				<?php if(isset($purchase_form->form_settings['zip']['status'])):?>
				<li class="form-row half left idinput">
					<label class="idfield_label" for="zip"><?php echo $tr_Zip; ?>:
						<?php if(isset($purchase_form->form_settings['zip']['mandatory'])): ?>
						<span class="required-mark"><?php echo $tr_Required;  ?></span>
						<?php endif; ?></label>
					<div class="idfield"><input type="text" name="zip" id="zip" class="<?php echo (isset($purchase_form->form_settings['zip']['mandatory']))?'required':''; ?>" /></div>
				</li>
				<?php endif; ?>
				<?php if(isset($purchase_form->form_settings['country']['status'])):?>
				<li class="form-row half idinput">
					<label class="idfield_label" for="country"><?php echo $tr_Country; ?>:
						<?php if(isset($purchase_form->form_settings['country']['mandatory'])): ?>
						<span class="required-mark"><?php echo $tr_Required;  ?></span>
						<?php endif; ?></label>
					<div class="idfield"><input type="text" name="country" id="country" class="<?php echo (isset($purchase_form->form_settings['country']['mandatory']))?'required':''; ?>" /></div>
				</li>
				<?php endif; ?>
				<?php $output = null; ?>
				<div id="payment-choices" class="payment-type-selector">
					<?php $pay_choices = '<a id="pay-with-paypal" class="pay-choice" href="#"><span>Pay with Paypal</span></a>'; ?>
					<?php echo apply_filters('id_pay_choices', $pay_choices, $project_id); ?>
				</div>
				<?php echo apply_filters('id_purchaseform_extrafields', $output); ?>
				<li class="form-row idinput">
					<?php 
					if (isset($level) && $level > 0 && $purchase_form->project_type !== 'pwyw') {
						if ($level == 1) {
							$is_level_invalid = getLevelLimitReached($project_id, $post_id, 1);
							$meta_title = $purchase_form->the_project->ign_product_title;
							$meta_price = get_post_meta( $post_id, $name="ign_product_price", true );
							$meta_desc = $purchase_form->the_project->product_details;
						}
						else {
							$is_level_invalid = getLevelLimitReached($project_id, $post_id, $level);
							$meta_title = get_post_meta( $post_id, $name="ign_product_level_".($level)."_title", true );
							$meta_price = get_post_meta( $post_id, $name="ign_product_level_".($level)."_price", true );
							$meta_desc = html_entity_decode(get_post_meta( $post_id, $name="ign_product_level_".($level)."_desc", true ));
						}
					} 
					else if (isset($purchase_form->project_type) && $purchase_form->project_type !== "pwyw") { ?>
							<label class="idfield_label" for="level_select"><?php echo $tr_Level; ?>:</label>
							<div class="idfield">
								<select name="level_select" id="level_select">
									<?php foreach ($purchase_form->level_data as $level_item) {
										if ($level_item->is_level_invalid) { ?>
											<option value="<?php echo $level_item->id; ?>" data-description="<?php echo html_entity_decode(isset($level_item->meta_desc) ? $level_item->meta_desc : ''); ?>" data-price="<?php echo number_format($level_item->meta_price, 2, '.', ','); ?>" disabled="disabled"><?php echo ($level_item->meta_title !== "" ? $level_item->meta_title.": " : $tr_Level." 1:"); ?><?php echo '<span class="id-buy-form-currency">'.$purchase_form->cCode.'</span>'; ?><?php echo number_format($level_item->meta_price, 2, '.', ','); ?> -- <?php echo $tr_Sold_Out; ?></option>
										<?php 
										} else { ?>
											<option value="<?php echo $level_item->id; ?>" data-description="<?php echo html_entity_decode(isset($level_item->meta_desc) ? $level_item->meta_desc : ''); ?>" data-price="<?php echo (isset($level_item->meta_price) ? number_format($level_item->meta_price, 2, '.', ',') : '');?>"><?php echo ($level_item->meta_title !== "" ? $level_item->meta_title.": " : $tr_Level." 1:"); ?><?php echo '<span class="id-buy-form-currency">'.$purchase_form->cCode.'</span>'; ?><?php echo number_format($level_item->meta_price, 2, '.', ',');?></option>
										<?php 
										} 
									} ?>
								</select>
							</div>
						<?php 
					}
					else { ?>
						<label class="idfield_label" for="price_entry"><?php echo $tr_Price_Entry; ?>:</label>
						<div class="idfield"><input type="text" name="price_entry" id="price_entry" value=""/></div>
						<input type="hidden" name="level_select" id="level_select" value="1"/>
					<?php }	?>
				</li>
				<li class="form-row idinput">
					<div class="id-checkout-level-desc" desc="$">
						<strong>
							<?php echo (isset($purchase_form->project_type) && $purchase_form->project_type !== "pwyw" ? $tr_Level.': ' : ''); ?>
						</strong>
						<?php if (isset($level) && $level >= 1) {
							echo (isset($meta_desc) ? strip_tags(html_entity_decode($meta_desc)) : '');
						}
						else {
							echo (isset($purchase_form->the_project) ? strip_tags(html_entity_decode($purchase_form->the_project->product_details)) : '');
						} ?>
					</div>
				</li>
						<input type="hidden" name="price" value="<?php 
							if (isset($level) && $level >= 1) {
								echo (isset($meta_price) ? $meta_price : '');
							}
							else {
								echo (isset($purchase_form->project_type) && $purchase_form->project_type !== "pwyw" ? $purchase_form->the_project->product_price : '');
							} ?>" />
						<input type="hidden" name="quantity" />
						<input type="hidden" name="project_type" id="project_type" value="<?php echo (isset($purchase_form->project_type) ? $purchase_form->project_type : 'level-based'); ?>"/>
						<input type="hidden" name="level" value="<?php echo (isset($level) && $level >= 1 ? $level : ''); ?>"/>
				<li class="input">
					<div class="ign-checkout-price idinput">
						<label class="idfield_label" for="price"><?php echo $tr_Total_Contribution; ?> </label>
						<div class="idfield">
							<span class="id-buy-form-currency"><?php echo (isset($purchase_form->cCode) ? $purchase_form->cCode : ''); ?></span>
							<span class="preorder-form-product-price">
								<?php 
								if (isset($level) && $level >= 1) {
									echo (isset($meta_price) ? $meta_price : '');
								}
								else {
									echo (isset($purchase_form->the_project) ? $purchase_form->the_project->product_price : '');
								} ?>
							</span>
						</div>
					</div>
					<div class="ign-checkout-button"><input class="main-btn" type="submit" value="<?php echo $tr_Make_Payment; ?>" name="<?php echo $purchase_form->submit_btn_name ?>" id="button_pay_purchase"/>
					</div>
					<div class="clear"></div>
				</li>
			</ul>
		</form>
	</div> <!-- widget payform -->
</div><!-- .id-purchase-form -->
</div><!-- .id-purchase-form-wrapper -->
</div><!-- .id-purchase-form-full -->