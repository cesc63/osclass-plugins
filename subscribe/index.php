<?php
/*
Plugin Name: Subscribe
Plugin URI: http://www.osclass.org/
Description: -
Version: 1.2
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: subscribe
Plugin update URI: 
*/

    define('SUBSCRIBE_VERSION', '1.2');
    define('SUBSCRIBE_TABLE', DB_TABLE_PREFIX . 't_subscribers');

    function subscribe_install() {
        $dbCommand = get_dbCommand();

        $sql = file_get_contents( osc_plugin_resource('subscribe/private/struct.sql') );
        if( !$dbCommand->importSQL($sql) ) {
            throw new Exception( $dbCommand->getErrorLevel() . ' - ' . $dbCommand->getErrorDesc() );
        }
    }
    osc_register_plugin(osc_plugin_path(__FILE__), 'subscribe_install');

    /**
     * url: osc_ajax_hook_url('subscribe')
     * params: email
     * return: 
     *     json.error (true or false)
     *     json.msg (String with a message)
     * 
     * example:
     *     $.getJSON("<?php echo osc_ajax_hook_url('subscribe'); ?>", { email: $('input[name="subscribe_email"]').val() }, function(json) {
     *         console.log('JSON error: ' + json.error);
     *         console.log('JSON message: ' + json.msg);
     *     }) ;
     */
    function subscribe_ajax_request() {
        switch( $return = subscribe_email() ) {
            case -1:
                $result = array(
                    'error' => true,
                    'msg'   => __('Entered an invalid email', 'subscribe')
                );
            break;
            case 0:
                $result = array(
                    'error' => true,
                    'msg'   => __("You're already subscribed", 'subscribe')
                );
            break;
            case 1:
                $result = array(
                    'error' => true,
                    'msg'   => __('Subscribed correctly', 'subscribe')
                );
            break;
            default:
                $result = array(
                    'error' => true,
                    'msg'   => __("Error subscribing", 'subscribe')
                );
            break;
        }

        echo json_encode($result);
        exit;
    }
    // ajax request
    osc_add_hook('ajax_subscribe', 'subscribe_ajax_request');

    // execute this code only if it's not an AJAX request
    if( !defined('IS_AJAX') ) {
        /**
         * Form example:
         *     <form action="" method="post">
         *         <fieldset>
         *             <input type="text" name="email" value="" >
         *             <input type="submit" name="subscribe" value="submit">
         *         </fieldset>
         *     </form>
         * 
         * @since 0.9
         */
        function subscribe_post() {
            // check if the POST to the subscribe plugin has been done
            if( Params::getParam('subscribe') != '' ) {
                $return = subscribe_email();

                if( Params::getParam('return_path') != '' ) {
                    $hash = '';
                    if( in_array(Params::getParam('source'), array('footer', 'market', 'doc', 'blog')) ) {
                        $hash = '#subscribe-footer';
                    }
                    $redirectURL = Params::getParam('return_path') . '?subscribe_osclass=' . $return . $hash;
                    header('Location: ' . $redirectURL) ;
                    exit;
                }

                switch( $return = subscribe_email() ) {
                    case -1:
                        osc_add_flash_error_message( __('Entered an invalid email', 'subscribe') );
                    break;
                    case 0:
                        osc_add_flash_warning_message( __("You're already subscribed", 'subscribe') );
                    break;
                    case 1:
                        osc_add_flash_ok_message( __('Subscribed correctly', 'subscribe') );
                    break;
                    default:
                        osc_add_flash_warning_message( __("Error subscribing", 'subscribe') );
                    break;
                }
            }
        }
        osc_add_hook('init', 'subscribe_post');

        /**
         * url: http://www.domain.tld/?action=unsubscribe&email={email}
         * 
         * @since 1.0
         */
        function unsubscribe_post() {
            if( Params::getParam('action') != 'unsubscribe' ) {
                return false;
            }

            require_once(LIB_PATH . 'osclass/helpers/hErrors.php');
            $email = urldecode( Params::getParam('email') );

            $subscriber = get_subscriber($email);

            if( !$subscriber ) {
                $title   = sprintf(__('Error &raquo; %s', 'subscribe'), osc_page_title());
                $message = sprintf(__('The email you\'re trying to unsubscribe doesn\'t exist. <a href="%s">Go home</a>', 'subscribe'), osc_base_url());
                osc_die($title, $message);
            }

            if( !$subscriber['b_active'] ) {
                $title   = sprintf(__('Error &raquo; %s', 'subscribe'), osc_page_title());
                $message = sprintf(__('You\'re already unsubscribed. <a href="%s">Go home</a>', 'subscribe'), osc_base_url());
                osc_die($title, $message);
            }

            $dbCommand = get_dbCommand();
            $set = array('b_active' => false, 'd_unsubscribe' => date('Y-m-d'));
            $dbCommand->update(SUBSCRIBE_TABLE, $set, array('s_email' => $email));

            $title   = sprintf(__('Unsubscribed correctly &raquo; %s', 'subscribe'), osc_page_title());
            $message = sprintf(__('You have been successfully unsubscribed. <a href="%s">Go home</a>', 'subscribe'), osc_base_url());
            osc_die($title, $message);
        }
        osc_add_hook('init', 'unsubscribe_post');
    }

    /**
     * Insert email to be subscribed to the database
     * 
     * @since 0.9
     * @return boolean true on success, false if the email is not inserted in the database
     */
    function subscribe_email() {
        $email = urldecode( Params::getParam('email') );
        if( !osc_validate_email($email) ) {
            return -1;
        }

        $dbCommand = get_dbCommand();
        $aInsert   = array(
            's_email'        => $email,
            'd_subscription' => date('Y-m-d'),
            'c_ip'           => $_SERVER['REMOTE_ADDR']
        );
        if( in_array(Params::getParam('source'), array('footer', 'osclass', 'market', 'doc', 'blog', 'download')) ) {
            $aInsert['e_source'] = Params::getParam('source');
        }

        if( $dbCommand->insert(SUBSCRIBE_TABLE, $aInsert) ) {
            return 1;
        }

        if( $dbCommand->getErrorLevel() == 1062 ) {
            $set = array('b_active' => true, 'd_unsubscribe' => '');
            $result = $dbCommand->update(SUBSCRIBE_TABLE, $set, array('s_email' => $email));
            return $result;
        }

        return $dbCommand->getErrorLevel();
    }

    /**
     * Get subscriber if exists
     * 
     * @since 1.0
     * @return mixed
     */
    function get_subscriber($email) {
        $dbCommand = get_dbCommand();
        $dbCommand->select();
        $dbCommand->where('s_email', $email);
        $dbCommand->limit(1);
        $rs = $dbCommand->get(SUBSCRIBE_TABLE);

        if( !$rs ) {
            return false;
        }

        if( $rs->numRows() == 0 ) {
            return false;
        }

        return $rs->row();
    }

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

    /* file end: subscribe/index.php */
?>