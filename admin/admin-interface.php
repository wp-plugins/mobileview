<?php global $mobileview; $current_scheme = get_user_option('admin_color'); $settings = mobileview_get_settings(); ?>
<?php
//twitter
class mobileview_twitter
{
	public $consumer_key = '9oniptvwS1XN16mCar5w';
	public $consumer_secret = 'RqEiNy3RksnYm29T3TCnb1pSbOZUcdIxZrAyS9Fs';
	/**
	* Linkify Twitter Text
	* 
	* @param string s Tweet
	* 
	* @return string a Tweet with the links, mentions and hashtags wrapped in <a> tags 
	*/
	function mobileview_linkify_twitter_text($tweet = ''){
		$url_regex = '/((https?|ftp|gopher|telnet|file|notes|ms-help):((\/\/)|(\\\\))+[\w\d:#@%\/\;$()~_?\+-=\\\.&]*)/';
		$tweet = preg_replace($url_regex, '<a href="$1" target="_blank">'. "$1" .'</a>', $tweet);
		$tweet = preg_replace( array(
		  '/\@([a-zA-Z0-9_]+)/', # Twitter Usernames
		  '/\#([a-zA-Z0-9_]+)/' # Hash Tags
		), array(
		  '<a href="http://twitter.com/$1" target="_blank">@$1</a>',
		  '<a href="http://twitter.com/search?q=%23$1" target="_blank">#$1</a>'
		), $tweet );
		
		return $tweet;
	}

