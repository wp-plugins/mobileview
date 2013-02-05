<?php get_header(); ?>	

<?php if ( wpmobi_hipnews_is_custom_latest_posts_page() ) { ?>
	<?php wpmobi_hipnews_custom_latest_posts_query(); ?>
	<?php locate_template( 'blog-loop.php', true ); ?>
<?php } else { ?>
	<?php if ( wpmobi_have_posts() ) { ?>
	
		<?php wpmobi_the_post(); ?>
		<div class="<?php wpmobi_post_classes(); ?> page-title-area">

			<?php if ( hipnews_use_thumbnail_icons() && hipnews_thumbs_on_pages() ) { ?>
				<?php locate_template( 'thumbnails.php', true ); ?>
			<?php } elseif ( wpmobi_page_has_icon() ) { ?>
				<img src="<?php wpmobi_page_the_icon(); ?>" alt="<?php the_title(); ?>-page-icon" />
			<?php } ?>

			<h2><?php wpmobi_the_title(); ?></h2>

			<?php wp_link_pages( __( 'Pages in the article:', 'wpmobi-me' ), '', 'number' ); ?>

		</div>	
		
		<div class="<?php wpmobi_post_classes(); ?>">
			
			<div class="<?php wpmobi_content_classes(); ?>">
				<?php wpmobi_the_content(); ?>
			</div>
			
					<?php wp_link_pages( 'before=<div class="post-page-nav">' . __( "Pages", "wpmobi-me" ) . ':&after=</div>&next_or_number=number&pagelink=page %&previouspagelink=&raquo;&nextpagelink=&laquo;' ); ?>          

		</div><!-- wpmobi_posts_classes() -->

	<?php } ?>
	
	<?php if ( hipnews_show_comments_on_pages() ) { ?>
		<?php comments_template(); ?>
	<?php } ?>
<?php } ?>

<?php get_footer(); ?>