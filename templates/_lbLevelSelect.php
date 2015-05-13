<?php
	// we need to hide/invalidate sold out levels
	if (isset($level)) {
		$level_invalid = getLevelLimitReached($project_id, $post_id, $level);
		if ($level_invalid) {
			$level = 0;
		}
	}
?>
<div class="ignitiondeck idc_lightbox mfp-hide">
	<div class="project_image" style="background-image: url(<?php echo $image; ?>);"><div class="aspect_ratio_maker"></div></div>
	<div class="lb_wrapper">
		<div class="form_header">
			<strong><?php _e('Step 1:', 'ignitiondeck'); ?></strong> <?php _e('Specify your contribution amount for', 'ignitiondeck'); ?> <em><?php echo get_the_title($post_id); ?></em>
		</div>
		<div class="form">
			<form action="<?php echo (isset($action) ? $action : ''); ?>" method="POST" name="idcf_level_select">
				<div class="form-row inline left twothird">
					<label for="level_select"><?php _e('Contribution Level', 'ignitiondeck'); ?>
						<span class="idc-dropdown <?php echo ($the_deck->disable_levels == 'on' ? 'disable_levels' : ''); ?>">
							<select name="level_select" class="idc-dropdown__select level_select">
								<?php foreach ($level_data as $level) {
									if (empty($level->level_invalid) || !$level->level_invalid) {
										echo '<option value="'.$level->id.'" data-price="'.(isset($level->meta_price) ? $level->meta_price : '').'" data-desc="'.$level->meta_short_desc.'">'.$level->meta_title.'</option>';
									}
								}
								?>
							</select>
						</span>
					</label>
				</div>
				<div class="form-row inline third total">
					<label for="total"><?php _e('Total', 'ignitiondeck'); ?></label>
					<?php if (isset($pwyw) && $pwyw) { ?>
						<input type="text" class="total" name="total" id="total" value="<?php // echo total; ?>" />
					<?php } else { ?>
						<span name="total" class="total" data-value=""></span>
					<?php } ?>
				</div>
				<div class="form-row text">
					<p>
						<?php // echo description; ?>
					</p>
				</div>
				<div class="form-hidden">
					<input type="hidden" name="project_id" value="<?php echo $project_id; ?>"/>
				</div>
				<div class="form-row submit">
					<input type="submit" name="lb_level_submit" class="btn lb_level_submit" value="<?php _e('Next Step', 'ignitiondeck'); ?>"/>
				</div>
			</form>
		</div>
	</div>
</div>