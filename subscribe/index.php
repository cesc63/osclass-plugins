<?php
/*
Plugin Name: Subscribe
Plugin URI: http://www.osclass.org/
Description: -
Version: 0.9
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: subscribe
Plugin update URI: 
*/

    define('SUBSCRIBE_VERSION', '0.9') ;
    define('SUBSCRIBE_TABLE', DB_TABLE_PREFIX . 't_subscribers') ;

    function subscribe_install() {
        $dbCommand = get_dbCommand() ;

        $sql = file_get_contents( osc_plugin_resource('subscribe/private/struct.sql') ) ;
        if( !$dbCommand->importSQL($sql) ) {
            throw new Exception( $dbCommand->getErrorLevel() . ' - ' . $dbCommand->getErrorDesc() ) ;
        }
    }
    osc_register_plugin(osc_plugin_path(__FILE__), 'subscribe_install') ;

    /**
     * url: osc_ajax_hook_url('subscribe')
     * params: email
     * return: 
     *     json.error (true or false)
     *     json.msg (String with a message)
     * 
     * example:
     *     $.getJSON("<?php echo osc_ajax_hook_url('subscribe') ; ?>", { email: $('input[name="subscribe_email"]').val() }, function(json) {
     *         console.log('JSON error: ' + json.error) ;
     *         console.log('JSON message: ' + json.msg) ;
     *     }) ;
     */
    function subscribe_ajax_request() {
        switch( $return = subscribe_email() ) {
            case -1:
                $result = array(
                    'error' => true,
                    'msg'   => __('Entered an invalid email', 'subscribe')
                ) ;
            break;
            case 0:
                $result = array(
                    'error' => true,
                    'msg'   => __("You're already subscribed", 'subscribe')
                ) ;
            break;
            case 1:
                $result = array(
                    'error' => true,
                    'msg'   => __('Subscribed correctly', 'subscribe')
                ) ;
            break;
            default:
                $result = array(
                    'error' => true,
                    'msg'   => __("Error subscribing", 'subscribe')
                ) ;
            break;
        }

        echo json_encode($result) ;
        exit ;
    }
    // ajax request
    osc_add_hook('ajax_subscribe', 'subscribe_ajax_request') ;

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
                switch( $return = subscribe_email() ) {
                    case -1:
                        osc_add_flash_error_message( __('Entered an invalid email', 'subscribe') ) ;
                    break;
                    case 0:
                        osc_add_flash_warning_message( __("You're already subscribed", 'subscribe') ) ;
                    break;
                    case 1:
                        osc_add_flash_ok_message( __('Subscribed correctly', 'subscribe') ) ;
                    break;
                    default:
                        osc_add_flash_warning_message( __("Error subscribing", 'subscribe') ) ;
                    break;
                }
            }
        }
        osc_add_hook('init', 'subscribe_post') ;
    }

    /**
     * Insert email to be subscribed to the database
     * 
     * @since 0.9
     * @return boolean true on success, false if the email is not inserted in the database
     */
    function subscribe_email() {
        $email = urldecode( Params::getParam('email') ) ;
        if( !osc_validate_email($email) ) {
            return -1 ;
        }

        $dbCommand = get_dbCommand() ;
        $aInsert   = array(
            's_email'        => $email,
            'd_subscription' => date('Y-m-d'),
            'c_ip'           => $_SERVER['REMOTE_ADDR']
        ) ;
        if( $dbCommand->insert(SUBSCRIBE_TABLE, $aInsert) ) {
            return 1 ;
        }

        if( $dbCommand->getErrorLevel() == 1062 ) {
            return 0 ;
        }

        return $dbCommand->getErrorLevel() ;
    }

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

    /* file end: subscribe/index.php */
?>