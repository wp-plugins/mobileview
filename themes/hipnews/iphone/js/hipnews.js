/* WPMobi HipNews JS */
/* This file holds all the default jQuery & Ajax functions for the HipNews theme on mobile */
/* Description: JavaScript for the HipNews theme on mobile */
/* Required jQuery version: 1.5.2+ */

var hipnewsJS = jQuery.noConflict();
var WPMobiWebApp = navigator.standalone;
var iOS5 = navigator.userAgent.match( 'OS 5_' );

/* For debugging Web-App mode in a browser */
//var WPMobiWebApp = true;

/* see http://cubiq.org/add-to-home-screen for additional options */
var addToHomeConfig = {
	animationIn: 'bubble',
	animationOut: 'drop',
	startDelay: 550,								// milliseconds
	lifespan: 1000*60,							// milliseconds  (set to: 30 secs)
	expire: 60*24*WPMobi.expiryDays,	// minutes (set in admin settings)
	bottomOffset: 14,
	hipnewsIcon: true,
	arrow: true,
	message: WPMobi.add2home_message
};

/*
If it's a device that supports onhipnewsstart & onhipnewsend, then we'll use the faster event handlers. 
Desktop browsers use click (onhipnewsstart/end is faster on iOS & Android).
*/
if ( typeof onhipnewsstart != 'undefined' && typeof onhipnewsend != 'undefined' ) { 
	var hipnewsStartOrClick = 'hipnewsstart', hipnewsEndOrClick = 'hipnewsend'; 
} else {
	var hipnewsStartOrClick = 'click', hipnewsEndOrClick = 'click'; 
};

/* Try to get out of frames! */
if ( window.top != window.self ) { 
	window.top.location = self.location.href
}

