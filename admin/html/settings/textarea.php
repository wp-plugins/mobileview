<label class="textarea" for="<?php wpmobi_the_tab_setting_name(); ?>">
	<?php wpmobi_the_tab_setting_desc(); ?>
</label>

<?php if ( wpmobi_the_tab_setting_has_tooltip() ) { ?>
<a href="#" class="wpmobi-tooltip" title="<?php wpmobi_the_tab_setting_tooltip(); ?>">&nbsp;</a>
<?php } ?><br />	
<textarea rows="5" class="textarea"  id="<?php wpmobi_the_tab_setting_name(); ?>" name="<?php wpmobi_the_tab_setting_name(); ?>"><?php echo htmlspecialchars( wpmobi_get_tab_setting_value() ); ?></textarea>