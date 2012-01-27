<?php
/*
Plugin Name: Universe
Plugin URI: http://www.osclass.org/
Description: -
Version: 0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: universe
Plugin update URI: offerButton
*/


    require_once('ModelUniverse.php');

    function universe_install() {
        ModelUniverse::newInstance()->import('universe/struct.sql') ;
        @mkdir(osc_content_path().'uploads/universe/');
        osc_set_preference('upload_path', osc_content_path().'uploads/universe/', 'universe', 'STRING');
        osc_set_preference('allowed_ext', 'zip', 'universe', 'INTEGER');
    }

    function universe_uninstall() {
        try {
            $files = ModelUniverse::newInstance()->getFiles();
            foreach($files as $file) {
                @unlink(osc_get_preference('upload_path', 'universe').$file['s_file']);
            }
            rmdir(osc_get_preference('upload_path','universe'));
            ModelUniverse::newInstance()->uninstall();
            osc_delete_preference('upload_path', 'universe');
            osc_delete_preference('allowed_ext', 'universe');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    function universe_admin_menu() {
        echo '<h3><a href="#">Universe</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'universe') . '</a></li>
            <li><a href="'.osc_admin_configure_plugin_url("universe/index.php").'">&raquo; ' . __('Configure categories', 'universe') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'general.php') . '">&raquo; ' . __('Overview', 'universe') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php') . '">&raquo; ' . __('Stats', 'universe') . '</a></li>
        </ul>';
    }
    
    function universe_redirect_to($url) {
        header('Location: ' . $url);
        exit;
    }
    
    function universe_configure_link() {
        osc_plugin_configure_view(osc_plugin_path(__FILE__) );
    }
    

    function universe_form($catId = null) {
        if($catId!="") {
            if(osc_is_this_category('universe', $catId)) {
                $universe_files = null;
                require_once 'item_edit.php';
            }
        }
    }

    function universe_item_detail() {
        if(osc_is_this_category('universe', osc_item_category_id())) {
            $universe_files = ModelUniverse::newInstance()->getFilesFromItem(osc_item_id());
            require_once 'item_detail.php';
        }
    }

    function universe_item_edit($catId = null, $item_id = null) {
        if(osc_is_this_category('universe', $catId)) {
            $universe_files = ModelUniverse::newInstance()->getFilesFromItem($item_id);
            $universe_item = Item::newInstance()->findByPrimaryKey($item_id);
            $secret = $universe_item['s_secret'];
            unset($universe_item);
            require_once 'item_edit.php';
        }
    }

    function universe_edit_post($catId = null, $item_id = null) {

        if($catId!=null) {
            if(osc_is_this_category('universe', $catId)) {
                $_slug = Params::getParam('universe_slug');
                if($_slug!='') {
                    $slug = $_slug;
                } else {
                    $ufiles = ModelUniverse::newInstance()->getFilesFromItem($item_id);
                    if(isset($ufiles[0]) && isset($ufiles[0]['s_slug'])) {
                        $slug = $ufiles[0]['s_slug'];
                    } else {
                        View::newInstance()->_exportVariableToView('item', Item::newInstance()->findByPrimaryKey($item_id));
                        $slug_tmp = $slug = osc_sanitizeString(osc_item_title());
                        $slug_unique = 2;
                        while(true) {
                            if(ModelUniverse::newInstance()->checkSlug($slug, $item_id)) {
                                break;
                            } else {
                                $slug = $slug_tmp . "_" . $slug_unique;
                                $slug_unique++;
                            }
                        }
                    }
                }
                $versions = Params::getParam('universe_version');
                $enables = Params::getParam('universe_enabled');
                if(is_array($versions)) {
                    if(osc_is_admin_user_logged_in()) {
                        foreach($versions as $k => $v) {
                            ModelUniverse::newInstance()->updateFile($item_id, $k, array('s_version' => $v, 'b_enabled' => (isset($enables[$k]) && $enables[$k]==1)?1:0));
                        }
                    } else {
                        foreach($versions as $k => $v) {
                            ModelUniverse::newInstance()->updateFile($item_id, $k, array('s_version' => $v));
                        }
                    }
                }
                $file = Params::getFiles('universe_file_new');
                if (isset($file['error']) && $file['error'] == UPLOAD_ERR_OK) {
                    require LIB_PATH . 'osclass/mimes.php';
                    $aMimesAllowed = array();
                    $aExt = explode(',', osc_get_preference('allowed_ext', 'universe'));
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
                            $path = osc_get_preference('upload_path', 'universe').$file_name;
                            if (move_uploaded_file($file['tmp_name'], $path)) {
                                $failed = ModelUniverse::newInstance()->insertFile(array('fk_i_item_id' => $item_id, 's_file' => $path, 's_version' => Params::getParam('universe_version_new')));
                            } else {
                                $failed = true;
                            }
                        } else {
                            $failed = true;
                        }
                    } else {
                        $failed = true;
                    }
                    if($failed) {
                        osc_add_flash_error_message(__('Some of the files were not uploaded because they have incorrect extension', 'universe'),'admin');
                    }
                }
                $utype = Params::getParam('universe_type');
                ModelUniverse::newInstance()->updateFilesFromItem($item_id, array('s_slug' => $slug, 'e_type' => ($utype==''?'PLUGIN':$utype)));
            }
        }
    }

    function universe_delete_item($item) {
        $files = ModelUniverse::newInstance()->getFilesFromItem($item);
        
    }

    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'universe_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'universe_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'universe_configure_link');


    osc_add_hook('item_detail', 'universe_item_detail');

    osc_add_hook('item_form', 'universe_form');
    osc_add_hook('item_edit', 'universe_item_edit');
    
    osc_add_hook('item_edit_post', 'universe_edit_post');
    osc_add_hook('item_form_post', 'universe_edit_post');

    osc_add_hook('delete_item', 'universe_delete_item');
    
    
    osc_add_hook('admin_menu', 'universe_admin_menu');
    
?>