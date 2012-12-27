<?php global $wpmobi; ?>
<?php $forum_posts = $wpmobi->clc_api->get_support_posts( 5 ); ?>
<ul>
<?php if ( $forum_posts ) { ?>
	<?php foreach ( $forum_posts as $forum_posting ) { ?>
    <li>
        <?php echo $forum_posting->topic_slug; ?>" target="_blank"><?php echo $forum_posting->topic_title; ?> <?php echo sprintf( __( 'by %s', 'wpmobi-me' ), '<em>' . htmlentities( $forum_posting->topic_poster_name ), ENT_COMPAT, "UTF-8" ) . '</em>'; ?>
    </li>
    <?php } ?>
<?php } else { ?>
	<li class="no-listings"><?php _e( "The ColorLabs Forum timed out.", "wpmobi-me" ); ?></li>
<?php } ?>
</ul>