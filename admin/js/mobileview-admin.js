/* MobileView.Me Admin JS */

/* ZeroClipboard backup key */
var mobileviewClip = 0;

/* Start onReady */
function MobileViewAdminReady() {	

/* Disable caching of AJAX responses */
	jQuery.ajaxSetup ({
	    cache: false
	});
	
/* General Admin Functions */
	MobileViewCookieSetup();
	MobileViewSetupTabSwitching();
	MobileViewTooltipSetup();
	MobileViewSetupPluginCompat();
	MobileViewSetupPluginDismiss();
	MobileViewSavedOrResetNotice();

/* Select & Checkbox toggles */
	MobileViewSetupSelects();
	MobileViewCheckToggle( '#mobileview_show_attached_image', '#setting_mobileview_show_attached_image_location' );
	MobileViewCheckToggle( '#enable_home_page_redirect', '.type-redirect' );
	MobileViewCheckToggle( 'input#mobileview_webapp_enabled', '.section-web-app-settings' );
	MobileViewCheckToggle( '#show_switch_link', '#setting_home_page_redirect_address, #setting_desktop_switch_css' );
	MobileViewCheckToggle( '#debug_log', '#setting_debug_log_level' );
	MobileViewCheckToggle( '#mobileview_show_webapp_notice', '#setting_mobileview_add2home_msg' );
	MobileViewCheckToggle( '#mobileview_webapp_use_loading_img', '#setting_mobileview_webapp_loading_img_location, #setting_mobileview_ipad_webapp_loading_img_location, #setting_webapp-copytext-info, #setting_mobileview_webapp_retina_loading_img_location, #setting_mobileview_ipad_webapp_landscape_loading_img_location' );
	MobileViewCheckToggle( '#menu_show_rss', '#setting_menu_custom_rss_url' );
	MobileViewCheckToggle( '#menu_show_email', '#setting_menu_custom_email_address' );
	MobileViewCheckToggle( '#cache_menu_tree', '#setting_cache_time' );
	MobileViewCheckToggle( '#mobileview_enable_custom_post_types', '#setting_mobileview_show_custom_post_taxonomy, #setting_mobileview_show_custom_post_taxonomy_on_blog' );
	MobileViewCheckToggle( '#include_functions_from_desktop_theme', '#setting_functions_php_inclusion_method' );

	/* Theme Browser Functions */
	MobileViewSetupActivateThemes();
	MobileViewUpdateThemes();
	MobileViewSetupDeleteThemes();
	MobileViewAccountLogin();
	
	/* Code for colorpicker.com window */
	jQuery( 'a#color-picker' ).live( 'click', function( e ) {
		NewWindow( this.href, 'av', '530', '530', 'no', 'no' );
		e.preventDefault();
	}); 

	// Ajax routines
	var ajaxRequests = 0;
	jQuery( 'div.admin-ajax' ).each( function() {
		var divTitle = jQuery( this ).attr( "title" );
		var divId = jQuery( this ).attr( "id" );
		
		if ( ajaxRequests == 0 ) {
			MobileViewAjaxOn();
		}
		
		ajaxRequests++;

		MobileViewAdminAjax( divTitle, {}, function( data ) {
			jQuery( '#' + divId ).html( data );
			
			ajaxRequests--;
			
			if ( ajaxRequests == 0 ) {
				//MobileViewSetupAjax();
			}	
		});
		
	});

	/* Show saving div when form submit, for some postive feedback */
	jQuery( '#colabsplugin-submit' ).live( 'click', function() {
		jQuery( '#saving-ajax' ).fadeIn( 200 );
	});

	/* Reset confirmation */
	jQuery( '#colabsplugin-submit-reset input' ).click( function() {
		var answer = confirm( MobileViewCustom.reset_admin_settings );
		if ( answer ) {
			jQuery.cookie( 'mobileview-tab', '' );
			jQuery.cookie( 'mobileview-list', '' );
		} else {
			return false;	
		}
	});
	
	jQuery( '#colabsplugin-form' ).live( 'click', function() {			
			var totalItems = '';
			var uncheckedMenuItems = jQuery( 'ul.icon-menu li input.checkbox:not(:checked)' );
			jQuery.each( uncheckedMenuItems, function( i, e ) {
				var menuItemTitle = jQuery( e ).attr( 'title' );
				totalItems = totalItems + menuItemTitle + ",";
			});
			
			jQuery( 'input#hidden-menu-items' ).attr( 'value', totalItems );
			return true;
	});
		
	// For handling forum postings
	var newForumPostSubmit = jQuery( '#support-form-submit' );
	if ( newForumPostSubmit.length ) {
		newForumPostSubmit.click( function() {
			var postTitle = jQuery( '#forum-post-title' ).val();
			if ( postTitle ) {
				var postTags = jQuery( '#forum-post-tag' ).val();
				if ( postTags ) {
					var postDesc = jQuery( '#forum-post-content' ).val();
					if ( postDesc ) {
						jQuery( '#support-form-inside' ).animate( { opacity: 0.5 } );
						
						var ajaxParams = {
							title: postTitle,
							tags: postTags,
							desc: postDesc	
						};
						
						// Ajax routine
						MobileViewAdminAjax( 'support-posting', ajaxParams, function( result ) {				
							if ( result == "ok" ) {
								alert( MobileViewCustom.forum_topic_success );
								
								jQuery( '#forum-post-title, #forum-post-tag, #forum-post-content' ).val( '' );
								
								mobileviewLoadSupportPosts();
							} else {
								alert( MobileViewCustom.forum_topic_failed );	
							}
							
							jQuery( '#support-form-inside' ).animate( { opacity: 1.0 } );
						});
					} else {
						alert( MobileViewCustom.forum_topic_text );	
					}
				} else {
					alert( MobileViewCustom.forum_topic_tags );	
				}
			} else {
				alert( MobileViewCustom.forum_topic_title );	
			}
			return false;
		});	
	}
	
	/* Add style colors to input for visual feedback */
	jQuery( '.section-body-style-settings input.text' ).each( function() {
		var inputColor = '#' + jQuery( this ).val();
		jQuery( this ).css( 'color', inputColor ).css( 'border-color', inputColor );
		jQuery( this ).parent().find( 'label' ).css( 'color', inputColor );
	});

	/* Remove Client Mode options for non-developers */
	if ( jQuery( '#mobileview-tabbed-area.developer' ).length == 0 ) {
		jQuery( '.section-clientmode' ).remove();
	}

	/* Move wordpress notification */
	jQuery('#colabsplugin .updated').prependTo('#colabsplugin');

}
/* End onReady */

