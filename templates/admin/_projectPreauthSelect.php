<br/>
<div id="project-charge-screen">
	<h3><?php _e('Process Project Pre-Authorizations', 'ignitiondeck'); ?></h3>
	<div id="project-charge-confirm"></div>
	<p><span class="alert"><?php _e('Warning:</span> This will process all pending authorizations related to the selected campaign', 'igntiiondeck'); ?>.</p>
	<p><strong><?php _e('Customers will only be charged once', 'ignitiondeck'); ?>.</strong></p>
	<div id="projects">
		<select id="project-list" name="project-list">
		</select>
	</div>
	<div>
		<input type="submit" name="btnProcessProjectPreauth" id="btnProcessProjectPreauth" projid="btnProcessProjectPreauth" value="<?php _e('Process Project Authorizations', 'ignitiondeck'); ?>" class="button" />
	</div>
</div>