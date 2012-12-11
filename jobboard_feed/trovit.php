<?php

function trovit_jobs() {
    echo '<?xml version="1.0" encoding="utf-8"?>
<trovit>' . PHP_EOL;

    if( osc_count_items() ) {
        while( osc_has_items() ) {
            $item = feed_get_job_data( osc_item() );

            $date = date('d/m/Y');
            $time = date('H:i');

            $date = osc_item_pub_date();
            if( strtotime(osc_item_pub_date()) < strtotime('2012-10-01 00:00') ) {
                $date = '2012-10-01 00:00';
            }
            if( preg_match('|([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2})|', $date, $tmp) ) {
                $date = $tmp[3] . '/' . $tmp[2] . '/' . $tmp[1];
                $time = $tmp[4] . ':' . $tmp[5];
            }

            echo '    <ad>' . PHP_EOL;
            ef_tag( 'id', osc_item_id() );
            ef_tag( 'url', osc_item_url() );
            ef_tag( 'title', osc_item_title() );
            ef_tag( 'content', osc_item_description() );
            ef_tag( 'category', osc_item_category() );
            $company = osc_page_title();
            if( function_exists('trovit_get_user_company_name') ) {
                $company = trovit_get_user_company_name();
            }
            ef_tag( 'company', $company);
            /* location */
            if( osc_item_region() != '' ) {
                ef_tag( 'region', osc_item_region() );
            }
            if( osc_item_city() != '' ) {
                ef_tag( 'city', osc_item_city() );
            }
            if( osc_item_city_area() != '' ) {
                ef_tag( 'city_area', osc_item_city_area() );
            }
            /* /location */

            /* jobboard attributes */
            if( array_key_exists('s_contract', $item) ) {
                if( $item['s_contract'] != '' ) {
                    ef_tag( 'contract', $item['s_contract'] );
                }
            }
            if( array_key_exists('s_studies', $item) ) {
                if( $item['s_studies'] != '' ) {
                    ef_tag( 'studies', $item['s_studies'] );
                }
            }
            if( array_key_exists('s_salary_text', $item) ) {
                if( $item['s_salary_text'] != '' ) {
                    ef_tag( 'salary', $item['s_salary_text'] );
                }
            }
            if( array_key_exists('s_desired_exp', $item) ) {
                if( $item['s_desired_exp'] != '' ) {
                    ef_tag( 'experience', $item['s_desired_exp'] );
                }
            }
            if( array_key_exists('e_position_type', $item) ) {
                if( $item['e_position_type'] != '' ) {
                    $position_types = get_jobboard_position_types();
                    ef_tag( 'working_hours', $position_types[$item['e_position_type']] );
                }
            }
            if( array_key_exists('s_minimum_requirements', $item) && array_key_exists('s_desired_requirements', $item) ) {
                $requirements = $item['s_desired_requirements'] . $item['s_minimum_requirements'] ;
                if( $requirements != '' ) {
                    ef_tag( 'requirements', $requirements );
                }
            }
            /* /jobboard attributes */

            ef_tag( 'date', $date );
            ef_tag( 'time', $time );
            echo '    </ad>' . PHP_EOL;
        }
    }
    echo '</trovit>' ;
}

// End of file