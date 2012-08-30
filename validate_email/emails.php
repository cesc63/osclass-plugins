<?php

if( $_REQUEST['contactEmail'] != '' ) {
    $email = $_REQUEST['contactEmail'];
} else if( $_REQUEST['yourEmail'] != '' ) {
    $email = $_REQUEST['yourEmail'];
} else if( $_REQUEST['friendEmail'] != '' ) {
    $email = $_REQUEST['friendEmail'];
} else if( $_REQUEST['new_email'] != '' ) {
    $email = $_REQUEST['new_email'];
} else {
    echo "false";
}

list($user, $domain) = split('@', $email);
if( @checkdnsrr($domain, 'MX') ) {
    echo "true";
} else {
    echo "false";
}

?>