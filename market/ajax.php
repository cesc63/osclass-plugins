<?php
    switch(Params::getParam('paction')) {
        case 'download':
            $code = Params::getParam('code') ;
            $json = array('msg' => '', 'error' => 1);
            if( $code != '' ) {
                if( preg_match('|(.+)@([A-Za-z0-9\.-]+)$|', $code, $m) ) {
                    $slug    = $m[1] ;
                    $version = $m[2] ;
                } else {
                    $slug    = $code ;
                    $version = '' ;
                }

                $file = ModelMarket::newInstance()->getFileForDownloadBySlug($slug, $version) ;
                if( !empty($file) ) {
                    ModelMarket::newInstance()->insertStat($file['fk_i_market_id'], $file['pk_i_id'], isset($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:'', isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'', '') ;
                    $json = array('msg' => '<iframe src="'.$file['s_download'].'" width="0" heigtht="0" style="width: 0px; height: 0px; display: none;"></iframe>', 'error' => 0);
                }
            }
            echo json_encode($json);
            break;
        case 'enable':
            $fileId     = Params::getParam('fileId') ;
            $user_id    = osc_logged_user_id();
            $success    = false;
            // if market file belongs to logged user or admin is logged ... will be enabled
            if( isset($fileId) && is_numeric($fileId) ) {
                $file   = ModelMarket::newInstance()->marketFileByPrimaryKey($fileId);
                $market = ModelMarket::newInstance()->findByPrimaryKey( $file['fk_i_market_id'] );
                $item   = Item::newInstance()->findByPrimaryKey( $market['fk_i_item_id'] );
                if( $user_id == $item['fk_i_user_id'] || osc_logged_admin_id() > 0 ) {
                    $success = ModelMarket::newInstance()->enable( $fileId );
                    if($success) {   
                        echo json_encode(array('msg' => __('File correctly enabled', 'market'), 'error' => 0));
                    } else {
                        echo json_encode(array('msg' => __('File could not be enabled', 'market'), 'error' => 1));
                    }
                }
            }
            break;
        case 'disable':
            $fileId     = Params::getParam('fileId') ;
            $user_id    = osc_logged_user_id();
            $success    = false;
            // if market file belongs to logged user or admin is logged ... will be disabled
            if( isset($fileId) && is_numeric($fileId) ) {
                $file   = ModelMarket::newInstance()->marketFileByPrimaryKey($fileId);
                $market = ModelMarket::newInstance()->findByPrimaryKey( $file['fk_i_market_id'] );
                $item   = Item::newInstance()->findByPrimaryKey( $market['fk_i_item_id'] );
                if( $user_id == $item['fk_i_user_id'] || osc_logged_admin_id() > 0 ) {
                    $success = ModelMarket::newInstance()->disable( $fileId );
                    if($success) {   
                        echo json_encode(array('msg' => __('File correctly disabled', 'market'), 'error' => 0));
                    } else {
                        echo json_encode(array('msg' => __('File could not be disabled', 'market'), 'error' => 1));
                    }
                }
            }
            break;
        case 'delete':
            if(ModelMarket::newInstance()->deleteFile(Params::getParam('id'), Params::getParam('item'), Params::getParam('secret'))) {
                echo json_encode(array('msg' => __('File correctly deleted', 'market'), 'error' => 0));
            } else {
                echo json_encode(array('msg' => __('File could not be deleted', 'market'), 'error' => 1));
            }
            break;
        case 'delete_banner':
            if(ModelMarket::newInstance()->removeBannerFile( Params::getParam('item'), Params::getParam('secret')) ) {
                echo json_encode(array('msg' => __('Banner correctly deleted', 'market'), 'error' => 0));
            } else {
                echo json_encode(array('msg' => __('Banner could not be deleted', 'market'), 'error' => 1));
            }
            break;
        case 'files':
        default:
            
            $page = Params::getParam('page')==''?0:Params::getParam('page'); 
            $files = ModelMarket::newInstance()->getLatest($page);
            
            $sOutput = '{';
            $sOutput .= '"iTotalRecords": '.(count($files)).', ';
            $sOutput .= '"iTotalDisplayRecords": '.(count($files)).', ';

            $sOutput .= '"aaData": [ ';
            if(count($files)>0) {
                foreach ($files as $file) {
                    
                    $tmp = explode("/", $file['s_file']);
                    $filename = end($tmp);
                    
                    $downloads = ModelMarket::newInstance()->getDownloads($file['pk_i_id']);
                    
                    $sOutput .= "[";
                    $sOutput .= "\"<input type='checkbox' name='id[]' value='".$file['pk_i_id']."' />\",";
                    $sOutput .= "\"".$file['s_slug']."\",";
                    $sOutput .= "\"".$file['s_version']."\",";
                    if($file['b_enabled']==1) {
                        $sOutput .= "\"".__('ENABLED', 'market')." <a href='".osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'general.php')."&paction=disable&id[]=".$file['pk_i_id']."'>(".__('disable', 'market').")</a>\",";
                    } else {
                        $sOutput .= "\"".__('DISABLED', 'market')." <a href='".osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'general.php')."&paction=enable&id[]=".$file['pk_i_id']."'>(".__('enable', 'market').")</a>\",";
                    }
                    $sOutput .= "\"<a href='".osc_item_url_ns($file['fk_i_item_id'])."'>item #".$file['fk_i_item_id']."</a>\",";
					$sOutput .= "\"<a href='".osc_base_url().'oc-content/uploads/market/'.$filename."'>".__('Download', 'market')."</a>\",";
					$sOutput .= "\"".$downloads."\",";
                    $var = 'onclick=\"javascript:return confirm(\''.__('This action can not be undone. Are you sure you want to continue?').'\')\"';
                    $sOutput .= "\"<a ".$var." href='".osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'general.php')."&paction=delete&id[]=".$file['pk_i_id']."' id='dt_link_delete'>".__('Delete')."</a>\",";
                    if(end($files) == $file) {
                        $sOutput .= "]";

                    } else {
                        $sOutput .= "],";
                    }
                }
            }
            $sOutput .= ']}';
            echo $sOutput;
            
            break;
    }
?>
