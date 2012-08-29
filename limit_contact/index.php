<?php
/*
Plugin Name: Limit contact
Plugin URI: http://www.osclass.org/
Description: Limit contact with seller by ip
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: Limit contact
Plugin update URI: limit_contact
*/

require_once 'ModelLimitContact.php' ;

/**
 * Set plugin preferences 
 */
function limit_contact_install() 
{
    ModelLimitContact::newInstance()->import('limit_contact/struct.sql');
    
    osc_set_preference('max_contacts', '10', 'limit_contact', 'BOOLEAN');
}

function limit_contact_uninstall() 
{
    ModelLimitContact::newInstance()->uninstall();
    osc_delete_preference('max_contacts', 'limit_contact');
}

function limit_contact_check_send()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $num = ModelLimitContact::newInstance()->countContacts($ip);
    if($num  >= osc_get_preference('max_contacts', 'limit_contact') ) {
        $aux = ModelLimitContact::newInstance()->lastInfoByip($ip);
        $params['subject']  = "Posible spam detected from ".$ip;
        $params['body']     = "<p>IP: ".$ip."</p>";
        $params['body']    .= "<p>EMAIL: ".$aux['s_email_from']."</p>";
        $params['body']    .= "<p>DATETIME: ".$aux['dt_date_time']."</p>";
        $params['to']       = osc_contact_email();
        osc_sendMail($params);
        header( "Location: ".osc_item_url() );
        exit;
    }
}

function limit_contact_register_contact( $item )
{
    ModelLimitContact::newInstance()->insert(array( 's_ip' => $_SERVER['REMOTE_ADDR'],
                                                    'dt_date_time' => date('Y-m-d H:i:s'),
                                                    's_email_from' => Params::getParam('yourEmail'),
                                                    'fk_i_item_id' => $item['pk_i_id']));
}

osc_register_plugin(osc_plugin_path(__FILE__), 'limit_contact_install');
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'limit_contact_uninstall');
    
osc_add_hook('pre_item_contact_post' , 'limit_contact_check_send');
osc_add_hook('post_item_contact_post', 'limit_contact_register_contact');

?>