/* Function to make it easy to add checkbox element toggles */
function MobileViewCheckToggle( checkBox, toggleElement ) {
	jQuery( checkBox ).change( function() {
		if ( jQuery( checkBox ).attr( 'checked' ) ) {
			jQuery( toggleElement ).slideDown();	
		} else {
			jQuery( toggleElement ).hide();
		}
	});	
	jQuery( checkBox ).change();
}

/* Function to take care of select switches */
function MobileViewSetupSelects() {

	jQuery( '#developer_mode' ).change( function() {
		var fadeDiv = jQuery( '#setting_developer_mode_device_class' ).get( 0 );
		switch( jQuery( this ).val() ) {
			case 'off':
				jQuery( fadeDiv ).hide();
				break;	
			default:
				jQuery( fadeDiv ).slideDown();
				break;
		}
	}).change();
	
	jQuery( '#custom_menu_name' ).change( function() {
		var fadeDiv = jQuery( '#setting_menu_sort_order' ).get( 0 );
		switch( jQuery( this ).val() ) {
			case 'none':
				jQuery( fadeDiv ).slideDown();
				break;	
			default:
				jQuery( fadeDiv ).hide();
				break;
		}
	}).change();
	
	jQuery( '#home_page_redirect_target' ).change( function() {
		var fadeDiv = jQuery( '#setting_home_page_redirect_custom' ).get( 0 );
		switch( jQuery( this ).val() ) {
			case 'custom':
				jQuery( fadeDiv ).slideDown();
				break;	
			default:
				jQuery( fadeDiv ).hide();
				break;
		}
	}).change();
	
	
	jQuery( '#mobileview_calendar_icon_bg' ).change( function() {
		switch( jQuery( this ).val() ) {
			case 'cal-custom':
				jQuery( '#setting_mobileview_custom_cal_icon_color' ).slideDown();
				break;	
			default:
				jQuery( '#setting_mobileview_custom_cal_icon_color' ).hide();
				break;
		}
	}).change();
	
	jQuery( '#mobileview_icon_type' ).change( function() {
		var ThumbDiv = '.section-thumbnail-icon-options';
		var customThumbDiv = '#setting_mobileview_custom_field_thumbnail_name';
		var calDiv = '.section-calendar-icon-options';
	
		switch( jQuery( this ).val() ) {
			case 'calendar':
				jQuery( calDiv ).slideDown();
				jQuery( ThumbDiv + ', ' + customThumbDiv ).hide();
				break;
			case 'thumbnails':
				jQuery( ThumbDiv ).slideDown();
				jQuery( calDiv + ', ' + customThumbDiv ).hide();
				break;	
			case 'custom_thumbs':
				jQuery( ThumbDiv + ', ' + customThumbDiv ).slideDown();
				jQuery( calDiv ).hide();
				break;	
			case 'none':
				jQuery( calDiv + ', ' + ThumbDiv + ', ' + customThumbDiv ).hide();
				break;
		}
	}).change();
/*
	jQuery( '#ipad_support' ).change( function() {
		var ipadDivs = '.section-ipad-style-settings, #setting_ipad-menubar-settings';
		switch( jQuery( this ).val() ) {
			case 'none':
				jQuery( ipadDivs ).hide();
				break;	
			default:
				jQuery( ipadDivs ).slideDown();
				break;
		}
	}).change();
	
	jQuery( '#mobileview_ipad_content_bg' ).change( function() {
		switch( jQuery( this ).val() ) {
			case 'custom':
				jQuery( '#setting_mobileview_ipad_content_bg_custom, #setting_mobileview_ipad_background_repeat' ).slideDown();
				break;	
			default:
				jQuery( '#setting_mobileview_ipad_content_bg_custom, #setting_mobileview_ipad_background_repeat' ).hide();
				break;
		}
	}).change();
	
	jQuery( '#mobileview_ipad_sidebar_bg' ).change( function() {
		switch( jQuery( this ).val() ) {
			case 'custom':
				jQuery( '#setting_mobileview_ipad_sidebar_bg_custom' ).slideDown();
				break;	
			default:
				jQuery( '#setting_mobileview_ipad_sidebar_bg_custom' ).hide();
				break;
		}
	}).change();
	*/
	jQuery( '.section-body-style-settings input.text, #setting_ipad-style-settings input.text' ).change( function() {
		var inputColor = '#' + jQuery( this ).val();
		jQuery( this ).css( 'color', inputColor ).css( 'border-color', inputColor );
		jQuery( this ).parent().find( 'label' ).css( 'color', inputColor );
	}).change();
	
	jQuery( '#backup_or_restore' ).change( function() {
		switch( jQuery( this ).val() ) {
			case 'backup':
				jQuery( '#setting_import' ).hide();
				jQuery( '#setting_backup' ).slideDown();			
				MobileViewHandleBackupClipboard();
				break;	
			case 'restore':
				jQuery( '#setting_backup' ).hide();
				jQuery( '#setting_import' ).slideDown();	
				break;	
		}
	}).change();
}

