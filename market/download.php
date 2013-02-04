<?php

    define( 'ABS_PATH', dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/' ) ;

    require_once( ABS_PATH . 'oc-load.php' ) ;

    $code = Params::getParam('code') ;

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
            $osclass_version = '';
            if(isset($_SERVER['HTTP_USER_AGENT'])) {
                error_log($_SERVER['HTTP_USER_AGENT']);
                if(preg_match("|Osclass \(v\.([0-9]+)\)|", $_SERVER['HTTP_USER_AGENT'], $match)) {
                    //
                    // @TODO incrementar contador de descargas desde oc-admin market pages!
                    //
                    $osclass_version = $match[1];
                    error_log('-> FROM oc-admin [' . $osclass_version . ']');
                } else {
                    error_log('FROM direct download');
                }
            }

            // save market download stats
            ModelMarket::newInstance()->insertStat($file['fk_i_market_id'], $file['pk_i_id'], isset($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:'', isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'', $osclass_version) ;

            error_log("market::plugin send file to download " . $file['s_source_file']);

            if($file['s_source_file']!='') {
                error_log("market download s_source:file ... ". $file['s_source_file']);
                header( 'Content-Description: File Transfer' ) ;
                header( 'Content-Type: application/octet-stream' ) ;
                header( 'Content-Disposition: attachment; filename=' . basename($file['s_source_file']) ) ;
                header( 'Content-Transfer-Encoding: binary' ) ;
                header( 'Expires: 0' ) ;
                header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' ) ;
                header( 'Pragma: public' ) ;
                header( 'Content-Length: ' . filesize($file['s_source_file']) ) ;
                @ob_clean() ;
                flush() ;
                readfile($file['s_source_file']) ;
                exit ;
            } else {
                error_log("market download s_download... ". $file['s_download']);
                header( 'Content-Description: File Transfer' ) ;
                header( 'Content-Type: application/octet-stream' ) ;
                header( 'Content-Disposition: attachment; filename=' . basename(str_replace("/download", "", $file['s_download'])) ) ;
                header( 'Content-Transfer-Encoding: binary' ) ;
                header( 'Expires: 0' ) ;
                header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' ) ;
                header( 'Pragma: public' ) ;
                //header( 'Content-Length: ' . filesize($file['s_source_file']) ) ;
                @ob_clean() ;
                flush() ;
                readfile($file['s_download']) ;
                exit ;
            }
        }
    }

?>