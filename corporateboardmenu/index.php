<?php
/*
Plugin Name: Corporateboardmenu
Plugin URI: http://www.osclass.org/
Description: Corporateboardmenu
Version: 0.9
Author: OSClass
Author URI: http://www.osclass.org/
Plugin update URI: 
*/

    //Redirect to Dashboard    
    osc_add_hook('init_admin','init_admin_fn');
    function init_admin_fn(){
        if(Params::getParam('page') == '' && Params::getParam('action') !== 'logout') {
            redirect_to_url(osc_admin_render_plugin_url('jobboard/dashboard.php'));
        }
    }

    //Dashborard
    $menu_title = __('Dashboard', 'corporateboardmenu');
    $url        = osc_admin_render_plugin_url('jobboard/dashboard.php');
    $menu_id    = 'dash';
    $icon_url   = null;
    $capability = 'moderator';
    osc_add_admin_menu_page($menu_title, $url, $menu_id, $capability);

    $menu_title = __('Vacancies', 'corporateboardmenu');
    $url        = osc_admin_base_url(true) .'?page=items';
    $menu_id    = 'items';
    $icon_url   = null;
    $capability = 'moderator';
    osc_add_admin_menu_page($menu_title, $url, $menu_id, $capability);

    osc_remove_admin_menu_page('jobboard');

    $menu_title = __('Applicants', 'corporateboardmenu');
    $url        = osc_admin_render_plugin_url('jobboard/people.php');
    $menu_id    = 'corporateboard';
    $icon_url   = null;
    $capability = 'moderator';
    osc_add_admin_menu_page($menu_title, $url, $menu_id, $capability);

    $menu_title = __('Pages', 'corporateboardmenu');
    $url        = osc_admin_base_url(true) .'?page=pages';
    $menu_id    = 'pages';
    $icon_url   = null;
    $capability = 'moderator';
    osc_add_admin_menu_page($menu_title, $url, $menu_id, $capability);

    $menu_title = __('Appearance', 'corporateboardmenu');
    $url        = osc_admin_render_theme_url('oc-content/themes/corporateboard/admin/colors.php');
    $menu_id    = 'appearance';
    $icon_url   = null;
    $capability = 'moderator';
    osc_add_admin_menu_page($menu_title, $url, $menu_id, $capability);

    $menu_title = __('Settings', 'corporateboardmenu');
    $url        = osc_admin_render_theme_url('oc-content/themes/corporateboard/admin/settings.php');
    $menu_id    = 'settings';
    $icon_url   = null;
    $capability = 'moderator';
    osc_add_admin_menu_page($menu_title, $url, $menu_id, $capability);