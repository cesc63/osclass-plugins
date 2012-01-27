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

        if( Params::getParam('files') == 1 ) {
            $files = Universe::newInstance()->getFilesBySlug($slug);
            if( !empty($files) ) {
                foreach( $files as $k => $v ) {
                    unset($files[$k]['s_file']) ;
                    $files[$k]['s_source_file'] = osc_base_url() . 'oc-content/plugins/universe/download.php?code=' . $code ;
                    $files[$k]['error'] = 0 ;
                }
                echo json_encode($files) ; exit ;
            }
        } else {
            $file = Universe::newInstance()->getFileBySlug($slug, $version) ;
            if( !empty($file) ) {
                unset($file['s_file']) ;
                $file['s_source_file'] = osc_base_url() . 'oc-content/plugins/universe/download.php?code=' . $code ;
                $file['error'] = 0;
                echo json_encode($file) ; exit ;
            }
        }
    }
    
    echo json_encode( array('error' => 1) ) ; exit ;
    
?>