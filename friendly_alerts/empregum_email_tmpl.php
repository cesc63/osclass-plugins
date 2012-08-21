<?php
    $params = array();

    $category = null;
    foreach( $aConditions['aCategories'] as $categoryID ) {
        $category = $categoryID;
        $params['sCategory'] = $categoryID;
        break;
    }

    $region   = null;
    foreach( $aConditions['regions'] as $regionID ) {
        $region = preg_replace('/.*?([0-9]+).*/', '$01', $regionID);
        $params['sRegion'] = $region;
        break;
    }

    $city     = null;
    foreach( $aConditions['cities'] as $cityID ) {
        $city = preg_replace('/.*?([0-9]+).*/', '$01', $cityID);
        $params['sCity'] = $city;
        break;
    }

    $text = '';
    if( $category !== null ) {
        $c = Category::newInstance()->findByPrimaryKey($category);
        $text .= strtolower($c['s_name']);
    }
    if( $city !== null ) {
        $ci = City::newInstance()->findByPrimaryKey($city);
        $text .= ' ' . strtolower($ci['s_name']);
    } else if( $region !== null ) {
        $r = Region::newInstance()->findByPrimaryKey($region);
        $text .= ' ' . strtolower($r['s_name']);
    }

    $ga_url_builder = '?utm_source=alert&utm_medium=alert';
    if( $text != '' ) {
        $ga_url_builder .= sprintf('&utm_term=%s', osc_sanitizeString($text));
    }
?>
<div style="background:#fff;font-family:arial;helvetica;sans-serif;font-size:1px;line-height:1px">
    <div style="padding:15px 10px;font-size:15px;line-height:18px;text-align:left;background-color:#ffffff;border-bottom:solid 1px #bbbbbb">
        <a style="text-decoration:underline;color:#15C;" href="<?php echo osc_base_url() . $ga_url_builder . '&utm_campaign=logo'; ?>" target="_blank"><img border="0" alt="<?php echo osc_page_title(); ?>" src="<?php echo get_logo_url(); ?>" height="60"></a>
    </div>
    <div style="font-size:15px;line-height:15px; text-align:center; padding:15px 0;">
         <a href="<?php echo osc_item_post_url() . $ga_url_builder . '&utm_campaign=publish'; ?>" target="_blank" style="text-decoration:underline;color:#15C;">Publique seu anúncio gratuitamente</a>
    </div>
    <?php if( $text !== '' ) { ?>
    <div style="padding-top:10px;padding-bottom:10px;padding-right:13px;padding-left:13px;border-bottom:1px solid #BBBBBB;font-size:19px;line-height:19px;border-top:solid 1px #bbbbbb">
        <?php echo $totalItems; ?> anúncios novos de <a href="<?php echo osc_search_url($params) . $ga_url_builder . '&utm_campaign=search'; ?>" target="_blank" style="color:#15C;"><?php echo $text; ?></a>
    </div>
    <?php } else { ?>
    <div style="padding-top:10px;padding-bottom:10px;padding-right:13px;padding-left:13px;border-bottom:1px solid #BBBBBB;font-size:19px;line-height:19px;border-top:solid 1px #bbbbbb">
        <a href="<?php echo osc_search_url($params) . $ga_url_builder . '&utm_campaign=search'; ?>" target="_blank" style="color:#15C;"><?php echo $totalItems; ?> anúncios novos</a>
    </div>
    <?php } ?>
    <!--  ITEM -->
    <?php while( osc_has_items() ) { ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:10px;border-bottom:1px dotted #BBBBBB">
        <tr>
            <td valing="top">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><a style="font-family:arial;helvetica;sans-serif;color:#15C;font-size:15px;line-height:15px;" href="<?php echo osc_item_url() . $ga_url_builder . '&utm_campaign=ad_title'; ?>" target="_blank"><?php echo osc_item_title() . ' ' . osc_item_city(); ?></a></td>
                        <td width="140" style="width:140px;text-align:right"><?php if( osc_price_enabled_at_items() ) { ?><a style="font-family:arial;helvetica;sans-serif;font-size:15px;line-height:15px;color:#15C;" href="<?php echo osc_item_url() . $ga_url_builder . '&utm_campaign=ad_price'; ?>" target="_blank"><?php echo osc_item_formated_price(); ?></a><?php } ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:5px">
                    <tr>
                        <td>
                            <p style="font-size:14px;line-height:15px;margin-bottom:0px;margin-right:10px;margin-top:0px;text-align:left">
                                <span><?php echo osc_highlight( strip_tags( osc_item_description() ) ); ?></span>
                                <?php /*<br>
                                <strong style="font-size:11px">%%Metadata%%</strong>*/ ?>
                            </p>
                        </td>
                        <?php /*
                        <?php if( osc_images_enabled_at_items() ) { ?>
                        <?php if(osc_count_item_resources()) { ?>
                        <td width="140" style="width:140px;text-align:right">
                            <a style="border:none;text-decoration:none" href="<?php echo osc_item_url() . $ga_url_builder . '&utm_campaign=ad_photo'; ?>" target="_blank"><img src="<?php echo osc_resource_thumbnail_url(); ?>" style="border: 1px solid #BBBBBB; padding: 1px;/"></a>
                        </td>
                        <?php } ?>
                        <?php } ?>
                        <!-- /Si tiene imagen -->
                        */ ?>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?php } ?>
    <!-- /ITEM -->
    <?php if( $text !== '' ) { ?>
    <div style="padding:15px 10px;font-size:15px;line-height:18px;text-align:center;background-color:#ffffff">
        <a href="<?php echo osc_search_url($params) . $ga_url_builder . '&utm_campaign=view_more'; ?>" target="_blank" style="text-decoration:underline;color:#15C;"><?php echo $totalItems; ?> anúncios novos de <?php echo $text; ?></a>
    </div>
    <?php } else { ?>
    <div style="padding:15px 10px;font-size:15px;line-height:18px;text-align:center;background-color:#ffffff">
        <a href="<?php echo osc_search_url($params) . $ga_url_builder . '&utm_campaign=view_more'; ?>" target="_blank" style="text-decoration:underline;color:#15C;"><?php echo $totalItems; ?> anúncios novos</a>
    </div>
    <?php } ?>
    <?php
        $unsub_link = osc_user_unsubscribe_alert_url($alert['pk_i_id'], $alert['s_email'], $alert['s_secret']);
    ?>
    <div style="padding:15px 10px;border-top:1px solid #BBBBBB;font-size:11px;line-height:12px; background-color:#f2f2f2">
         Receba este boletim em <?php echo $alert['s_email']; ?>.<br>
         Caso deseje deixar de receber este boletim, <a style="text-decoration:underline;color:#888;" href="<?php echo $unsub_link; ?>" target="_blank">clique aqui</a>.
    </div>
</div>