<td class="box-table-number" id="wpmobi-licenses-remaining">
	<?php $remaining = wpmobi_get_bloginfo( 'support_licenses_remaining' ); ?>
	<?php if ( $remaining == CLC_WPMOBI_UNLIMITED ) { ?>
		<a href="#" rel="licenses" class="wpmobi-admin-switch">&infin;</a>
	<?php } else { ?>
		<a href="#" rel="licenses" class="wpmobi-admin-switch"><?php echo $remaining; ?></a>
	<?php } ?>
</td>
<td class="box-table-text"><a href="#" rel="licenses" class="wpmobi-admin-switch"><?php _e( "Licenses Remaining", "wpmobi-me" ); ?></a></td>