function MobileViewSetupPluginDismiss() {
	var dismissButtons = jQuery( 'a.dismiss-button' );
	if ( dismissButtons.length > 0 ) {
		dismissButtons.live( 'click', function() {
			
			jQuery( this ).parent().parent().fadeOut( 250 );
						
			var ajaxParams = {
				plugin: jQuery( this ).attr( 'id' )
			};
			
			MobileViewAdminAjax( 'dismiss-warning', ajaxParams, function( result ) {
				if ( result == '0' ) {
					jQuery( 'tr#board-warnings' ).remove();
				} else {
					jQuery( 'tr#board-warnings td.box-table-number' ).html( result );	
				}			
				
			});					
			
		e.preventDefault();
		});	
	}
}

function MobileViewCookieSetup() {
	/* Top menu tabs */
	jQuery( '#mobileview-top-menu li a:not([target="_blank"])' ).live( 'click', function( e ) {
		var tabId = jQuery( this ).attr( "id" );
		var url = jQuery(this).attr("href");
		
		jQuery.cookie( 'mobileview-tab', tabId );
		
		jQuery( '.pane-content' ).hide();
		jQuery( '#pane-content-' + tabId ).show();
		
		jQuery( '#pane-content-' + tabId + ' .left-area li a:first' ).click();
		
		jQuery( '#mobileview-top-menu li a' ).removeClass( 'active' ).parent().removeClass( 'active-menu' );
		jQuery( '#mobileview-top-menu li a' ).removeClass( 'round-top-6' );
		
		jQuery( this ).addClass( 'active' ).addClass( 'round-top-6' ).parent().addClass( 'active-menu' );
		e.preventDefault();
	});

	/* Left menu tabs */
	jQuery( '#mobileview-admin-form .left-area li a' ).live( 'click', function( e ) {
		var relAttr = jQuery( this ).attr( "rel" );
		
		jQuery.cookie( 'mobileview-list', relAttr );
		jQuery( ".setting-right-section" ).hide();
		jQuery( "#setting-" + relAttr ).show();
		jQuery( '#mobileview-admin-form .left-area li a' ).removeClass( 'active' );
		jQuery( this ).addClass( 'active' );
		
		if ( jQuery( this ).attr( 'id' ) == 'tab-section-backup-restore' ) {
			MobileViewHandleBackupClipboard();
			mobileviewClip.show();
		} else {
			if ( mobileviewClip ) {
				mobileviewClip.hide();
			}
		}
		
		e.preventDefault();
	});
	
	/* Cookie saving for tabs */
	var tabCookie = jQuery.cookie( 'mobileview-tab' );
	if ( tabCookie ) {
		var tabLink = jQuery( "#mobileview-top-menu li a[id='" + tabCookie + "']" ); 
		jQuery( '.pane-content' ).hide();
		jQuery( '#pane-content-' + tabCookie ).show();	
		tabLink.addClass( 'active' ).addClass( 'round-top-6' );
		tabLink.parent().addClass( 'active-menu' );
		
		var listCookie = jQuery.cookie( 'mobileview-list' );
		if ( listCookie ) {
			var menuLink = jQuery( "#mobileview-admin-form .left-area li a[rel='" + listCookie + "']");
			jQuery( ".setting-right-section" ).hide();
			jQuery( "#setting-" + listCookie ).show();	
			jQuery( '#mobileview-admin-form .left-area li a' ).removeClass( 'active' ).parent().removeClass( 'active-menu' );	
			menuLink.click();			
		} else {
			jQuery( "#mobileview-admin-form .left-area li a:first" ).click();
		}
	} else {
		jQuery( '#mobileview-top-menu li a:first' ).click();
	}	
}

