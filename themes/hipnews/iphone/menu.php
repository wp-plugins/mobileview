<ul>
	<?php while ( wpmobi_has_menu_items() ) { ?>
		<?php wpmobi_the_menu_item(); ?>	
		
		<?php if ( !wpmobi_menu_is_disabled() ) { ?>	
		<li class="<?php wpmobi_the_menu_item_classes(); ?>">
			
			<a href="<?php wpmobi_the_menu_item_link(); ?>">
				<?php if ( wpmobi_can_show_menu_icons() ) { ?>
					<img src="<?php wpmobi_the_menu_icon(); ?>" alt="" />
				<?php } ?>
			
				<?php wpmobi_the_menu_item_title(); ?>
			</a>
				
			<?php if ( wpmobi_menu_has_children() ) { ?>
				<?php wpmobi_show_children( 'menu.php' ); ?>
			<?php } ?>
		</li>
		<?php } ?>
	<?php } ?>
</ul>