<label class="text-array" for="<?php mobileview_the_tab_setting_name(); ?>">
	<?php mobileview_the_tab_setting_desc(); ?>
	
	<?php if ( mobileview_the_tab_setting_has_tooltip() ) { ?>
	<a href="#" class="mobileview-tooltip" title="<?php mobileview_the_tab_setting_tooltip(); ?>">&nbsp;</a>		
	<?php } ?>				
</label>							

<br />
<?php $setting_count = 1; ?>
<?php $cur_value = mobileview_get_tab_setting_value(); ?>
<?php if ( is_array( $cur_value ) && count( $cur_value ) ) { ?>
	<?php foreach( $cur_value as $value ) { ?>
		<input class="text-array" type="text" value="<?php echo $value; ?>" name="<?php echo mobileview_get_tab_setting_name() . '_' . $setting_count; ?>" />
		<?php $setting_count++; ?>
		<br />
	<?php } ?>
<?php } ?>

<input class="text-array" type="text" value="" name="<?php echo mobileview_get_tab_setting_name() . '_' . $setting_count; ?>" />