<?php $market_aux = ModelMarket::newInstance()->getFileFromItem(osc_item_id()); ?>
<script>
    $("#download_market").on("click", function() {
        $.getJSON(
        '<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>&paction=download&website=ocadmin_manualy',
            {"code" : '<?php echo @$market_aux['s_slug']?>'},
            function(data){
                if(data.error==0) {
                    $("#download_market").append(data.msg);
                }
            }
        );
    });
</script>