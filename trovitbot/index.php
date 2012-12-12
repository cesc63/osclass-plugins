<?php
/*
Plugin Name: Trovit bot
Plugin URI: http://osclass.org/
Description: Send an email each time the trovit bot download the feed
Version: 0.1
Author: Osclass
Author URI: http://osclass.org/
Short Name: trovitbot
*/

osc_add_hook('before_search', 'trovitbot_email');
function trovitbot_email() {
    if( !isset($_SERVER['HTTP_USER_AGENT']) ) {
        return false;
    }

    $UserAgent = trim($_SERVER['HTTP_USER_AGENT']);

    if( preg_match('|trovitBot|', $UserAgent) ) {
        sendmail_trovitbot();
    }

    return ;
}

function sendmail_trovitbot() {
    $params = array();
    $params['to'][]    = 'oscar@osclass.org';
    $params['to'][]    = 'juanramon@osclass.org';
    $params['to'][]    = 'mauricio@trovit.com';
    $params['subject'] = 'Trovitbot acaba de visitar listings.trovit.com';
    $params['body']    = 'El bot de Trovit acaba de descargarse el feed de trovit a las ' . date('Y-m-d H:i:s');
    osc_sendMail($params);
}

// End of file: ./trovitbot/index.php