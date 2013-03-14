<?php
/*
Plugin Name: Market
Plugin URI: http://www.osclass.org/
Description: This is for internal use only, DO NOT make public
Version: 1.2
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: market
Plugin update URI:
*/

    define( 'MARKET_PLUGIN_PATH', osc_plugins_path() . 'market/' ) ;

    require_once( MARKET_PLUGIN_PATH . '/ModelMarket.php' ) ;
    require_once( MARKET_PLUGIN_PATH . '/helpers.php' ) ;

    function market_install() {
        ModelMarket::newInstance()->import('market/struct.sql') ;

        $conn      = DBConnectionClass::newInstance();
        $data      = $conn->getOsclassDb();
        $dbCommand = new DBCommandClass($data);

        // load csv database
        $abs_path_to_geoip = dirname(__FILE__) . '/geoip/GeoIPCountryWhois.csv';
        $dbCommand->query(sprintf("LOAD DATA LOCAL INFILE '%s'
        INTO TABLE %st_ip_ranges
        FIELDS TERMINATED BY ','
        ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'", $abs_path_to_geoip, DB_TABLE_PREFIX));

        if(!is_dir(osc_content_path().'uploads/market/')) {
            @mkdir(osc_content_path().'uploads/market/');
        }
        osc_set_preference('upload_path', osc_content_path().'uploads/market/', 'market', 'STRING');
        osc_set_preference('allowed_ext', 'zip', 'market', 'INTEGER');
        osc_set_preference('market_version', '13', 'market', 'STRING');
    }

    function market_uninstall() {
        try {
            osc_deleteDir(osc_get_preference('upload_path','market'));
            ModelMarket::newInstance()->uninstall();
            osc_delete_preference('upload_path', 'market') ;
            osc_delete_preference('allowed_ext', 'market') ;
            osc_delete_preference('market_version', 'market') ;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function market_update_version()
    {
        // convert version
        $version = osc_get_preference('market_version', 'market');
        if($version=='') {
            $version = 0;
        }

        if($version < 10) {
            // alter tables
            // add total downloads column
            $conn      = DBConnectionClass::newInstance();
            $data      = $conn->getOsclassDb();
            $dbCommand = new DBCommandClass($data);
            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN i_total_downloads INT(10) NOT NULL DEFAULT \'0\' AFTER s_preview', ModelMarket::newInstance()->getTable()));
            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN i_total_downloads INT(10) NOT NULL DEFAULT \'0\' AFTER b_enabled', ModelMarket::newInstance()->getTable_Files()));
            // b_featured at t_market table
            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN b_featured TINYINT(1) NOT NULL DEFAULT \'0\'  AFTER i_total_downloads', ModelMarket::newInstance()->getTable()));

            // fill i_total_download [t_market]
            $result = $dbCommand->query(sprintf('SELECT fk_i_market_id, count(1) as i_downloads FROM %s GROUP BY fk_i_market_id', ModelMarket::newInstance()->getTable_Stats()));
            if($result->numRows > 0) {
                foreach($result->result() as $aux) {
                    $market_id      = $aux['fk_i_market_id'];
                    $i_downloads    = $aux['i_downloads'];
                    ModelMarket::newInstance()->update(array('i_total_downloads' => $i_downloads), array('pk_i_id' => $market_id) );
                }
            }
            // fill i_total_download [t_market_files]
            $result = $dbCommand->query(sprintf('SELECT fk_i_market_id, fk_i_file_id, count(1) as i_downloads FROM %s GROUP BY fk_i_file_id', ModelMarket::newInstance()->getTable_Stats()));
            if($result->numRows > 0) {
                foreach($result->result() as $aux) {
                    $market_id      = $aux['fk_i_market_id'];
                    $market_file_id = $aux['fk_i_file_id'];
                    $i_downloads    = $aux['i_downloads'];
                    ModelMarket::newInstance()->updateFile($market_id, $market_file_id, array('i_total_downloads' => $i_downloads) );
                }
            }
            osc_set_preference('market_version', '10', 'market', 'STRING');
            osc_reset_preferences();
        }

        // added s_banner_path at t_market table
        if($version < 11) {
            error_log('actualizando a 11 ...');
            $conn      = DBConnectionClass::newInstance();
            $data      = $conn->getOsclassDb();
            $dbCommand = new DBCommandClass($data);

            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN s_banner_path VARCHAR(250) NULL DEFAULT NULL AFTER s_banner', ModelMarket::newInstance()->getTable()));

            $error = false;
            // prepare banner path
            $banner_path = osc_get_preference('upload_path', 'market');
            $rel_banner_path  = str_replace(ABS_PATH, '', $banner_path);
            // get all market items
            $aMarketItems = ModelMarket::newInstance()->listAll();
            foreach($aMarketItems as $aux) {
                if($aux['s_banner'] != null) {
                    $res = ModelMarket::newInstance()->update(
                            array('s_banner_path' => $rel_banner_path),
                            array('pk_i_id'       => $aux['pk_i_id'])
                            );
                    if($res===false) {
                        $error = true;
                    }
                }
            }

            if($error) {
                osc_add_flash_error_message(__('Banner was not updated with new s_banner_path', 'market'), 'admin');
            }

            osc_set_preference('market_version', '11', 'market', 'STRING');
            osc_reset_preferences();
        }
        //add i_ocadmin_downloads
        if($version < 12) {
            $conn      = DBConnectionClass::newInstance();
            $data      = $conn->getOsclassDb();
            $dbCommand = new DBCommandClass($data);

            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN i_ocadmin_downloads INT(10) NOT NULL DEFAULT \'0\' AFTER i_total_downloads', ModelMarket::newInstance()->getTable()));
            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN i_ocadmin_downloads INT(10) NOT NULL DEFAULT \'0\' AFTER i_total_downloads', ModelMarket::newInstance()->getTable_Files()));

            // calcular las descargas procedentes de oc-admin, que tengan version en la tabla t_market_stats, s_osclass_version
            // init i_ocadmin_downloads -> t_market
            ModelMarket::newInstance()->recountMarketStats();
            // init i_ocadmin_downloads -> t_market_files
            ModelMarket::newInstance()->recountMarketFilesStats();

            osc_set_preference('market_version', '12', 'market', 'STRING');
            osc_reset_preferences();
        }

        // add geoip country table
        if($version < 13) {
            $conn      = DBConnectionClass::newInstance();
            $data      = $conn->getOsclassDb();
            $dbCommand = new DBCommandClass($data);

            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN s_country_code VARCHAR(2) NULL DEFAULT NULL AFTER s_osclass_version', ModelMarket::newInstance()->getTable_Stats()));

            $dbCommand->query(sprintf('
            CREATE TABLE %st_ip_ranges (
            ip1_temp char(16),
            ip2_temp char(16),
            ip_from int unsigned NOT NULL PRIMARY KEY,
            ip_to int unsigned NOT NULL UNIQUE,
            code char(2) NOT NULL,
            country varchar(100) NOT NULL,
            INDEX (code)) ENGINE = InnoDB', DB_TABLE_PREFIX ));

            // load csv database
            $abs_path_to_geoip = dirname(__FILE__) . '/geoip/GeoIPCountryWhois.csv';
            $dbCommand->query(sprintf("LOAD DATA LOCAL INFILE '%s'
            INTO TABLE %st_ip_ranges
            FIELDS TERMINATED BY ','
            ENCLOSED BY '\"'
            LINES TERMINATED BY '\n'", $abs_path_to_geoip, DB_TABLE_PREFIX));

//            // fill s_country_code using geoiplite
//            // @todo, segmentar las actualizaciones para no petar el sevidor ni tener que
//
//            // create temporary table and insert all market_stats data
//            $dbCommand->query(sprintf('
//            CREATE TEMPORARY TABLE `oc_t_market_stats_tmp` (
//            `fk_i_market_id` int(10) unsigned NOT NULL,
//            `fk_i_file_id` int(10) unsigned NOT NULL,
//            `s_hostname` varchar(250) NOT NULL,
//            `s_ip` varchar(250) NOT NULL,
//            `dt_date` datetime NOT NULL,
//            `s_osclass_version` varchar(6) DEFAULT NULL,
//            `s_country_code` varchar(2) DEFAULT NULL
//            ) ENGINE=InnoDB DEFAULT CHARSET=utf8', DB_TABLE_PREFIX));

            // update table with s_country_code using t_ip_ranges
//            $result = $dbCommand->query(sprintf('select count( distinct(s_ip) ) as total from %st_market_stats', DB_TABLE_PREFIX));
//            $result_count = $result->row();
//            $total_stats = $result_count['total'];
//            error_log('total stats ' . $total_stats);
//            if($total_stats>0) {
//                $offset      = 100;
//                for($i=0;($i*$offset)<$total_stats;$i++) {
//                    $start = $i*$offset;
//                    $sql = sprintf('select * from %st_market_stats group by s_ip limit %d, %d', DB_TABLE_PREFIX, $start, $offset );
//
//                    $step_result = $dbCommand->query($sql);
//                    $step_result = $step_result->result();
//                    $array_ip    = array();
//                    foreach($step_result as $stat) {
//                        $array_ip[] = '("'.$stat['s_ip'].'")';
//                    }
//                    $result = $dbCommand->query( sprintf('insert into %st_market_stats_tmp (s_ip) values %s', DB_TABLE_PREFIX, implode(',', $array_ip) ));
//                }
//
//                // get country_code for each ip
//
////                do {
////
////                    $result_ips = $dbCommand->query( sprintf('select count(*) as total from %st_market_stats_tmp', DB_TABLE_PREFIX) );
////                    $sql = sprintf('update %st_market_stats_tmp SET s_country_code = ( select code from oc_t_ip_ranges where ip_from <= INET_ATON(oc_t_market_stats_tmp.s_ip) AND ip_to >= INET_ATON(oc_t_market_stats_tmp.s_ip) LIMIT 1 ) where s_country_code IS NULL limit %s', DB_TABLE_PREFIX, $offset);
////
////                    $dbCommand->query();
////                    $error = $result->numRows();
////                } while ($error > 0);
//
//
//                $result_ips = $dbCommand->query( sprintf('select count(*) as total from %st_market_stats_tmp', DB_TABLE_PREFIX) );
//                $result_ips = $result_ips->row();
//                $total_ips = $result_ips['total'];
//                if($total_ips > 0) {
//                    for( $i=0;($i*$offset)<$total_ips;$i++ ) {
//                        $start = $i*$offset;
//                        $dbCommand->query();
//                    }
//                }
//            }

            osc_set_preference('market_version', '13', 'market', 'STRING');
            osc_reset_preferences();
        }

    }
    osc_add_hook('init', 'market_update_version');

    // remove dash menu entry
    $adminmenu = AdminMenu::newInstance();
    $adminmenu->clear_menu();
    // replace dash for market/dashboard.php
    osc_add_admin_menu_page(
        __('Dashboard', 'market'),
        osc_admin_render_plugin_url('market/dashboard.php'),
        'dash_market',
        'moderator'
        );
    $adminmenu->init();
    osc_remove_admin_menu_page('dash');


    // market oc-admin dashboard title
    function market_dashboard_title($string){
        if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'market/dashboard.php'){
            $string = __('Dashboard', 'market') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
        }
        return $string;
    }
    osc_add_filter('custom_plugin_title', 'market_dashboard_title');

    // market oc-admin dashboard page -> redirect to market/dashboard.php
    function market_redirect_dashboard()
    {
        $page    = Params::getParam('page');
        $action  = Params::getParam('action');
        if($page=='' && $action=='') {
            market_redirect_to( osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'dashboard.php') );
        }
    }
    osc_add_hook('init_admin', 'market_redirect_dashboard');

    function market_admin_menu_plugin() {
        echo '<h3><a href="#">Market</a></h3>
        <ul>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'market') . '</a></li>
            <li><a href="' . osc_admin_configure_plugin_url("market/index.php").'">&raquo; ' . __('Configure categories', 'market') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php') . '">&raquo; ' . __('Stats', 'market') . '</a></li>
        </ul>' ;
    }

    function market_redirect_to($url) {
        header('Location: ' . $url);
        exit;
    }

    function market_is_checked($version, $versions) {
        $versions = explode(",", $versions);
        foreach($versions as $v) {
            if($version==$v) {
                return true;
            }
        }
        return false;
    }

    function market_configure_link() {
        osc_plugin_configure_view(osc_plugin_path(__FILE__) );
    }


    function market_form($catId = null) {
        if($catId!="") {
            if(osc_is_this_category('market', $catId)) {
                $detail = array();
                $aSize        = market_banner_size($catId);

                $is_theme_category  = false;
                $sCategory          = osc_get_preference('market_categories_theme','market');
                $aCategory          = explode(',', $sCategory );
                if(in_array($catId , $aCategory)) {
                    $is_theme_category = true;
                }

                $market_files = null;
                // session variables
                $detail = get_market_session_variables($detail);

                include_once( MARKET_PLUGIN_PATH . 'item_edit.php' ) ;
                Session::newInstance()->_clearVariables();
            }
        }
    }

    function market_item_detail() {
        if(osc_is_this_category('market', osc_item_category_id())) {
            $market_files = ModelMarket::newInstance()->getFileFromItem(osc_item_id());
            require_once( MARKET_PLUGIN_PATH . 'item_detail.php' );
        }
    }

    function market_load_data() {
        if(osc_is_ad_page()) {
            $market = ModelMarket::newInstance()->findByItemId(osc_item_id());
            $market['files'][0] = ModelMarket::newInstance()->getFileFromItem(osc_item_id());
            View::newInstance()->_exportVariableToView("market_ad", $market);
        }
    }

    function market_item_edit($catId = null, $item_id = null) {
        if( osc_is_this_category('market', $catId) ) {
            $market_files   = ModelMarket::newInstance()->getFilesFromItem($item_id);
            $market         = ModelMarket::newInstance()->findByItemId($item_id);
            $market_item    = Item::newInstance()->findByPrimaryKey($item_id);

            $secret = $market_item['s_secret'];

            $is_theme_category  = false;
            $sCategory          = osc_get_preference('market_categories_theme','market');
            $aCategory          = explode(',', $sCategory );
            if(in_array($catId , $aCategory)) {
                $is_theme_category = true;
            }

            $aSize = market_banner_size($catId);
//            error_log( 'market antes de rellenar con los datos de la session  ' . print_r($market, true) );
            // session variables
            $detail = get_market_session_variables($market);
            View::newInstance()->_exportVariableToView("market_ad", $detail);
//            error_log(print_r($detail, true));

            unset($market_item);
            include_once( MARKET_PLUGIN_PATH . 'item_edit.php' ) ;
        }
    }

    /**
     * Add/edit item
     *  add: slug, demo url
     *  edit: demo url (slug is used as identifier cannot be updated)
     *
     * @param type $catId
     * @param type $item_id
     */
    function market_edit_post($catId = null, $item_id = null)
    {
        if($catId!=null) {
            if(osc_is_this_category('market', $catId)) {

                $market = ModelMarket::newInstance();
                // CREATE SLUG
                $_slug = Params::getParam('market_slug');
                if($item_id != null) {
                    if($_slug!='') {
                        $slug = $_slug;
                    } else {
                        View::newInstance()->_exportVariableToView('item', Item::newInstance()->findByPrimaryKey($item_id));
                        $slug_tmp = $slug = osc_sanitizeString(osc_item_title());
                        $slug_unique = 2;
                        while(true) {
                            if($market->checkSlug($slug, $item_id)) {
                                break;
                            } else {
                                $slug = $slug_tmp . "_" . $slug_unique;
                                $slug_unique++;
                            }
                        }
                    }
                }

                // @TODO , capturar <input type='checkbox' value='1' name='b_featured'> DESTACAR </input>
                $featured = Params::getParam('market_featured');
                if($featured!='') {
                    $featured = 1;
                } else {
                    $featured = 0;
                }


                // NEED TO INSERT NEW FILE?
                $market_id = $market->marketExists($item_id);
                if($market_id==false) {
                    $market_id = $market->insertMarket($item_id, $slug, $featured, Params::getParam('market_preview'));
                } else {
                  $market->update(array('s_slug' => $slug, 'b_featured' => (int)$featured, 's_preview' => Params::getParam('market_preview')), array('pk_i_id' => $market_id));
                }

                // UPLOAD NEW BANNER
                $banner = Params::getFiles('market_banner');
                if (isset($banner['error']) && $banner['error'] == UPLOAD_ERR_OK) {
                    require LIB_PATH . 'osclass/mimes.php';
                    $aMimesAllowed = array();
                    $aExt = explode(',', osc_allowed_extension());
                    foreach($aExt as $ext){
                        $mime = $mimes[$ext];
                        if( is_array($mime) ){
                            foreach($mime as $aux){
                                if( !in_array($aux, $aMimesAllowed) ) {
                                    array_push($aMimesAllowed, $aux );
                                }
                            }
                        } else {
                            if( !in_array($mime, $aMimesAllowed) ) {
                                array_push($aMimesAllowed, $mime );
                            }
                        }
                    }
                    $failed = false;
                    $maxSize = osc_max_size_kb() * 1024;
                    $bool_img = false;
                    $size = $banner['size'];
                    if($size <= $maxSize){
                        $fileMime = $banner['type'] ;
                        if(in_array($fileMime,$aMimesAllowed)) {
                            if (move_uploaded_file($banner['tmp_name'], osc_get_preference('upload_path', 'market').$item_id."_.jpg")) {
                                @unlink(osc_get_preference('upload_path', 'market').$item_id.".jpg");

                                // prepare banner path
                                $banner_path = osc_get_preference('upload_path', 'market');
                                $rel_banner_path  = str_replace(ABS_PATH, '', $banner_path);


                                $aSize = market_banner_size($catId);

                                ImageResizer::fromFile($banner_path . $item_id."_.jpg")->resizeTo($aSize['w'], $aSize['h'])->saveToFile($banner_path . $item_id.".jpg") ;

                                @unlink($banner_path . $item_id."_.jpg");
                                $market->update(array('s_banner_path' => $rel_banner_path, 's_banner' => $item_id.".jpg"), array('pk_i_id' => $market_id));
                            } else {
                                if(OC_ADMIN) {
                                    osc_add_flash_error_message(__('Banner was not uploaded because it has incorrect extension', 'market'), 'admin');
                                } else {
                                    osc_add_flash_error_message(__('Banner was not uploaded because it has incorrect extension', 'market'));
                                }
                            }
                        } else {
                            if(OC_ADMIN) {
                                osc_add_flash_error_message(__('Banner was not uploaded because it has incorrect extension', 'market'), 'admin');
                            } else {
                                osc_add_flash_error_message(__('Banner was not uploaded because it has incorrect extension', 'market'));
                            }
                        }
                    } else {
                        if(OC_ADMIN) {
                            osc_add_flash_error_message(__('The banner is too big', 'market'), 'admin');
                        } else {
                            osc_add_flash_error_message(__('The banner is too big', 'market'));
                        }
                    }

                }
            }
        }
    }

    function market_delete_item($item) {
        $files = ModelMarket::newInstance()->getFilesFromItem($item);
        foreach($files as $file) {
            $id = $file['pk_i_id'];
            // delete file
            ModelMarket::newInstance()->deleteMarket_stat($id);
            // delete file stats
            ModelMarket::newInstance()->deleteMarket_file($id);
        }
        // delete market entry
        return ModelMarket::newInstance()->deleteMarket($item);
    }

    function market_pre_item_post() {
        // --- save attributes into session ------------------------------------
        Session::newInstance()->_setForm('market_preview'   , Params::getParam("market_preview") );
        Session::newInstance()->_setForm('market_slug'      , Params::getParam("market_slug") );
        Session::newInstance()->_setForm('market_banner'    , Params::getParam("market_banner") );
        Session::newInstance()->_setForm('market_featured'  , Params::getParam("market_featured") );
        // keep form
        Session::newInstance()->_keepForm('market_preview');
        Session::newInstance()->_keepForm('market_slug');
        Session::newInstance()->_keepForm('market_banner');
        Session::newInstance()->_keepForm('market_featured');

        // ---------------------------------------------------------------------
        $market_slug            = Params::getParam("market_slug");

        $aError = array();
        $error = false;

        // at least one compatible version checked
        if( $market_slug == '' ) {
            $aError[] = __('Slug cannot be empty', 'market');
        }

        if(count($aError) > 0) {
            $error = implode('<br/>', $aError);
            if(OC_ADMIN) {
                osc_add_flash_error_message( $error, 'admin');
                header('Location: '.$_SERVER['HTTP_REFERER'] );
                exit;
            } else {
                osc_add_flash_error_message( $error, 'market');
            }

        }
    }

    function get_market_session_variables(&$detail) {
        if( Session::newInstance()->_getForm('market_preview') != '' ) {
            $detail['s_preview'] = Session::newInstance()->_getForm('market_preview');
        }
        if( Session::newInstance()->_getForm('market_slug') != '' ) {
            $detail['s_slug'] = Session::newInstance()->_getForm('market_slug');
        }
        if( Session::newInstance()->_getForm('market_banner') != '' ) {
            $detail[''] = Session::newInstance()->_getForm('market_banner');
        }
        if( Session::newInstance()->_getForm('market_featured') != '' ) {
            $detail['b_featured'] = 1;
        }

        return $detail;
    }

    /*
     * Add menus at dashboard user
     */
    function market_user_menu($options)
    {
        $last = array_pop($options);

        $url_market_front = osc_render_file_url(osc_plugin_folder(__FILE__) . 'list_market_front.php');
        $options[] = array('name' => __('Market listings'), 'url' => $url_market_front, 'class' => 'opt_market') ;;
        $options[] = $last;
        return $options;
    }
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'market_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'market_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'market_configure_link');

    osc_add_hook('item_detail', 'market_item_detail');
    osc_add_hook('before_html', 'market_load_data');

    osc_add_hook('pre_item_post',   'market_pre_item_post');

    osc_add_hook('item_form', 'market_form');
    osc_add_hook('item_edit', 'market_item_edit');

    osc_add_hook('item_edit_post', 'market_edit_post');
    osc_add_hook('item_form_post', 'market_edit_post');

    osc_add_hook('delete_item', 'market_delete_item');

    if(osc_version() < 300) {

    } else {
        osc_add_filter('user_menu_filter', 'market_user_menu');
    }

    if(osc_version() < 300) {
        osc_add_hook('admin_menu', 'market_admin_menu_plugin');
    } else {
        osc_add_admin_menu_page(__('Market', 'market'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php'), 'plugin_market');
        osc_add_admin_submenu_page('plugin_market', __('Manage Market', 'market'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'manage.php'), 'plugin_market_settings');
        osc_add_admin_submenu_page('items', __('Manage Market', 'market'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'list_market.php'), 'plugin_market_settings');
        osc_add_admin_submenu_page('plugin_market', __('Settings', 'market'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php'), 'plugin_market_settings');
        osc_add_admin_submenu_page('plugin_market', __('Configure categories', 'market'), osc_admin_configure_plugin_url("market/index.php"), 'plugin_market_categories');
        osc_add_admin_submenu_page('plugin_market', __('Stats', 'market'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php'), 'plugin_market_stats');
    }

    function market_style_admin_menu() {
    ?><style>
        #plugin_market .ico {
            background-image: url('<?php echo osc_base_url();?>oc-content/plugins/<?php echo osc_plugin_folder(__FILE__);?>img/split.png') !important;
            background-position:0px -48px;
        }
        #plugin_market .ico:hover,
        .current #plugin_market .ico{
            background-position:0px -0px;
        }
        body.compact #plugin_market .ico{
            background-position:-48px -48px;
        }
        body.compact #plugin_market .ico:hover,
        body.compact .current #plugin_market .ico{
            background-position:-48px 0px;
        }
    </style>

    <?php

    if(Params::getParam('page')=='items') {
        if(Params::getParam('action')=='post') {
            $title  = __('Add market item');
        }
        if(Params::getParam('action')=='item_edit') {
            $title  = __('Edit market item');
        }
        $step = Params::getParam('step');

    ?>
    <style>
        textarea#meta_short-description,
        .photo_container{
            width: 516px;
        }
        #market_banner,
        .fit_market{
            width: 540px;
        }
        <?php if(Params::getParam('action')=='post') { ?>
        div#plugin-hook {
            display:none;
            width: 540px;
        }
        .meta_list {
            display:none;
        }
        #plugin-hook .row label {
            float:none;
        }
        <?php } ?>
    </style>
    <?php if(Params::getParam('action')=='post') { ?>
    <script type="text/javascript">
        // prepare step 1
        $(document).ready(function(){
            // remove right-side
            $('#right-side').hide();
            $('#left-side').attr('id', '');
            // complete with some information
            $('h2.render-title').text('<?php echo $title; ?>');

            // hide upload images
            $('.photo_container').hide();

            // hide title and description
            $('.input-title-wide').hide();
            $('.input-description-wide').hide();

            // hide input submit
            $('input[type="submit"]').hide();
        });
    </script>
        <?php
            }
        } ?>
        <?php
    }

    osc_add_hook('admin_footer','market_style_admin_menu');

    /**
     * Improve add/edit market item flow
     */
    function market_item_flow()
    {
        $step           = Params::getParam('market_step');
        $next_step      = __('Next step', 'market');
        $s_step_2       = __(' - market information', 'market');
        $error_step_1   = __('All fields are required', 'market');
        $s_screenshots  = __('Screenshots', 'market');

        $admin_email    = osc_logged_admin_email();
        $admin_user     = osc_logged_admin_name();

    ?>
        <script type="text/javascript">
            $(document).ready(function(){
            <?php if(Params::getParam('page')=='items' && Params::getParam('action')=='post') { ?>
                // set default user / email
                $('input#contactName').val('<?php echo osc_esc_js($admin_user); ?>');
                $('input#contactEmail').val('<?php echo osc_esc_js($admin_email); ?>');
                // button next step
                var next_btn = $('<input class="btn btn-primary next-step" type="button"/>').val('<?php echo osc_esc_js($next_step); ?>');

                next_btn.click(function(){
                    var category    =  $('select#parentCategory').val();
                    if($.trim(category)!=''){
                        // prepare step 2
                        step_two();
                    } else {
                        // show flash message
                        alert('<?php echo osc_esc_js($error_step_1);?>');
                    }
                });

                $('div.form-actions').append(next_btn);
            <?php } else if(Params::getParam('page')=='items' && Params::getParam('action')=='item_edit') { ?>

            <?php } ?>
            });

            function step_two() {
                console.log('step two');
                // update title
                $('h2.render-title').text($('h2.render-title').text()+'<?php echo $s_step_2; ?>');

                // hide step ONE
                $('.category').hide();
                // show step TWO
                $('.input-title-wide').show();
                $('.input-description-wide').show();
                $('.meta_list').show();

                var meta_copy = $('div.meta_list');
                $('div.meta_list').remove();
                $('div.input-description-wide').before(meta_copy);
                $('textarea#meta_short-description').attr('rows', 5);

                $('#plugin-hook').css('display', 'block');

                // show submit input
                $('input[type="submit"]').show();
                $('.next-step').hide();
            }
        </script>
    <?php
    }
    osc_add_hook('admin_header','market_item_flow');

    function ajax_featured_off()
    {
        $return = false;
        $item = Item::newInstance()->findByPrimaryKey(Params::getParam('itemId'));
        if($item!== FALSE ) {
//            error_log($item['s_secret'].'    ==    '.Params::getParam('secret') );
            if($item['s_secret'] == Params::getParam('secret')) {

                $return = ModelMarket::newInstance()->featuredOff(Params::getParam('itemId'));
            }
        }

        if($return) {
            osc_add_flash_ok_message(__('Listing Featured successfully', 'market'), 'admin');
        } else {
            osc_add_flash_error_message(__('Listing cannot be featured successfully', 'market'), 'admin');
        }
        market_redirect_to( $_SERVER['HTTP_REFERER'] );
    }
    osc_add_hook('ajax_admin_featured_off', 'ajax_featured_off');

    function ajax_featured_on()
    {
        $return = false;
        $item = Item::newInstance()->findByPrimaryKey(Params::getParam('itemId'));
        if($item!== FALSE ) {
//            error_log($item['s_secret'].'    ==    '.Params::getParam('secret') );
            if($item['s_secret'] == Params::getParam('secret')) {
                $return = ModelMarket::newInstance()->featuredOn(Params::getParam('itemId'));
            }
        }

        if($return) {
            osc_add_flash_ok_message(__('Listing Featured successfully', 'market'), 'admin');
        } else {
            osc_add_flash_error_message(__('Listing cannot be featured successfully', 'market'), 'admin');
        }
        market_redirect_to( $_SERVER['HTTP_REFERER'] );
    }
    osc_add_hook('ajax_admin_featured_on', 'ajax_featured_on');

    /**
     * highlight featured items
     */
    function market_highlight_featured($array, $item)
    {
        $market = ModelMarket::newInstance()->findByItemId($item['pk_i_id']);
        if($market['b_featured'] == 1) {
            $array['title'] = $array['title'].'<i style="float: right; padding-right: 15px;"><b>'.__('Featured', 'market').'</b></i>';
        }
        return $array;
    }
    osc_add_hook('items_processing_row', 'market_highlight_featured');

    /*
     * Add action at manage listings
     */
    function market_add_actions( $options, $item )
    {
        // si market files ...
        $aux = array();
        // add actions
        $market = ModelMarket::newInstance()->findByItemId($item['pk_i_id']);
        $aux[] = '<a href="'.osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'market_file_frm.php').'?itemId='.$item['pk_i_id'].'">'.__('Add/Edit files', 'market').'</a>';
        $aux[] = '<a href="'.osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php').'?itemId='.$item['pk_i_id'].'">'.__('Show stats', 'market').'</a>';
        if($market['b_featured']==1) {
            $aux[] = '<a href="'.osc_admin_ajax_hook_url('featured_off').'&itemId='.$item['pk_i_id'].'&secret='.$item['s_secret'].'">'.__('Featured off', 'market').'</a>';
        } else {
            $aux[] = '<a href="'.osc_admin_ajax_hook_url('featured_on').'&itemId='.$item['pk_i_id'].'&secret='.$item['s_secret'].'">'.__('Featured on', 'market').'</a>';
        }

        foreach($options as $value) {
            $aux[] = $value;
        }
        return $aux;
    }

    osc_add_filter('actions_manage_items', 'market_add_actions');
    /*
     FRONT END CUSTOM PAGES - MARKET PAGES -
     List market listings - DONE
     http://localhost/osclass_git/index.php?page=custom&file=market/list_market_front.php

     List Market files - DONE
     http://localhost/osclass_git/index.php?page=custom&file=market/market_manage_files.php?itemId=6

     * Add/edit market files - DONE
     http://localhost/osclass_git/index.php?page=custom&file=market/market_file_frm_front.php&itemId=6
     */

    // ------ sumbit forms  ---------
    /*
     * Common add new MARKET FILES code
     */
    function _market_add_file($item_id, $market, &$error, &$aError, &$file_version, &$file_download_url, &$aCompatible, $status = 1)
    {
        // add new file - check required params
        $market_id = ModelMarket::newInstance()->marketExists($item_id);

        // file version
        $file_version = Params::getParam('market_version_new');
        if( $file_version == '') {
            $error = true;
            $aError[] = __('File version cannot be empty', 'market');
        } else {
            $file_version = str_replace(',', '.', $file_version);
            if(preg_match('|^\d+(\.\d+)+$|', $file_version)) {
                // check if file version doesn't exist
                $market_aux_file = ModelMarket::newInstance()->getFileBySlug( $market['s_slug'], $file_version );
                if( !empty($market_aux_file) ){
                    $error = true;
                    $aError[] = __('File version exist', 'market'). " " .$file_version;
                }
            } else {
                $error = true;
                $aError[] = __('File version incorrect format, ex: 1.2.3 (Cannot )', 'market');
            }
        }
        // download file
        $info_img           = Params::getFiles('market_file_new');

        if($info_img['name'] == '') {
            $error = true;
            $aError[] = __('File must be uploaded', 'market');
        }

        $aCompatible = Params::getParam('market_new_comp_versions');
        if( !is_array($aCompatible) || count($aCompatible) == 0) {
            $aCompatible = array();
            $error = true;
            $aError[] = __('At least one compatible version must be specified', 'market');
        }

        if(!$error) {
            // UPLOAD NEW FILE
            $file = Params::getFiles('market_file_new');
            if (isset($file['error']) && $file['error'] == UPLOAD_ERR_OK) {
                // upload file market
                $result = _market_upload_market_file( $item_id, $file , $aError, $error);
                // insert
                if( !$result['error'] ) {
                    $path = $result['msg'];
                    ModelMarket::newInstance()->insertFile($market_id, $path, '', $file_version, Params::getParam('market_new_comp_versions'),$status);
                    // no errors, update item dt_mod_date
                    Item::newInstance()->update(
                        array('dt_mod_date' => date('Y-m-d H:i:s') ),
                        array('pk_i_id' => $item_id)
                        );
                }
            }
        }
    }

    /*
     * Common update MARKET FILES code
     */
    function _market_update_file($item_id, $file_id, $market, &$error, &$aError, &$file_version, &$file_download_url, &$aCompatible, $path)
    {
        // download url OR download file
        $haveFile           = Params::getParam('market_file_exist');
        $file               = Params::getFiles('market_file_new');

        if($haveFile != 1) {
            if( $file['name'] == '') {
                $error = true;
                $aError[] = __('At least one download file must be specified', 'market');
            }
        }

        $aSet = array();

        // compatible versions
        $aCompatible = Params::getParam('market_new_comp_versions');

        if( !is_array($aCompatible) || count($aCompatible) == 0) {
            $error = true;
            $aError[] = __('At least one compatible version must be specified', 'market');
        } else {
            $aCompatible = array_keys($aCompatible);
            $aSet['s_compatible'] = implode(",", $aCompatible);
        }

        if( !$error ) {
            $result     = false;
            $market_id  = ModelMarket::newInstance()->marketExists($item_id);

            if (isset($file['error']) && $file['error'] == UPLOAD_ERR_OK) {
                // upload file market
                $result = _market_upload_market_file( $item_id, $file , $aError, $error);
                // insert
                if( !$result['error'] ) {
                    $aSet['s_file']  =  $result['msg'];
                }
            } else if($file['error'] == UPLOAD_ERR_INI_SIZE) {
                $error = true;
                $aError[] = __('Exceeded max file size', 'market');
            }
            // update
//            error_log( "_market_update_file " . print_r($aSet, true) );
            $result = ModelMarket::newInstance()->updateFile($market_id, $file_id, $aSet );
        }

        if(!$result) {
            $error = true;
            $aError[] = __('There are problems updating file market', 'market');
        } else {
            // no errors, update item dt_mod_date
            Item::newInstance()->update(
                    array('dt_mod_date' => date('Y-m-d H:i:s') ),
                    array('pk_i_id' => $item_id)
                    );
        }

    }

    function _market_upload_market_file( $item_id, $file, $aError, $error ) {
        require LIB_PATH . 'osclass/mimes.php';
        $aMimesAllowed = array();
        $aExt = explode(',', osc_get_preference('allowed_ext', 'market'));
        foreach($aExt as $ext) {
            $mime = $mimes[$ext];
            if( is_array($mime) ){
                foreach($mime as $aux){
                    if( !in_array($aux, $aMimesAllowed) ) {
                        array_push($aMimesAllowed, $aux );
                    }
                }
            } else {
                if( !in_array($mime, $aMimesAllowed) ) {
                    array_push($aMimesAllowed, $mime );
                }
            }
        }
        $failed = false;
        $maxSize = osc_max_size_kb() * 1024;
        $bool_img = false;
        $size = $file['size'];
        if($size <= $maxSize) {
            $fileMime = $file['type'] ;
            if(in_array($fileMime,$aMimesAllowed)) {
                // category item
                $item       = Item::newInstance()->findByPrimaryKey( $item_id );
                $aItem[]    = $item;
                $aItem      = Item::newInstance()->extendCategoryName($aItem);
                $item       = $aItem[0];

                $date = date('YmdHis');
                $file_name = $date.'_'.$item_id.'_'.$file['name'];
                $path = osc_get_preference('upload_path', 'market').$item['s_category_name'].'/'.$file_name;


                if (move_uploaded_file($file['tmp_name'], $path)) {

                    return array('error' => false, 'msg' => $path);

                } else {
                    $error = true;
                    $aError[] = __('Some of the files were not uploaded because they have incorrect extension', 'market');
                }
            } else {
                $error = true;
                $aError[] = __('Some of the files were not uploaded because they have incorrect extension', 'market');
            }
        } else {
            $error = true;
            $aError[] = __('The file is too big', 'market');
        }
        return array('error' => true);
    }
?>