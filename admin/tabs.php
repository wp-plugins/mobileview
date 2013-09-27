<?php while ( mobileview_has_tabs() ) { ?>
	<?php mobileview_the_tab(); ?>
	
	<div id="pane-content-pane-<?php mobileview_the_tab_id(); ?>" class="pane-content" style="display: none;">
		<div class="left-area">
			<ul>
				<?php while ( mobileview_has_tab_sections() ) { ?>
					<?php mobileview_the_tab_section(); ?>
					<li>
						<a id="tab-section-<?php mobileview_the_tab_section_class_name(); ?>" rel="<?php mobileview_the_tab_section_class_name(); ?>" href="#">
							<span class="icon-menu"></span>
							<span class="menu-text"><?php mobileview_the_tab_name(); ?></span>
							<span class="menu-arrow"></span>
							<span class="menu-hover"></span>
						</a>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="right-area">
			<?php mobileview_rewind_tab_settings(); ?>
			
			<?php while ( mobileview_has_tab_sections() ) { ?>
				<?php mobileview_the_tab_section(); ?>

				<div style="display: none;" class="setting-right-section" id="setting-<?php mobileview_the_tab_section_class_name(); ?>">
					<?php while ( mobileview_has_tab_section_settings() ) { ?>
						<?php mobileview_the_tab_section_setting(); ?>

						<div class="mobileview-setting type-<?php mobileview_the_tab_setting_type(); ?><?php if ( mobileview_tab_setting_has_tags() ) echo ' ' . mobileview_tab_setting_the_tags(); ?>"<?php if ( mobileview_get_tab_setting_class_name() ) echo ' id="setting_' .  mobileview_get_tab_setting_class_name() . '"'; ?>>
							
							<?php if ( file_exists( dirname( __FILE__ ) . '/options/' . mobileview_get_tab_setting_type() . '.php' ) ) { ?>
								<?php include( 'options/' . mobileview_get_tab_setting_type() . '.php' ); ?>
							<?php } else { ?>
								<?php do_action( 'mobileview_show_custom_setting', mobileview_get_tab_setting_type() ); ?>
							<?php } ?>
						</div>
					<?php } ?>
				</div>				
			<?php } ?>	
			
			<br class="clearer" />		
		</div>
		<br class="clearer" />
	</div>
<?php } ?>