function doHipnewsReady() {

	/*  Header #tab-bar tabs */
	hipnewsJS( function() {
	    var tabContainers = hipnewsJS( '#menu-container > div' );
	
	    hipnewsJS( '#tab-inner-wrap-left a' ).click( function() {
	        tabContainers.hide().filter( this.hash ).show();
	    	hipnewsJS( '#tab-inner-wrap-left a' ).removeClass( 'selected' );
	   		hipnewsJS( this ).addClass( 'selected' );
	        return false;
	    }).filter( ':first' ).click();
	});

	hipnewsJS( 'a#header-menu-toggle' ).click( function() {
//		if ( WPMobiWebApp && iOS5 ) {
//			hipnewsJS( this ).toggleClass( 'menu-toggle-open' );
//			hipnewsJS( '#main-menu' ).toggleClass( 'open' ).toggleClass( 'closed' );	
//			return false;		
//		} else {
			hipnewsJS( this ).toggleClass( 'menu-toggle-open' );
			hipnewsJS( '#main-menu' ).opacityToggle( 380 );	
			return false;
//		}
	});	

	hipnewsJS( '#main-menu ul li a:not(li.has_children > a)' ).bind( 'click', function(){
		hipnewsJS( this ).parent().addClass( 'active' );
	});

	hipnewsJS( 'a#tab-search' ).click( function() {
		hipnewsJS( '#search-bar' ).toggleClass( 'show-search' );
		hipnewsJS( this ).toggleClass( 'search-toggle-open' );
		return false;
	});	

	/* Filter parent link href's and make them toggles for thier children */
	hipnewsJS( '#main-menu' ).find( 'li.has_children ul' ).hide();
	
	hipnewsJS( '#main-menu ul li.has_children > a' ).click( function() {
		hipnewsJS( this ).parent().children( 'ul' ).opacityToggle( 380 );
		hipnewsJS( this ).toggleClass( 'arrow-toggle' );
		hipnewsJS( this ).parent().toggleClass( 'open-tree' );
		return false;
		});

	/* Try to make imgs and captions nicer in posts */	
		hipnewsJS( '.content img, .content .wp-caption' ).each( function() {
			if ( !hipnewsJS( this ).hasClass( 'aligncenter' ) && hipnewsJS( this ).width() > 105 ) {
				hipnewsJS( this ).addClass( 'aligncenter' );
			}
		});

	/* Pesky plugin image protect stuff */	
	hipnewsJS( '.single .p3-img-protect' ).each( function() {
		hipnewsJS( '.p3-overlay' ).remove();
		var insideContent = hipnewsJS( this ).html();
		hipnewsJS( this ).replaceWith( insideContent );
	});

	/* .active styling to mimic default iOS functionality */
		hipnewsJS( '#action-buttons a, .comment-buttons a, a#cancel-comment-reply-link, a.com-toggle' ).bind( hipnewsStartOrClick, function() {
			hipnewsJS( this ).addClass( 'active' );
		}).bind( hipnewsEndOrClick, function() {
			hipnewsJS( this ).removeClass( 'active' );
		});

	/* Add a rounded top left corner to the first gravatar in comments, removes double bordering */
	hipnewsJS( '.commentlist li :first, .commentlist img.avatar:first' ).addClass( 'first' );

	hipnewsJS( 'a.com-toggle' ).bind( 'click', function() {
		hipnewsJS( 'ol.commentlist' ).toggleClass( 'hidden' );
		hipnewsJS( 'img#com-arrow' ).toggleClass( 'com-arrow-down' );
		return false;
	});
		
	/* Detect window width and add corresponding 'portrait' or 'landscape' classes onload */
	if ( hipnewsJS( window ).width() >= 480 ) { 
		hipnewsJS( 'body' ).addClass( 'landscape' );
	} else {
		hipnewsJS( 'body' ).addClass( 'portrait' );
	}

	/* Detect orientation change and add or remove corresponding 'portrait' or 'landscape' classes */
	window.onorientationchange = function() {
		var scrollPosition = hipnewsJS( 'body' ).scrollTop() + 1;
		var orientation = window.orientation;
			switch( orientation ) {
				//Portrait
				case 0:
				case 180:
				hipnewsJS( 'body' ).addClass( 'portrait' ).removeClass( 'landscape' );
				window.scrollTo( 0, scrollPosition,100 );
				break;
				//Landscape
				case 90:
				case -90:
				hipnewsJS( 'body' ).addClass( 'landscape' ).removeClass( 'portrait' );
				window.scrollTo( 0, scrollPosition,100 );
				break;
				default:
				hipnewsJS( 'body' ).addClass( 'portrait' ).removeClass( 'landscape' );				
			}
	}
	
	// var header = hipnewsJS( '#header' ).get(0);
	// header.addEventListener( 'hipnewsmove', hipnewshipnewsMove, false );
    
    // Check to make sure the menu bar is in the DOM
    if ( hipnewsJS( '#tab-bar' ).length ) {
        var tabBar = hipnewsJS( '#tab-bar' ).get(0);
        tabBar.addEventListener( 'hipnewsmove', hipnewshipnewsMove, false );
    }
	
	/* Ajaxify commentform */
	var postURL = document.location;
	var CommentFormOptions = {
		beforeSubmit: function() {
			hipnewsJS( '#commentform textarea' ).addClass( 'loading' );			
		},
		success: function() {
			hipnewsJS( '#commentform textarea' ).removeClass( 'loading' ).addClass( 'success' );			
			alert( WPMobi.comment_success );
			setTimeout( function () { 
				hipnewsJS( '#commentform textarea' ).removeClass( 'success' );
			}, 1500 );
//			hipnewsJS( 'ol.commentlist' ).load( postURL + ' ol.commentlist > li', function(){ 
//				comReplyArrows();
//			});
		},
		error: function() {
			hipnewsJS( '#commentform textarea' ).removeClass( 'loading' ).addClass( 'error' );
			alert( WPMobi.comment_failure );
			setTimeout( function () { 
				hipnewsJS( '#commentform textarea' ).removeClass( 'error' );
			}, 3000 );
		},
		resetForm: true,
		timeout:   10000
	} 	//end options
	
	if ( hipnewsJS.isFunction( hipnewsJS.fn.ajaxForm ) ) {
		hipnewsJS( '#commentform' ).ajaxForm( CommentFormOptions );
	}

	loadMoreEntries();
	loadMoreComments();
	comReplyArrows();
	welcomeMessage();
	webAppLinks();
	webAppOnly();
	
	hipnewsJS( 'a.login-req, a.comment-reply-login' ).bind( 'click', function() {
		hipnewsJS( 'a#header-menu-toggle, a#tab-login' ).click();
		scrollTo( 0,0,1 );
		return false;
	});
			
	/* Hide addressBar */
	if ( hipnewsJS( 'body' ).hasClass( 'hide-addressbar' ) ) {
		hipnewsJS( window ).load( function() {
		    setTimeout( function(){ scrollTo( 0, 0 ) }, 1 );
		});
	}
	
	/*Single post Back to Top */
	hipnewsJS( 'a.back-to-top' ).click( function(){
	    hipnewsJS( 'body' ).animate( { scrollTop: hipnewsJS( 'html' ).offset().top }, 750 );		
		return false;
	});
	
	/*Single postSkip to Comments */
	hipnewsJS( 'a.middle-link' ).click( function(){
	    hipnewsJS( 'body' ).animate( { scrollTop: hipnewsJS( '.nav-bottom' ).offset().top }, 750 );		
		return false;
	});

	/* add dynamic automatic video resizing via fitVids */

	var videoSelectors = [
		"iframe[src^='http://player.vimeo.com']",
		"iframe[src^='http://www.youtube.com']",
		"iframe[src^='http://www.kickstarter.com']",
		"object",
		"embed",
		"video"
	];
	
	var allVideos = hipnewsJS( '.content' ).find(videoSelectors.join(','));
	
	hipnewsJS( allVideos ).each( function(){ 
		hipnewsJS( this ).unwrap().addClass( 'wpmobi-videos' ).parentsUntil( '.content', 'div:not(.fluid-width-video-wrapper), span' ).removeAttr( 'width' ).removeAttr( 'height' ).removeAttr( 'style' );
	});

	hipnewsJS( '.content' ).fitVids();

	/* Set tabindex automagically */
	hipnewsJS( function(){
	var tabindex = 1;
		hipnewsJS( 'input, select, textarea' ).each( function() {
			if ( this.type != "hidden" ) {
				var inputToTab = hipnewsJS( this );
				inputToTab.attr( 'tabindex', tabindex );
				tabindex++;
			}
		});
	});
	
	/* New Toggle Switch JS */
	var onLabel = WPMobi.toggle_on, offLabel = WPMobi.toggle_off;
	hipnewsJS( '.on' ).text( onLabel );
	hipnewsJS( '.off' ).text( offLabel );
	
	hipnewsJS( '#switch .switcher-wrapper' ).bind( hipnewsEndOrClick, function(){ 
		var switchURL = hipnewsJS( this ).attr( 'title' );
		jQuery(this).toggleClass('active');
		hipnewsJS( '.on' ).toggleClass( 'active' );
		hipnewsJS( '.off' ).toggleClass( 'active' );
		setTimeout( function () { window.location = switchURL }, 1000 );
		return false;
	});

	hipnewsHandleShortcodes();
}
/* End Document Ready */