function MobileViewReloadThemeArea() {
	jQuery( '#colabsplugin-form' ).load( MobileViewCustom.plugin_url + ' #colabsplugin', function( d ) {		
		jQuery( document ).unbind().die();
		MobileViewAdminReady();		
		jQuery( '#pane-content-pane-2 .right-area' ).animate( { opacity: 1 } );
		jQuery( '#ajax-saved' ).fadeOut( 200 );
	});				
}

function MobileViewSetupActivateThemes() {	
	jQuery( 'a.activate-theme' ).live( 'click', function( e ) {
		jQuery( '#pane-content-pane-2 .right-area' ).animate( { opacity: .5 } );	
		jQuery( '#ajax-saving' ).fadeIn( 200 ); 
		
		var themeLocation = jQuery( this ).parents('.theme-wrap').find( 'input.theme-location' ).attr( 'value' );
		var themeName = jQuery( this ).parents('.theme-wrap').find( 'input.theme-name' ).attr( 'value' );
		
		var ajaxParams = {
			name: themeName,
			location: themeLocation
		};
		
		MobileViewAdminAjax( 'activate-theme', ajaxParams, function( result ) {
			setTimeout( function() {  
				jQuery( "#ajax-saving" ).hide();
				jQuery( "#ajax-saved" ).show();
			}, 1000);
			setTimeout( function() {  	
				MobileViewReloadThemeArea();
			}, 500 );
		});

		e.preventDefault();
	});	
}

