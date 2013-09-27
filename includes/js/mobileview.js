/* MobileView Basic Client-side Ajax Routines */

function MobileViewAjax( actionName, actionParams, callback ) {	
	var ajaxData = {
		action: "mobileview_client_ajax",
		mobileview_action: actionName,
		mobileview_nonce: MobileView.security_nonce
	};
	
	for ( name in actionParams ) { ajaxData[name] = actionParams[name]; }

	jQuery.post( MobileView.ajaxurl, ajaxData, function( result ) {
		callback( result );	
	});	
}