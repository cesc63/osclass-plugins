<?php
/*
Plugin Name: Remove users
Plugin URI: http://www.osclass.org/
Description: Allows registered users to unsubcribe their accounts
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: remove_users
Plugin update URI: remove-users
*/

function remove_users_unsubscribe_user( $userId )
{
     
}

function _add_menu_remove_account( $aMenu )
{
    $logout = array_pop($aMenu);
    
    $url_remove_user = osc_render_file_url(osc_plugin_folder(__FILE__) . 'confirm_unsubscribe.php');
    array_push($aMenu, array('name' => __('Unsubcribe account', 'remove_users'), 'url' => $url_remove_user, 'class' => 'opt_unsubscribe') );
    array_push($aMenu, $logout);
    
    return $aMenu;
}

osc_add_filter('user_menu_filter', '_add_menu_remove_account');

function remove_users_redirect_to($url) {
    header('Location: ' . $url);
    exit;
}


?>