function hipnewshipnewsMove( e ){
	e.preventDefault();
}

/* New jQuery function opacityToggle() */
hipnewsJS.fn.opacityToggle = function( speed, easing, callback ) { 
	return this.animate( { opacity: 'toggle' }, speed, easing, callback ); 
}

/* New jQuery function viewportCenter() */
jQuery.fn.viewportCenter = function() {
    this.css( 'position', 'absolute' );
    this.css( 'top', ( ( hipnewsJS( window ).height() - this.outerHeight() ) / 3 ) + hipnewsJS( window ).scrollTop() + 'px' );
    this.css( 'left', ( ( hipnewsJS( window ).width() - this.outerWidth() ) / 2 ) + hipnewsJS( window ).scrollLeft() + 'px' );
	this.show();
    return this;
}

function welcomeMessage() {
	if ( !WPMobiWebApp ) {	
		hipnewsJS( '#welcome-message' ).show();
		hipnewsJS( 'a#close-msg' ).bind( 'click', function() {
			WPMobiCreateCookie( 'wpmobi_welcome', '1', 365 );
			hipnewsJS( '#welcome-message' ).fadeOut( 350 );
			return false;
		});
	}
}

function webAppLinks() {
	if ( WPMobiWebApp ) {
		// The New Sauce ( Nobody makes tasty gravy like mom )		
		// bind to all links, except UI controls and such
		var webAppLinks = hipnewsJS( 'a' ).not(
			'.no-ajax, .email a, .feed a, a#header-menu-toggle, .has_children > a, a.load-more-link, .load-more-comments-link a, .GTTabs a' 
		);

 		webAppLinks.each( function(){
			var targetUrl = hipnewsJS( this ).attr( 'href' ), targetLink = hipnewsJS( this );
			var localDomain = location.protocol + '//' + location.hostname,  rootDomain = location.hostname.split( '.' ), masterDomain = rootDomain[1] + '.' + rootDomain[2];
//			var localDomain = location.hostname.match(/\.?([^.]+)\.[^.]+.?$/)[1];	
//			var localDomain = location.hostname;	
	
			// link is local, but set to be non-mobile
			if ( typeof wpmobi_ignored_urls != 'undefined' ) {
				hipnewsJS.each( wpmobi_ignored_urls, function( i, val ) {
					if ( targetUrl.match( val ) ) {
						targetLink.addClass( 'ignored' );
					}
				});
			}
			
		   // filetypes, images class name additions
	       if ( targetUrl.match( ( /[^\s]+(\.(pdf|numbers|pages|xls|xlsx|doc|docx|zip|tar|gz|csv|txt))$/i ) ) ) {
				targetLink.addClass( 'external' );
	       } else if ( targetUrl.match( ( /[^\s]+(\.(jpg|jpeg|gif|png|bmp|tiff))$/i ) ) ) {
				targetLink.addClass( 'img-link' );
	       }

			hipnewsJS( targetLink ).unbind( 'click' ).bind( 'click', function( e ) {

				// is this an external link? Confirm to leave WAM
				if ( hipnewsJS( targetLink ).hasClass( 'external' ) || hipnewsJS( targetLink ).parent( 'li' ).hasClass( 'external' ) ) {
			       	confirmForExternal = confirm( WPMobi.external_link_text + ' \n' + WPMobi.open_browser_text );
					if ( confirmForExternal ) {
						return true;
					} else {			
						return false;
					}
				// prevent images with links to larger ones from opening in web-app mode
				} else if ( hipnewsJS( targetLink ).hasClass( 'img-link' ) ) {
					return false;

				// local http link or no http present: 
				} else if ( targetUrl.match( localDomain ) || !targetUrl.match( 'http://' ) ) {
					// make sure it's not in the ignored list first
					if ( hipnewsJS( targetLink ).hasClass( 'ignored' ) || hipnewsJS( targetLink ).parent( 'li' ).hasClass( 'ignored' ) ) {
				       	confirmForExternal = confirm( WPMobi.wpmobi_ignored_text + ' \n' + WPMobi.open_browser_text );
							if ( confirmForExternal ) {
								return true;	
							} else {
								return false;
							}
					// okay, it's passed the tests, this is a local link, fire WAM
					} else {
						/* Check to see if menu is showing */
						if ( hipnewsJS( '#main-menu' ).hasClass( 'show-menu' ) ) {
							/* Menu is showing, so lets close it */
							hipnewsJS( this ).opacityToggle( 380 );
							hipnewsJS( 'a#header-menu-toggle' ).toggleClass( 'menu-toggle-open' );
						}
						loadPage( targetUrl ); 
						return false;
					}
				// not local, not ignored, doesn't have no-ajax but it's got an external http domain url
				} else {
			       	confirmForExternal = confirm( WPMobi.external_link_text + ' \n' + WPMobi.open_browser_text );
					if ( confirmForExternal ) {
						return true;
					} else {			
						return false;
					}					
				}
			}); /* end click bindings */
		}); /* end .each loop */
	} else {
		// Do non web-app setup
		hipnewsJS( 'li.target a' ).attr( 'target', '_blank' );
	}
}

