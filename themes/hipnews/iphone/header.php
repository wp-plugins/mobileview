<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php wpmobi_bloginfo('html_type'); ?>; charset=<?php wpmobi_bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width">
	<title><?php wpmobi_title(); ?></title>
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
	<?php wpmobi_head(); ?>
  <link href='http://fonts.googleapis.com/css?family=Rokkitt:700' rel='stylesheet' type='text/css'> 
	<link type="text/css" rel="stylesheet" media="screen" href="<?php hipnews_the_static_css_url( 'iphone' ); ?>?version=<?php hipnews_the_static_css_version( 'iphone' ); ?>"></link>
</head>
<?php flush(); ?>
<body class="<?php wpmobi_body_classes(); ?>">
<!-- New noscript check, we need js on always folks to do cool stuff -->
<noscript>
	<div id="noscript">
		<h2><?php _e( "Notice", "wpmobi-me" ); ?></h2>
		<p><?php _e( "JavaScript is currently off.", "wpmobi-me" ); ?></p>
		<p><?php _e( "Turn it on in browser settings to view this mobile website.", "wpmobi" ); ?></p>
	</div>
</noscript>
	<?php if ( wpmobi_has_welcome_message() && !hipnews_is_web_app_mode() ) { ?>
		<div id="welcome-message">
			<?php wpmobi_the_welcome_message(); ?>
			<a href="<?php wpmobi_the_welcome_message_dismiss_url(); ?>" id="close-msg"><?php _e( "Close Message", "wpmobi-me" ); ?></a>	
		</div>
	<?php } ?>

    <header class="header container">
    
		<?php if ( hipnews_mobile_has_logo() ) { ?>
			<a id="custom-logo-title" href="<?php wpmobi_bloginfo( 'url' ); ?>">&nbsp;</a>
				<?php hipnews_mobile_logo_img(); ?>
		<?php } else { ?>
            <h1 class="branding"><a id="logo-title" href="<?php wpmobi_bloginfo( 'url' ); ?>">
				<?php wpmobi_bloginfo( 'site_title' ); ?>
			</a></h1>
		<?php } ?>
                
        <!-- If you disable the menu this menu button won't show, so you'll have to roll your own! -->
        <?php if ( wpmobi_has_menu() ) { ?>
            <button class="collapse-toggle collapsed">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- This brings in menu.php // remove it and the whole menu won't show at all -->
            <?php include_once( 'tab-bar.php' ); ?>
            
        <?php } ?>

    </header><!-- .header -->

    <?php include_once( 'featured-slider.php' ); ?>

	<div id="outer-ajax">
		<div id="inner-ajax">
			
			<?php do_action( 'wpmobi_body_top' ); ?>
		
			<section class="container main-container">
                <div class="<?php if(is_single()){ echo 'content-left'; }else{ echo 'post-list row'; } ?>">
				<?php do_action( 'wpmobi_advertising_top' ); ?>