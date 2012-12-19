<?php get_header(); ?>	
<!-- Custom links template, adds itself as a drop menu option -->
	<div class="<?php wpmobi_post_classes(); ?> wpmobi-custom-page page-title-area">

		<?php if ( wpmobi_page_has_icon() ) { ?>
				<img src="<?php wpmobi_page_the_icon(); ?>" alt="<?php the_title(); ?>-page-icon" class="page-icon" />
		<?php } ?>

		<h2><?php _e( 'Links', 'wpmobi-me' ); ?></h2>

	</div>	
			
	<ul>	
		<?php wp_list_bookmarks(); ?>
	</ul>

<?php get_footer(); ?>