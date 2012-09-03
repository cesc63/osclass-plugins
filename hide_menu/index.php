<?php
/*
Plugin Name: Hide Menu
Plugin URI: http://www.osclass.org/
Description: Hide Menu.
Version: 2.1.4
Author: OSClass
Author URI: http://www.osclass.org/
Plugin update URI: 
*/




osc_remove_admin_menu_page('dash'); //$this->add_menu( __('Dashboard'), osc_admin_base_url(), 'dash', 'moderator');

osc_remove_admin_menu_page('items');   //            $this->add_menu( __('Listing'), osc_admin_base_url(true).'?page=items', 'items', 'moderator') ; 
osc_remove_admin_submenu_page('items','items_manage');//            $this->add_submenu( 'items', __('Manage listings'), osc_admin_base_url(true).'?page=items', 'items_manage', 'moderator') ;
osc_remove_admin_submenu_page('items','items_reported');//            $this->add_submenu( 'items', __('Reported listings'), osc_admin_base_url(true).'?page=items&action=items_reported', 'items_reported', 'moderator') ;
osc_remove_admin_submenu_page('items','items_media');//            $this->add_submenu( 'items', __('Manage media'), osc_admin_base_url(true).'?page=media', 'items_media', 'moderator') ;
osc_remove_admin_submenu_page('items','items_comments');//            $this->add_submenu( 'items', __('Comments'), osc_admin_base_url(true).'?page=comments', 'items_comments', 'moderator') ;
osc_remove_admin_submenu_page('items','items_cfields');//            $this->add_submenu( 'items', __('Custom fields'), osc_admin_base_url(true).'?page=cfields', 'items_cfields', 'administrator') ;
osc_remove_admin_submenu_page('items','items_settings');//            $this->add_submenu( 'items', __('Settings'), osc_admin_base_url(true).'?page=items&action=settings', 'items_settings', 'administrator') ;

osc_remove_admin_menu_page('categories'); 
osc_remove_admin_menu_page('appearance');

osc_remove_admin_menu_page('plugins');

osc_remove_admin_menu_page('stats');

osc_remove_admin_menu_page('settings');

osc_remove_admin_menu_page('pages');

osc_remove_admin_menu_page('users');

osc_remove_admin_menu_page('tools');