function MobileViewUpdateThemes() {
	
	jQuery( 'a.update-theme' ).live( 'click', function( e ) {
		var $button = jQuery(this);
		var cookie = jQuery(this).data('cookie');
		var themeLocation = jQuery( this ).siblings( 'input.theme-location' ).attr( 'value' );
		var themeName = jQuery( this ).siblings( 'input.theme-name' ).attr( 'value' );
		var $loader = jQuery('.updater-loader');
		var $success_msg = jQuery('.update-notif');
		
		var ajaxParams = {
			cookie: cookie,
			name: themeName,
			location: themeLocation
		};
		
		var ajaxData = {
			action: 'mobileview_ajax',
			mobileview_action: 'update-theme',
			mobileview_nonce: MobileViewCustom.admin_nonce
		};
		for ( name in ajaxParams ) { ajaxData[name] = ajaxParams[name]; }
		
		jQuery.ajax({
			type: 'post',
			url: ajaxurl,
			data: ajaxData,
			beforeSend: function(){
				jQuery('.update-message').slideUp(300, function(){
					$loader.fadeIn(300);
				});
			},
			success: function( result ){
				$loader.fadeOut(300, function(){
					$success_msg.html( result );
					$success_msg.fadeIn(300);
					setTimeout(function(){
						window.tb_remove();
						window.location.href = window.location.href;
					}, 1000);
				});
			}
		});	
		
		e.preventDefault();
	});
	
	jQuery( 'a.cancel-update-theme' ).live( 'click', function( e ) {
		
		setTimeout(function(){
						window.tb_remove();
						window.location.href = window.location.href;
					}, 1000);
		
		e.preventDefault();
	});
}

function MobileViewAccountLogin() {
	
	jQuery( 'a.mobileview-login' ).live( 'click', function( e ) {
		
		var username = jQuery( this ).parents('#TB_ajaxContent').find( '#amember_login' ).attr( 'value' );
		var password = jQuery( this ).parents('#TB_ajaxContent').find( '#amember_pass' ).attr( 'value' );
		
		var ajaxParams = {
			username: username,
			password: password	
		};
		
		MobileViewAdminAjax( 'mobileview-login', ajaxParams, function( result ) {
			if( result != '' ) {
				jQuery('.mobileview-login-form').hide();
				jQuery('.mobileview-update-confirm').show();
				jQuery('.update-theme').data('cookie', result);
			}else{
				jQuery('.login-msg').hide();
				jQuery('.alert-account').show();
			}

		});
		
		e.preventDefault();
	});
	
}

function MobileViewSetupDeleteThemes() {
	jQuery( 'a.delete-theme' ).live( 'click', function( e ) {
		
		var answer = confirm( MobileViewCustom.are_you_sure_delete );
		if ( answer ) {		
		
			// remove clicked item's parent
			jQuery( this ).parents( '.theme-wrap' ).fadeOut( 350 );

			jQuery( '#ajax-saving' ).fadeIn( 200 );

			var themeLocation = jQuery( this ).parents('.theme-wrap').find( 'input.theme-location' ).attr( 'value' );
			var themeName = jQuery( this ).parents('.theme-wrap').find( 'input.theme-name' ).attr( 'value' );
			
			var ajaxParams = {
				name: themeName,
				location: themeLocation	
			};
			
			MobileViewAdminAjax( 'delete-theme', ajaxParams, function( result ) {
				setTimeout( function() {
					jQuery( '#ajax-saving' ).hide();
					jQuery( '#ajax-saved' ).show().fadeOut( 200 );
				}, 750 );
			});			

		}

		e.preventDefault();
	});		
}


