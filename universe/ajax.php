<?php
    switch(Params::getParam('paction')) {
        case 'files':
        default:
            
            $page = Params::getParam('page')==''?0:Params::getParam('page'); 
            $files = ModelUniverse::newInstance()->getLatest($page);
            
            $sOutput = '{';
            $sOutput .= '"iTotalRecords": '.(count($files)).', ';
            $sOutput .= '"iTotalDisplayRecords": '.(count($files)).', ';

            $sOutput .= '"aaData": [ ';
            if(count($files)>0) {
                foreach ($files as $file) {
                    
                    $tmp = explode("/", $file['s_file']);
                    $filename = end($tmp);
                    
                    $downloads = ModelUniverse::newInstance()->getDownloads($file['pk_i_id']);
                    
                    $sOutput .= "[";
                    $sOutput .= "\"<input type='checkbox' name='id[]' value='".$file['pk_i_id']."' />\",";
                    $sOutput .= "\"".$file['s_slug']."\",";
                    $sOutput .= "\"".$file['s_version']."\",";
                    if($file['b_enabled']==1) {
                        $sOutput .= "\"".__('ENABLED', 'universe')." <a href='".osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'general.php')."&paction=disable&id[]=".$file['pk_i_id']."'>(".__('disable', 'universe').")</a>\",";
                    } else {
                        $sOutput .= "\"".__('DISABLED', 'universe')." <a href='".osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'general.php')."&paction=enable&id[]=".$file['pk_i_id']."'>(".__('enable', 'universe').")</a>\",";
                    }
                    $sOutput .= "\"<a href='".osc_item_url_ns($file['fk_i_item_id'])."'>item #".$file['fk_i_item_id']."</a>\",";
					$sOutput .= "\"<a href='".osc_base_url().'oc-content/uploads/universe/'.$filename."'>".__('Download', 'universe')."</a>\",";
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
