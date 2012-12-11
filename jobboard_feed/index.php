<?php
/*
Plugin Name: Jobboard feed
Plugin URI: http://www.osclass.org/
Description: Jobboard feed
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: jobboard_feed
*/

define('JOBBOARD_FEED_PATH', dirname(__FILE__) . '/') ;

require_once(JOBBOARD_FEED_PATH . '/common.php');

function feed_trovit_jobs() {
    require_once(JOBBOARD_FEED_PATH . 'trovit.php');
    trovit_jobs();
}

function feed_jobrapido() {
    require_once(JOBBOARD_FEED_PATH . 'jobrapido.php');
    jobrapido_xml();
}

function feed_get_job_data($item) {
    $detail       = ModelJB::newInstance()->getJobsAttrByItemId($item['pk_i_id']);
    $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId($item['pk_i_id']);

    foreach($descriptions as $desc) {
        if( $desc['fk_c_locale_code'] === osc_current_user_locale() ) {
            foreach($desc as $k => $v) {
                if( !array_key_exists($k, $detail) ) {
                    $detail[$k] = $v;
                }
            }
        }
    }

    foreach($detail as $k => $v) {
        if( !array_key_exists($k, $item) ) {
            $item[$k] = $v;
        }
    }

    return $item;
}

osc_add_filter('feed_trovit', 'feed_trovit_jobs');
osc_add_filter('feed_trovit_jobs', 'feed_trovit_jobs');
osc_add_filter('feed_jobrapido', 'feed_jobrapido');