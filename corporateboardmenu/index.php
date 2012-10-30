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
        if( (Params::getParam('page') !== 'login') && (Params::getParam('page') === '' && Params::getParam('action') !== 'logout') ) {
            redirect_to_url(osc_admin_render_plugin_url('jobboard/dashboard.php'));
        }
    }

    function corporateboardmenu_admin_menu() {
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
        osc_add_admin_menu_page(
            __('Dashboard', 'corporateboardmenu'),
            osc_admin_render_plugin_url('jobboard/dashboard.php'),
            'dash',
            'moderator'
            );

        osc_add_admin_menu_page(
            __('Vacancies', 'corporateboardmenu'),
            osc_admin_base_url(true) .'?page=items',
            'items',
            'moderator'
            );

        osc_add_admin_menu_page(
            __('Applicants', 'corporateboardmenu'),
            osc_admin_render_plugin_url('jobboard/people.php'),
            'corporateboard',
            'moderator'
            );

        osc_add_admin_menu_page(
            __('Pages', 'corporateboardmenu'),
            osc_admin_base_url(true) .'?page=pages',
            'pages',
            'moderator'
            );

        osc_add_admin_menu_page(
            __('Appearance', 'corporateboardmenu'),
            osc_admin_render_theme_url('oc-content/themes/corporateboard/admin/colors.php'),
            'appearance',
            'moderator'
            );

        osc_add_admin_menu_page(
            __('Settings', 'corporateboardmenu'),
            '#',
            'settings',
            'moderator'
            );

            osc_add_admin_submenu_page(
                'settings',
                __('General settings', 'corporateboardmenu'),
                osc_admin_render_plugin_url('corporateboardmenu/admin/settings.php'),
                'settings_general',
                'moderator'
                );

            osc_add_admin_submenu_page(
                'settings',
                __('Theme settings', 'corporateboardmenu'),
                osc_admin_render_theme_url('oc-content/themes/corporateboard/admin/settings.php'),
                'settings_theme',
                'moderator'
                );

    }
    osc_add_hook('admin_header', 'corporateboardmenu_admin_menu', 6);
    /* file end: corporateboardmenu/index.php */