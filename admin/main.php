<?php 
	// Main menu hook for admin panel 
?>
<ul class="icon-menu">
<?php if ( mobileview_has_menu_items() ) { ?>
	<?php while ( mobileview_has_menu_items() ) { ?>
		<?php mobileview_the_menu_item(); ?>
		<li class="<?php mobileview_the_menu_item_classes(); ?>">
			<div class="icon-drop-target <?php mobileview_the_menu_item_classes(); ?>" title="<?php mobileview_the_menu_id(); ?>">
				<img src="<?php mobileview_the_menu_icon(); ?>" alt="" />
			</div>
					
			<div class="menu-enable">		
				<input class="checkbox" type="checkbox" title="<?php mobileview_the_menu_id(); ?>" <?php if ( !mobileview_menu_is_disabled() ) echo "checked"; ?> />
			</div>
			
			<?php if ( mobileview_menu_has_children() ) { ?>
				<a href="#" class="expand title"><?php mobileview_the_menu_item_title(); ?></a>
			<?php } else { ?>
				<span class="title"><?php mobileview_the_menu_item_title(); ?></span>
			<?php } ?>
	
			
			<div class="clearer"></div>
			
		</li>
	<?php } ?>
<?php } else { ?>
	<li><span class="title"><?php echo __( "There are no WordPress pages available to configure.", "mobileviewlang" ); ?></span></li>
<?php } ?>
</ul>