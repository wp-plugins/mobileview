<div id="wpmobi-admin-profile">
	
	<?php if ( wpmobi_has_site_licenses() ) { ?>
		<p><?php _e( "You have activated these sites for automatic upgrades & support:", "wpmobi-me" ); ?></p>
		<ol class="round-3">
			<?php while ( wpmobi_has_site_licenses() ) { ?>
				<?php wpmobi_the_site_license(); ?>
				<li <?php if ( wpmobi_can_delete_site_license() ) { echo 'class="green-text"'; } ?>>
					<?php wpmobi_the_site_license_name(); ?> <?php if ( wpmobi_can_delete_site_license() ) { ?><a class="wpmobi-remove-license" href="#" rel="<?php wpmobi_the_site_license_name(); ?>" title="<?php _e( "Remove license?", "wpmobi-me" ); ?>">(x)</a><?php } ?></li>
			<?php } ?>
		</ol>
	<?php } ?>
	<!-- end site licenses -->
		
	<?php if ( wpmobi_get_site_licenses_remaining() != CLC_WPMOBI_UNLIMITED ) { ?>
		<p><?php echo sprintf( __( "%s%d%s license(s) remaining.", "wpmobi-me" ), '<strong>', wpmobi_get_site_licenses_remaining(), '</strong>' ); ?></p>
		
		<?php if ( !wpmobi_get_site_licenses_remaining() ) { ?>
		 	<p class="inline-button">
		 	<?php _e( "Purchase More Licenses", "wpmobi-me" ); ?>
		 	</p>
		<?php } ?>
	<?php } ?>

	<?php if ( wpmobi_get_site_licenses_remaining() ) { ?>
		<?php if ( !wpmobi_is_licensed_site() ) { ?>
			<p class="red-text"><?php _e( "You have not activated a license for this WordPress installation.", "wpmobi-me" ); ?></p>
			<p class="inline-button">
				<a class="wpmobi-add-license round-24 button" class="button" id="partial-activation" href="#">
					<?php _e( "Activate This WordPress installation &raquo;", "wpmobi-me" ); ?>
				</a>
			</p>
		<?php } ?>
	<?php } ?>

	<?php if ( wpmobi_get_site_licenses_in_use() ) { ?>
		<?php if ( wpmobi_can_do_license_reset() ) { ?>
			<p class="inline-button">
				<a href="#" id="reset-licenses" class="button"><?php _e( "Reset Licenses Now", "wpmobi-me" ); ?></a>
			</p>
			<br /><br />
			<p>
				<small>
					<?php echo sprintf( __( "You can reset all support and auto-upgrade licenses every %d days.", "wpmobi-me" ), wpmobi_get_license_reset_days() ); ?>
				</small>
			</p>
		<?php } else { ?>
			<br /><br />
			<p>
				<small>
					<?php echo sprintf( __( "You will be able to reset all licenses again in %d day(s).", "wpmobi-me" ), wpmobi_get_license_reset_days_until() ); ?>
				</small>
			</p>
		<?php } ?>	
	<?php } ?>
</div>

<?php
global $wpmobi;
$wpmobi->clc_api->verify_site_license( 'wpmobi-me' );
