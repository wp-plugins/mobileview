<?php get_header(); ?>	
<!-- Custom photos template, looks for FlicrkRSS plugin adds itself as a drop menu option -->
	<div class="<?php mobileview_post_classes(); ?> mobileview-custom-page page-title-area">

		<?php if ( mobileview_page_has_icon() ) { ?>
			<img src="<?php mobileview_page_the_icon(); ?>" alt="<?php the_title(); ?>-page-icon" />
		<?php } ?>

		<h2><?php _e( 'Photos', 'mobileviewlang' ); ?></h2>

	</div>	
		
		<div class="mobileview-flickr-photos post">
			<?php 
				if ( function_exists( 'get_flickrRSS' ) )
				get_flickrRSS( array (
				    'num_items' => 20, 
			    	'html' => '<a href="%flickr_page%" target="_blank" title="%title%"><img src="%image_square%" alt="%title%"/></a>')
			   	); 
			?>
		</div>

<?php get_footer(); ?>