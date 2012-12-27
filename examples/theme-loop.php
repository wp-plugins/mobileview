<?php while ( wpmobi_have_posts() ) { ?>
	<?php wpmobi_the_post(); ?>
	
	<div class="<?php wpmobi_post_classes(); ?>">
	
		<h1><?php wpmobi_the_title(); ?></h1>
	
		<!-- The Date Contents -->
		<div class="<?php wpmobi_date_classes(); ?>">
			<?php wpmobi_the_time( 'F jS, Y' ); ?>
		</div>

		<!-- Post Content Goes Here -->
	</div>
<?php }