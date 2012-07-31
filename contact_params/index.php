<?php
/*
Plugin Name: Contact params
Plugin URI: http://www.osclass.org/
Description: -
Version: 0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: contact_params
Plugin update URI: 
*/

    define('CONTACT_PARAMS', '0.1');

    function contact_params_filter($params) {
        $emails = str_replace(' ', '', osc_get_preference('contact_emails', 'contact_params'));
        $params['add_bcc'] = explode(',', $emails);
        return $params;
    }
    osc_add_filter('contact_params', 'contact_params_filter');

    function contact_params_actions_admin() {
        if( Params::getParam('file') != 'contact_params/render.php' ) {
            return '';
        }

        if( Params::getParam('option') == 'submit' ) {
            osc_set_preference('contact_emails', Params::getParam('contact_emails'), 'contact_params');
            osc_add_flash_ok_message(__('The contact emails have been updated', 'contact_params'), 'admin');
            header('Location: ' . osc_admin_render_plugin_url('contact_params/render.php')); exit;
        }
    }
    osc_add_hook('init_admin', 'contact_params_actions_admin');

    function contact_params_call_after_install() {
        osc_set_preference('contact_emails', '', 'contact_params');
    }
    osc_register_plugin(osc_plugin_path(__FILE__), 'contact_params_call_after_install');

    osc_admin_menu_plugins('Contact', osc_admin_render_plugin_url('contact_params/render.php'), 'contact_params_submenu');
    /* file end: contact_params/index.php */
?>