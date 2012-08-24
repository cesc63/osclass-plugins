<?php

$user_id = Params::getParam('userId');
$secret  = Params::getParam('secret');

$user = User::newInstance()->findByIdPasswordSecret($user_id, $secret);

if( !empty ($user) ) {
    
    $res = User::newInstance()->deleteUser( $user_id );
    if( $res ) {
        osc_add_flash_ok_message( __("Unsubscribed succesfully.", 'remove_users') );
    } else {
        osc_add_flash_error_message(__('Cannot unsubscribe user.', 'remove_users'));
    }
    remove_users_redirect_to( osc_base_url() ); 
} else {
    osc_add_flash_error_message(__('Cannot unsubscribe user.', 'remove_users'));
    remove_users_redirect_to( osc_base_url() ); 
}

?>