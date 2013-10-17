<label class="textarea" for="<?php mobileview_the_tab_setting_name(); ?>">
	<?php mobileview_the_tab_setting_desc(); ?>
</label>

<?php if ( mobileview_the_tab_setting_has_tooltip() ) { ?>
<a href="#" class="mobileview-tooltip" title="<?php mobileview_the_tab_setting_tooltip(); ?>">&nbsp;</a>
<?php } ?><br />	
<textarea rows="5" class="textarea"  id="<?php mobileview_the_tab_setting_name(); ?>" name="<?php mobileview_the_tab_setting_name(); ?>"><?php echo htmlspecialchars( mobileview_get_tab_setting_value() ); ?></textarea>

<?php if ( mobileview_restore_failed() ) { ?>
	<div class="warning round-3"><?php _e( 'The import key you used is not valid.  Please try a copy/paste of your key again.', 'mobileviewlang' ); ?></div>
<?php } ?>