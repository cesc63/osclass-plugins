<?php

    require_once('../../../oc-load.php');
    $code = Params::getParam('code');
    // to know the version of OSClass
    //$_SERVER['HTTP_USER_AGENT']
    if($code!='') {
        
        if(preg_match('|(.+)@([A-Za-z0-9\.-]+)$|', $code, $m)) {
            $slug = $m[1];
            $version = $m[2];
        } else {
            $slug = $code;
            $version = '';
        }

        if(Params::getParam('files')==1) {
            $files = ModelUniverse::newInstance()->getFilesBySlug($slug);
            if(!empty($files)) {
                foreach($files as $k => $v) {
                    unset($files[$k]['s_file']);
                    $files[$k]['s_source_file'] = osc_market_url($code);
                    $files[$k]['error'] = 0;
                }
                echo json_encode($file);exit;
            }
        } else {
            $file = ModelUniverse::newInstance()->getFileBySlug($slug, $version);
            if(!empty($file)) {
                unset($file['s_file']);
                $file['s_source_file'] = osc_market_url($code);
                $file['error'] = 0;
                echo json_encode($file);exit;
            }
        }
        
    }
    
    echo json_encode(array('error' => 1));exit;
    
?>