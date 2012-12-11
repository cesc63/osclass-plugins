<?php

function jobrapido_xml() {
    echo '<?xml version="1.0" encoding="utf-8"?>
<jobs>' . PHP_EOL;

    if( osc_count_items() ) {
        while( osc_has_items() ) {
            $item = feed_get_job_data( osc_item() );

            $date = date('d/m/Y');
            $time = date('H:i');

            $date = osc_item_pub_date();
            if( strtotime(osc_item_pub_date()) < strtotime('2012-10-01 00:00') ) {
                $date = '20121001';
            }
            if( preg_match('|([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2})|', $date, $tmp) ) {
                $date = $tmp[1] . $tmp[2] . '/' . $tmp[3];
            }

            echo '    <job>' . PHP_EOL;
            ef_tag( 'url', osc_item_url() );
            ef_tag( 'title', osc_item_title() );
            ef_tag( 'publishDate', $date );
            ef_tag( 'company', osc_page_title());
            /* location */
            $location = array();
            if( osc_item_city_area() != '' ) {
                $location[] = osc_item_city_area();
            }
            if( osc_item_city() != '' ) {
                $location[] = osc_item_city();
            }
            if( osc_item_region() != '' ) {
                $location[] = osc_item_region();
            }
            ef_tag( 'location', implode(', ', $location) );
            /* /location */
            if( array_key_exists('s_salary_text', $item) ) {
                if( $item['s_salary_text'] != '' ) {
                    ef_tag( 'salary', $item['s_salary_text'] );
                }
            }
            ef_tag( 'description', osc_item_description() );
            echo '    </job>' . PHP_EOL;
        }
    }
    echo '</jobs>' ;
}

// End of file