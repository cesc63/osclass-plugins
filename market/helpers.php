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

    function market_banner_path() {
        $market = market_ad();
        return @$market['s_banner_path'];
    }

    function market_preview_url() {
        $market = market_ad();
        return @$market['s_preview'];
    }


    function market_file() {
        $market = market_ad();
        if(isset($market['files'][0])) {
            return $market['files'][0];
        } else {
            return array();
        }
    }

    function market_file_download() {
        $file = market_file();
        return @$file['s_download'];
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

    /**
     * Return type {THEME/LANGUAGE/PLUGIN} depends on category id.
     * @param type $categoryId
     * @return type
     */
    function market_get_type($categoryId) {

        $aCategoryPlugins       = explode(',', osc_get_preference('market_categories_plugins','market'));
        $aCategoryThemes        = explode(',', osc_get_preference('market_categories_theme','market'));
        $aCategoryLanguages     = explode(',', osc_get_preference('market_categories_languages','market'));

        if( in_array($file['fk_i_category_id'], $aCategoryThemes) ) {
            return 'THEME';
        } else if( in_array($file['fk_i_category_id'], $aCategoryLanguages) ) {
            return 'LANGUAGE';
        } else {
            return 'PLUGIN';
        }
    }

    /**
     * Return type widh and height for resize market banners depending on category id.
     *
     * res['w'] = width
     * res['h'] = height
     *
     * @param type $categoryId
     * @return type
     */
    function market_banner_size($categoryId) {

        $aSizes = array(
            'plugins'       => array('w' => 624, 'h' => 224),
            'themes'        => array('w' => 400, 'h' => 400),
            'languages'     => array('w' => 400, 'h' => 400)
        );

        $aCategoryPlugins       = explode(',', osc_get_preference('market_categories_plugins','market'));
        $aCategoryThemes        = explode(',', osc_get_preference('market_categories_theme','market'));
        $aCategoryLanguages     = explode(',', osc_get_preference('market_categories_languages','market'));

        if( in_array($categoryId, $aCategoryThemes) ) {
            $size = $aSizes['plugins'];
        } else if( in_array($categoryId, $aCategoryLanguages) ) {
            $size = $aSizes['themes'];
        } else {
            $size = $aSizes['languages'];
        }
        return $size;
    }

    function market_file_download_url($slug, $version = '')
    {
        $url = osc_base_url() . 'oc-content/plugins/market/download.php?code=' . $slug ;
        if($version!='') {
            $url += '@' . $version;
        }
        return $url;
    }
?>
