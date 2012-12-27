<?php while ( wpmobi_has_tabs() ) { ?>
	<?php wpmobi_the_tab(); ?>
	
	<div id="pane-content-pane-<?php wpmobi_the_tab_id(); ?>" class="pane-content" style="display: none;">
		<div class="left-area">
			<ul>
				<?php while ( wpmobi_has_tab_sections() ) { ?>
					<?php wpmobi_the_tab_section(); ?>
					<li><a id="tab-section-<?php wpmobi_the_tab_section_class_name(); ?>" rel="<?php wpmobi_the_tab_section_class_name(); ?>" href="#"><?php wpmobi_the_tab_name(); ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="right-area">
			<?php wpmobi_rewind_tab_settings(); ?>
			
			<?php while ( wpmobi_has_tab_sections() ) { ?>
				<?php wpmobi_the_tab_section(); ?>

				<div style="display: none;" class="setting-right-section" id="setting-<?php wpmobi_the_tab_section_class_name(); ?>">
					<?php while ( wpmobi_has_tab_section_settings() ) { ?>
						<?php wpmobi_the_tab_section_setting(); ?>

						<div class="wpmobi-setting type-<?php wpmobi_the_tab_setting_type(); ?><?php if ( wpmobi_tab_setting_has_tags() ) echo ' ' . wpmobi_tab_setting_the_tags(); ?>"<?php if ( wpmobi_get_tab_setting_class_name() ) echo ' id="setting_' .  wpmobi_get_tab_setting_class_name() . '"'; ?>>
							
							<?php if ( file_exists( dirname( __FILE__ ) . '/settings/' . wpmobi_get_tab_setting_type() . '.php' ) ) { ?>
								<?php include( 'settings/' . wpmobi_get_tab_setting_type() . '.php' ); ?>
							<?php } else { ?>
								<?php do_action( 'wpmobi_show_custom_setting', wpmobi_get_tab_setting_type() ); ?>
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