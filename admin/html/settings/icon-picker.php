<?php
// The main icon picker for the menu items
?>

<?php require_once( WPMOBI_ADMIN_DIR . '/template-tags/icons.php' ); ?>
<?php require_once( WPMOBI_DIR . '/include/template-tags/menu.php' ); ?>

<div id="wpmobi-icon-area">
	<div class="pool-wrapper">
		<div class="round-3" id="wpmobi-icon-packs">
			<div id="icon-select">
				<label for="active-icon-set"><?php _e( "Active Icon Set: ", "wpmobi-me" ); ?></label>
				<select name="active-icon-set" id="active-icon-set">
				<?php while ( wpmobi_have_icon_packs() ) { ?>
					<?php wpmobi_the_icon_pack(); ?>
					<option value="<?php wpmobi_the_icon_pack_name(); ?>"><?php wpmobi_the_icon_pack_name(); ?></option>	
				<?php } ?>
				</select>
			</div>		
			<div id="wpmobi-icon-list"></div>		
		</div>
	</div><!-- pool wrapper -->
	
	<h4 id="menu-h4"><?php _e( 'General Icons + Menu Setup', 'wpmobi-me' ); ?></h4>

	<div class="round-3" id="wpmobi-icon-menu">	
		<div id="menu-select">
			<ul>
				<li class="tab-left"><a href="#mixed-area"><?php _e( 'Site, Theme &amp; Bookmark', 'wpmobi-me' ); ?></a></li>
				<li class="tab-right"><a href="#pages-area"><?php _e( 'Pages / Custom Menu', 'wpmobi-me' ); ?></a></li>
			</ul>
		</div>

	<div id="page-tab-container">
		<div id="mixed-area" class="menu-tab-div">
			<div class="menu-meta">			
				<a id="reset-menu-all" href="/"><?php _e( 'Reset All Pages & Icons', 'wpmobi-me' ); ?></a>
			</div>	
		
			<ul class="icon-menu">
				<?php while ( wpmobi_has_site_icons() ) { ?>
					<?php wpmobi_the_site_icon(); ?>
					
					<li class="<?php wpmobi_the_site_icon_classes(); ?>">
						<div class="icon-drop-target<?php if ( wpmobi_site_icon_has_dark_bg() ) echo ' dark'; ?>" title="<?php wpmobi_the_site_icon_id(); ?>">
							<img src="<?php wpmobi_the_site_icon_icon(); ?>" alt="" /> 
						</div>
						<span class="title"><?php wpmobi_the_site_icon_name(); ?></span>
						<div class="clearer"></div>
					</li>
				<?php } ?>
			</ul>
		</div>
	
		<div id="pages-area" class="menu-tab-div">
			<div class="menu-meta">			
				<?php _e( "Show / Hide", "wpmobi-me" ); ?>: <a href="#checkall" id="pages-check-all"><?php _e( "Check All", "wpmobi-me" ); ?></a> | <a href="#checknone" id="pages-check-none"><?php _e( "None", "wpmobi-me" ); ?></a>
			</div>	
		
			<?php wpmobi_show_menu( WPMOBI_ADMIN_DIR . '/html/icon-menu/main.php' ); ?>
			<input type="hidden" name="hidden-menu-items" id="hidden-menu-items" value="" />
		</div>
	</div>
	
		<div id="remove-icon-area">
			<?php _e( "Trash", "wpmobi-me" ); ?>
			<small><?php _e( "(drag here to reset icon)", "wpmobi-me" ); ?></small>
		</div>	
	</div>
	
	<div class="clearer"></div>
</div>