	/**
	* Get User Timeline
	* 
	*/
	function mobileview_get_user_timeline( $username = '', $limit = 5 ) {
		$key = "twitter_user_timeline_{$username}_{$limit}";

		// Check if cache exists
		$timeline = get_transient( $key );
		if ($timeline !== false) {
		  return $timeline;
		} else {
		  $headers = array( 'Authorization' => 'Bearer ' . $this->mobileview_get_access_token() );
		  $response = wp_remote_get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name={$username}&count={$limit}", array( 
			'headers' => $headers, 
			'timeout' => 40,
			'sslverify' => false 
		  ));
		  if ( is_wp_error($response) ) {
			// In case Twitter is down we return error
			dbgx_trace_var($response);
			return array('error' => __('There is problem fetching twitter timeline', 'colabsthemes'));
		  } else {
			// If everything's okay, parse the body and json_decode it
			$json = json_decode(wp_remote_retrieve_body($response));

			// Check for error
			if( !count( $json ) ) {
			  return array('error' => __('There is problem fetching twitter timeline', 'colabsthemes'));
			} elseif( isset( $json->errors ) ) {
			  return array('error' => $json->errors[0]->message);
			} else {
			  set_transient( $key, $json, 60 * 60 );
			  return $json;
			}
		  }
		}
	}

	/**
	* Get Twitter application-only access token
	* @return string Access token
	*/
	function mobileview_get_access_token() {
		$consumer_key = urlencode( $this->consumer_key );
		$consumer_secret = urlencode( $this->consumer_secret );
		$bearer_token = base64_encode( $consumer_key . ':' . $consumer_secret );

		$oauth_url = 'https://api.twitter.com/oauth2/token';

		$headers = array( 'Authorization' => 'Basic ' . $bearer_token );
		$body = array( 'grant_type' => 'client_credentials' );

		$response = wp_remote_post( $oauth_url, array(
		  'headers' => $headers,
		  'body' => $body,
		  'timeout' => 40,
		  'sslverify' => false
		) );

		if( !is_wp_error( $response ) ) {
		  $response_json = json_decode( $response['body'] );
		  return $response_json->access_token;
		} else {
		  return false;
		}
	}

	/**
	* Builder Twitter timeline HTML markup
	*/
	function mobileview_build_twitter_markup( $timelines = array() ) { ?>
		<ul class="tweets">
		<?php foreach( $timelines as $item ) : ?>
		  <?php 
			$screen_name = $item->user->screen_name;
			$profile_link = "http://twitter.com/{$screen_name}";
			$status_url = "http://twitter.com/{$screen_name}/status/{$item->id}";
		  ?>
		  <li>
			<span class="content">
			  <?php echo $this->mobileview_linkify_twitter_text( $item->text ); ?>
			  <a href="<?php echo $status_url; ?>" style="font-size:85%" class="time" target="_blank">
				<?php echo date('M j, Y', strtotime($item->created_at)); ?>
			  </a>
			</span>
		  </li>
		<?php endforeach; ?>
		</ul>
		<?php 
	}
}
?>
<form method="post" action="" id="colabsplugin-form" enctype="multipart/form-data" class="<?php if ( $mobileview->locale ) echo 'locale-' . strtolower( $mobileview->locale ); ?>">
	<div id="colabsplugin" class="<?php echo $current_scheme; ?> <?php echo 'normal'; ?> wrap">
		<?php if ( $settings->developer_mode != 'off' ) { ?>
			<div id="message" class="error"><p><?php _e( "MobileView Developer Mode: ON", "colabsthemes" ); ?></p></div>
		<?php } ?>		
		
		<div class="mobileview_twitter_stream">

			<div class="stream-label"><?php _e('News On Twitter:','colabsthemes');?></div>				

		  <?php 
			  $mobileview_twit = new mobileview_twitter();
			  $user_timeline = $mobileview_twit->mobileview_get_user_timeline( 'colorlabs', 5 );
			  if( isset( $user_timeline['error'] ) ) : ?>
				<p><?php echo $user_timeline['error']; ?></p>
			  <?php 
			  else : 
				$mobileview_twit->mobileview_build_twitter_markup( $user_timeline );
			  endif; 
		  ?>


		</div>
		<!-- .colabs_twitter-stream -->

		<div id="mobileview-admin-form">
			<div class="mobile-view-admin-header">
			<div id="mobileview-main-top">
				<h3>
					<img src="<?php echo MOBILEVIEW_URL; ?>/admin/images/logo.png">
					<a href="http://colorlabsproject.com/plugins/mobileview/" target="_blank" title="ColorLabs & Company"><?php echo MOBILEVIEW_PRODUCT_NAME ;?></a> <span class="version"><?php echo MOBILEVIEW_VERSION; ?></span>
				</h3>
			</div>
			<ul id="mobileview-top-menu">
			
				<?php do_action( 'mobileview_pre_menu' ); ?>
				
				<?php $pane = 1; ?>
				<?php foreach( $mobileview->tabs as $name => $value ) { ?>
					<li>
						<a id="pane-<?php echo $pane; ?>" class="pane-<?php echo mobileview_string_to_class( $name ); ?>" href="#">
						<img src="<?php echo MOBILEVIEW_URL; ?>/admin/images/<?php echo $value['icon_url']; ?>">
						<span><?php echo $name; ?></span>
						</a>
					</li>
					<?php $pane++; ?>
				<?php } ?>
		
				<?php do_action( 'mobileview_post_menu' ); ?>
				<li>
					<a id="mobileview-documentation" class="mobileview-documentation" href="http://colorlabsproject.com/documentation/mobileview/" target='_blank'>
					<img src="<?php echo MOBILEVIEW_URL; ?>/admin/images/book.png">
					<span><?php _e('Documentation','mobileviewlang')?></span>
					</a>
				</li>
			</ul>
			<div class="loading-ajax">
					<div class="mobileview-ajax-results" id="ajax-loading" style="display:none"><?php _e( "Loading...", "mobileviewlang" ); ?></div>
					<div class="mobileview-ajax-results" id="ajax-saving" style="display:none"><?php _e( "Saving...", "mobileviewlang" ); ?></div>
					<div class="mobileview-ajax-results" id="ajax-saved" style="display:none"><?php _e( "Done", "mobileviewlang" ); ?></div>
					<div class="mobileview-ajax-results" id="ajax-fail" style="display:none"><?php _e( "Oops! Try saving again.", "mobileviewlang" ); ?></div>
					<br class="clearer" />
				</div>
			</div>		
			<div id="mobileview-tabbed-area"  class="main-panel">
				<?php mobileview_show_tab_settings(); ?>
				
			</div>
			
			<br class="clearer" />
			
			<input type="hidden" name="mobileview-admin-tab" id="mobileview-admin-tab" value="" />
			<input type="hidden" name="mobileview-admin-menu" id="mobileview-admin-menu" value="" />
		</div>
		<input type="hidden" name="mobileview-admin-nonce" value="<?php echo wp_create_nonce( 'mobileview-post-nonce' ); ?>" />
		
		<div class="mobileview-button-wrap">
			<p class="submit" id="colabsplugin-submit">
				<input class="button-primary" type="submit" name="mobileview-submit" title="Save" tabindex="1" value="<?php _e( "Save Changes", "mobileviewlang" ); ?>" />
			</p>
		
			<p class="submit" id="colabsplugin-submit-reset">
				<input class="button" type="submit" name="mobileview-submit-reset" title="Reset" tabindex="2" value="<?php _e( "Reset Settings", "mobileviewlang" ); ?>" />
				<span id="saving-ajax">
					<?php _e( "Saving", "mobileviewlang" ); ?>&hellip; <img src="<?php echo MOBILEVIEW_URL . '/admin/images/ajax-loader.gif'; ?>" alt="ajax image" />
				</span>
			</p>
		</div>

		<p id="colabsplugin-trademark"><a href="http://colorlabsproject.com/" target="_blank" title="ColorLabs & Company"><img src="<?php echo MOBILEVIEW_URL . '/admin/images/colorlabs.png'; ?>" alt="ColorLabs & Company" /></a></p>
		<div class="poof">&nbsp;</div>
	</div> <!-- mobileview-admin-area -->
</form>
