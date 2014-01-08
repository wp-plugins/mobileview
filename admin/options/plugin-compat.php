<?php if ( mobileview_has_plugin_warnings() ) { ?>
<table>
	<tr>
		<th><?php _e( "Problem Area", "mobileviewlang" ); ?></th>
		<th><?php _e( "Description", "mobileviewlang" ); ?></th>
		<th><?php _e( "Action", "mobileviewlang" ); ?></th>
	</tr>
	<?php while ( mobileview_has_plugin_warnings() ) { ?>
		<?php mobileview_the_plugin_warning(); ?>
		<tr>
			<td class="plugin-name"><?php mobileview_plugin_warning_the_name(); ?></td>
			<td class="warning-item-desc"><?php mobileview_plugin_warning_the_desc(); ?></td>
			<td>
			<?php if ( mobileview_plugin_warning_has_link() ) { ?>
				<a href="<?php mobileview_plugin_warning_the_link(); ?>" class="info-button" target="_blank"><?php _e( "More Info", "mobileviewlang" ) ?></a>
			<?php } ?>
			<a href="#" id="<?php mobileview_plugin_warning_the_name(); ?>" class="dismiss-button"><?php _e( "Dismiss", "mobileviewlang" ) ?></a></td>
		</tr>
	<?php } ?>	
</table>
<?php } else { ?>
	<p class="no-warnings"><?php _e( "No known warnings or conflicts.", "mobileviewlang" ) ?></p>
<?php } ?>