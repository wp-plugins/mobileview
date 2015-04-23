<?php get_header(); ?>
<?php if ( mobileview_is_custom_latest_posts_page() ) { ?>
	<?php mobileview_custom_latest_posts_query(); ?>
	<?php locate_template( 'blog-loop.php', true ); ?>
<?php } else { ?>
 
	<?php if ( mobileview_have_posts() ) { ?>
	
		<?php mobileview_the_post(); ?>

		<div class="<?php mobileview_post_classes(); ?> entry-content">	

			<h2 class="entry-title"><?php mobileview_the_title(); ?></h2>
			
			<div class="<?php mobileview_content_classes(); ?>">
				<?php mobileview_the_content(); ?>
				<br class="clearfix" />
				<?php if ( wp_link_pages( 'echo=0' ) ) { ?>
					<div class="single-post-meta-bottom">
						<?php wp_link_pages( 'before=<div class="post-page-nav">' . __( "Article Pages", "mobileviewlang" ) . ':&after=</div>&next_or_number=number&pagelink=page %&previouspagelink=&raquo;&nextpagelink=&laquo;' ); ?>
						<?php if ( hipnews_should_show_taxonomy() ) { ?>
							<?php if ( hipnews_has_custom_taxonomy() ) { ?>
								<?php $custom_tax = hipnews_get_custom_taxonomy(); ?>
								<?php if ( $custom_tax && count( $custom_tax ) ) { ?>
									<?php foreach( $custom_tax as $tax_name => $contents ) { ?>
										<div class="post-page-cats">
											<?php echo $tax_name . ': '; ?>
											<?php $tax_array = array(); ?>
											<?php foreach( $contents as $term ) { ?>
												<?php $tax_array[] = '<a href="' . $term->link . '">' . $term->name . '</a>'; ?>
											<?php } ?>
											<?php echo implode( ', ', $tax_array ); ?>
										</div>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</div>   
				<?php } ?>
			</div>


        </div><!-- ./entry-content -->

		<?php } ?>
		<?php if ( hipnews_show_comments_on_pages() ) { ?>
					<?php comments_template(); ?>
				<?php } ?>
<?php } ?>

<?php get_footer(); ?>