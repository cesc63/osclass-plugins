<?php
/*
Plugin Name: Extra akismet
Plugin URI: http://www.osclass.org/
Description: Use Akismet in contact forms
Version: 0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: extra_akismet
*/

if( !function_exists('akismet_contact_spam') ) {
    function akismet_contact_spam() {
        $location = Rewrite::newInstance()->get_location();
        $section  = Rewrite::newInstance()->get_section();

        $become  = (osc_is_static_page() && (Params::getParam('become_an_expert') == 'submit'));
        $premium = (osc_is_static_page() && (Params::getParam('premium_support') == 'submit'));
        $contact = ($location == 'contact' && $section == 'contact_post');

        if( $become || $premium || $contact ) {
            if( !osc_akismet_key() ) {
                return ;
            }

            require_once( osc_lib_path() . 'Akismet.class.php' );
            $akismet = new Akismet(osc_base_url(), osc_akismet_key());
            $akismet->setCommentType('contact');
            $akismet->setCommentAuthor(Params::getParam('yourName'));
            $akismet->setCommentAuthorEmail(Params::getParam('yourEmail'));
            $akismet->setCommentContent(Params::getParam('message'));

            if( $akismet->isCommentSpam() ) {
                require_once(LIB_PATH . 'osclass/helpers/hErrors.php');

                $params = array(
                    'subject'  => 'Form marked as spam',
                    'to'       => 'juanramon.diaz@gmail.com',
                    'to_name'  => 'Juan Ramón',
                    'body'     => Params::getParam('message'),
                    'alt_body' => Params::getParam('message')
                ) ;
                osc_sendMail($params) ;

                $title   = 'OSClass &raquo; Error';
                $message = 'Your message has been marked as spam';
                osc_die($title, $message);
            }
        }

        return ;
    }
    osc_add_hook('init', 'akismet_contact_spam', 1);
}

?>