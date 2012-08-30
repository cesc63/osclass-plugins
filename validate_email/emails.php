<?php

if( $_REQUEST['contactEmail'] != '' ) {
    $email = $_REQUEST['contactEmail'];
} else if( $_REQUEST['yourEmail'] != '' ) {
    $email = $_REQUEST['yourEmail'];
} else if( $_REQUEST['friendEmail'] != '' ) {
    $email = $_REQUEST['friendEmail'];
} else {
    return false;
}

list($user, $domain) = split('@', $email);
if( @checkdnsrr($domain, 'MX') ) {
    echo "true";
} else {
    echo "false";
}

?>