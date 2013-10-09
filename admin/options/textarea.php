<label class="textarea" for="<?php mobileview_the_tab_setting_name(); ?>">
	<?php mobileview_the_tab_setting_desc(); ?>
</label>

<?php if ( mobileview_the_tab_setting_has_tooltip() ) { ?>
<a href="#" class="mobileview-tooltip" title="<?php mobileview_the_tab_setting_tooltip(); ?>">&nbsp;</a>
<?php } ?><br />	
<textarea rows="5" class="textarea"  id="<?php mobileview_the_tab_setting_name(); ?>" name="<?php mobileview_the_tab_setting_name(); ?>"><?php echo htmlspecialchars( mobileview_get_tab_setting_value() ); ?></textarea>