/* Load domain urls with Ajax (works with webAppLinks(); ) */
function loadPage( targetUrl ) {
	var persistenceOn = hipnewsJS( 'body.loadsaved' ).length;
	if ( hipnewsJS( 'body.ajax-on' ).length ) {
		hipnewsJS( 'body' ).append( '<div id="progress"></div>' );
		hipnewsJS( '#progress' ).viewportCenter();
		hipnewsJS( document ).unbind();
		hipnewsJS( '#outer-ajax' ).load( targetUrl + ' #inner-ajax', function( allDone ) {
			hipnewsJS( '#progress' ).addClass( 'done' );
			if ( persistenceOn ) {
		  		WPMobiCreateCookie( 'wpmobi-load-last-url', targetUrl, 365 );
			} else {
			  	WPMobiEraseCookie( 'wpmobi-load-last-url' );	
			}
			doHipnewsReady();
			scrollTo( 0, 0, 100 );
		});
	} else {
		hipnewsJS( 'body' ).append( '<div id="progress"></div>' );
		hipnewsJS( '#progress' ).viewportCenter();
		if ( persistenceOn ) {
	  		WPMobiCreateCookie( 'wpmobi-load-last-url', targetUrl, 365 );
		}
		setTimeout( function () { window.location = targetUrl; }, 550 );
	}
}

