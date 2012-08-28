<?php
/*
Plugin Name: Geo IP Database
Plugin URI: http://www.osclass.org/
Description: Geo IP Database
Version: 0.9
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: geoip_database
Plugin update URI: geoip_database
*/

    define('GEOIP_DATABASE_VERSION', '0.9');
    define('GEOIP_DATABASE_PATH', dirname(__FILE__) . '/');
    define('GEOIP_DATABASE_TABLE', DB_TABLE_PREFIX . 't_geo_country_ip');

    function ban_selected_countries() {
        $IP    = get_ip();
        $aIP   = explode('.', $IP);

        if( count($aIP) != 4 ) {
            return false;
        }

        $dbCommand = get_dbCommand();
        $numIP     = (16777216 * $aIP[0]) + (65536 * $aIP[1]) + (256 * $aIP[2]) + $aIP[3];

        $dbCommand->select();
        $dbCommand->from(GEOIP_DATABASE_TABLE);
        $dbCommand->where('begin_num <=', $numIP);
        $dbCommand->where('end_num >=', $numIP);
        $dbCommand->limit(1);
        
        $rs = $dbCommand->get();

        if( !$rs ) {
            return false;
        }

        if( $rs->numRows() == 0 ) {
            return false;
        }

        $country = $rs->row();
        // en el array van todos los países que se quieran banear
        if( in_array($country['name'], array('SN')) ) {
            require_once(osc_lib_path() . 'osclass/helpers/hErrors.php');
            $title   = 'OSClass &raquo; Error';
            $message = 'Your not allowed to visit the site from your country';
            osc_die($title, $message);
        }
    }
    osc_add_hook('init', 'ban_selected_countries');

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
    /* file end: geopip_database/index.php */