<?php
/*
Plugin Name: Show IP in Manage Listings
Plugin URI: http://www.osclass.org/
Description: Show a column with the IP of who has inserted/edited the listing
Version: 0.9
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: show_ip_manage_listings
Plugin update URI: 
*/

    define('SHOW_IP_MANAGE_LISTINGS_VERSION', '0.9');

    function show_ip_table_header($table) {
        $table->addColumn('s_ip', 'IP');
        $table->removeColumn('country');
    }
    osc_add_hook('admin_items_table', 'show_ip_table_header');

    function show_ip_listing_row($row, $aRow) {
        $row['s_ip'] = sprintf('<a href="http://lacnic.net/cgi-bin/lacnic/whois?query=%1$s" target="_blank">%1$s</a>', $aRow['s_ip']);

        return $row;
    }
    osc_add_filter('items_processing_row', 'show_ip_listing_row');    

    /* file end: show_ip_manage_listings/index.php */