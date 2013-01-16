<?php get_header(); ?>	

	<?php if ( wpmobi_have_posts() ) { ?>
	
		<?php wpmobi_the_post(); ?>

		<div class="<?php wpmobi_post_classes(); ?> entry-content">

			<?php if ( hipnews_use_thumbnail_icons() && hipnews_thumbs_on_single() ) { ?>
				<?php locate_template( 'thumbnails.php', true ); ?>
			<?php } ?>	

			<h2 class="entry-title"><?php wpmobi_the_title(); ?></h2>

            <ul class="entry-meta">
                <?php if ( hipnews_show_author_single() ) { ?>
                    <li><?php _e( "By", "wpmobi-me" ); ?> <?php the_author(); ?></li>
                <?php } ?>
                <?php if ( hipnews_show_date_single() ) { ?>
                    <li><time class="entry-date"><?php wpmobi_the_time( 'F jS, Y' ); ?></time></li>
                <?php } ?>
                <?php if ( wpmobi_get_comment_count() ) { ?>
                    <li><a class="entry-comment-count" href="#wpmobi-comments"><?php comments_number( __('No Comment', 'wpmobi-me'), __('1 Comment', 'wpmobi-me'), __('% Comments', 'wpmobi-me') ); ?></a></li>
                <?php } ?>
            </ul>
        
    		<!-- text for 'back and 'next' is hidden via CSS, and replaced with arrow images -->
			<div class="post-navigation nav-top">
				<div class="post-nav-fwd">
					<?php hipnews_get_next_post_link(); ?>
				</div>				
				<div class="post-nav-middle">
					<?php if ( wpmobi_get_comment_count() > 0 ) echo '<a href="javascript: return false" class="middle-link no-ajax">' . __( "Skip to Responses", "wpmobi-me" ) . '</a>' ; ?>
				</div>
				<div class="post-nav-back">
						<?php hipnews_get_previous_post_link(); ?>
				</div>
			</div>

			
			<div class="<?php wpmobi_content_classes(); ?>">
				<?php wpmobi_the_content(); ?>
				<br class="clearfix" />
				<?php if ( wp_link_pages( 'echo=0' ) ) { ?>
					<div class="single-post-meta-bottom">
						<?php wp_link_pages( 'before=<div class="post-page-nav">' . __( "Article Pages", "wpmobi-me" ) . ':&after=</div>&next_or_number=number&pagelink=page %&previouspagelink=&raquo;&nextpagelink=&laquo;' ); ?>
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

			<div class="post-navigation nav-bottom">
				<div class="post-nav-fwd">
					<?php hipnews_get_next_post_link(); ?>
				</div>	
				<div class="post-nav-middle">
					<a href="#header" class="back-to-top no-ajax"><?php _e( "Back to Top", "wpmobi-me" ); ?></a>
				</div>
				<div class="post-nav-back">
					<?php hipnews_get_previous_post_link(); ?>
				</div>
			</div>

        </div><!-- ./entry-content -->

		<?php } ?>

		<?php comments_template(); ?>

<?php get_footer(); ?>