<?php 

$settings = mobileview_get_settings();
$hipnews_slider_disable = $settings->hipnews_slider_disable;
$hipnews_slider_cat = $settings->hipnews_slider_cat;
$hipnews_slider_count = $settings->hipnews_slider_count;

if( !$hipnews_slider_disable && is_front_page() ){

query_posts( array( 'cat' => $hipnews_slider_cat, 'posts_per_page' => $hipnews_slider_count ) );

$first = 0;
global $hipnews_slider_ID; $hipnews_slider_ID = array();

if ( mobileview_have_posts() ) { ?>

    <div class="featured-slider">
      <div class="flexslider">
        <ul class="slides">

<?php 
    while ( mobileview_have_posts() ) { 
        mobileview_the_post(); $first++; global $post;
        $hipnews_slider_ID[] = $post->ID; 
?>

          <li>
            <img src="<?php mobileview_the_post_thumbnail( false,'feat-thumbnail' ); ?>" class="attachment-post-thumbnail slider-thumbnail" alt="post thumbnail" />
            <h3 class="slide-title"><a href="<?php mobileview_the_permalink(); ?>"><?php mobileview_the_title(); ?></a></h3>
          </li>

<?php } ?>

        </ul>
      </div>
    </div><!-- .featured-slider -->
    
<?php }
    wp_reset_query();
}
?>