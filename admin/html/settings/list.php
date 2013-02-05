<select name="<?php wpmobi_the_tab_setting_name(); ?>" id="<?php wpmobi_the_tab_setting_name(); ?>" class="list">
	<?php while ( wpmobi_tab_setting_has_options() ) { ?>
		<?php wpmobi_tab_setting_the_option(); ?>
		
		<option value="<?php wpmobi_tab_setting_the_option_key(); ?>"<?php if ( wpmobi_tab_setting_is_selected() ) echo " selected"; ?>><?php wpmobi_tab_setting_the_option_desc(); ?></option>
	<?php } ?>
</select>

<label class="list" for="<?php wpmobi_the_tab_setting_name(); ?>">
	<?php wpmobi_the_tab_setting_desc(); ?>	
</label>
<?php if ( wpmobi_the_tab_setting_has_tooltip() ) { ?>
<a href="#" class="wpmobi-tooltip" title="<?php wpmobi_the_tab_setting_tooltip(); ?>">&nbsp;</a>	
<?php } ?>