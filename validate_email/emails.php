<?php

if( array_key_exists($_REQUEST['contactEmail']) ) {
    $email = $_REQUEST['contactEmail'];
} else if( array_key_exists($_REQUEST['yourEmail']) ) {
    $email = $_REQUEST['yourEmail'];
} else if( array_key_exists($_REQUEST['friendEmail'] ) {
    $email = $_REQUEST['friendEmail'];
} else if( array_key_exists($_REQUEST['new_email'] ) {
    $email = $_REQUEST['new_email'];
} else if( array_key_exists($_REQUEST['s_email'])  ) {
    $email = $_REQUEST['s_email'];
} else {
    echo "false";
}

list($user, $domain) = explode('@', $email);
if( @checkdnsrr($domain, 'MX') ) {
    echo "true";
} else {
    echo "false";
}

?>
