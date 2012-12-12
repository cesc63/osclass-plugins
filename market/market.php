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
            unset($file['s_file']) ;
            $file['s_source_file'] = osc_base_url() . 'oc-content/plugins/market/download.php?code=' . $code ;
            $file['error'] = 0;
            echo json_encode($file) ; exit ;
        }
    } else {

        $section = Params::getParam('section');
        $page = Params::getParam('iPage')==''?0:Params::getParam('iPage');


        $mUni = ModelMarket::newInstance();

        switch($section) {
            case 'search':
                $results = $mUni->getSearch(Params::getParam('pattern'), $page);
                echo json_encode(array('results' => $results)); exit;
                break;

            case 'plugins':
                $total   = $mUni->countData('PLUGIN');
                $plugins = $mUni->getPlugins($page);
                $array = array(
                    'plugins'   => $plugins,
                    'total'     => $total,
                    'page'      => $page,
                    'sizePage'  => $mUni->pageSize()
                );
                echo json_encode($array); exit;
                break;

            case 'themes':
                $total   = $mUni->countData('THEME');
                $themes  = $mUni->getThemes($page);
                $array = array(
                    'themes'    => $themes,
                    'total'     => $total,
                    'page'      => $page,
                    'sizePage'  => $mUni->pageSize()
                    );
                echo json_encode($array); exit;
                break;

            case 'languages':
                $total     = $mUni->countData('LANGUAGE');
                $languages = $mUni->getLanguages($page);
                $array = array(
                    'languages' => $languages,
                    'total'     => $total,
                    'page'      => $page,
                    'sizePage'  => $mUni->pageSize()
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
