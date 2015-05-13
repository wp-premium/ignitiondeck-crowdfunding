<?php echo $tr_Show_Mini; ?>
</p>
<p>
<input type="radio" name="<?php echo $this->get_field_name('widget_options'); ?>" id="showwidget1" value="projectpage" /><?php echo $tr_Widget_Option_Page; ?></p><p><input type="radio" name="<?php echo $this->get_field_name('widget_options'); ?>" id="showwighet2" value="allpages" />On All Pages</p>
<p><input type="radio" name="<?php echo $this->get_field_name('widget_options'); ?>" id="showwighet3" value="miniwidget" /><?php echo $tr_Widget_Option_Small; ?></p>



<?php

echo popupInsertLink($i);

?>