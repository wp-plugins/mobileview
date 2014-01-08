<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php mobileview_bloginfo('html_type'); ?>; charset=<?php mobileview_bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width">
	<title><?php mobileview_title(); ?></title>
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
	<?php mobileview_head(); ?>
  <link href='http://fonts.googleapis.com/css?family=Rokkitt:700' rel='stylesheet' type='text/css'> 
	<link type="text/css" rel="stylesheet" media="screen" href="<?php hipnews_the_static_css_url( 'iphone' ); ?>?version=<?php hipnews_the_static_css_version( 'iphone' ); ?>"></link>
</head>
<?php flush(); ?>
<body class="<?php mobileview_body_classes();?>">

	<div id="top-sliding-menu">  
		<?php get_search_form();?>
		<?php 
			$settings = mobileview_get_settings();
			wp_nav_menu( array( 
			'menu' => $settings->custom_menu_name, 
			'container_class' => '', 
			'container' => false,
			'menu_class' => 'menu topmenu'
		) );?>
	</div>
<!-- New noscript check, we need js on always folks to do cool stuff -->
		<noscript>
			<div id="noscript">
				<h2><?php _e( "Notice", "mobileviewlang" ); ?></h2>
				<p><?php _e( "JavaScript is currently off.", "mobileviewlang" ); ?></p>
				<p><?php _e( "Turn it on in browser settings to view this mobile website.", "mobileview" ); ?></p>
			</div>
		</noscript>
    <div class="header container">
		
			<?php if ( mobileview_has_welcome_message() && !hipnews_is_web_app_mode() ) { ?>
				<div id="welcome-message">
					<?php mobileview_the_welcome_message(); ?>
					<a href="<?php mobileview_the_welcome_message_dismiss_url(); ?>" id="close-msg"><img src="<?php echo MOBILEVIEW_URL . '/includes/images/close.png' ?>" /></a>	
				</div>
			<?php } ?>
		<div class="wrappe">
			<!-- If you disable the menu this menu button won't show, so you'll have to roll your own! -->
			<a class="collapse-toggle collapsed" href="#top-sliding-menu">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
				<h1 class="branding"><a id="logo-title" href="<?php mobileview_bloginfo( 'url' ); ?>">
					<?php mobileview_bloginfo( 'site_title' ); ?>
				</a></h1>		
		</div>
    </div><!-- .header -->
	
	<div class="outer-wrapper">
    <?php include_once( 'featured-slider.php' ); ?>

	<div id="outer-ajax">
		<div id="inner-ajax">
			
			<?php do_action( 'mobileview_body_top' ); ?>
		
			<div class="container main-container">
                <div class="<?php if(is_single() || is_page() || is_404()){ echo 'content-left'; }else{ echo 'post-list row'; } ?>">