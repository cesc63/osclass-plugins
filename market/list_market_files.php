<?php 
//pre_item_post
// correct version ?
        if( !preg_match("/\d+(\.\d+)+/i", $market_new_version) ) {
            $aError[] = __('Version - incorrect format (ex, 1.2.0)', 'market');
        } else {
            // check if version already exist
            $aux = ModelMarket::newInstance()->getFileBySlug($market_slug, $market_new_version);
            if(isset($aux['s_update_url'])) {
                $aError[] = __('Existing file version', 'market');
            }
        }
        ?>