function MobileViewSetupPluginCompat() {
	jQuery( 'a.regenerate-plugin-list' ).live( 'click', function( e ) {
		
		MobileViewAdminAjax( 'regenerate-plugin-list', {}, function( result ) {
			jQuery( '.section-plugin-compatibility' ).load( MobileViewCustom.plugin_url + " .section-plugin-compatibility .option-container", function( a ) {
				jQuery( '.section-plugin-compatibility' ).animate( { opacity: 1.0 } );
				
				MobileViewSetupPluginCompat();
			});
		});
		e.preventDefault();
	});	
}

function MobileViewSetupTabSwitching() {
	var adminTabSwitchLinks = jQuery( 'a.mobileview-admin-switch' );
	if ( adminTabSwitchLinks.length ) {
		adminTabSwitchLinks.live( 'click', function( e ) {
			var targetTabClass = '';
			var targetTabSection = '';
			var targetArea = jQuery( this ).attr( 'rel' );

			if ( targetArea == 'themes' ) {
				targetTabClass = 'pane-theme-browser';
				targetTabSection = 'tab-section-installed-themes';				

			} else if ( targetArea == 'plugin-conflicts' ) {
				targetTabClass = 'pane-general';
				targetTabSection = 'tab-section-compatibility';

			} else if ( targetArea == 'ipad-settings' ) {
				targetTabClass = 'pane-active-theme';
				targetTabSection = 'tab-section-ipad-theme-settings';
			}
			
			jQuery( 'a.' + targetTabClass + ',' + 'a#' + targetTabSection ).click();				
			e.preventDefault();
		});
	}
}

function MobileViewDoManageStatus( header, status, all_done, class_to_add ) {
	setTimeout( 
		function() {
			jQuery( '#manage-status' ).removeClass().addClass( class_to_add );
			
			if ( all_done ) {
				jQuery( '#manage-status h6' ).removeClass().addClass( 'end' ).html( header );
			} else {
				jQuery( '#manage-status h6' ).removeClass().html( header );
			}
			
			jQuery( '#manage-status p.info' ).html( status );		
			
			if ( all_done ) {
				jQuery( '#manage-spinner, #manage-set-upload-name' ).hide();	
			} else {
				jQuery( '#manage-spinner' ).show();	
			}										
		},
		250
	);	
}

function MobileViewDoTreeDisable() {
	jQuery( 'ul.icon-menu input.checkbox' ).attr( 'disabled', false );
	
	var enabledCheckboxes = jQuery( 'ul.icon-menu input.checkbox:not(:checked)' );
	enabledCheckboxes.each( function() {
		var parentItems = jQuery( this ).parents( 'li' );
		jQuery( parentItems.get( 0 ) ).find( 'ul input.checkbox' ).attr( 'disabled', true );
	});
}

