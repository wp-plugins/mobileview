<?php 

$settings = wpmobi_get_settings();
$hipnews_slider_exclude = $settings->hipnews_slider_exclude;
global $hipnews_slider_ID;
if($hipnews_slider_exclude && is_front_page()){
    query_posts( array( post__not_in => $hipnews_slider_ID ) );
}

$first = 0; global $post_ID; 
if ( wpmobi_have_posts() ) { while ( wpmobi_have_posts() ) { ?>

<?php wpmobi_the_post(); ?>
<?php $first++; ?>

    <article class="<?php wpmobi_post_classes(); ?> entry-post">
    
    <?php if ( is_sticky() ) echo '<div class="sticky-pushpin"></div>'; ?>
      
      <?php if ( hipnews_use_thumbnail_icons() ) { ?>
        <?php $template = locate_template( 'thumbnails.php' ); ?>
        <?php include( $template ); ?>
      <?php } ?>
    
      <div class="entry-content">
        <h2 class="entry-title"><a href="<?php wpmobi_the_permalink(); ?>"><?php wpmobi_the_title(); ?></a></h2>
        
		<?php if ( hipnews_show_date_in_posts() ) { ?>
            <time class="<?php wpmobi_date_classes(); ?> entry-date"><?php wpmobi_the_time( 'F jS, Y' ); ?></time>
		<?php } ?>
        
        <?php if ( hipnews_show_author_in_posts() ) { ?>
			<span class="entry-author">
				<?php echo sprintf( __( 'by %s', 'wpmobi-me' ), get_the_author() ); ?> 
			</span>
		<?php } ?>
        
        <?php if ( wpmobi_get_comment_count() ) { ?>
            <a class="entry-comment-count" href="#"><?php comments_number( __('No Comment', 'wpmobi-me'), __('1 Comment', 'wpmobi-me'), __('% Comments', 'wpmobi-me') ); ?></a>
        <?php } ?>
        
    	<?php if ( hipnews_should_show_taxonomy() ){ ?>
    		<?php if ( hipnews_has_custom_taxonomy() ){ ?>
    			<?php $custom_tax = hipnews_get_custom_taxonomy(); ?>
    			<?php if ( $custom_tax && count( $custom_tax ) ){ ?>
    				<?php foreach( $custom_tax as $tax_name => $contents ){ ?>
    					<div class="tags-and-categories">
    						<?php echo $tax_name . ': '; ?>
    						<?php $tax_array = array(); ?>
    						<?php foreach( $contents as $term ){ ?>
    							<?php $tax_array[] = '<a href="' . $term->link . '">' . $term->name . '</a>'; ?>
    						<?php } ?>
    						<?php echo implode( ', ', $tax_array ); ?>
    					</div>
    				<?php } ?>
    			<?php } ?>			
    		<?php } ?>
    	<?php } ?>        

      </div>
      
    </article>
    
<?php } } ?>

<?php if ( wpmobi_has_next_posts_link() ) { ?>
	<?php if ( !hipnews_is_ajax_enabled() ) { ?>
    
        <nav class="post-nav">
			<div class="left nav-button nav-previous"><?php previous_posts_link( __( "&lsaquo; Back", "wpmobi-me" ) ) ?></div>
			<div class="right nav-button nav-next"><?php next_posts_link( __( "Next &rsaquo;", "wpmobi-me" ) ) ?></div>
        </nav>    
    
	<?php } else { ?>
		<a class="load-more-link no-ajax" href="javascript:return false;" rel="<?php echo get_next_posts_page_link(); ?>">
			<?php _e( "Load More Entries&hellip;", "wpmobi-me" ); ?>
		</a>
	<?php } ?>
<?php } ?>