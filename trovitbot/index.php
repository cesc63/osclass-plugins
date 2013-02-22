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
    $countries = array(
        'CL' => 'Chile',
        'CO' => 'Colombia',
        'PT' => 'Portugal'
    );

    if( !isset($_SERVER['HTTP_USER_AGENT']) ) {
        return false;
    }

    $UserAgent = trim($_SERVER['HTTP_USER_AGENT']);

    if( preg_match('|trovitBot|', $UserAgent) ) {
        $num_items = count($items);
        $country   = '';
        if( array_key_exists(Params::getParam('sCountry'), $countries) ) {
            $country = $countries[Params::getParam('sCountry')];
        }
        sendmail_trovitbot($country, $num_items);
    }

    return ;
}

function sendmail_trovitbot($country, $num_items) {
    $params = array();
    $params['to']      = 'listings.notifications@osclass.org';
    $params['subject'] = 'Trovitbot acaba de visitar listings.trovit.com';
    $params['body']    = sprintf('El bot de Trovit acaba de descargarse el feed de <strong>Trovit %1$s</strong> a las %2$s que contiene <strong>%3$d anuncios</strong>', $country, date('Y-m-d H:i:s'), $num_items);
    osc_sendMail($params);
}
// End of file: ./trovitbot/index.php