<input type="text" autocomplete="off" class="text mobileview-color-field" id="<?php mobileview_the_tab_setting_name(); ?>" name="<?php mobileview_the_tab_setting_name(); ?>" value="<?php mobileview_the_tab_setting_value(); ?>" />
<label class="text" for="<?php mobileview_the_tab_setting_name(); ?>">
	<?php mobileview_the_tab_setting_desc(); ?>
</label>			
<?php if ( mobileview_the_tab_setting_has_tooltip() ) { ?>
<a href="#" class="mobileview-tooltip" title="<?php mobileview_the_tab_setting_tooltip(); ?>">&nbsp;</a> 
<?php } ?>