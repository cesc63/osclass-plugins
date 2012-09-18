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
    function jobboard_customPageHeader_vacancies() { ?>
        <h1><?php _e('Vacancies'); ?>
            <a href="#" class="btn ico ico-32 ico-help float-right"></a>
            <a href="<?php echo osc_admin_base_url(true) . '?page=items&action=post' ; ?>" class="btn btn-green ico ico-32 ico-add-white float-right"><?php _e('Add vacancy', 'corporateboardmenu'); ?></a>
        </h1>
<?php
    }
    function jobboard_customPageHeader_vacancies_post() { ?>
        <h1><?php _e('Vacancies', 'corporateboardmenu'); ?>
            <a href="#" class="btn ico ico-32 ico-help float-right"></a>
        </h1>
<?php
    }
    function corporateboard_remove_title_header(){
        osc_remove_hook('admin_page_header','customPageHeader');

    }
    if(Params::getParam('page') == 'items'){
        osc_add_hook('admin_header','corporateboard_remove_title_header');
        if(Params::getParam('action') == ''){
            osc_add_hook('admin_page_header','jobboard_customPageHeader_vacancies');
        } else {
            osc_add_hook('admin_page_header','jobboard_customPageHeader_vacancies_post');
        }
    }
    if(Params::getParam('page') == 'appearance' && Params::getParam('file') == 'oc-content/themes/corporateboard/admin/settings.php' ){
        osc_add_hook('admin_header','corporateboard_remove_title_header');
        osc_add_hook('admin_page_header','jobboard_customPageHeader_settings');
    }
    function jobboard_customPageHeader_settings(){
        echo '<h1>'.__('Settings', 'corporateboardmenu'). '<a href="#" class="btn ico ico-32 ico-help float-right"></a></h1>';
    }
    //Custom title
    osc_add_filter('custom_plugin_title','jobboard_dashboard_title');
    function jobboard_dashboard_title($string){
        if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'jobboard/dashboard.php'){
            $string = __('Dashboard', 'corporateboardmenu');
        }
        if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'jobboard/people.php'){
            $string = __('Applicants', 'corporateboardmenu') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
        }
        return $string;
    }

    osc_add_filter('custom_appearance_title','jobboard_appearance_title');
    function jobboard_appearance_title($string){
        if(Params::getParam('page') == 'appearance' && Params::getParam('file') == 'oc-content/themes/corporateboard/admin/colors.php'){
            $string = __('Appearance', 'corporateboardmenu') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
        }
        return $string;
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