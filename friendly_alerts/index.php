<?php
/*
Plugin Name: Friendly alerts
Plugin URI: http://www.osclass.org/
Description: -
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: frienly_alerts
Plugin update URI: 
*/

    define('FRIENDLY_ALERTS', '1.0');
    define('FRIENDLY_ALERTS_PATH', dirname(__FILE__) . '/');

    function get_logos_sites() {
        $logo['milavisos'] = 'http://milavisos.cl/oc-content/themes/multimodern/images/logo_milavisos.jpg';
        $logo['avisame']   = 'http://avisame.com.pe/oc-content/themes/multimodern/images/logo_avisame.jpg';
        $logo['olar']      = 'http://olar.com.br/oc-content/themes/olar/images/logo.jpg';
        $logo['empregum']  = 'http://empregum.com.br/oc-content/themes/multimodern/images/logo_empregum.jpg';
        $logo['emcarro']   = 'http://emcarro.com.br/oc-content/themes/multimodern/images/logo_emcarro.jpg';

        return $logo;
    }

    function get_subjects($site, $number) {
        $subject['milavisos'] = "Tenemos $number ofertas nuevas para ti";
        $subject['avisame']   = "Tenemos $number ofertas nuevas para ti";
        $subject['olar']      = "Temos $number novas ofertas para ti";
        $subject['empregum']  = "Temos $number novas ofertas para ti";
        $subject['emcarro']   = "Temos $number novas ofertas para ti";

        return $subject[$site];
    }

    function get_site_name() {
        $parse_url = parse_url(osc_base_url());
        return str_replace(array('.devel','.com','.pe','.br','.cl'), '', $parse_url['host']);
    }

    function get_logo_url() {
        $logos = get_logos_sites();
        return $logos[get_site_name()];
    }

    function daily_friendly_alert() {
        $aAlert = Alerts::newInstance()->findByType('DAILY', true);

        foreach($aAlert as $alert) {
            $json_alert  = base64_decode($alert['s_search']);
            $aConditions = (array) json_decode($json_alert);

            $mSearch = new Search();
            $mSearch->setJsonAlert($aConditions);
            $mSearch->addConditions(sprintf(" %st_item.dt_pub_date > '%s' ", DB_TABLE_PREFIX, date('Y-m-d H:i:s', strtotime('-1 day'))));
            $mSearch->limit(0, 20);

            $aItem      = $mSearch->doSearch();
            $totalItems = $mSearch->count();

            if( count($aItem) > 5 ) {
                View::newInstance()->_exportVariableToView('items', $aItem);
                // get email content
                ob_start(); 
                include(FRIENDLY_ALERTS_PATH . get_site_name() . '_email_tmpl.php');
                $email_tmpl = ob_get_contents();
                ob_end_clean();
                View::newInstance()->_erase('items');

                // send email
                $params = array(
                    'subject'  => get_subjects(get_site_name(), $totalItems),
                    'from'     => osc_contact_email(),
                    'to'       => $alert['s_email'],
                    //'to'       => 'juanramon@osclass.org',
                    'body'     => $email_tmpl
                );

                osc_sendMail($params);
                AlertsStats::newInstance()->increase(date('Y-m-d'));
            }
        }
    }
    osc_add_hook('cron_daily', 'daily_friendly_alert');
    osc_remove_hook('cron_daily', 'daily_alert');
    /* file end: friendly_alerts/index.php */