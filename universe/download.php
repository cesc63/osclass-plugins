<?php

    define( 'ABS_PATH', dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/' ) ;

    require_once( ABS_PATH . 'oc-load.php' ) ;

    $code = Params::getParam('code') ;

    if($code!='') {
        if(preg_match('|(.+)@([A-Za-z0-9\.-]+)$|', $code, $m)) {
            $slug = $m[1];
            $version = $m[2];
        } else {
            $slug = $code;
            $version = '';
        }
        
        $file = Universe::newInstance()->getFileBySlug($slug, $version);
        if(!empty($file)) {
            Universe::newInstance()->insertStat($file['pk_i_id'], @$_SERVER['REMOTE_HOST'], @$_SERVER['REMOTE_ADDR']) ;
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file['s_file']));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file['s_file']));
            @ob_clean();
            flush();
            readfile($file['s_file']);
            exit;
            
        }
        
    }
    
?>