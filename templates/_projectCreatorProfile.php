<div class="ignitiondeck id-creatorprofile">
	<div class="id-creator-avatar"><a href="<?php echo $durl.'?creator_profile='.$profile['author']; ?>"><img src="<?php echo (isset($profile['logo']) ? $profile['logo'] : ''); ?>" title="<?php echo (isset($profile['name']) ? $profile['name'] : ''); ?>"/></a></div>
	<div class="id-creator-content">
		<div class="id-creator-name"><a href="<?php echo $durl.'?creator_profile='.$profile['author']; ?>"><?php echo (isset($profile['name']) ? $profile['name'] : ''); ?></a></div>
		<div class="id-creator-location"><?php echo (isset($profile['location']) ? $profile['location'] : ''); ?></div>
	</div>
	<div class="id-creator-links">
		<?php if (!empty($profile['twitter'])) { ?>
		<a href="<?php echo $profile['twitter']; ?>" class="twitter"><?php _e('Twitter', 'ignitiondeck'); ?></a>
		<?php } ?>
		<?php if (!empty($profile['facebook'])) { ?>
		<a href="<?php echo $profile['facebook']; ?>" class="facebook"><?php _e('Facebook', 'ignitiondeck'); ?></a>
		<?php } ?>
		<!--<a href="#" class="googleplus"></a>-->
		<?php if (!empty($profile['url'])) { ?>
		<a href="<?php echo apply_filters('ide_company_url', $profile['url']); ?>" class="website"><?php echo $profile['url']; ?></a>
		<?php } ?>
	</div>
	<div class="cf"></div>
</div>