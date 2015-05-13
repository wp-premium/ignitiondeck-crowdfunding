<div class="wrap">
	<div class="postbox-container" style="width:95%; margin-right: 5%">
		<div class="metabox-holder">
			<div class="meta-box-sortables" style="min-height:0;">
				<div class="postbox">
					<h3 class="hndle"><span><?php echo $tr_Deck_Builder; ?></span></h3>
					<div class="inside">
						<p style="width: 50%"><?php echo $tr_Select_Components; ?></p>
						<form method="POST" action="" id="idmsg-settings" name="idmsg-settings">
							<div class="form-select">
								<p>
									<label for="deck_select"><?php echo $tr_Create_Select; ?></label><br/>
									<select name="deck_select" id="deck_select">
										<option><?php echo $tr_New_Deck; ?></option>
									</select>
								</p>
							</div>
							<div class="form-input">
								<p>
									<label for="deck_title"><?php echo $tr_Deck_Title; ?></label><br/>
									<input type="text" name="deck_title" id="deck_title" class="deck-attr-text" value="" />
								</p>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_title" id="project_title" class="deck-attr" value="1"/>
								<label for="project_title"><?php echo $tr_Project_Title; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_image" id="project_image" class="deck-attr" value="1"/>
								<label for="project_image"><?php echo $tr_Product_Image; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_bar" id="project_bar" class="deck-attr" value="1"/>
								<label for="project_bar"><?php echo $tr_Percentage_Bar; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_pledged" id="project_pledged" class="deck-attr" value="1"/>
								<label for="project_pledged"><?php echo $tr_Total_Raised; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_goal" id="project_goal" class="deck-attr" value="1"/>
								<label for="project_goal"><?php echo $tr_Project_Goal; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_pledgers" id="project_pledgers" class="deck-attr" value="1"/>
								<label for="project_pledgers"><?php echo $tr_Total_Orders; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="days_left" id="days_left" class="deck-attr" value="1"/>
								<label for="days_left"><?php echo $tr_Days_To_Go; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_end" id="project_end" class="deck-attr" value="1"/>
								<label for="project_end"><?php echo $tr_End_Date; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_button" id="project_button" class="deck-attr" value="1"/>
								<label for="project_button"><?php echo $tr_Buy_Button; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_description" id="project_description" class="deck-attr" value="1"/>
								<label for="project_description"><?php echo $tr_meta_project_description; ?></label>
							</div>
							<div class="form-check">
								<input type="checkbox" name="project_levels" id="project_levels" class="deck-attr" value="1"/>
								<label for="project_levels"><?php echo $tr_Levels; ?></label>
							</div>
							<div class="submit">
								<input type="submit" name="deck_submit" id="submit" class="button button-primary"/>
								<input type="submit" name="deck_delete" id="deck_delete" class="button" value="Delete Deck" style="display: none;"/>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>