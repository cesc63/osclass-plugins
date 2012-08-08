<?php

    define( 'ABS_PATH', dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/' ) ;

    require_once( ABS_PATH . 'oc-load.php' ) ;
    //require_once('ModelMarket.php');

    $code = Params::getParam('code') ;
    
    $item_id = ModelMarket::newInstance()->getItemIdBySlug($code);
    
    if( is_numeric($item_id) ) {
        $item = Item::newInstance()->findByPrimaryKey( $item_id );
        View::newInstance()->_exportVariableToView('item', $item);
        $url = osc_item_url();
        header( 'Location: '.$url);
    } else {
        header( 'Location: http://market.osclass.org' ) ;
    }
    
?>