function MobileViewSetupIconDragDrag() {
	jQuery( '#mobileview-icon-list img' ).draggable({
		revert: true,
		cursorAt: { top: 0 },
		revertDuration: 150
	});	
	
	jQuery( '#mobileview-icon-menu div.icon-drop-target' ).droppable({
		drop: function( event, ui ) {
			var droppedDiv = jQuery( this );
			var sourceIcon = ui.draggable.attr( 'src' );
			var menuId = jQuery( this ).attr( "title" );
			var parentListItem = jQuery( this ).parent();
			
			var imageHtml = '<img src="' + sourceIcon + '" />';
			
			droppedDiv.html( imageHtml ).addClass( 'noborder' );			
		
			if ( jQuery( '#mobileview-icon-list ul li:first' ).hasClass('dark' ) ) {
				droppedDiv.addClass( 'dark' );
			} else {
				droppedDiv.removeClass( 'dark' );
			}	
			
			var ajaxParams = {
				title: droppedDiv.attr( 'title' ),
				icon: sourceIcon
			};
			
			MobileViewAdminAjax( 'set-menu-icon', ajaxParams, function( result ) {
				
				if ( parentListItem.hasClass( 'default-prototype' ) ) {
					jQuery( 'div.icon-drop-target.default' ).html( imageHtml );
					
					if ( jQuery( '#mobileview-icon-list ul li:first' ).hasClass('dark' ) ) {
						jQuery( 'div.icon-drop-target.default' ).addClass( 'dark' );
					} else {
						jQuery( 'div.icon-drop-target.default' ).removeClass( 'dark' );
					}
				}	
				
				droppedDiv.removeClass( 'default' );
			});
			
			MobileViewSetupIconDragDrag();
		},
		hoverClass: 'active-drop'
	});		
	
	jQuery( '#mobileview-icon-menu div.icon-drop-target' ).draggable({ 
		revert: true,
		cursorAt: { top: 0 },
		revertDuration: 250,
		scope: 'trash'
	});		
	
}

function MobileViewAdminAjax( actionName, actionParams, callback ) {	
	var ajaxData = {
		action: 'mobileview_ajax',
		mobileview_action: actionName,
		mobileview_nonce: MobileViewCustom.admin_nonce
	};
	
	for ( name in actionParams ) { ajaxData[name] = actionParams[name]; }

	jQuery.post( ajaxurl, ajaxData, function( result ) {
		callback( result );	
	});	
}

function MobileViewSetupLicenseArea() {		
	jQuery( 'a.mobileview-remove-license' ).live( 'click', function( e ) {
		var siteToRemove = jQuery( this ).attr( 'rel' );
		jQuery( '#mobileview-license-area' ).animate( { opacity: 0.4 } );		
		var ajaxParams = {
			site: siteToRemove	
		};
		MobileViewAdminAjax( 'remove-license', ajaxParams, function( data ) { 
			window.location.reload();
		});
		
		e.preventDefault();
	});
	
	jQuery( 'a.mobileview-add-license' ).bind( 'click', function( e ) {
		jQuery( this ).unbind();
		jQuery( 'a.mobileview-add-license' ).animate( { opacity: 0.5 } ).text( MobileViewCustom.activating_license );
		MobileViewAdminAjax( 'activate-license', {}, function( data ) { 
			window.location.reload();
		});
		e.preventDefault();
	});
	
	jQuery( '#reset-licenses' ).bind( 'click', function( e ) {
		var answer = confirm( MobileViewCustom.reset_license_text );
		if ( answer ) {
			MobileViewAdminAjax( 'reset-all-licenses', {}, function( data ) { 
				if ( data == 'ok' ) {
					window.location.reload();
				} else {
					alert( MobileViewCustom.reset_license_error );	
				}
			});
		}
		e.preventDefault();
	});
}

function MobileViewTooltipSetup() {
	doClcTooltip( 'a.mobileview-tooltip', '#mobileview-tooltip', 10, -40 );
}

function MobileViewAjaxOn() {
//	jQuery( '#colabsplugin' ).append( '<div id="mobileview-saving"></div>' );
	jQuery( '#ajax-loading' ).fadeIn( 200 );
}

function MobileViewAjaxOff() {
	jQuery( '#ajax-loading' ).fadeOut( 200 );	
}

function MobileViewHandleBackupClipboard() {	
	if ( mobileviewClip == 0 && jQuery( '#copy-text-button' ).is( ':visible' ) ) {
		mobileviewClip = new ZeroClipboard.Client();

		mobileviewClip.glue( 'copy-text-button' );
		
		var textToCopy = jQuery( '.type-backup textarea' ).text();
		mobileviewClip.setText( textToCopy );

		mobileviewClip.addEventListener( 'complete', function( client, text ) {
			alert( MobileViewCustom.copying_text );
		});	
	}
}

