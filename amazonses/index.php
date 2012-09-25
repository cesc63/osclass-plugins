<?php
/*
Plugin Name: Amazon SES
Plugin URI: http://www.osclass.org/
Description: Amazon SES Plugin
Version: 1.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: amazonses
Plugin update URI: amazonses
*/

    define('AMAZONSES_VERSION', '1.1');
    define('AMAZONSES_PATH', dirname(__FILE__) . '/');

    if( osc_get_preference('amazonses_installed', 'amazonses') === 'true' ) {
        require_once(AMAZONSES_PATH . 'phpmailer/class.phpmailer.php');
    }

    if( defined('AMAZON_EMAIL_ADDRESS') ) {
        function amazon_ses_email_address($email_address) {
            return AMAZON_EMAIL_ADDRESS;
        }
        osc_add_filter('mail_from', 'amazon_ses_email_address');
    }

    if( defined('AMAZON_EMAIL_NAME') ) {
        function amazon_ses_email_name($email_name) {
            return AMAZON_EMAIL_NAME;
        }
        osc_add_filter('mail_from_name', 'amazon_ses_email_name');
    }

    function amazonses_install() {
        osc_set_preference('amazonses_awsaccesskeyid', '', 'amazonses');
        osc_set_preference('amazonses_awssecretkey', '', 'amazonses');
        osc_set_preference('amazonses_installed', 'true', 'amazonses');
    }
    osc_register_plugin(osc_plugin_path(__FILE__), 'amazonses_install');

    function amazonses_init_admin_actions() {
        if( Params::getParam('file') != 'amazonses/settings.php' ) {
            return '';
        }

        $option = Params::getParam('amazonses_hidden');
        if( $option == 'configuration' ) {
            osc_set_preference('amazonses_awsaccesskeyid', Params::getParam('amazonses_awsaccesskeyid'), 'amazonses');
            osc_set_preference('amazonses_awssecretkey', Params::getParam('amazonses_awssecretkey'), 'amazonses');

            osc_add_flash_ok_message(__('The Amazon SES keys have been updated', 'amazonses'), 'admin');
            header('Location: ' . osc_admin_render_plugin_url('amazonses/settings.php')); exit;
        }
    }
    osc_add_hook('init_admin', 'amazonses_init_admin_actions');

    function amazonses_phpmailer_init($mail) {
        $amazonses_awsaccesskeyid = osc_get_preference('amazonses_awsaccesskeyid', 'amazonses');
        if( defined('AMAZONSES_AWSACCESSKEYID') ) {
            $amazonses_awsaccesskeyid = AMAZONSES_AWSACCESSKEYID;
        }

        $amazonses_awssecretkey = osc_get_preference('amazonses_awssecretkey', 'amazonses');
        if( defined('AMAZONSES_AWSSECRETKEY') ) {
            $amazonses_awssecretkey = AMAZONSES_AWSSECRETKEY;
        }

        if( ($amazonses_awsaccesskeyid === '') && (osc_get_preference($amazonses_awssecretkey, 'amazonses') === '') ) {
            return $mail;
        }

        $mail->IsAmazonSES();
        $mail->AddAmazonSESKey($amazonses_awsaccesskeyid, $amazonses_awssecretkey);

        return $mail;
    }
    osc_add_filter('init_send_mail', 'amazonses_phpmailer_init');

    function amazonses_settings() {
        osc_admin_render_plugin('amazonses/settings.php');
    }
    osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'amazonses_settings');
    /* file end: amazonses/index.php */