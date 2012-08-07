<?php
/*
Plugin Name: Extra akismet
Plugin URI: http://www.osclass.org/
Description: Use Akismet in contact forms
Version: 0.2
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: akismet_item_contact
*/

    define('AKISMET_EXTRA_VERSION', '0.2');
    define('AKISMET_ITEM_CONTACT_TABLE', DB_TABLE_PREFIX . 't_item_contact');
    define('AKISMET_ITEM_SEND_FRIEND_TABLE', DB_TABLE_PREFIX . 't_item_send_friend');
    define('AKISMET_CONTACT_TABLE', DB_TABLE_PREFIX . 't_contact');

    function store_in_db_item_contact($item) {
        $dbCommand = get_dbCommand();

        $aInsert   = array(
            'fk_i_item_id'   => $item['pk_i_id'],
            'dt_contact'     => date('Y-m-d H:i:s'),
            's_user_email'   => Params::getParam('yourEmail'),
            's_user_name'    => Params::getParam('yourName'),
            's_user_phone'   => Params::getParam('phoneNumber'),
            's_user_message' => Params::getParam('message'),
            's_ip'           => get_ip(),
            's_user_agent'   => $_SERVER['HTTP_USER_AGENT']
        );

        if( !$dbCommand->insert(AKISMET_ITEM_CONTACT_TABLE, $aInsert) ) {
            return 0;
        }

        return $dbCommand->insertedId();
    }

    function store_in_db_item_send_friend($item) {
        $dbCommand = get_dbCommand();

        $aInsert   = array(
            'fk_i_item_id'   => $item['pk_i_id'],
            'dt_contact'     => date('Y-m-d H:i:s'),
            's_user_email'   => Params::getParam('yourEmail'),
            's_user_name'    => Params::getParam('yourName'),
            's_friend_email' => Params::getParam('friendEmail'),
            's_friend_name'  => Params::getParam('friendName'),
            's_user_message' => Params::getParam('message'),
            's_ip'           => get_ip(),
            's_user_agent'   => $_SERVER['HTTP_USER_AGENT']
        );

        if( !$dbCommand->insert(AKISMET_ITEM_SEND_FRIEND_TABLE, $aInsert) ) {
            return 0;
        }

        return $dbCommand->insertedId();
    }

    function store_in_db_contact() {
        $dbCommand = get_dbCommand();

        $aInsert   = array(
            'dt_contact'     => date('Y-m-d H:i:s'),
            's_user_email'   => Params::getParam('yourEmail'),
            's_user_name'    => Params::getParam('yourName'),
            's_subject'      => Params::getParam('subject'),
            's_message'      => Params::getParam('message'),
            's_ip'           => get_ip(),
            's_user_agent'   => $_SERVER['HTTP_USER_AGENT']
        );

        if( !$dbCommand->insert(AKISMET_CONTACT_TABLE, $aInsert) ) {
            return 0;
        }

        return $dbCommand->insertedId();
    }

    if( !function_exists('akismet_item_contact') ) {
        function akismet_item_contact($item) {
            $dbCommand = get_dbCommand();
            $contactID = store_in_db_item_contact($item);

            if( !osc_akismet_key() ) {
                return ;
            }

            require_once( osc_lib_path() . 'Akismet.class.php' );
            $akismet = new Akismet(osc_base_url(), osc_akismet_key());
            $akismet->setCommentType('email');
            $akismet->setCommentAuthor(Params::getParam('yourName'));
            $akismet->setCommentAuthorEmail(Params::getParam('yourEmail'));
            $akismet->setCommentContent(sprintf('Phone number: %s %s', Params::getParam('phoneNumber'), Params::getParam('message')));

            $contactSpam = $akismet->isCommentSpam();
            if( $contactSpam ) {
                $set    = array('b_spam' => true);
                $result = $dbCommand->update(AKISMET_ITEM_CONTACT_TABLE, $set, array('pk_i_id' => $contactID));

                akismet_spam_message_die();
            }

            return ;
        }
        osc_add_hook('pre_item_contact_post', 'akismet_item_contact');
    }

    if( !function_exists('akismet_item_send_friend') ) {
        function akismet_item_send_friend($item) {
            $dbCommand = get_dbCommand();
            $contactID = store_in_db_item_send_friend($item);

            if( !osc_akismet_key() ) {
                return ;
            }

            require_once( osc_lib_path() . 'Akismet.class.php' );
            $akismet = new Akismet(osc_base_url(), osc_akismet_key());
            $akismet->setCommentType('email');
            $akismet->setCommentAuthor(Params::getParam('yourName'));
            $akismet->setCommentAuthorEmail(Params::getParam('yourEmail'));
            $akismet->setCommentContent(Params::getParam('message'));

            $contactSpam = $akismet->isCommentSpam();
            if( $contactSpam ) {
                $set    = array('b_spam' => true);
                $result = $dbCommand->update(AKISMET_ITEM_SEND_FRIEND_TABLE, $set, array('pk_i_id' => $contactID));

                akismet_spam_message_die();
            }

            return ;
        }
        osc_add_hook('pre_item_send_friend_post', 'akismet_item_send_friend');
    }

    if( !function_exists('akismet_contact_form') ) {
        function akismet_contact_form() {
            $dbCommand = get_dbCommand();
            $contactID = store_in_db_contact();

            if( !osc_akismet_key() ) {
                return ;
            }

            require_once( osc_lib_path() . 'Akismet.class.php' );
            $akismet = new Akismet(osc_base_url(), osc_akismet_key());
            $akismet->setCommentType('email');
            $akismet->setCommentAuthor(Params::getParam('yourName'));
            $akismet->setCommentAuthorEmail(Params::getParam('yourEmail'));
            $akismet->setCommentContent(sprintf('%s %s', Params::getParam('subject'), Params::getParam('message')));

            $contactSpam = $akismet->isCommentSpam();
            if( $contactSpam ) {
                $set    = array('b_spam' => true);
                $result = $dbCommand->update(AKISMET_CONTACT_TABLE, $set, array('pk_i_id' => $contactID));

                akismet_spam_message_die();
            }

            return ;
        }
        osc_add_hook('pre_contact_post', 'akismet_contact_form');
    }

    if( !function_exists('akismet_add_listing') ) {
        function akismet_add_listing($catID, $itemID) {
            if( !osc_akismet_key() ) {
                return ;
            }

            $aItem = Item::newInstance()->findByPrimaryKey($itemID);

            require_once( osc_lib_path() . 'Akismet.class.php' );
            $akismet = new Akismet(osc_base_url(), osc_akismet_key());
            $akismet->setCommentType('listing');
            $akismet->setCommentAuthor($aItem['s_contact_name']);
            $akismet->setCommentAuthorEmail($aItem['s_contact_email']);
            $akismet->setCommentContent(sprintf('%s %s', $aItem['s_title'], $aItem['s_description']));

            if( $akismet->isCommentSpam()) {
                $mItem = new ItemActions(false);
                $mItem->spam($itemID);
                akismet_spam_message_die();
            }
        }
        osc_add_hook('item_form_post', 'akismet_add_listing');
    }

    function akismet_spam_message_die() {
        require_once(osc_lib_path() . 'osclass/helpers/hErrors.php');
        $title   = 'OSClass &raquo; Error';
        $message = 'Your message has been marked as spam';
        osc_die($title, $message);
    }

    function akismet_extra_install() {
        $dbCommand = get_dbCommand();

        $sql = file_get_contents( osc_plugin_resource('akismet_extra/private/struct.sql') );
        if( !$dbCommand->importSQL($sql) ) {
            throw new Exception( $dbCommand->getErrorLevel() . ' - ' . $dbCommand->getErrorDesc() );
        }
    }
    osc_register_plugin(osc_plugin_path(__FILE__), 'akismet_extra_install');

    if( !function_exists('get_dbCommand') ) {
        /**
         * Get DBCommandClass object
         * 
         * @since 0.9
         * @return DBCommandClass
         */
        function get_dbCommand() {
            $conn = DBConnectionClass::newInstance();
            $data = $conn->getOsclassDb();
            return new DBCommandClass($data);
        }
    }

    /* file end: akismet_item_contact/index.php */