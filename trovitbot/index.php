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

osc_add_hook('feed_trovit', 'trovitbot_email');
function trovitbot_email($items) {
    if( !isset($_SERVER['HTTP_USER_AGENT']) ) {
        return false;
    }

    $UserAgent = trim($_SERVER['HTTP_USER_AGENT']);

    if( preg_match('|trovitBot|', $UserAgent) ) {
        $num_items = count($items);
        sendmail_trovitbot($num_items);
    }

    return ;
}

function sendmail_trovitbot($num_items) {
    $params = array();
    $params['to']      = 'listings.notifications@osclass.org';
    $params['subject'] = 'Trovitbot acaba de visitar listings.trovit.com';
    $params['body']    = sprintf('El bot de Trovit acaba de descargarse el feed de trovit a las %1$s que contiene <strong>%2$d anuncios</strong>', date('Y-m-d H:i:s'), $num_items);
    osc_sendMail($params);
}
// End of file: ./trovitbot/index.php