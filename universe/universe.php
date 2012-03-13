<?php

    define( 'ABS_PATH', dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/' ) ;

    require_once( ABS_PATH . 'oc-load.php' ) ;
    //require_once('ModelUniverse.php');

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
            $files = ModelUniverse::newInstance()->getFilesBySlug($slug);
            if( !empty($files) ) {
                foreach( $files as $k => $v ) {
                    unset($files[$k]['s_file']) ;
                    $files[$k]['s_source_file'] = osc_base_url() . 'oc-content/plugins/universe/download.php?code=' . $code ;
                    $files[$k]['error'] = 0 ;
                }
                echo json_encode($files) ; exit ;
            }
        } else {
            $file = ModelUniverse::newInstance()->getFileBySlug($slug, $version) ;
            if( !empty($file) ) {
                unset($file['s_file']) ;
                $file['s_source_file'] = osc_base_url() . 'oc-content/plugins/universe/download.php?code=' . $code ;
                $file['error'] = 0;
                echo json_encode($file) ; exit ;
            }
        }
    } else {
        
        $section = Params::getParam('section');
        $page = Params::getParam('page')==''?0:Params::getParam('page');
        
        $mUni = ModelUniverse::newInstance();
        
        switch($section) {
            case 'search':
                $results = $mUni->getSearch(Params::getParam('pattern'), $page);
                echo json_encode(array('results' => $results)); exit;
                break;
            
            case 'plugins':
                $plugins = $mUni->getPlugins($page);
                echo json_encode(array('plugins' => $plugins)); exit;
                break;
            
            case 'themes':
                $themes = $mUni->getThemes($page);
                echo json_encode(array('themes' => $themes)); exit;
                break;
            
            case 'languages':
                $languages = $mUni->getLanguages($page);
                echo json_encode(array('languages' => $languages)); exit;
                break;
            
            default:
                $plugins = $mUni->getPlugins();
                $themes = $mUni->getThemes();
                $languages = $mUni->getLanguages();
                echo json_encode(array('plugins' => $plugins, 'themes' => $themes, 'languages' => $languages)); exit;
                break;
        }
        
    }
    
    echo json_encode( array('error' => 1) ) ; exit ;
    
?>