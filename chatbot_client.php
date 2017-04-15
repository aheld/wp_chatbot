<?php
/**
 *  Chatbox client that can be injected to the view page
 */

function chatbox_generate_client( $content )
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
EOT;

return $content . $chatbox;
    }
    return null;
}