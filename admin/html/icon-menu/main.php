<?php 
	// Main menu hook for admin panel 
?>
<ul class="icon-menu">
<?php if ( wpmobi_has_menu_items() ) { ?>
	<?php while ( wpmobi_has_menu_items() ) { ?>
		<?php wpmobi_the_menu_item(); ?>
		<li class="<?php wpmobi_the_menu_item_classes(); ?>">
			<div class="icon-drop-target <?php wpmobi_the_menu_item_classes(); ?>" title="<?php wpmobi_the_menu_id(); ?>">
				<img src="<?php wpmobi_the_menu_icon(); ?>" alt="" />
			</div>
					
			<div class="menu-enable">		
				<input class="checkbox" type="checkbox" title="<?php wpmobi_the_menu_id(); ?>" <?php if ( !wpmobi_menu_is_disabled() ) echo "checked"; ?> />
			</div>
			
			<?php if ( wpmobi_menu_has_children() ) { ?>
				<a href="#" class="expand title"><?php wpmobi_the_menu_item_title(); ?></a>
			<?php } else { ?>
				<span class="title"><?php wpmobi_the_menu_item_title(); ?></span>
			<?php } ?>
	
			
			<div class="clearer"></div>
			
			<?php if ( wpmobi_menu_has_children() ) { ?>
				<?php wpmobi_show_children( WPMOBI_ADMIN_DIR . '/html/icon-menu/submenu.php', true ); ?>
			<?php } ?>
		</li>
	<?php } ?>
<?php } else { ?>
	<li><span class="title"><?php echo __( "There are no WordPress pages available to configure.", "wpmobi-me" ); ?></span></li>
<?php } ?>
</ul>