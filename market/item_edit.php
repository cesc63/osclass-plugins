<?php $market_versions = explode(",", osc_get_preference('compatible_versions', 'market')); ?>

<?php 
    $item = __get('item');
    if(@$item_id) {
        $market = ModelMarket::newInstance()->findByItemId( $item_id );
        $market['files'][0] = ModelMarket::newInstance()->getFileFromItem( $item_id );
        View::newInstance()->_exportVariableToView("market_ad", $market);
    }
?>
<style>
    .input {
        display: inline-block;
        width: 208px;
        height: 18px;
        padding: 4px 6px;
        font-size: 13px;
        line-height: 18px;
        color: #555;
        background-color: white;
        border: 1px solid #DDD;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        vertical-align: top;
    }
</style>
<h2><?php _e('Market attributes'); ?></h2>
<?php if(!OC_ADMIN) { ?>
<div style="margin-top:10px;margin-bottom:10px;">
    <span style="font-style: italic;"><b><?php _e('NOTE', 'market'); ?>:</b> <?php _e('Once you have inserted the listing, through your my account you can add new market files','market');?></span>
</div>
<?php } ?>
<div class="grid-row grid-100">
    <h3><?php _e('Information', 'market'); ?></h3>
    <div>
        <?php if(osc_is_admin_user_logged_in()) { ?> 
        <label><?php _e('Slug','market'); ?> <?php if(@$item_id) { ?><b>( <?php _e('Slug cannot be updated is used as ID','market'); ?>) </b><?php } ?></label>
        <div>
            <?php if(@$item_id) { ?>
            <span class="input"><?php echo @$market['s_slug']; ?></span>
            <input type="hidden" name="market_slug" id="market_slug" value="<?php echo @$market['s_slug']; ?>" />
            <?php } else { ?>
            <input <?php if(@$item_id){ echo "disabled";}?> type="text" name="market_slug" id="market_slug" value="<?php echo @$market['s_slug']; ?>" />
            <?php } ?>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div>
        <label><?php _e('Live Preview (in case of themes)','market'); ?></label>
        <div>
            <input type="text" name="market_preview" id="market_preview" value="<?php echo @$market['s_preview']; ?>" />
        </div>
        <?php }; ?>
    </div>
    <div style="clear: both;"></div>
    <div id="market_banner">
        <label><?php _e('Upload a banner', 'market') ; ?></label>
        <div style="padding: 10px;background-color: #F3F3F3;border: solid 1px #DDD;border-radius: 4px;-webkit-border-radius: 4px;">
            <input type="file" name="market_banner" />
            <?php if(@$item_id) { ?><br/>(<?php _e('Banner will be overwritten if there is an existing one'); ?>)<?php } ?>
        </div>
    </div>
        <?php if(isset($market['s_banner']) && $market['s_banner']!='') { ?>
            <div id="banner">
                <img src="<?php echo osc_base_url()."oc-content/uploads/market/".$market['s_banner']; ?>" />
                <div class="clear"></div>
                <a class="btn btn-red" href="javascript:delete_market_banner(<?php echo  @$market['fk_i_item_id'] . ", '" . $secret ."'";?>);"><?php _e('Remove image', 'market'); ?></a>
                <div class="clear"></div>
            </div>
        <?php }; ?>
    </div>
    
    <br/>
    
    <div class="clear"></div>
    
<script type="text/javascript">
    
    function delete_market_banner(item_id, secret) {
        var result = confirm('<?php echo __('This action can\\\'t be undone. Are you sure you want to continue?', 'market'); ?>');
        if(result) {
            $.ajax({
                type: "POST",
                url: '<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>&paction=delete_banner&item='+item_id+'&secret='+secret,
                dataType: 'json',
                success: function(data) {
                    if(data.error==0) {
                        $('#banner').remove();
                    }
                    
                    $(".jsMessage > p").html(data.msg);
                    $(".jsMessage").slideDown('slow').delay(3000).slideUp('slow');
                    scrollTo(0,0);
                }
            });
        }
    }
</script>