				  </div><!-- .post-list -->
                </div><!-- .main-container -->
					
				<?php do_action( 'mobileview_body_bottom' ); ?>

    <div class="footer">
        <h3 class="footer-branding">
            <a href="<?php mobileview_bloginfo( 'url' ); ?>"><?php mobileview_bloginfo( 'site_title' ); ?></a>
        </h3>
        <div class="footer-nav container copyrights">
            <?php show_mobileview_message_in_footer();?>
        </div><!-- .footer-nav -->
        <div class="<?php mobileview_footer_classes(); ?> switcher container">
            <?php if ( mobileview_show_switch_link() && !hipnews_is_web_app_mode() ) { ?>
				<div id="switch" class="mobileview-desktop-switch switcher">
					<div id="mobileview-main-top">
						<h3>
							<img src="http://demo.colorlabsproject.com/colabs1/wp-content/plugins/mobileview/admin/images/logo.png">
							<a href="http://colorlabsproject.com/plugins/mobileview/" target="_blank" title="ColorLabs &amp; Company">MobileView</a>
						</h3>
					</div>
					
					<?php
					
					$settings = mobileview_get_settings();
					$get_colour = $settings->switch_colour;
					
					if (!empty($get_colour)){
					echo '<style>
							.check-ios:checked ~ span{
								background-color:'.$get_colour.'
							}
						</style>';
					}
					?>
					<div class="holder">
						<input type="checkbox" id="check_s" name="check" class="check-ios" checked data-url="<?php mobileview_the_mobile_switch_link(); ?>"/>
						<label for="check_s"></label>
						<span></span>
					</div>
				
				</div>
			<?php } ?>
        </div>
        <?php do_action( 'mobileview_advertising_bottom' ); ?>
    </div><!-- .footer -->
    <!-- <?php echo 'Built with MobileView ' . MOBILEVIEW_VERSION; ?> -->

			</div> <!-- #inner-ajax -->
		</div> <!-- #outer-ajax -->
		</div><!-- Wrapperr -->
    <div class="custom-footer"><?php mobileview_footer(); ?></div>
    </body>
</html>						
