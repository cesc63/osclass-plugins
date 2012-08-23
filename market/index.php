<?php
/*
Plugin Name: Market
Plugin URI: http://www.osclass.org/
Description: This is for internal use only, DO NOT make public
Version: 0.2
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: market
Plugin update URI: 
*/

    define( 'MARKET_VERSION', 0.2) ;
    define( 'MARKET_PLUGIN_PATH', osc_plugins_path() . 'market/' ) ;

    require_once( MARKET_PLUGIN_PATH . '/ModelMarket.php' ) ;
    require_once( MARKET_PLUGIN_PATH . '/helpers.php' ) ;

    function market_install() {
        ModelMarket::newInstance()->import('market/struct.sql') ;
        if(!is_dir(osc_content_path().'uploads/market/')) {
            @mkdir(osc_content_path().'uploads/market/');
        }
        osc_set_preference('upload_path', osc_content_path().'uploads/market/', 'market', 'STRING');
        osc_set_preference('allowed_ext', 'zip', 'market', 'INTEGER');
    }

    function market_uninstall() {
        try {
            osc_deleteDir(osc_get_preference('upload_path','market'));
            ModelMarket::newInstance()->uninstall();
            osc_delete_preference('upload_path', 'market') ;
            osc_delete_preference('allowed_ext', 'market') ;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    function market_admin_menu_plugin() {
        echo '<h3><a href="#">Market</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'market') . '</a></li>
            <li><a href="'.osc_admin_configure_plugin_url("market/index.php").'">&raquo; ' . __('Configure categories', 'market') . '</a></li>
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
                $market_files = null;
                require_once( MARKET_PLUGIN_PATH . 'item_edit.php' ) ;
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
            $market_files = ModelMarket::newInstance()->getFilesFromItem($item_id);
            $market = ModelMarket::newInstance()->findByItemId($item_id);
            $market_item = Item::newInstance()->findByPrimaryKey($item_id);
            
            $secret = $market_item['s_secret'];
            
            unset($market_item);
            require_once( MARKET_PLUGIN_PATH . 'item_edit.php' ) ;
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
    function market_edit_post($catId = null, $item_id = null) {

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
                
                // NEED TO INSERT NEW FILE?
                $market_id = $market->marketExists($item_id);
                if($market_id==false) {
                    $market_id = $market->insertMarket($item_id, $slug, Params::getParam('market_preview'));
                } else {
                  $market->update(array('s_slug' => $slug, 's_preview' => Params::getParam('market_preview')), array('pk_i_id' => $market_id));  
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
                                ImageResizer::fromFile(osc_get_preference('upload_path', 'market').$item_id."_.jpg")->resizeTo(624,224)->saveToFile(osc_get_preference('upload_path', 'market').$item_id.".jpg") ;
                                @unlink(osc_get_preference('upload_path', 'market').$item_id."_.jpg");
                                $market->update(array('s_banner' => $item_id.".jpg"), array('pk_i_id' => $market_id));
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
        Session::newInstance()->_setForm('market_preview', Params::getParam("market_preview") );
        Session::newInstance()->_setForm('market_slug'   , Params::getParam("market_slug") );
        Session::newInstance()->_setForm('market_banner' , Params::getParam("market_banner") );
        // keep form
        Session::newInstance()->_keepForm('market_preview');
        Session::newInstance()->_keepForm('market_slug');
        Session::newInstance()->_keepForm('market_banner');
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
    </style><?php
    }

    osc_add_hook('admin_footer','market_style_admin_menu');
    
    /*
     * Add action at manage listings
     */
    function market_add_actions( $options, $item )
    {
        // si market files ... 
        $aux = array();
        // add actions
        $aux[] = '<a href="'.osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'market_file_frm.php').'?itemId='.$item['pk_i_id'].'">'.__('Add/Edit files', 'market').'</a>';
        $aux[] = '<a href="'.osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php').'?itemId='.$item['pk_i_id'].'">'.__('Show stats', 'market').'</a>';
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
    function _market_add_file($item_id, $market, $error, $aError, $file_version, $file_download_url, $aCompatible, $status = 1) 
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
        // download url OR download file
        $info_img           = Params::getFiles('market_file_new');
        $file_download_url  = Params::getParam('market_download_url');
        if($file_download_url == '' && $info_img['name'] == '') {
            $error = true;
            $aError[] = __('At least one download file must be specified', 'market');
        }
        // compatible versions
        $aCompatible = Params::getParam('market_new_comp_versions');
        
        if( !is_array($aCompatible) || count($aCompatible) == 0) { 
            $aCompatible = array();
            $error = true;
            $aError[] = __('At least one compatible version must be specified', 'market');
        }  else {
            $aCompatible = array_keys($aCompatible);
        }

        if(!$error) { 
            // UPLOAD NEW FILE
            $file = Params::getFiles('market_file_new');
            if($file_download_url != '') {
                ModelMarket::newInstance()->insertFile($market_id, '', Params::getParam('market_download_url'), $file_version, Params::getParam('market_new_comp_versions'), $status);
            } else if (isset($file['error']) && $file['error'] == UPLOAD_ERR_OK) {
                // upload file market 
                $result = _market_upload_market_file( $item_id, $file , $aError, $error);
                // insert
                if( !$result['error'] ) {
                    $path = $result['msg'];
                    ModelMarket::newInstance()->insertFile($market_id, $path, '', $file_version, Params::getParam('market_new_comp_versions'),$status);
                }
            }
        }
    }
    
    /*
     * Common update MARKET FILES code
     */
    function _market_update_file($item_id, $file_id, $market, $error, $aError, $file_version, $file_download_url, $aCompatible, $path)
    {
        // download url OR download file
        $haveFile           = Params::getParam('market_file_exist');
        $file               = Params::getFiles('market_file_new');
        $file_download_url  = Params::getParam('market_download_url');
        if($haveFile != 1) {
            if($file_download_url == '' && $file['name'] == '') {
                
                $error = true;
                $aError[] = __('At least one download file must be specified', 'market');
            }
        }
        
        // compatible versions
        $aCompatible = Params::getParam('market_new_comp_versions');
        
        if( !is_array($aCompatible) || count($aCompatible) == 0) { 
            $error = true;
            $aError[] = __('At least one compatible version must be specified', 'market');
        } else {
            $aCompatible = array_keys($aCompatible);
        }

        if( !$error ) {
            $result     = false; 
            $market_id  = ModelMarket::newInstance()->marketExists($item_id);
            // UPLOAD NEW FILE
            if($file_download_url != '') {
                $aSet = array(
                    's_download'    => $file_download_url,
                    's_compatible'  => implode(",", $aCompatible),
                    's_file'        => ''
                );
                
                $result = ModelMarket::newInstance()->updateFile($market_id, $file_id, $aSet );
                
                if($result && $haveFile) {
                    @unlink($path);
                }
            } else if (isset($file['error']) ) { 
                if($file['error'] == UPLOAD_ERR_OK) {
                    // upload file market 
                    $result = _market_upload_market_file( $item_id, $file , $aError, $error);
                    // insert
                    if( !$result['error'] ) {
                        $path = $result['msg'];
                        $aSet = array(
                            's_file'        => $path,
                            's_compatible'  => implode(",", $aCompatible),
                            's_download'    => ''
                        );
                        $result = ModelMarket::newInstance()->updateFile($market_id, $file_id, $aSet );
                    }
                } else if($file['error'] == UPLOAD_ERR_INI_SIZE) {
                    $error = true;
                    $aError[] = __('Exceeded max file size', 'market');
                }
            } else if($haveFile) {
                $aSet = array(
                    's_compatible'  => implode(",", $aCompatible),
                    's_download'    => ''
                );
                $result = ModelMarket::newInstance()->updateFile($market_id, $file_id, $aSet );
            }
            if(!$result) {
                $error = true;
                $aError[] = __('There are problems updating file market', 'market');
            }
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
                $date = date('YmdHis');
                $file_name = $date.'_'.$item_id.'_'.$file['name'];
                $path = osc_get_preference('upload_path', 'market').$file_name;
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