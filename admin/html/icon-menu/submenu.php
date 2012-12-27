<ul<?php if ( wpmobi_get_menu_depth() == 1 ) echo ' style="display: none;"'; ?>>
	<?php while ( wpmobi_has_menu_items() ) { ?>
		<?php wpmobi_the_menu_item(); ?>
		
		<?php if ( !wpmobi_menu_item_duplicate() ) { ?>

		<li class="<?php wpmobi_the_menu_item_classes(); ?>">
			<div class="icon-drop-target <?php wpmobi_the_menu_item_classes(); ?>" title="<?php wpmobi_the_menu_id(); ?>">
				<img src="<?php wpmobi_the_menu_icon(); ?>" alt="" />
			</div>
			
			<div class="menu-enable">		
				<input class="checkbox" type="checkbox" title="<?php wpmobi_the_menu_id(); ?>" <?php if ( !wpmobi_menu_is_disabled() ) echo "checked"; ?> />
			</div>
			
			<span class="title"><?php wpmobi_the_menu_item_title(); ?></span>

			<div class="clearer"></div>
			
			<?php if ( wpmobi_menu_has_children() ) { ?>
				<?php wpmobi_show_children( WPMOBI_ADMIN_DIR . '/html/icon-menu/submenu.php', true ); ?>
			<?php } ?>			
		</li>
		
		<?php } ?>

	<?php } ?>
</ul>