/* Things to do only when in Web-App Mode */
function webAppOnly() {
	if ( WPMobiWebApp ) {
		var persistenceOn = hipnewsJS( 'body.loadsaved' ).length;
		if ( !persistenceOn ) {
			WPMobiEraseCookie( 'wpmobi-load-last-url' );
		}
		hipnewsJS( 'body' ).addClass( 'web-app' );
		hipnewsJS( 'body.black-translucent' ).css( 'margin-top', '20px' );
		hipnewsJS( 'a.comment-reply-link, a.comment-edit-link' ).remove();
		setTimeout( function () { hipnewsJS( '#progress' ).remove(); }, 150 );
	}
}

function hipnewsHandleShortcodes() {
	// For web application mode
	if ( WPMobiWebApp ) {
		var webAppDivs = jQuery( '.wpmobi-shortcode-webapp-only' );
		if ( webAppDivs.length ) {
			webAppDivs.show();
		}
	}
}

function loadMoreEntries() {
	var loadMoreLink = hipnewsJS( 'a.load-more-link' );
	var ajaxDiv = '.ajax-page-target';
	loadMoreLink.live( 'click', function() {
		hipnewsJS( this ).addClass( 'ajax-spinner' ).text( WPMobi.loading_text );
		var loadMoreURL = hipnewsJS( this ).attr( 'rel' );
		hipnewsJS( '.post-list' ).append( "<div class='ajax-page-target'></div>" );
		hipnewsJS( ajaxDiv ).hide().load( loadMoreURL + ' .post-list .post, .post-list .load-more-link', function() {
			hipnewsJS( this ).replaceWith( hipnewsJS( this ).html() );
			hipnewsJS( 'a.load-more-link.ajax-spinner' ).fadeOut( 350 );
			webAppLinks();
			// hipnewsJS( '.content' ).fitVids();
		});
		return false;
	});	
}

function loadMoreComments() {
	var loadMoreLink = hipnewsJS( 'li.load-more-comments-link a' );
	var ajaxDiv = '.ajax-page-target';
	loadMoreLink.live( 'click', function() {
		hipnewsJS( this ).addClass( 'ajax-spinner' );
		var loadMoreURL = hipnewsJS( this ).attr( 'href' );
		hipnewsJS( 'ol.commentlist' ).append( "<div class='ajax-page-target'></div>" );
		hipnewsJS( ajaxDiv ).hide().load( loadMoreURL + ' ol.commentlist > li', function() {
			hipnewsJS( this ).replaceWith( hipnewsJS( this ).html() );	
			hipnewsJS( '.load-more-comments-link a.ajax-spinner' ).parent().fadeOut( 350 );
			if ( WPMobiWebApp ) { 
				hipnewsJS( 'a.comment-reply-link, a.comment-edit-link' ).remove();
				webAppLinks(); 
			}
		});
		return false;
	});	
}

function comReplyArrows() {
	var comReply = hipnewsJS( 'ol.commentlist li li > .comment-top' );
	hipnewsJS.each( comReply, function() {
		hipnewsJS( comReply ).prepend( "<div class='com-down-arrow'></div>" );
	});
}

function WPMobiCreateCookie( name, value, days ) {
	if ( days ) {
		var date = new Date();
		date.setTime( date.getTime() + ( days*24*60*60*1000 ) );
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path="+WPMobi.siteurl;
}

function WPMobiEraseCookie( name ) {
	WPMobiCreateCookie( name,"",-1 );
}

hipnewsJS( document ).ready( function() { doHipnewsReady(); } );