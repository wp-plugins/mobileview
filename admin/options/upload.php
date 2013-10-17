<input type="text" autocomplete="off" class="text" id="<?php mobileview_the_tab_setting_name(); ?>" name="<?php mobileview_the_tab_setting_name(); ?>" value="<?php mobileview_the_tab_setting_value(); ?>" />
<input id="upload_<?php mobileview_the_tab_setting_name(); ?>" class="upload_button button-primary" type="button" value="<?php _e( 'Upload','mobileviewlang' );?>" />
<label class="text" for="<?php mobileview_the_tab_setting_name(); ?>">
	<?php mobileview_the_tab_setting_desc(); ?>
</label>			
<?php if ( mobileview_the_tab_setting_has_tooltip() ) { ?>
<a href="#" class="mobileview-tooltip" title="<?php mobileview_the_tab_setting_tooltip(); ?>">&nbsp;</a> 
<?php } ?>