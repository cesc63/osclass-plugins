<?php

    define( 'ABS_PATH', dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/' ) ;

    require_once( ABS_PATH . 'oc-load.php' ) ;
    //require_once('ModelMarket.php');

    $code = Params::getParam('code') ;

    if( $code != '' ) {
        if( preg_match('|(.+)@([A-Za-z0-9\.-]+)$|', $code, $m) ) {
            $slug    = $m[1] ;
            $version = $m[2] ;
        } else {
            $slug    = $code ;
            $version = '' ;
        }

        // only show enabled files.
        $file = ModelMarket::newInstance()->getFileBySlug($slug, $version, true);
        if( !empty($file) ) {
            if($file['s_download']=='') {
                $file['s_source_file'] = osc_base_url() . 'oc-content/plugins/market/download.php?code=' . $code ;
            }
            $file['error'] = 0;
            echo json_encode($file) ; exit ;
        }

    } else {

        $mUni = ModelMarket::newInstance();

        $section = Params::getParam('section');
        $page = Params::getParam('iPage')==''?0:Params::getParam('iPage');
        // page Size / page Length
        $pageSize = Params::getParam('pageLength')==''?$mUni->pageSize():Params::getParam('pageLength');

        // sort result
        $sort = null;
        if(Params::getParam('sort')!='') {
            $sort = Params::getParam('sort');
        }
        $order = null;
        if(Params::getParam('order')!='') {
            $order = Params::getParam('order');
        }
        error_log($section." ,,,,,...----> " . $sort . " _ " . $order);


        switch($section) {
            case 'search':
                $results = $mUni->getSearch(Params::getParam('pattern'), $page);
                echo json_encode(array('results' => $results)); exit;
                break;

            case 'plugins':
                $total   = $mUni->countData('PLUGIN');

                error_log('plugins ... ');

                $plugins = $mUni->getPlugins($page, $pageSize, $sort, $order);
                $array = array(
                    'plugins'   => $plugins,
                    'total'     => $total,
                    'page'      => $page,
                    'sizePage'  => $pageSize
                );
                echo json_encode($array); exit;
                break;

            case 'themes':
                $total   = $mUni->countData('THEME');
                $themes  = $mUni->getThemes($page, $pageSize, $sort, $order);
                $array = array(
                    'themes'    => $themes,
                    'total'     => $total,
                    'page'      => $page,
                    'sizePage'  => $pageSize
                    );
                echo json_encode($array); exit;
                break;

            case 'languages':
                $total     = $mUni->countData('LANGUAGE');
                $languages = $mUni->getLanguages($page, $pageSize, $sort, $order);
                $array = array(
                    'languages' => $languages,
                    'total'     => $total,
                    'page'      => $page,
                    'sizePage'  => $pageSize
                    );
                echo json_encode($array); exit;
                break;

            case 'count':
                $totalP     = $mUni->countData('PLUGIN');
                $totalT     = $mUni->countData('THEME');
                $totalL     = $mUni->countData('LANGUAGE');

                $array = array(
                    'pluginsTotal'   => $totalP,
                    'themesTotal'    => $totalT,
                    'languagesTotal' => $totalL
                    );
                echo json_encode($array); exit;
                break;
            case 'featured':
                // RewriteRule ^api/featured/(.*)/num/(.*)/?$ oc-content/plugins/market/market.php?section=featured&type=$1&num=$2
                // api/section/featured/{plugin/theme/language}/(num/{num})?/
                $aValidSection  = array('plugins', 'themes', 'languages');
                $max_num        = 12;
                $num            = Params::getParam('num');
                if($num=='') {
                    $num = 6;
                }
                $type = Params::getParam('type');
                switch($type) {
                    case 'plugins':
                        $array = array('plugins' => $mUni->getFeatured('PLUGIN', $num) );
                    break;
                    case 'themes':
                        $array = array('themes' => $mUni->getFeatured('THEME', $num) );
                    break;
                    case 'languages':
                        $array = array('languages' => $mUni->getFeatured('LANGUAGE', $num) );
                    break;
                    default:
                        $array = array('error' => 1);
                    break;
                }
                echo json_encode($array); exit;
                break;
            default:
                $plugins    = $mUni->getPlugins();
                $themes     = $mUni->getThemes();
                $languages  = $mUni->getLanguages();
                $totalP     = $mUni->countData('PLUGIN');
                $totalT     = $mUni->countData('THEME');
                $totalL     = $mUni->countData('LANGUAGE');

                $array = array(
                    'plugins'       => $plugins,
                    'pluginsTotal'  => $totalP,
                    'themes'        => $themes,
                    'themesTotal'   => $totalT,
                    'languages'     => $languages,
                    'languagesTotal' => $totalL
                    );
                echo json_encode($array); exit;
                break;
        }

    }

    echo json_encode( array('error' => 1) ) ; exit ;

?>