osc_remove_admin_menu_page('jobboard');
/*
            $this->add_menu( __('Appearance'), osc_admin_base_url(true) .'?page=appearance', 'appearance', 'administrator') ;
            $this->add_submenu( 'appearance', __('Manage themes'), osc_admin_base_url(true) .'?page=appearance', 'appearance_manage', 'administrator') ;
            $this->add_submenu( 'appearance', __('Market'), osc_admin_base_url(true).'?page=market&action=themes', 'appearance_market', 'administrator') ;
            $this->add_submenu( 'appearance', __('Manage widgets'), osc_admin_base_url(true) .'?page=appearance&action=widgets', 'appearance_widgets', 'administrator') ;
*/
/*
            $this->add_menu(__('Plugins'), osc_admin_base_url(true) .'?page=plugins', 'plugins', 'administrator') ; 
            $this->add_submenu( 'plugins', __('Manage plugins'), osc_admin_base_url(true) .'?page=plugins', 'plugins_manage', 'administrator') ;
            $this->add_submenu( 'plugins', __('Market'), osc_admin_base_url(true).'?page=market&action=plugins', 'plugins_market', 'administrator') ;
*/
/*
            $this->add_menu( __('Statistics'), osc_admin_base_url(true) .'?page=stats&action=items', 'stats', 'moderator' );
            $this->add_submenu( 'stats', __('Listings'), osc_admin_base_url(true) .'?page=stats&action=items', 'stats_items', 'moderator') ;
            $this->add_submenu( 'stats', __('Reports'), osc_admin_base_url(true) .'?page=stats&action=reports', 'stats_reports', 'moderator') ;
            $this->add_submenu( 'stats', __('Users'), osc_admin_base_url(true) .'?page=stats&action=users', 'stats_users', 'moderator') ;
            $this->add_submenu( 'stats', __('Comments'), osc_admin_base_url(true) .'?page=stats&action=comments', 'stats_comments', 'moderator') ;
*/
/*
            $this->add_menu(__('Settings'), osc_admin_base_url(true) .'?page=settings', 'settings', 'administrator') ;
            $this->add_submenu( 'settings', __('General'), osc_admin_base_url(true) .'?page=settings', 'settings_general', 'administrator') ;
            $this->add_submenu( 'settings', __('Comments'), osc_admin_base_url(true) .'?page=settings&action=comments', 'settings_comments', 'administrator') ;
            $this->add_submenu( 'settings', __('Locations'), osc_admin_base_url(true) .'?page=settings&action=locations', 'settings_locations', 'administrator') ;
            $this->add_submenu( 'settings', __('Email templates'), osc_admin_base_url(true) .'?page=emails', 'settings_emails_manage', 'administrator') ;
            $this->add_submenu( 'settings', __('Languages'), osc_admin_base_url(true) .'?page=languages', 'settings_language', 'administrator') ;
            $this->add_submenu( 'settings', __('Permalinks'), osc_admin_base_url(true) .'?page=settings&action=permalinks', 'settings_permalinks', 'administrator') ;
            $this->add_submenu( 'settings', __('Spam and bots'), osc_admin_base_url(true) .'?page=settings&action=spamNbots', 'settings_spambots', 'administrator') ;
            $this->add_submenu( 'settings', __('Currencies'), osc_admin_base_url(true) .'?page=settings&action=currencies', 'settings_currencies', 'administrator') ;
            $this->add_submenu( 'settings', __('Mail server'), osc_admin_base_url(true) .'?page=settings&action=mailserver', 'settings_mailserver', 'administrator') ;
            $this->add_submenu( 'settings', __('Media'), osc_admin_base_url(true) .'?page=settings&action=media', 'settings_media', 'administrator') ;
            $this->add_submenu( 'settings', __('Latest searches'), osc_admin_base_url(true) .'?page=settings&action=latestsearches', 'settings_searches', 'administrator') ;
*/
/*
            $this->add_menu( __('Pages'), osc_admin_base_url(true) .'?page=pages', 'pages', 'administrator' ) ;
*/
/*
            $this->add_menu( __('Users'), osc_admin_base_url(true) .'?page=users', 'users', 'moderator') ;
            $this->add_submenu( 'users', __('Users'), osc_admin_base_url(true) .'?page=users', 'users_manage', 'administrator') ;
            $this->add_submenu( 'users', __('User settings'), osc_admin_base_url(true) .'?page=users&action=settings', 'users_settings', 'administrator') ;
            $this->add_submenu( 'users', __('Administrators'), osc_admin_base_url(true) .'?page=admins', 'users_administrators_manage', 'administrator') ;
            $this->add_submenu( 'users', __('Your Profile'), osc_admin_base_url(true) .'?page=admins&action=edit', 'users_administrators_profile', 'moderator') ;
*/
/*
            $this->add_menu( __('Tools'), osc_admin_base_url(true) .'?page=tools&action=import', 'tools', 'administrator') ;
            $this->add_submenu( 'tools', __('Import data'), osc_admin_base_url(true) .'?page=tools&action=import', 'tools_import', 'administrator') ;
            $this->add_submenu( 'tools', __('Backup data'), osc_admin_base_url(true) .'?page=tools&action=backup', 'tools_backup', 'administrator') ;
            $this->add_submenu( 'tools', __('Upgrade OSClass'), osc_admin_base_url(true) .'?page=tools&action=upgrade', 'tools_upgrade', 'administrator') ;
            $this->add_submenu( 'tools', __('Location stats'), osc_admin_base_url(true) .'?page=tools&action=locations', 'tools_location', 'administrator') ;
            $this->add_submenu( 'tools', __('Category stats'), osc_admin_base_url(true) .'?page=tools&action=category', 'tools_category', 'administrator') ;
            $this->add_submenu( 'tools', __('Maintenance mode'), osc_admin_base_url(true) .'?page=tools&action=maintenance', 'tools_maintenance', 'administrator') ;
*/
?>