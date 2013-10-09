<?php
if (isset($_POST['mobileview-skin-upload'])) {
	if(($_POST['mobileview-skin-upload']=='true')&&(!empty($_FILES['skinzip']['tmp_name']))){
	$wp_filesystem = WP_Filesystem();
	$tmpfname = $_FILES['skinzip']['tmp_name'];

	$skin_dir = MOBILEVIEW_BASE_CONTENT_DIR . '/themes/';
	$do_unzip = unzip_file($tmpfname, $skin_dir);
	unlink($tmpfname);
		if ( is_wp_error($do_unzip) ) {
		$error = $do_unzip->get_error_code();
			if('incompatible_archive' == $error) {
					echo "<div class='error' ><p>". __("Failed: Incompatible Archive","mobileviewlang")."</p></div>";
			}
			if('empty_archive' == $error) {
					echo "<div class='error' ><p>". __("Failed: Empty Archive","mobileviewlang")."</p></div>";
			}
			if('mkdir_failed' == $error) {
					echo "<div class='error' ><p>". __("Failed: mkdir Failure","mobileviewlang")."</p></div>";
			}
			if('copy_failed' == $error) {
					echo "<div class='error'><p>". __("Failed: Copy Failed","mobileviewlang")."</p></div>";
			}
		}else{
			echo "<div class='updated'><p>". __("Successfully upload skin","mobileviewlang")."</p></div>";
		}
	}
}
?>

<div class="mobileview-skin-uploader">
	<h4><?php _e('Install a skin in .zip format','mobileviewlang');?></h4>
	<p class="install-help"><?php _e('If you have a theme in a .zip format, you may install it by uploading it here.','mobileviewlang');?></p>
		<input type="hidden" name="mobileview-skin-upload" value="true">
		<input type="file" name="skinzip" id="skinzip">
		<input type="submit" name="install-skin-submit" id="install-skin-submit" class="button" value="Install Now">
	
