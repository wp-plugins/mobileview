/* WPMobi Basic Client-side Ajax Routines */

function WPMobiAjax( actionName, actionParams, callback ) {	
	var ajaxData = {
		action: "wpmobi_client_ajax",
		wpmobi_action: actionName,
		wpmobi_nonce: WPMobi.security_nonce
	};
	
	for ( name in actionParams ) { ajaxData[name] = actionParams[name]; }

	jQuery.post( WPMobi.ajaxurl, ajaxData, function( result ) {
		callback( result );	
	});	
}