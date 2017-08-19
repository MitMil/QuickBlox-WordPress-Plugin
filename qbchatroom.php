<?php
/*
Plugin Name: QB Group Chat Room (XMPP)
Plugin URI: http://quickblox.com/developers/Wordpress_Group_Chat_Room_(XMPP)_plugin
Description: Chat room - add it to a sidebar, page or post and have your visitors chat with you and each other. Easy Facebook login (alternative: own registration). Seamless integration with your Web and native mobile (iOS, Android, BlackBerry and Windows Phone) apps - just put same app credentials to connect all of your user base cross platform in your chat.
Version: 2.3.0
Author: QuickBlox
Author URI: http://quickblox.com
*/

/* 
 * QB Chat Room params by default
 * Once this plugin is activated the demo account settings will be used.
 * To change it need create your own chat room and update the app credentials in the form on settings page.
 * This is indicated the section "Documentation and Support" on Plugin Settings page
 */
define('QB_CHATROOM_APP_ID', 3907);
define('QB_CHATROOM_AUTH_KEY','jRVze-6OzVDh-WX');
define('QB_CHATROOM_AUTH_SECRET','uX8dZDexGW8TrEe');
define('QB_CHATROOM_ROOM_JID','3907_demo_room@muc.chat.quickblox.com');
define('QB_CHATROOM_WIDTH','100%');
define('QB_CHATROOM_HEIGHT','350px');

register_activation_hook(__FILE__, 'qb_chatroom_init_defaults');
function qb_chatroom_init_defaults() {
	update_option('qb_chatroom_activated', 1);
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'qb_chatroom_settings_link');
function qb_chatroom_settings_link($links) {
	$settingsLink = '<a href="admin.php?page='.__FILE__.'">Settings</a>';
	array_unshift($links, $settingsLink);
	return $links;
}

add_action('admin_menu', 'qb_chatroom_create_menu');
function qb_chatroom_create_menu() {
	add_menu_page('QuickBlox Chat Room (XMPP): Settings', 'QB Chat Room', 8, __FILE__, 'qb_chatroom_settings_form', plugins_url('favicon.ico', __FILE__));
	add_action('admin_init', 'qb_chatroom_register_mysettings');
}

function qb_chatroom_register_mysettings() {
	$arr = array('app_id', 'auth_key', 'auth_secret', 'room_jid', 'widget_title', 'widget_width', 'widget_height');
	foreach ($arr as $value) {
		register_setting('qb-chatroom-settings-group', "qb_chatroom_$value");
	}
}