function MobileViewSavedOrResetNotice() {
	if ( jQuery( '#colabsplugin-form .saved' ).length ) {
		setTimeout( function() {
			jQuery( '#colabsplugin-form .saved' ).fadeOut( 200 );
		}, 1000 );
	}

	if ( jQuery( '#colabsplugin-form .reset' ).length ) {
		setTimeout( function() {
			jQuery( '#colabsplugin-form .reset' ).fadeOut( 200 );
		}, 1000 );
	}
}

/* New jQuery function opacityToggle() */
jQuery.fn.opacityToggle = function( speed, easing, callback ) { 
	return this.animate( { opacity: 'toggle' }, speed, easing, callback ); 
}

jQuery( document ).ready( function() { MobileViewAdminReady(); } );

/* Mobile Tab Menu
------------------------------------------------------------------- */
(function($){
	$('html').on('click', function(){
		if( $('.dropdown-mobile').is(':visible') ) {
			$('.pane-content:visible .left-area').hide();
		} else {
			$('.pane-content:visible .left-area').show();
		}
	});
	$('html').on('click', '.dropdown-mobile', function(e){
		e.preventDefault();
		e.stopPropagation();
		$('.pane-content:visible .left-area').toggle();
	});
})(jQuery);



/* Use WordPress uploader
------------------------------------------------------------------- */
(function($){
	$(document).ready(function(){
		var formfield,
				send_to_editor = window.send_to_editor;
		
		// Click Event
		$('.upload_button').click(function(event){
			event.preventDefault();
			formfield = $(event.currentTarget).prev('input');
			if( typeof wp !== "undefined") {
				wp.media.editor.id = function(id) { return ''; }
				wp.media.editor.open();
			} else {
				tb_show('', 'media-upload.php?type=image&TB_iframe=true');
			}
		});
		
		// Upload callback
		window.send_to_editor = function(result){
			var imgurl = $('img',result).attr('src');
			formfield.val(imgurl);
			
			if( typeof wp !== "undefined") {
			} else {
				tb_remove();
			}
			//window.send_to_editor = send_to_editor;
		}
		
		/* Twitter Stream ticker
		----------------------------------------------------------------- */
		var $t_stream = $('.mobileview_twitter_stream'),
				$t_stream_list = $t_stream.find('ul');

		// Only run this script when twitter feed fetched
		if( $t_stream_list.length > 0 ) {
			var $item = $t_stream_list.find('li'),
					item_length = $item.length,
					current_visible = $item.filter(':visible').index();

			// Hide all list except the first one
			$t_stream_list.find('li:not(:first)').hide();
			setInterval(function(){
				var next_visible = current_visible + 1;
				if( next_visible > item_length - 1 ) {
					next_visible = 0;
				}
				current_visible = next_visible;
				$item.hide();
				$item.eq(next_visible).fadeTo(250, 1);
			}, 5000);
		}
		
		$('.mobileview-color-field').wpColorPicker();
	});
})(jQuery);


/* Sticky Header
------------------------------------------------------------------- */
(function($){
	// Trigger save or reset button when user clicked
	// the button on the header
	$('html').on('click', '.header-save-button a', function(){
		var $button = $(this),
				trigger_button;

		if( $button.hasClass('button-save-trigger') ) {
			trigger_button = '#colabsplugin-submit';
		} else {
			trigger_button = '#colabsplugin-submit-reset';
		}

		$(trigger_button).find('button').trigger('click');
	});

	$(document).ready(function(){
		if( $('.mobile-view-admin-header').length > 0 ) {

			var $body = $('body'),
					$admin_header = $('.mobile-view-admin-header');
					header_height = $admin_header.height(),
					header_pos = $admin_header.position().top;

			$(window).on('scroll', function(e){
				if( $(this).scrollTop() >= header_pos ) {
					$body.addClass('header-fixed');

					// Set position for left menu
					$('.pane-content:visible .left-area').css({
						top: $(this).scrollTop() - header_pos
					});
				}

				else {
					$body.removeClass('header-fixed');
				}
			});

		}
	});
})(jQuery);

