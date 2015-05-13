<div class="wrap">
	<div class="postbox-container" style="width:95%; margin-right: 5%">
		<div class="metabox-holder">
			<div class="meta-box-sortables" style="min-height:0;">
				<?php if ($super) { ?>
				<div class="postbox">
					<h3 class="hndle"><span><?php echo $tr_License_Settings; ?></span></h3>
					<div class="inside">
						<form name="licenseSettings" action="" method="post">
							<ul>
								<li>
									<p><?php echo $tr_license_description; ?></p>
									<label for="license_key" class=""><i class="fa fa-key"></i> <?php echo $tr_License_Key; ?></label><br/>
									<input type="text" name="license_key" id="license_key" value="<?php echo (isset($license_key) ? $license_key : ''); ?>"/>
								</li>
								<li>
									<button class="button button-primary"><?php _e('Validate', 'ignitiondeck'); ?></button>
								</li>
							</ul>
						</form>
						<div class="license_validation">
							<p>
								<?php echo ($is_pro || $is_basic ? '<i class="fa fa-check"></i>'.__(' License Valid for', 'ignitiondeck').$type_msg : 'You are using IgnitionDeck Basic.<br/><a href="http://ignitiondeck.com/id/ignitiondeck-pricing/?utm_source=licensepage&utm_medium=link&utm_campaign=freemium" target="_blank">Upgrade now</a> to receive support, commerce, and free updates for a year.'); ?>
							</p>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="postbox">
					<h3 class="hndle"><span><?php echo $tr_General_Settings; ?></span></h3>
					<div class="inside">
						<form name="formSettings" action="" method="post">
							<ul>
								<li>
									<label for="theme_value" class="title"><?php echo $tr_Widget_Theme; ?></label>
									<a href="javascript:toggleDiv('hTheme');" class="idMoreinfo">[?]</a>
									<div id="hTheme" class="idMoreinfofull">
									<div class="idSSwrap"><span>IgnitionDeck (Light)</span><img src="<?php echo plugins_url('/images/help/ss-1.jpg', dirname(dirname(__FILE__))); ?>"></div>
									<div class="idSSwrap"><span>IgnitionDeck (Dark)</span><img src="<?php echo plugins_url('/images/help/ss-1d.jpg', dirname(dirname(__FILE__))); ?>"></div>
									<div class="idSSwrap"><span>Corporate</span><img src="<?php echo plugins_url('/images/help/ss-2.jpg', dirname(dirname(__FILE__))); ?>"></div>
									<div class="idSSwrap"><span>Clean (Light)</span><img src="<?php echo plugins_url('/images/help/ss-3.jpg', dirname(dirname(__FILE__))); ?>"></div>
									<div class="idSSwrap"><span>Clean (Dark)</span><img src="<?php echo plugins_url('/images/help/ss-3d.jpg', dirname(dirname(__FILE__))); ?>"></div>
									<div class="idSSwrap"><span>Skyscraper (Light)</span><img src="<?php echo plugins_url('/images/help/ss-4.jpg', dirname(dirname(__FILE__))); ?>"></div>
									<div class="idSSwrap"><span>Skyscraper (Dark)</span><img src="<?php echo plugins_url('/images/help/ss-4d.jpg', dirname(dirname(__FILE__))); ?>"></div>
									<div class="clear"></div>
									</div>
									<div><select name="theme_value" id="theme_value">
										<option <?php echo (isset($data) && $data->theme_value == "style1" ? 'selected="selected"' : '')?> value="style1"><?php echo $tr_IgnitionDeck_Light; ?></option>
										<option <?php echo (isset($data) && $data->theme_value == "style1-dark" ? 'selected="selected"' : '')?> value="style1-dark"><?php echo $tr_IgnitionDeck_Dark; ?></option>
										<option <?php echo (isset($data) && $data->theme_value == "style2" ? 'selected="selected"' : '')?> value="style2"><?php echo $tr_Clean; ?></option>
										<option <?php echo (isset($data) && $data->theme_value == "style2-dark" ? 'selected="selected"' : '')?> value="style2-dark"><?php echo $tr_Clean_Dark; ?></option>
										<option <?php echo (isset($data) && $data->theme_value == "style3" ? 'selected="selected"' : '')?> value="style3"><?php echo $tr_Skyscraper; ?></option>
										<option <?php echo (isset($data) && $data->theme_value == "style3-dark" ? 'selected="selected"' : '')?> value="style3-dark"><?php echo $tr_Skyscraper_Dark; ?></option>
										<option <?php echo (isset($data) && $data->theme_value == "style4" ? 'selected="selected"' : '')?> value="style4"><?php echo $tr_Corporate; ?></option>
										<?php echo apply_filters('id_skin', $content); ?>
									</select></div>
									<br/>
									<label for="skin-instructions" class="title"><?php echo $tr_Skin_Instructions; ?></label>
									<a href="javascript:toggleDiv('hSkin');" class="idMoreinfo">[?]</a>
									<div id="hSkin" class="idMoreinfofull">
										<p>How to add Deck skins:</p>
										<ol>
											<li>Upload skin assets to the /skins directory via FTP.</li>
											<li>CSS file will be named ignitiondeck-skinname.css. Enter the &lsquo;skinname&rsquo; in the box and click &lsquo;Add Skin&rsquo;.</li>
											<li>To delete, select skin and click &lsquo;Delete Skin&rsquo;.</li>
										</ol>
									</div>
									<br/>
									<div>
										<input type="submit" name="add-skin" id="add-skin" class="button" value="<?php echo $tr_Add_Skin; ?>"/>
										<input type="text" name="skin-name" id="skin-name"/>
									</div>
									<br/>
									<div>
										<input type="submit" name="delete-skin" id="delete-skin" class="button" value="<?php echo $tr_Delete_Skin; ?>"/>
										<select name="deleted-skin" id="deleted-skin">
											<option>-- <?php echo $tr_Delete_Skin; ?> --</option>
											<?php echo $deleted_skin_list; ?>
										</select>
									</div>	
								</li>
								
								<li>
									<div><input <?php echo (isset($data) && $data->id_widget_logo_on == 1 ? 'checked="checked"' : ''); ?> name="id_widget_logo_on" type="checkbox" id="id_widget_logo_on" class="main-setting" value="1" /> 
									<label for="id_widget_logo_on"><img src="<?php echo plugins_url('/images/ignitiondeck-menu.png', dirname(dirname(__FILE__))); ?>"><?php echo $tr_Ignition_Deck_Logo; ?></label>
									<a href="javascript:toggleDiv('hLogo');" class="idMoreinfo">[?]</a>
									<div id="hLogo" class="idMoreinfofull">
									<img src="<?php echo plugins_url('/images/help/powered-by-id.jpg', dirname(dirname(__FILE__))); ?>"><?php echo $tr_text_this_allow_deactive; ?>
									</div></div>
								</li>
								<li>
									<strong><?php echo $tr_Affiliate_Settings; ?></strong>
									<div>
									<label for="id_widget_link"><?php echo $tr_Affiliate_Link; ?></label>
									<a href="javascript:toggleDiv('hAffiliate');" class="idMoreinfo">[?]</a>
									<div id="hAffiliate" class="idMoreinfofull">
									<a href="http://www.shareasale.com/shareasale.cfm?merchantID=46545" alt="IgnitionDeck Affiliate" title="IgnitionDeck Affiliate Program" target="_blank">Click here</a> to sign up for our referral program, and paste your unique URL here. Set this to http://ignitiondeck.com for default setting.
									</div><br>
									<input name="id_widget_link" type="text" id="id_widget_link" value="<?php echo $affiliate_link; ?>" /> 
									</div>
								</li>
								
								<li>
									<div>
									<?php if(count($data) > 0) {?>
										<input class="button-primary" type="submit" name="btnIgnSettings" id="btnAddOrder" value="<?php echo $tr_Update; ?>" />
									<?php } else { ?>
										<input class="button-primary" type="submit" name="btnIgnSettings" id="btnAddOrder" value="<?php echo $tr_Add; ?>" />
									<?php } ?>
									</div>
								</li>
							</ul>
						</form>
					</div>
				</div>
				<div class="postbox">
					<h3 class="hndle"><span><?php echo $GLOBALS['tr_Embed_Widget_Code']; ?></span></h3>
					<div class="inside">
						<form method="post" action="" enctype="multipart/form-data" id="formSubmit" name="formSubmit">
							<div class="id-embedwidget-ss">
								<img src="<?php echo plugins_url('/images/help/embed-widget.jpg', dirname(dirname(__FILE__))); ?>">
							</div>
							<ul class="id-embedwidget-code">
								<li>
									<label for="product_number"><?php echo $tr_Product_Create_Embed; ?></label>
									<a href="javascript:toggleDiv('hProject');" class="idMoreinfo">[?]</a>
									<div id="hProject" class="idMoreinfofull">
										<?php echo $tr_Embed_Project; ?>
										<div class="clear"></div>
									</div>
									<div>
										<select name="product_number" id="product_number">
											<?php
											foreach ($products as $product) {
												$project = new ID_Project($product->id);
												$post_id = $project->get_project_postid();
												$post = get_post($post_id);
												if (!empty($post)) {
													echo '<option value="'.$product->id.'">'.get_the_title($post_id).'</option>';
												}
											}
											?>
										</select>
									</div>
								</li>
								<li>
									<input class="button" type="button" name="btn_generate_code" id="btn_generate_code" value="Generate Code" onclick="" />
								</li>
								<li>
									<label for="embed_code"><?php echo $tr_Embed_Code; ?></label>
									<a href="javascript:toggleDiv('hEmbed');" class="idMoreinfo">[?]</a>
									<div id="hEmbed" class="idMoreinfofull">
										<?php echo $tr_Embed_Help; ?>
									</div>
									<div>
										<textarea name="embed_code" cols="50" rows="5" id="embed_code"></textarea>
									</div>
								</li>
							</ul>
							<br style="clear: both;"/>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>