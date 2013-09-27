<!-- Code for Using Thumbnails  -->
<div class="entry-image">
	<?php if(is_single()||is_page()):?>
		<img src="<?php mobileview_the_post_thumbnail(false,'large'); ?>" alt="post thumbnail" />
	<?php else:?>
		<img src="<?php mobileview_the_post_thumbnail(); ?>" alt="post thumbnail" />
	<?php endif;?>
</div>
	
