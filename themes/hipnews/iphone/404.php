<?php get_header(); ?>	

	<div class="post four-oh-four-title">
		<h2><?php _e( "Page or Post Not Found", "wpmobi-me" ); ?></h2>
	</div>
	
	<div class="post four-oh-four-content">
		<?php wpmobi_the_404_message(); ?>
	</div>		

<?php get_footer(); ?>