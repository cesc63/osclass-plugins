<?php
/*
Plugin Name: Market
Plugin URI: http://www.osclass.org/
Description: This is for internal use only, DO NOT make public
Version: 0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: market
Plugin update URI: 
*/

    define( 'MARKET_VERSION', 0.1) ;
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

    function market_edit_post($catId = null, $item_id = null) {

        if($catId!=null) {
            if(osc_is_this_category('market', $catId)) {
                
                $market = ModelMarket::newInstance();
                
                // CREATE SLUG
                $_slug = Params::getParam('market_slug');
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
                
                // NEED TO INSERT NEW FILE?
                $market_id = $market->marketExists($item_id);
                if($market_id==false) {
                    $market_id = $market->insertMarket($item_id, $slug);
                }

                // UPDATE VERSIONS
                $versions = Params::getParam('market_version');
                $enables = Params::getParam('market_enabled');
                $comp_versions = Params::getParam('market_comp_versions');
                if(is_array($versions)) {
                    if(OC_ADMIN) {
                        foreach($versions as $k => $v) {
                            $c_v = array();
                            if($comp_versions!='') {
                                foreach($comp_versions[$k] as $kk => $vv) {
                                    $c_v[] = $kk;
                                }
                            }
                            $market->updateFile($market_id, $k, array('s_version' => $versions[$k], 's_compatible' => implode(",", $c_v), 'b_enabled' => (isset($enables[$k]) && $enables[$k]==1)?1:0));
                        }
                    } else {
                        foreach($versions as $k => $v) {
                            $c_v = array();
                            if($comp_versions!='') {
                                foreach($comp_versions[$k] as $kk => $vv) {
                                    $c_v[] = $kk;
                                }
                            }
                            $market->updateFile($market_id, $k, array('s_compatible' => implode(",", $c_v), 's_version' => $versions[$k]));
                        }
                    }
                }
                
                
                // UPLOAD NEW FILE
                $file = Params::getFiles('market_file_new');
                if(Params::getParam('market_download_url')!='') {
                    $market->insertFile($market_id, '', Params::getParam('market_download_url'), Params::getParam('market_version_new'), Params::getParam('market_new_comp_versions'));
                } else if (isset($file['error']) && $file['error'] == UPLOAD_ERR_OK) {
                    require LIB_PATH . 'osclass/mimes.php';
                    $aMimesAllowed = array();
                    $aExt = explode(',', osc_get_preference('allowed_ext', 'market'));
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
                    $size = $file['size'];
                    if($size <= $maxSize){
                        $fileMime = $file['type'] ;

                        if(in_array($fileMime,$aMimesAllowed)) {
                            $date = date('YmdHis');
                            $file_name = $date.'_'.$item_id.'_'.$file['name'];
                            $path = osc_get_preference('upload_path', 'market').$file_name;
                            if (move_uploaded_file($file['tmp_name'], $path)) {
                                $market->insertFile($market_id, $path, '', Params::getParam('market_version_new'), Params::getParam('market_new_comp_versions'));
                            } else {
                                if(OC_ADMIN) {
                                    osc_add_flash_error_message(__('Some of the files were not uploaded because they have incorrect extension', 'market'), 'admin');
                                } else {
                                    osc_add_flash_error_message(__('Some of the files were not uploaded because they have incorrect extension', 'market'));
                                }
                            }
                        } else {
                            if(OC_ADMIN) {
                                osc_add_flash_error_message(__('Some of the files were not uploaded because they have incorrect extension', 'market'), 'admin');
                            } else {
                                osc_add_flash_error_message(__('Some of the files were not uploaded because they have incorrect extension', 'market'));
                            }
                        }
                    } else {
                        if(OC_ADMIN) {
                            osc_add_flash_error_message(__('The file is too big', 'market'), 'admin');
                        } else {
                            osc_add_flash_error_message(__('The file is too big', 'market'));
                        }
                    }

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
    }

    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'market_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'market_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'market_configure_link');

    osc_add_hook('item_detail', 'market_item_detail');
    osc_add_hook('before_html', 'market_load_data');

    osc_add_hook('item_form', 'market_form');
    osc_add_hook('item_edit', 'market_item_edit');
    
    osc_add_hook('item_edit_post', 'market_edit_post');
    osc_add_hook('item_form_post', 'market_edit_post');

    osc_add_hook('delete_item', 'market_delete_item');

    osc_add_hook('admin_menu', 'market_admin_menu_plugin');

?>