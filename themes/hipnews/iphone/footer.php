				  </div><!-- .post-list -->
                </section><!-- .main-container -->
					
				<?php do_action( 'wpmobi_body_bottom' ); ?>

    <footer class="footer">
        <h3 class="footer-branding">
            <a href="<?php wpmobi_bloginfo( 'url' ); ?>"><?php wpmobi_bloginfo( 'site_title' ); ?></a>
        </h3>
        <div class="footer-nav container">
    		<?php if ( wpmobi_show_switch_link() && !hipnews_is_web_app_mode() ) { ?>
    			<div id="switch" class="wpmobi-desktop-switch">
    				<span class="switch-text">
    					<?php _e( "Mobile Theme", "wpmobi-me" ); ?>
    				</span>
    				<div title="<?php wpmobi_the_mobile_switch_link(); ?>">
    					<span class="on active"></span>
    					<span class="off"></span>
    				</div>
    			</div>
    		<?php } ?>
        </div><!-- .footer-nav -->
        <div class="<?php wpmobi_footer_classes(); ?> copyrights container">
            <?php wpmobi_footer(); ?>
        </div>
        <?php do_action( 'wpmobi_advertising_bottom' ); ?>
    </footer><!-- .footer -->
    <!-- <?php echo 'Built with MobileView ' . WPMOBI_VERSION; ?> -->

			</div> <!-- #inner-ajax -->
		</div> <!-- #outer-ajax -->
    
    </body>
</html>						