function qb_chatroom_settings_form() {
$activated = get_option('qb_chatroom_activated');
$appId = get_option('qb_chatroom_app_id') ? get_option('qb_chatroom_app_id') : QB_CHATROOM_APP_ID;
$authKey = get_option('qb_chatroom_auth_key') ? get_option('qb_chatroom_auth_key') : QB_CHATROOM_AUTH_KEY;
$authSecret = get_option('qb_chatroom_auth_secret') ? get_option('qb_chatroom_auth_secret') : QB_CHATROOM_AUTH_SECRET;
$roomJid = get_option('qb_chatroom_room_jid') ? get_option('qb_chatroom_room_jid') : QB_CHATROOM_ROOM_JID;
?>
<div class="wrap">
	<h2><img src="<?php echo plugins_url('logo.png', __FILE__) ?>" alt="QuickBlox Logo" width="40" style="vertical-align:middle" /> QuickBlox Chat Room (XMPP): Settings</h2>
	
	<div id="poststuff">
		<div class="postbox">
			<h3>Documentation and Support</h3>
			<div class="inside">
				<p><b>Important</b>: you need to create your own chat room and update the credentials here otherwise a default chat room will be displayed and you will find a lot of strangers chatting in your website!<br>
					 It is easy and free to create a QB account and a chat room following the steps below:</p>
				<ol>
					<li>Create a <a href="http://quickblox.com/signup/" target="_blank">new developer account</a> at QuickBlox.</li>
					<li>Add an app in your account (name doesn't matter, you only need ID, key and secret from it).</li>
					<li>Go into Chat module and create a chat room.</li>
					<li>Update the form below with your newly created app credentials (<a href="http://qblx.co/O6SCBk">http://qblx.co/O6SCBk</a>) and chat room address.</li>
				</ol>
				<p>If you have any difficulties please check out the <a href="http://quickblox.com/developers/5_Minute_Guide" target="_blank">5 minute guide</a> or submit your issue to our support via <a href="mailto:web@quickblox.com">web@quickblox.com</a>.<br>
					Also check the video guide through the process below and feel free to visit the official documentation page (<a href="http://quickblox.com/developers" target="_blank">http://quickblox.com/developers</a>) and ask questions there.</p>
			</div>
		</div>
		
		<form method="post" action="options.php">
			<?php settings_fields('qb-chatroom-settings-group'); ?>
			<div class="postbox">
				<h3>Chat Settings</h3>
				<div class="inside">
					<p><strong>Attention: once this plugin is activated the demo account settings will be used.</strong><br>
						 Use the guidance above to create your own chat room and update the credentials below.</p>
					<hr>
					<table class="form-table">
						<tr>
							<th><label for="qb_chatroom_app_id"><strong>Application id</strong></label></th>
							<td><input name="qb_chatroom_app_id" type="text" id="qb_chatroom_app_id" class="regular-text" placeholder="e.g. 3907" value="<?php echo get_option('qb_chatroom_app_id') ?>"></td>
						</tr>
						<tr>
							<th><label for="qb_chatroom_auth_key"><strong>Authorization key</strong></label></th>
							<td><input name="qb_chatroom_auth_key" type="text" id="qb_chatroom_auth_key" class="regular-text" placeholder="e.g. jRVze-6OzVDh-WX" value="<?php echo get_option('qb_chatroom_auth_key') ?>"></td>
						</tr>
						<tr>
							<th><label for="qb_chatroom_auth_secret"><strong>Authorization secret</strong></label></th>
							<td><input name="qb_chatroom_auth_secret" type="text" id="qb_chatroom_auth_secret" class="regular-text" placeholder="e.g. uX8dZDexGW8TrEe" value="<?php echo get_option('qb_chatroom_auth_secret') ?>"></td>
						</tr>
						<tr>
							<th><label for="qb_chatroom_room_jid"><strong>XMPP Room JID</strong></label></th>
							<td><input name="qb_chatroom_room_jid" type="text" id="qb_chatroom_room_jid" class="regular-text" placeholder="e.g. 3907_demo_room@muc.chat.quickblox.com" value="<?php echo get_option('qb_chatroom_room_jid') ?>"></td>
						</tr>
					</table>
				</div>
			</div>
			
			<div class="postbox">
				<h3>Adding to sidebar, page or post</h3>
				<div class="inside">
					<h4>Sidebar</h4>
					<p>Go into <a href="widgets.php">Appearance -> Widgets</a>, drag&drop QB Chat Room widget to the sidebar or other area you want.</p>
					<h4>Pages and Posts</h4>
					<p>Add the following code to your page or blog entry using the editor in HTML mode: <code>[qbchatroom]</code></p>
					<p>Advanced settings to set width and height for your chat room: <code>[qbchatroom width="200px" height="300px"]</code></p>
				</div>
			</div>
			
			<div class="postbox">
				<h3>Widget Settings</h3>
				<div class="inside">
					<table class="form-table">
						<tr>
							<th><label for="qb_chatroom_widget_title"><strong>Widget Title</strong></label></th>
							<td><input name="qb_chatroom_widget_title" type="text" id="qb_chatroom_widget_title" class="regular-text" value="<?php echo get_option('qb_chatroom_widget_title') ?>" placeholder="My XMPP Chat"></td>
						</tr>
						<tr>
							<th><label for="qb_chatroom_widget_width"><strong>Width</strong></label></th>
							<td><input name="qb_chatroom_widget_width" type="text" id="qb_chatroom_widget_width" class="regular-text" value="<?php echo get_option('qb_chatroom_widget_width') ?>" placeholder="e.g. 100% or 100px"></td>
						</tr>
						<tr>
							<th><label for="qb_chatroom_widget_height"><strong>Height</strong></label></th>
							<td><input name="qb_chatroom_widget_height" type="text" id="qb_chatroom_widget_height" class="regular-text" value="<?php echo get_option('qb_chatroom_widget_height') ?>" placeholder="e.g. 100% or 100px"></td>
						</tr>
					</table>
				</div>
			</div>
			
			<?php submit_button(); ?>
		</form>	
		<hr />
		<div id="qb-banner" style="text-align:center">
			<a href="http://quickblox.com" target="_blank" title="Visit www.quickblox.com (opens in a new window/tab)">
				<img src="<?php echo plugins_url('admin_banner.jpg', __FILE__) ?>" alt="QuickBlox Chat Community" />
			</a>
		</div>
	</div>
</div>
<?php
}
?>

<?php 
add_shortcode('qbchatroom', 'qb_chatroom_shortcode_handler');
function qb_chatroom_shortcode_handler($atts) {
	extract(shortcode_atts( array(
		'width' => QB_CHATROOM_WIDTH,
		'height' => QB_CHATROOM_HEIGHT
	), $atts));
	return get_qb_chatroom_code($width, $height);
}

function get_qb_chatroom_code($w, $h)
{
    $key = get_option('qb_chatroom_key');
    $title = get_option('qb_chatroom_widget_title');
    $html = '<link rel="stylesheet" href="'.plugins_url('quickblox-chat-room').'/css/styles.css">';
    // $start_time = get_option('radiochat_start', array());
    // $end_time = get_option('radiochat_end', array() );

    // $roomId = $groupid;//get_option('radiochat_groupid', array() );
    // $roomJid = get_option('qb_chatroom_app_id').'_'.$roomId.'@muc.chat.quickblox.com';
    // $time = gmmktime();
    // $currentDate_time = date('Y-m-d H:i:s', $time);
    // $currentDate= strtotime($currentDate_time);
    //check user logged in or not
	global $current_user;
    $current_user = wp_get_current_user();
    $user_id = $current_user->data->ID;
    if ($user_id != 0) {
	    $html .= "<script>
		var QBAPP = {
			appID: '".get_option('qb_chatroom_app_id')."',
			authKey:'".get_option('qb_chatroom_auth_key')."',
			authSecret: '".get_option('qb_chatroom_auth_secret')."',
			publicRoomId: '".get_option('qb_chatroom_room_id')."',
			publicRoomJid: '".get_option('qb_chatroom_room_jid')."'
		};
		</script>
		";
	    $html .= '
		<section id="wrap">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Live Chat</h3>
					<button type="button" id="logout" class="btn tooltip-title" data-toggle="tooltip" data-placement="bottom" title="Exit" style="display:none">
						<span class="glyphicon glyphicon-log-out"></span>
					</button>
				</div>
				<div class="chat panel-body" style="height:600px;overflow-x: hidden;">
					<div class="chat-user-list list-group" style="display:none;"></div>
					<div class="chat-content">

						<div class="messages">
								<div class="loader-block">
								<img src="'.plugins_url('/images/loading.gif', __FILE__).'" alt="loading" class="loading" id="loading_gif">
								</div>
						</div>
						
					</div>
				</div>
				<div class="chat">
				<form action="#" class="controls">
							<div class="input-group">
								<span class="uploader input-group-addon">
									<span class=""><img src="'.plugins_url('/images/file.png', __FILE__).'"></img></span>
									<input type="file" class="tooltip-title" data-toggle="tooltip" data-placement="right" title="Attach file">
									<div class="attach"></div>
								</span>
								<input type="text" class="form-control" placeholder="Type Your Message">
								<span class="input-group-btn send-btn">
									<button type="submit" class="sendMessage btn btn-primary">Send</button>
								</span>
							</div>
						</form>
					</div>
			</div>
		</section>
		';
    
        $quickblox_user_id = get_user_meta($user_id, 'quickblox_user_id', true);
        $quickblox_user_login = get_user_meta($user_id, 'quickblox_user_login', true);
        $quickblox_user_email = get_user_meta($user_id, 'quickblox_user_email', true);
        $quickblox_password = get_user_meta($user_id, 'quickblox_password', true);
        $token = createQuickBloxToken();
        if (empty($quickblox_password)) {
            $generate_access_token_length = 16;
            $quickblox_password = generateRandomString($generate_access_token_length);

            // Register QuickBlox user
            $registration_response = QuickBloxRegistration($token, $user_id, $current_user->data->user_login, $quickblox_password, $current_user->data->user_email, $current_user->data->display_name);
            if ($registration_response['user']) {
                $quickblox_user_id = $registration_response['user']['id'];
                $quickblox_user_login = $current_user->data->user_login;
                $quickblox_user_email = $current_user->data->user_email;

                update_user_meta($user_id, 'quickblox_registration', 1);
                update_user_meta($user_id, 'quickblox_user_id', $quickblox_user_id);
                update_user_meta($user_id, 'quickblox_user_login', $quickblox_user_login);
                update_user_meta($user_id, 'quickblox_user_email', $quickblox_user_email);
                update_user_meta($user_id, 'quickblox_password', $quickblox_password);
                update_user_meta($user_id, 'quickblox_registration_response', serialize($registration_response));
                //Login after Successfully registration
                //$quickblox_user_email = get_user_meta( $user_id, 'quickblox_user_email',true);
                //$login_response = $json_api->introspector->QuickBloxLogin($token, $quickblox_user_email, $quickblox_password);
            } else {
            	global $post;
				$post_slug = $post->post_name;
            	$html .= '<section id="wrap1">
							<div class="panel panel-primary">
								<div class="panel-heading">
									<h3 class="panel-title">Live Chat</h3>
								</div>
								<div class="chat panel-body" style="height:150px;overflow-x: hidden;">
									<div class="chat-content">
										<div class="messages">
											<div class="allow"><div>QuickBlox email or login has already been taken</div></div>
												<a class="small-submit" href="/login/?login_chat='.$post_slug.'" id="login-but">Login</a>
												<a class="small-submit" href="/register/" id="join-but">Register</a>
										</div>					
									</div>
								</div>
							</div>
						</section>';
            	return $html;
            }
        }
        $html .= '
	<section id="loginForm">
			<form name="loginForm1" class="login__form">
                  <input type="hidden" id="userName" name="userName" value="'.$quickblox_user_email.'"/>
             
                  <input type="hidden" id="password" name="password" value="'.$quickblox_password.'"/>
                  <input type="hidden" id="email" name="email" value="'.$current_user->data->user_email.'"/>
                  <input type="hidden" id="full_name" name="full_name" value="'.$current_user->data->display_name.'"/>    
          	</form>
	</section>
		<script src="https://unpkg.com/navigo@4.3.6/lib/navigo.min.js" defer></script>
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore.js" defer></script>
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/quickblox/2.5.4/quickblox.min.js"></script>
	    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.0.js"></script>
		<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js" defer></script>
		<script src="'.plugins_url('quickblox-chat-room').'/libs/jquery.timeago.js" defer></script>
		<script src="'.plugins_url('quickblox-chat-room').'/libs/jquery.scrollTo-min.js" defer></script>
		<script src="'.plugins_url('quickblox-chat-room').'/js/helpers.js" defer></script>
		<script src="'.plugins_url('quickblox-chat-room').'/groupchat.js" defer></script>		
	';
    } else {
    	global $post;
		$post_slug = $post->post_name;
		//$current_url = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		//$post_slug = substr($current_url, strpos($current_url, site_url()));
		//echo $post_slug;
		//echo $current_url;die;
    	$html .='<section id="wrap1">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Live Chat</h3>
				<button type="button" id="logout" class="btn tooltip-title" data-toggle="tooltip" data-placement="bottom" title="Exit" style="display:none">
					<span class="glyphicon glyphicon-log-out"></span>
				</button>
			</div>
			<div class="chat panel-body" style="height:150px;overflow-x: hidden;">
				<div class="chat-content">
					<div class="messages">
						<div class="allow"> Only logged in users are allowed to enter the chat</div>
							<a class="small-submit" href="/login/?login_chat='.$post_slug.'" id="login-but">Login</a>
							<a class="small-submit" href="/register/" id="join-but">Register</a>
					</div>					
				</div>
			</div>
		</div>
	</section>';
    } 
    return $html;
}

add_action('widgets_init', 'register_qb_chatroom_widget');
function register_qb_chatroom_widget()
{
    register_widget('qb_chatroom_widget');
}

// Creating the widget 
class qb_chatroom_widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(

        // Base ID of your widget
        'qb_chatroom_widget',

        // Widget name will appear in UI
        __('QB Chat Room', 'qb_chatroom_widget_domain'),

        // Widget description 
        array('description' => __('QuickBlox Chat Room', 'qb_chatroom_widget_domain'))
        );
    }
    // Creating widget front-end

    public function widget($args, $instance)
    {
        extract($args);
        echo $before_widget;
        echo $before_title;
        echo $after_title;
        echo get_qb_chatroom_widget_code();
        echo $after_widget;
    }

    // Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
		$title = $instance[ 'title' ];
		}
		else {
		$title = __( 'New title', 'qb_chatroom_widget_domain' );
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
	<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}
function get_qb_chatroom_widget_code()
{
    $width = get_option('qb_chatroom_widget_width') ? get_option('qb_chatroom_widget_width') : QB_CHATROOM_WIDTH;
    $height = get_option('qb_chatroom_widget_height') ? get_option('qb_chatroom_widget_height') : QB_CHATROOM_HEIGHT;
    // $groupid = get_option('radiochat_groupid', array() ) ? get_option('radiochat_groupid', array() ) : '';

    return get_qb_chatroom_code($width, $height);
}
//Generate password foe QuickBlox
function generateRandomString($length = 10, $capital = false)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if (!$capital) {
        $characters .= 'abcdefghijklmnopqrstuvwxyz';
    }
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; ++$i) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}
//Create token for QuickBlox
function createQuickBloxToken()
{

    // QuickBlox Configs.
    $application_id = get_option('qb_chatroom_app_id');
    $auth_key = get_option('qb_chatroom_auth_key');
    $auth_secret = get_option('qb_chatroom_auth_secret');

    $body = array(
        'application_id' => $application_id,
        'auth_key' => $auth_key,
        'nonce' => time(),
        'timestamp' => time(),
    );
    $built_query = urldecode(http_build_query($body));
    $signature = hash_hmac('sha1', $built_query, $auth_secret);
    $body['signature'] = $signature;
    $post_body = http_build_query($body);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.quickblox.com/session.json');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_body);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $token = json_decode($response, true)['session']['token'];
    if ($token) {
        return $token;
    }
}
//Register User in QuickBlox
function QuickBloxRegistration($token, $user_id, $username, $password, $email = '', $full_name = '')
{
    $tag_list = array('dev');
    $body = array(
          'user' => array(
            'full_name' => $full_name,
            'email' => $email,
            'login' => $email,
            'password' => $password,
            'phone' => '',
            'website' => '',
            'external_user_id' => '',
            'facebook_id' => '',
            'twitter_id' => '',
            'blob_id' => '',
            'custom_data' => '',
            'twitter_digits_id' => null,
            'tag_list' => implode(',', $tag_list),
           ),
        );

    $built_query = urldecode(http_build_query($body));
    $body['token'] = $token;
    $post_body = http_build_query($body);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.quickblox.com/users.json');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_body);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $registration_response = json_decode($response, true);

    return $registration_response;
}
?>
