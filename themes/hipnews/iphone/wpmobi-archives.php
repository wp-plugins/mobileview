<?php get_header(); ?>	
<!-- Custom archives template, adds itself as a drop menu option -->
	<div class="<?php wpmobi_post_classes(); ?> wpmobi-custom-page page-title-area">

		<?php if ( wpmobi_page_has_icon() ) { ?>
			<img src="<?php wpmobi_page_the_icon(); ?>" alt="<?php the_title(); ?>-page-icon" />
		<?php } ?>

		<h2><?php _e( 'Archives', 'wpmobi-me' ); ?></h2>

	</div>	
		
	<h2 class="wpmobi-archives"><?php _e( 'Browse Last 15 Posts', 'wpmobi-me' ); ?></h2>	
		<ul class="wpmobi-archives">
			<?php wp_get_archives( 'type=postbypost&limit=15' ); ?>
		</ul>
				
	<h2 class="wpmobi-archives"><?php _e( 'Browse Last 12 Months', 'wpmobi-me' ); ?></h2>
		<ul class="wpmobi-archives">
			<?php wp_get_archives( 'type=monthly&limit=12' ); ?>
		</ul>

<?php get_footer(); ?>