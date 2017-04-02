<?php
/**
 * @package Chatbot
 */
/*
Plugin Name: Chatbot
Plugin URI: http://www.aaronheld.com
Description: plugin to add a chat bot
Version: 0
Author: Aaron
Author URI: http://www.aaronheld.com
Text Domain: chatbot
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'CHATBOT__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


/**
 * Proper way to enqueue scripts and styles
 */

function chatbot_scripts_basic()
{
    wp_register_script( 'chatbot-client', plugins_url( '/script.js', __FILE__ ) );

 
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'chatbot-client' );
    wp_enqueue_script( 'chatbot-style' );
}
add_action( 'wp_enqueue_scripts', 'chatbot_scripts_basic' );

function add_ajax_actions() {
    add_action("wp_ajax_chatbox_call_ms", "chatbox_call_ms");
    add_action('wp_ajax_nopriv_chatbox_call_ms', 'chatbox_call_ms');
}
add_action( 'init', 'add_ajax_actions' );
add_filter('the_content', 'chatbox_generate_client');


/**
 * Publisht the chatbox
 */
function chatbox_generate_client($content)
{
    // run only for single post page
    if (is_single() && in_the_loop() && is_main_query()) {
        // add query arguments: action, post, nonce

        $nonce = wp_create_nonce('chatbox_call_ms');
        $ajax_url = admin_url( 'admin-ajax.php' );
        $chatbox = <<<EOT
<div id='bodybox'>
  <div id='chatborder'>
    <p id="chatlog7" class="chatlog">&nbsp;</p>
    <p id="chatlog6" class="chatlog">&nbsp;</p>
    <p id="chatlog5" class="chatlog">&nbsp;</p>
    <p id="chatlog4" class="chatlog">&nbsp;</p>
    <p id="chatlog3" class="chatlog">&nbsp;</p>
    <p id="chatlog2" class="chatlog">&nbsp;</p>
    <p id="chatlog1" class="chatlog">&nbsp;</p>
    <input type="text" name="chat" id="chatbox" placeholder="Hi there! Ask your question!" onfocus="placeHolder()">
  </div>
</div>
<script>
var chatbox_ajax_url = '$ajax_url';
var chatbox_params = { nonce: '$nonce', action: 'chatbox_call_ms' };
</script>

EOT;

        return $content . $chatbox;
        //' <a href="' . esc_url($url) . '">' . esc_html__('Delete Post', 'wporg') . '</a>';
    }
    return null;
}
 
/**
 * request handler
 */
function chatbox_call_ms()
{ 
    if (
        wp_verify_nonce($_POST['nonce'], 'chatbox_call_ms')
    ) {
        $return = json_decode(call_ms_service($_POST['question']));
      }
      else {
          $return = array('answer'	=> 'Error!');
      }
    wp_send_json($return);
    die();  
}
 
function call_ms_service($question){
//
$options = get_option( 'chatbox_settings' );
$path = '/knowledgebases/' . $options['chatbox_text_knowledegebase_id'] . '/generateAnswer';
$api_key_header =  'Ocp-Apim-Subscription-Key: ' . $options['chatbox_text_subscriber_key']; 
// ****

$hostPart = 'https://westus.api.cognitive.microsoft.com/qnamaker/v1.0';

$url = $hostPart . $path;
$arr = array( 'question' => urlencode($question));
$data_string = json_encode($arr);
//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    $api_key_header,
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);
//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);

return $result;
}


// ** admin section

add_action( 'admin_menu', 'chatbox_add_admin_menu' );
add_action( 'admin_init', 'chatbox_settings_init' );


function chatbox_add_admin_menu(  ) { 
	add_options_page( 'chatbox', 'chatbox', 'manage_options', 'chatbox', 'chatbox_options_page' );
}

function chatbox_settings_init(  ) { 

	register_setting( 'pluginPage', 'chatbox_settings' );

	add_settings_section(
		'chatbox_pluginPage_section', 
		__( 'Enter the API information for the KB', 'wordpress' ), 
		'chatbox_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'chatbox_text_knowledegebase_id', 
		__( 'Knowledgebase id', 'wordpress' ), 
		'chatbox_text_knowledegebase_id_render', 
		'pluginPage', 
		'chatbox_pluginPage_section' 
	);

	add_settings_field( 
		'chatbox_text_subscriber_key', 
		__( 'subscriber key', 'wordpress' ), 
		'chatbox_text_subscriber_key_render', 
		'pluginPage', 
		'chatbox_pluginPage_section' 
	);


}


function chatbox_text_knowledegebase_id_render(  ) { 

	$options = get_option( 'chatbox_settings' );
	?>
	<input type='text' name='chatbox_settings[chatbox_text_knowledegebase_id]' value='<?php echo $options['chatbox_text_knowledegebase_id']; ?>'>
	<?php

}


function chatbox_text_subscriber_key_render(  ) { 

	$options = get_option( 'chatbox_settings' );
	?>
	<input type='text' name='chatbox_settings[chatbox_text_subscriber_key]' value='<?php echo $options['chatbox_text_subscriber_key']; ?>'>
	<?php

}


function chatbox_settings_section_callback(  ) { 

	echo __( 'docs would be here', 'wordpress' );

}


function chatbox_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>chatbox</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}
