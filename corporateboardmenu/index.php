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
            __('Killer Questions', 'corporateboardmeun'),
            osc_admin_render_plugin_url("jobboard/manage_killer.php"),
            'killer_questions',
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

            osc_add_admin_submenu_page(
                'settings',
                __('Default locations', 'corporateboardmenu'),
                osc_admin_render_plugin_url('jobboard/admin/settings.php'),
                'settings_jobboard',
                'moderator'
                );

    }
    osc_add_hook('admin_header', 'corporateboardmenu_admin_menu', 6);


    function corporateboardmenu_save_settings() {
        $iUpdated     = 0;
        $array        = array();
        $adminManager = Admin::newInstance();
        $aAdmin       = $adminManager->findByPrimaryKey(osc_logged_admin_id());
        $conditions   = array('pk_i_id' => osc_logged_admin_id());

        $sPassword    = Params::getParam('s_password', false, false);
        $sPassword2   = Params::getParam('s_password2', false, false);
        $sOldPassword = Params::getParam('old_password', false, false);
        $sName        = Params::getParam('s_name');
        $sEmail       = Params::getParam('contactEmail');
        $sUserName    = Params::getParam('s_username');

        // cleaning parameters
        $sPassword   = strip_tags($sPassword);
        $sPassword   = trim($sPassword);
        $sPassword2  = strip_tags($sPassword2);
        $sPassword2  = trim($sPassword2);
        $sName       = strip_tags($sName);
        $sName       = trim($sName);
        $sEmail      = strip_tags($sEmail);
        $sEmail      = trim($sEmail);
        $sUserName   = strip_tags($sUserName);
        $sUserName   = trim($sUserName);

        osc_set_preference('show_in_osclass', Params::getParam('show_in_osclass')=='notshow'?0:1, 'corporateboardmenu');

        // Checks for legit data
        if( !osc_validate_email($sEmail, true) ) {
            osc_add_flash_warning_message(__("Email invalid", 'corporateboardmenu'), 'admin');
            redirect_to_url(osc_admin_render_plugin_url('corporateboardmenu/admin/settings.php'));
        }

        if( !osc_validate_username($sUserName) ) {
            osc_add_flash_warning_message(__("Username invalid", 'corporateboardmenu'), 'admin');
            redirect_to_url(osc_admin_render_plugin_url('corporateboardmenu/admin/settings.php'));
        }

        if( $sName == '' ) {
            osc_add_flash_warning_message(__("Name invalid", 'corporateboardmenu'), 'admin');
            redirect_to_url(osc_admin_render_plugin_url('corporateboardmenu/admin/settings.php'));
        }

        if( Params::getParam('pageTitle') == '' ) {
            osc_add_flash_warning_message(__("Company name invalid", 'corporateboardmenu'), 'admin');
            redirect_to_url(osc_admin_render_plugin_url('corporateboardmenu/admin/settings.php'));
        }

        if( $aAdmin['s_username'] != $sUserName ) {
            if( $adminManager->findByUsername( $sUserName ) ) {
                osc_add_flash_warning_message(__('Existing username', 'corporateboardmenu'), 'admin');
                redirect_to_url(osc_admin_render_plugin_url('corporateboardmenu/admin/settings.php'));
            }
        }

        if($sOldPassword != '' ) {
            if( $sPassword=='' ) {
                osc_add_flash_warning_message(__("Password invalid", 'corporateboardmenu'), 'admin');
                redirect_to_url(osc_admin_render_plugin_url('corporateboardmenu/admin/settings.php'));
            } else {
                $firstCondition  = ( sha1($sOldPassword) == $aAdmin['s_password'] );
                $secondCondition = ( $sPassword == $sPassword2 );
                if( $firstCondition && $secondCondition ) {
                    $array['s_password'] = sha1($sPassword);
                } else {
                    osc_add_flash_warning_message(__("The password couldn't be updated. Passwords don't match", 'corporateboardmenu'), 'admin');
                    redirect_to_url(osc_admin_render_plugin_url('corporateboardmenu/admin/settings.php'));
                }
            }
        }
        // update preference
        osc_set_preference('contactEmail', Params::getParam('contactEmail'));
        osc_set_preference('pageTitle', Params::getParam('pageTitle'));
        osc_set_preference('googleanalytics_trackingid', Params::getParam('googleanalytics_trackingid'), 'corporateboardmenu');
        osc_reset_preferences();

        $array['s_name']     = $sName; //Params::getParam('s_name');
        $array['s_username'] = $sUserName;
        $array['s_email']    = $sEmail;

        $iUpdated = $adminManager->update($array, $conditions);

        osc_add_flash_ok_message(__('The admin has been updated', 'corporateboardmenu'), 'admin');
        redirect_to_url(osc_admin_render_plugin_url('corporateboardmenu/admin/settings.php'));
    }

    function corporateboardmenu_init() {
        if(Params::getParam('page') == 'plugins' &&
                Params::getParam('action') == 'renderplugin' &&
                Params::getParam('file') == 'corporateboardmenu/admin/settings.php' &&
                Params::getParam('subaction') == 'update-settings' ) {
                    corporateboardmenu_save_settings();
        }
    }
    osc_add_hook('init_admin', 'corporateboardmenu_init');


    /* file end: corporateboardmenu/index.php */