</div>
<?php if ( mobileview_has_themes() ) { ?>
	<?php while ( mobileview_has_themes() ) { ?>
		<?php mobileview_the_theme(); ?>
		
		<div class="<?php mobileview_the_theme_classes( 'theme-wrap round-3' ); ?>">

			<input type="hidden" class="theme-location" value="<?php mobileview_the_theme_location(); ?>" />
			<input type="hidden" class="theme-name" value="<?php mobileview_the_theme_title(); ?>" />
					
			<div class="mobileview-theme-left-wrap round-3">
				<img src="<?php mobileview_the_theme_screenshot(); ?>" alt="<?php echo sprintf( __( '%s Theme Image', 'mobileviewlang' ), mobileview_get_theme_title() ); ?>" />
			</div>
			<div class="mobileview-theme-right-wrap">
				<?php if ( mobileview_is_theme_custom() && mobileview_is_theme_update() ) : ?>
					
					<?php
					$url_storefront_skin_version = wp_remote_get('http://colorlabsproject.com/updates/mobileview-skins/'.trim(mobileview_get_theme_title()).'/readme.txt');
					if($url_storefront_skin_version){
						if ( preg_match( '#Version: (.*)#i', $url_storefront_skin_version['body'], $matches ) ) {
							$storefront_skin_version = $matches[1];
						}
					}	
					$changelog_uri = add_query_arg(array('TB_iframe' => 'true'), 'http://colorlabsproject.com/updates/mobileview-skins/'.mobileview_get_theme_title().'/readme.txt');
					
					printf( __('<div class="update-available">There is a new version of <strong>%1$s</strong> is available. <a href="%2$s" title="%1$s" class="thickbox">View version %3$s details.</a></div>'), mobileview_get_theme_title(),$changelog_uri,$storefront_skin_version );
					?>
					
				<?php endif;?>
				<h4>
					<?php mobileview_the_theme_title(); ?>
					<span><?php echo sprintf( __( '(%s)', 'mobileviewlang' ), mobileview_get_theme_version() ); ?></span>
				</h4>
				<p class="mobileview-theme-author green-text"><?php echo sprintf( __( 'By %s', 'mobileviewlang' ), mobileview_get_theme_author() ); ?></p>
				<p class="mobileview-theme-description"><?php mobileview_the_theme_description(); ?></p>
				<?php if ( mobileview_theme_has_features() ) { ?>
					<p class="mobileview-theme-features"><?php echo sprintf( __( 'Features: %s', 'mobileviewlang' ), implode( mobileview_get_theme_features(), ', ' ) ); ?></p>
				<?php } ?>		
				<?php if ( mobileview_is_theme_custom() ) { ?>
					<p class="location"><?php echo sprintf( __( 'Theme Location (relative to wp-content): %s', 'mobileviewlang' ), mobileview_get_theme_location() ); ?></p>
				<?php } ?>
				<ul class="option-list">
				<?php if ( !mobileview_is_theme_active() ) { ?>					
					<li><a href="#" class="activate-theme ajax-button"><?php _e( 'Activate', 'mobileviewlang' ); ?></a></li>
				<?php } ?>
				<?php if ( mobileview_is_theme_custom() ) { ?>
					<li><a href="#" class="delete-theme ajax-button"><?php _e( 'Delete', 'mobileviewlang' ); ?></a></li>
				<?php } ?>
				<?php if ( mobileview_is_theme_custom() && mobileview_is_theme_update() ) { ?>
					<li>
						<?php add_thickbox(); ?>
						<a href="#TB_inline?width=400&height=260&inlineId=mobileview-update-login-id" class="thickbox ajax-button"><?php _e( 'Update', 'mobileviewlang' ); ?></a>
						<div id="mobileview-update-login-id" style="display:none;">
							<div class="mobileview-login-form">
								<p class="alert-account" style="display:none;">
									<?php _e('The user name or password is incorrect. Please try again or <a href="http://colorlabsproject.com/member/signup" target="_blank">renew your account</a>','mobileviewlang');?>
								</p>
								<p class="login-msg"><?php _e('Please login with your ColorLabs account to updating your skin.','mobileviewlang');?></p>
								<p>
									<label class="element-title" for="amember_login"><?php _e('E-Mail Address:','mobileviewlang');?></label> 
									<input id="amember_login" name="amember_login" size="15" value="" type="text">
								</p>
								<p>
									<label class="element-title" for="amember_pass"><?php _e('Password:','mobileviewlang');?></label> 
									<input id="amember_pass" name="amember_pass" size="15" type="password">
								</p>
								<p>
									<a href="#" class="mobileview-login button button-primary button-large"><?php _e( 'Login', 'mobileviewlang' ); ?></a>
									&nbsp;&nbsp;<?php _e('<a href="http://colorlabsproject.com/member/member/#am-forgot-block" target="_blank">Forgot Password?</a>','mobileviewlang');?>
								</p>			
							</div>
							<div class="mobileview-update-confirm" style="display:none;">
								<div class="update-message">
									<?php
									printf( __('<p class="alert-success">You can update to <strong>%1$s</strong> Skin automatically. To use the automatic update feature, cURL must be enabled on your hosting. If cURL is disabled, please contact your hosting. Updating this skin will lose any customizations you have made. We recommend backing up your skin files before updating.</p>'), mobileview_get_theme_title() );
									?>
									<h4><?php _e('Are you sure to update this skin?','mobileviewlang');?></h4>
									<a href="#" class="update-theme button button-secondary button-large" ><?php _e( 'Yes', 'mobileviewlang' ); ?></a>
									<a href="#" class="cancel-update-theme button button-secondary button-large" ><?php _e( 'No', 'mobileviewlang' ); ?></a>
									<input type="hidden" class="theme-location" value="<?php mobileview_the_theme_location(); ?>" />
									<input type="hidden" class="theme-name" value="<?php mobileview_the_theme_title(); ?>" />
								</div>
								<h2 class="updater-loader" style="display:none">
									<?php _e('Please Wait....','mobileviewlang');?>
									<img src="<?php echo MOBILEVIEW_URL . '/admin/images/ajax-install.gif';?>" />
								</h2>
								<div class="update-notif" style="display:none"></div>
								
							</div>
						</div>
					</li>
				<?php } ?>
				</ul>
				<br class="clearer" />	
			</div>
			<br class="clearer" />	
		</div>
	<?php } ?>
<?php } else { ?>
	<?php _e( "There are currently no themes installed.", "mobileviewlang" ); ?>
<?php }