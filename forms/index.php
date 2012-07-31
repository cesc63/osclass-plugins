<?php
/*
Plugin Name: Forms
Plugin URI: http://www.osclass.org/
Description: -
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: forms
Plugin update URI: 
*/

    define('FORM_VERSION', '1.0') ;

    function form_install() {
        $dbCommand = get_dbCommand() ;

        // become an expert
        $mPage = Page::newInstance() ;
        $mPage->insert(
            array(
                's_internal_name' => 'email_become_an_expert',
                'b_indelible'     => '1'
            ),
            array('en_US' => array('s_title' => 'Become an expert', 's_text' => 'Text'))
        ) ;

        $mPage = Page::newInstance() ;
        $mPage->insert(
            array(
                's_internal_name' => 'email_premium_support',
                'b_indelible'     => '1'
            ),
            array('en_US' => array('s_title' => 'Premium support', 's_text' => 'Text'))
        ) ;
    }
    osc_register_plugin(osc_plugin_path(__FILE__), 'form_install') ;

    if( !defined('IS_AJAX') ) {
        function submit_extra_forms() {
            $aPage  = array() ;
            $locale = osc_current_user_locale() ;

            // get parameters
            $name        = Params::getParam('yourName') ;
            $email       = Params::getParam('yourEmail') ;
            $phoneNumber = Params::getParam('yourPhoneNumber') ;
            $website     = Params::getParam('yourWebsite') ;
            $message     = Params::getParam('message') ;
            if( osc_is_static_page() && (Params::getParam('become_an_expert') == 'submit') ) {
                $aPage = Page::newInstance()->findByInternalName('email_become_an_expert') ;
                header('Location: ' . osc_base_url() . 'page/become_an_expert') ;
                osc_add_flash_ok_message(__('Thank your for contacting us', 'forms')) ;
            }
            if( osc_is_static_page() && (Params::getParam('premium_support') == 'submit') ) {
                $aPage = Page::newInstance()->findByInternalName('email_premium_support') ;                
                header('Location: ' . osc_base_url() . 'page/premium_support') ;
                osc_add_flash_ok_message(__('Thank your for contacting us', 'forms')) ;
            }

            // if is submit
            if( count($aPage) == 0 ) {
                return ;
            }

            $sSubject = $aPage['locale'][$locale]['s_title'] ;
            $sBody    = $aPage['locale'][$locale]['s_text'] ;

            $words   = array() ;
            $words[] = array('{NAME}', '{EMAIL}', '{PHONE_NUMBER}', '{WEBSITE}', '{MESSAGE}') ;
            $words[] = array($name, $email, $phoneNumber, $website, $message) ;
            $subject = osc_mailBeauty($sSubject, $words) ;
            $body    = osc_mailBeauty($sBody, $words) ;

            $params = array(
                'subject'  => $subject,
                'to'       => osc_contact_email(),
                'to_name'  => osc_page_title(),
                'body'     => $body,
                'alt_body' => $body
            ) ;

            $result = osc_sendMail($params) ;
            exit ;
        }
        osc_add_hook('init', 'submit_extra_forms') ;
    }

    if( !function_exists('get_dbCommand') ) {
        /**
         * Get DBCommandClass object
         * 
         * @since 0.9
         * @return DBCommandClass
         */
        function get_dbCommand() {
            $conn = DBConnectionClass::newInstance() ;
            $data = $conn->getOsclassDb();
            return new DBCommandClass($data) ;
        }
    }

    /* file end: forms/index.php */
?>