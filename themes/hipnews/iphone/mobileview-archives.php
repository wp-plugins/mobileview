<?php get_header(); ?>	
<!-- Custom archives template, adds itself as a drop menu option -->
	<div class="<?php mobileview_post_classes(); ?> mobileview-custom-page page-title-area">

		<?php if ( mobileview_page_has_icon() ) { ?>
			<img src="<?php mobileview_page_the_icon(); ?>" alt="<?php the_title(); ?>-page-icon" />
		<?php } ?>

		<h2><?php _e( 'Archives', 'mobileviewlang' ); ?></h2>

	</div>	
		
	<h2 class="mobileview-archives"><?php _e( 'Browse Last 15 Posts', 'mobileviewlang' ); ?></h2>	
		<ul class="mobileview-archives">
			<?php wp_get_archives( 'type=postbypost&limit=15' ); ?>
		</ul>
				
	<h2 class="mobileview-archives"><?php _e( 'Browse Last 12 Months', 'mobileviewlang' ); ?></h2>
		<ul class="mobileview-archives">
			<?php wp_get_archives( 'type=monthly&limit=12' ); ?>
		</ul>

<?php get_footer(); ?>