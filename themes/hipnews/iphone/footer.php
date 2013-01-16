				  </div><!-- .post-list -->
                </section><!-- .main-container -->
					
				<?php do_action( 'wpmobi_body_bottom' ); ?>

    <footer class="footer">
        <h3 class="footer-branding">
            <a href="<?php wpmobi_bloginfo( 'url' ); ?>"><?php wpmobi_bloginfo( 'site_title' ); ?></a>
        </h3>
        <div class="footer-nav container copyrights">
            <?php wpmobi_footer(); ?>
        </div><!-- .footer-nav -->
        <div class="<?php wpmobi_footer_classes(); ?> switcher container">
            <?php if ( wpmobi_show_switch_link() && !hipnews_is_web_app_mode() ) { ?>
    			<div id="switch" class="wpmobi-desktop-switch clearfix">
    				<span class="switch-text">
    					<?php _e( "Mobile Theme", "wpmobi-me" ); ?>
    				</span>
    				<div class="switcher-wrapper" title="<?php wpmobi_the_mobile_switch_link(); ?>">
                        <div class="switcher-inner">
        					<span class="on active"></span>
                            <span class="switcher-btn"></span>               
        					<span class="off"></span>
                        </div>               
    				</div>
    			</div>
    		<?php } ?>
        </div>
        <?php do_action( 'wpmobi_advertising_bottom' ); ?>
    </footer><!-- .footer -->
    <!-- <?php echo 'Built with MobileView ' . WPMOBI_VERSION; ?> -->

			</div> <!-- #inner-ajax -->
		</div> <!-- #outer-ajax -->
    
    </body>
</html>						
