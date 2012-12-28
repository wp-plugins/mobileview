<?php if ( wpmobi_has_plugin_warnings() ) { ?>
<table>
	<tr>
		<th><?php _e( "Problem Area", "wpmobi-me" ); ?></th>
		<th><?php _e( "Description", "wpmobi-me" ); ?></th>
		<th><?php _e( "Action", "wpmobi-me" ); ?></th>
	</tr>
	<?php while ( wpmobi_has_plugin_warnings() ) { ?>
		<?php wpmobi_the_plugin_warning(); ?>
		<tr>
			<td class="plugin-name"><?php wpmobi_plugin_warning_the_name(); ?></td>
			<td class="warning-item-desc"><?php wpmobi_plugin_warning_the_desc(); ?></td>
			<td>
			<?php if ( wpmobi_plugin_warning_has_link() ) { ?>
				<a href="<?php wpmobi_plugin_warning_the_link(); ?>" class="info-button" target="_blank"><?php _e( "More Info", "wpmobi-me" ) ?></a>
			<?php } ?>
			<a href="#" id="<?php wpmobi_plugin_warning_the_name(); ?>" class="dismiss-button"><?php _e( "Dismiss", "wpmobi-me" ) ?></a></td>
		</tr>
	<?php } ?>	
</table>
<?php } else { ?>
	<p class="no-warnings"><?php _e( "No known warnings or conflicts.", "wpmobi-me" ) ?></p>
<?php } ?>