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
            case 'dashboardbox':
                $totalP     = $mUni->countData('PLUGIN');
                $totalT     = $mUni->countData('THEME');

                $output  = '<div id="banner-randomid"><div class="title">Get a new look for your website and do the impossible with hundreds of themes and plugins available for free!</div><a href="{URL_MARKET_THEMES}" class="box">'.$totalT.'<span>themes</span></a> <a href="{URL_MARKET_PLUGINS}" class="right box">'.$totalP.'<span>plugins</span></a><a href="{URL_MARKET_THEMES}" class="browse">browse all themes</a> <a href="{URL_MARKET_PLUGINS}" class="browse right">browse all plugins</a></div>';
                $output .= '<style>#banner-randomid{background-color:#4d4d4d;color:#fff;padding:15px;width:380px;overflow:hidden;height:220px}#banner-randomid .title{font-size:20px;font-weight:200;text-align:center;padding-bottom:17px}#banner-randomid a.box{border-radius:5px;padding:2px 15px 15px;background:#7ed1e1;color:white;font-size:60px;font-weight:200;font-family:"HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,Verdana,sans-serif;display:block;float:left;margin-left:40px;width:100px}#banner-randomid a.box span{font-size:16px;display:block;line-height:16px;margin-top:-6px}#banner-randomid a.box:hover{text-decoration:none}#banner-randomid a.browse{margin-left:40px;padding-top:5px;display:block;clear:both;color:#bababa;text-decoration:underline;float:left;width:130px}#banner-randomid a.right{float:right;margin-right:40px;margin-left:0;clear:none}</style>';

                echo $output; exit;
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
