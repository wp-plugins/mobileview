<?php global $wpmobi; ?>
<?php if ( isset( $wpmobi->post['area'] ) && $wpmobi->post['area'] == 'manage' ) $manage = true; else $manage = false; ?>

	<?php if ( !$manage ) { ?>
	<div id="icon-help-message">
		<?php _e( "Drag icons from the pool to", "wpmobi-me" ); ?><br />
		<?php _e( "associate them with menu pages.", "wpmobi-me" ); ?><br />
		<?php _e( "Don't forget to save your changes!", "wpmobi-me" ); ?>
	</div>
	<?php } else { ?>
		<?php $pack = $wpmobi->get_icon_pack( $wpmobi->post['set'] ); ?>
		<div id="manage-set-desc">
			<h5><em><?php echo htmlspecialchars( $pack->name ); ?></em>
			<?php if ( isset( $pack->author ) ) { ?> 
				by <?php echo htmlentities( $pack->author ); ?>
				</h5>
				<div id="manage-set-desc-links">
					<?php if ( isset( $pack->author_url ) ) { ?><a href="<?php echo $pack->author_url; ?>" target="_blank"><?php _e( 'Author Website', 'wpmobi-me' ); ?></a> | <?php } ?><a href="#" class="delete-set"><?php _e( 'Delete Set', 'wpmobi-me' ); ?></a>
				</div>
			<?php } else { ?>
				</h5>
				<?php if ( !( $manage && $wpmobi->post['set'] == __( "Custom Icons", "wpmobi-me" ) ) ) { ?>
				<div id="manage-set-desc-links">
					<a href="#" class="delete-set"><?php _e( 'Delete Set', 'wpmobi-me' ); ?></a>
				</div>			
				<?php } ?>
			<?php } ?>
		</div>
	<?php } ?>
	
	<?php if ( wpmobi_have_icons( $wpmobi->post['set'] ) ) { ?>	
	<?php $pack = $wpmobi->get_icon_pack( $wpmobi->post['set'] ); ?>
	<ul>
		<?php while ( wpmobi_have_icons( $wpmobi->post['set'] ) ) { ?>
			<?php wpmobi_the_icon(); ?>
			<li class="<?php wpmobi_the_icon_class_name(); ?> <?php if ( $pack->dark_background ) echo 'dark'; else echo 'light'; ?>">
				<?php if ( $manage && $wpmobi->post['set'] == __( "Custom Icons", "wpmobi-me" ) ) { ?>
					<a href="#" class="delete-icon">X</a>
				<?php } ?>
				<div class="icon-image"><img src="<?php wpmobi_the_icon_url(); ?>" alt="" /></div>
				<div class="icon-info">
					<span class="icon-name"><?php wpmobi_the_icon_short_name(); ?></span>
					<?php if ( wpmobi_icon_has_image_size_info() ) { ?>
					<span class="icon-size"><?php wpmobi_icon_the_width(); ?>x<?php wpmobi_icon_the_height(); ?></span>
					<?php } ?>
				</div>
			</li>
		<?php } ?>
	</ul>
<?php } else { ?>
	<?php if ( $manage ) { ?>
		<div id="empty-icon-pool"><?php _e( "No Custom Icons to Display", "wpmobi-me" ); ?></div>
	<?php } else { ?>
		<div id="empty-icon-pool"><?php echo __( "No Custom Icons to Display", "wpmobi-me" ) . '<br />' . __( "Add them in the 'Manage Icons + Sets' area", "wpmobi-me" ); ?></div>
	<?php } ?>
<?php } ?>
<div class="clearer"></div>