<?php get_header(); ?>	
<!-- Custom photos template, looks for FlicrkRSS plugin adds itself as a drop menu option -->
	<div class="<?php wpmobi_post_classes(); ?> wpmobi-custom-page page-title-area">

		<?php if ( wpmobi_page_has_icon() ) { ?>
			<img src="<?php wpmobi_page_the_icon(); ?>" alt="<?php the_title(); ?>-page-icon" />
		<?php } ?>

		<h2><?php _e( 'Photos', 'wpmobi-me' ); ?></h2>

	</div>	
		
		<div class="wpmobi-flickr-photos post">
			<?php 
				if ( function_exists( 'get_flickrRSS' ) )
				get_flickrRSS( array (
				    'num_items' => 20, 
			    	'html' => '<a href="%flickr_page%" target="_blank" title="%title%"><img src="%image_square%" alt="%title%"/></a>')
			   	); 
			?>
		</div>

<?php get_footer(); ?>