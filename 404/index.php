<?php
/*
Plugin Name: 404
Plugin URI: http://www.osclass.org/
Description: Show an Error 400 in the following pages: search, item, user, login, register and custom.
Version: 0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: 404
Plugin update URI: 
*/

    if( !function_exists('throw_404') ) {
        function throw_404() {
            $location = Rewrite::newInstance()->get_location();
            $section  = Rewrite::newInstance()->get_section();

            switch ($location) {
                case('search'):
                case('item'):
                case('user'):
                case('login'):
                case('register'):
                case('custom'):
                    Rewrite::newInstance()->set_location('error');
                    header('HTTP/1.1 404 Not Found');
                    osc_current_web_theme_path('404.php');
                    exit ;
                break;
            }
        }
        osc_add_hook('init', 'throw_404');
    }

?>