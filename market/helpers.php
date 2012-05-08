<?php

    function market_ad() {
        if(View::newInstance()->_exists("market_ad")) {
            return View::newInstance()->_get("market_ad");
        } else {
            $market = ModelMarket::newInstance()->findByItemId(osc_item_id());
            $market['files'][0] = ModelMarket::newInstance()->getFileFromItem(osc_item_id());
            View::newInstance()->_exportVariableToView("market_ad", $market);
            return $market;
        }
    }
    
    function market_id() {
        $market = market_ad();
        return @$market['pk_i_id'];
    }

    function market_slug() {
        $market = market_ad();
        return @$market['s_slug'];
    }

    function market_banner_url() {
        $market = market_ad();
        return @$market['s_banner'];
    }

    function market_download() {
        $market = market_ad();
        return @$market['s_download'];
    }


    
    
    function market_file() {
        $market = market_ad();
        if(isset($market['files'][0])) {
            return $market['files'][0];
        } else {
            return array();
        }
    }
    
    function market_file_version() {
        $file = market_file();
        return @$file['s_version'];
    }
    
    function market_file_compatible_versions() {
        $file = market_file();
        return @$file['s_compatible'];
    }
    
    function market_file_compatible_versions_show() {
        $file = market_file();
        return @$file['s_compatible_show'];
    }
    
    function market_file_path() {
        $file = market_file();
        return @$file['s_file'];
    }

    function market_file_enabled() {
        $file = market_file();
        return @$file['b_enabled']==1?true:false;
    }
    
?>
