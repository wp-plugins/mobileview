<select name="<?php mobileview_the_tab_setting_name(); ?>" id="<?php mobileview_the_tab_setting_name(); ?>" class="list">
	<?php while ( mobileview_tab_setting_has_options() ) { ?>
		<?php mobileview_tab_setting_the_option(); ?>
		
		<option value="<?php mobileview_tab_setting_the_option_key(); ?>"<?php if ( mobileview_tab_setting_is_selected() ) echo " selected"; ?>><?php mobileview_tab_setting_the_option_desc(); ?></option>
	<?php } ?>
</select>

<label class="list" for="<?php mobileview_the_tab_setting_name(); ?>">
	<?php mobileview_the_tab_setting_desc(); ?>	
</label>
<?php if ( mobileview_the_tab_setting_has_tooltip() ) { ?>
<a href="#" class="mobileview-tooltip" title="<?php mobileview_the_tab_setting_tooltip(); ?>">&nbsp;</a>	
<?php } ?>