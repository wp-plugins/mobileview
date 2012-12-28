<div id="manage-sets">	
	<div id="manage-upload-area">
		<div id="manage-upload-button">	
			<a href="#"><?php _e( "Upload Icon / Set", "wpmobi-me" ); ?></a>
		</div>
		<div id="manage-status-area">			
			<div id="manage-set-upload">	
				<div id="manage-set-upload-name"></div>		
			</div>		
			<div id="manage-status">
				<img id="manage-spinner" src="<?php echo WPMOBI_URL . '/admin/images/spinner.gif'; ?>" style="display:none;" alt="" />
				<h6><?php _e( "Ready for upload...", "wpmobi-me" ); ?></h6>
				<p class="info"></p>
				
				<div id="wpmobi-set-input-area" style="display:none;">
					<label for="wpmobi-set-name"><?php _e( "Set name", "wpmobi-me" ); ?></label>
					<input type="text" class="text" name="wpmobi-set-name" />
					
					<label for="wpmobi-set-description"><?php _e( "Set description", "wpmobi-me" ); ?></label>
					<input type="text" class="text" name="wpmobi-set-description" />
					
					<input type="submit" class="button" name="wpmobi-set-info-submit" value="<?php _e( "Save", "wpmobi-me" ); ?>" />
				</div>
			</div>
		</div>
	</div>
	
	<div id="manage-info-area">
		<h4><?php _e( "Information + Help", "wpmobi-me" ); ?></h4>
		<h5><?php _e( "Uploading Icons", "wpmobi-me" ); ?>:</h5>
		<p><?php echo sprintf( __( "Single images and those in .ZIP packages <em>must</em> be in .PNG format. When you upload a .ZIP you <em>must</em> name the set. The .ZIP size limit on your server is %dMB.", "wpmobi-me" ), wpmobi_get_bloginfo( 'max_upload_size' ) ); ?></p>
		<h5><?php _e( "Homescreen Icons", "wpmobi-me" ); ?>:</h5>
		<p><?php _e( "For images that will used as a Homescreen (Bookmark) icon, they should be 59x60 pixels or higher for best results on iPhone 2G, 3G and 3GS, and 113x114 pixels for iPhone 4.", "wpmobi-me" ); ?></p>
		<h5><?php _e( "Resources", "wpmobi-me" ); ?>:</h5>
		<p>
			<?php echo sprintf( __( '%sOnline Icon Generator%s', 'wpmobi-me' ), '<a href="http://www.midnightmobility.com/iphone-icon/" target="_blank">', '</a>' ); ?><br />
		</p>
	</div>
	<div class="clearer"></div>
	
	<div id="manage-icon-area">
		<h4><?php _e( "Manage Installed Icons + Sets", "wpmobi-me" ); ?></h4>
		<div id="pool-color-switch">
			<?php _e( "Pool Background Color", "wpmobi-me" ); ?>: <a href="#" class="light"><?php _e( "Light", "wpmobi-me" ); ?></a> | <a href="#" class="dark"><?php _e( "Dark", "wpmobi-me" ); ?></a>
		</div>
		<div class="clearer"></div>
		
		<div id="manage-icon-set-area" class="round-3">
			<ul id="icon-set-list">
				<?php while ( wpmobi_have_icon_packs() ) { ?>
					<?php wpmobi_the_icon_pack(); ?>
					<li class="<?php if ( wpmobi_get_icon_pack_dark_bg() ) echo 'dark'; else echo 'light'; ?>"><a href="#" title="<?php wpmobi_the_icon_pack_name(); ?>"><?php wpmobi_the_icon_pack_name(); ?></a></li>
				<?php } ?>
			</ul>
			
			<div id="manage-icon-ajax"></div>
		</div>
	</div>
</div>