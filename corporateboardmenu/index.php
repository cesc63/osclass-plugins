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

    osc_add_hook('init_admin','init_admin_fn');
    function init_admin_fn() {
        if(Params::getParam('page') == '' && Params::getParam('action') !== 'logout') {
            redirect_to_url(osc_admin_render_plugin_url('jobboard/dashboard.php'));
        }
    }

    osc_remove_admin_menu_page('dash');
    osc_remove_admin_menu_page('items');
    osc_remove_admin_submenu_page('items','items_manage');
    osc_remove_admin_submenu_page('items','items_reported');
    osc_remove_admin_submenu_page('items','items_media');
    osc_remove_admin_submenu_page('items','items_comments');
    osc_remove_admin_submenu_page('items','items_cfields');
    osc_remove_admin_submenu_page('items','items_settings');
    osc_remove_admin_menu_page('categories');
    osc_remove_admin_menu_page('appearance');
    osc_remove_admin_menu_page('plugins');
    osc_remove_admin_menu_page('stats');
    osc_remove_admin_menu_page('settings');
    osc_remove_admin_menu_page('pages');
    osc_remove_admin_menu_page('users');
    osc_remove_admin_menu_page('tools');
    osc_remove_admin_menu_page('jobboard');

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

    /* file end: corporateboardmenu/index.php */