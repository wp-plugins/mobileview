<?php global $wpmobi; ?>
<?php $news = $wpmobi->get_latest_news(); ?>
<ul>
<?php if ( $news ) { ?>
	<?php foreach ( $news as $item ) { ?>
    <li><a href="<?php echo $item->get_permalink(); ?>" target="_blank"><?php echo $item->get_title(); ?></a></li>
    <?php } ?>
<?php } else { ?>
	<li class="no-listings"><?php _e( "The ColorLabs Blog timed out.", "wpmobi-me" ); ?></li>
<?php } ?>
</ul>