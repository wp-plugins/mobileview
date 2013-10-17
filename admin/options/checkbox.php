<input type="checkbox" class="checkbox" name="<?php mobileview_the_tab_setting_name(); ?>" id="<?php mobileview_the_tab_setting_name(); ?>"<?php if ( mobileview_the_tab_setting_is_checked() ) echo " checked"; ?> />	
<label class="checkbox" for="<?php mobileview_the_tab_setting_name(); ?>">
	<?php mobileview_the_tab_setting_desc(); ?>
	
	<?php if ( mobileview_the_tab_setting_has_tooltip() ) { ?>
	<a href="#" class="mobileview-tooltip" title="<?php mobileview_the_tab_setting_tooltip(); ?>">&nbsp;</a>
	<?php } ?>
</label>			
<input type="hidden" name="<?php mobileview_the_tab_setting_name(); ?>-hidden" />