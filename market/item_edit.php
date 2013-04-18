<?php $market_versions = explode(",", osc_get_preference('compatible_versions', 'market')); ?>

<?php
// @todo mirar de ponerlo en index.php
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
    #market_slug {
        width: 516px;
    }

    h2 {
        margin: 0;
        margin-bottom: 20px;
        color: #616161;
        font-size: 21px;
        font-weight: normal;
    }
</style>
<?php if(!OC_ADMIN) { ?>
<div style="margin-top:10px;margin-bottom:10px;">
    <span style="font-style: italic;"><b><?php _e('NOTE', 'market'); ?>:</b> <?php _e('Once you have inserted the listing, through your my account you can add new market files','market');?></span>
</div>
<?php } ?>
<div class="grid-100">
    <h2><?php _e('Information', 'market'); ?></h2>
    <div>
        <?php if(osc_is_admin_user_logged_in()) { ?>
        <label><?php _e('Slug','market'); ?> <?php if(@$item_id) { ?><b>( <?php _e('Slug cannot be updated is used as ID','market'); ?>) </b><?php } ?></label>
        <div>
            <?php if(@$item_id) { ?>
            <span class="input"><?php echo @$detail['s_slug']; ?></span>
            <input type="hidden" name="market_slug" id="market_slug" value="<?php echo @$detail['s_slug']; ?>" />
            <?php } else { ?>
            <input <?php if(@$item_id){ echo "disabled";}?> type="text" name="market_slug" id="market_slug" value="<?php echo @$detail['s_slug']; ?>" />
            <?php } ?>
        </div>
    </div>
    <?php if($is_theme_category) { ?>
    <div style="clear: both;"></div>
    <div>
        <label><?php _e('Live Preview (in case of themes)','market'); ?></label>
        <div>
            <input type="text" name="market_preview" id="market_preview" value="<?php echo @$detail['s_preview']; ?>" />
        </div>
        <?php }; ?>
    </div>
    <?php } ?>
    <div style="clear: both;"></div>
    <div>
        <label><?php _e('Featured item','market'); ?></label>
        <div>
            <label>
                <input type='checkbox' value='1' <?php if(@$detail['b_featured']==1) { ?>checked="checked"<?php } ?> name='market_featured' />
                <?php _e('Mark as featured','market'); ?>
            </label>
        </div>
    </div>
    <br/>
    <div style="clear: both;"></div>
    <div class="fit_market">
        <h2><?php _e('Media resources', 'market'); ?></h2>
        <hr/>
    </div>
    <div id="market_banner">
        <hr/>
        <label><?php _e('Upload a banner', 'market') ; ?></label>
        <div class="jsMessage flashmessage flashmessage-info screenshot-info" style="display: block; width: 480px; margin-bottom: 15px;">
            <?php echo sprintf(__('Important information: Banner image will be resized to <b>%sx%s</b>, uploaded image should keep that ratio.', 'market'), $aSize['w'], $aSize['h'] ); ?>
        </div>
        <div style="padding: 10px;background-color: #F3F3F3;border: solid 1px #DDD;border-radius: 4px;-webkit-border-radius: 4px;">
            <input type="file" name="market_banner" />
            <?php if(@$item_id) { ?><br/>(<?php _e('Banner will be overwritten if there is an existing one'); ?>)<?php } ?>
        </div>
    </div>
        <?php if(isset($detail['s_banner']) && @$detail['s_banner']!='') { ?>
            <div id="banner">
                <img src="<?php echo osc_base_url()."oc-content/uploads/market/".@$detail['s_banner']; ?>" />
                <div class="clear"></div>
                <a class="btn btn-red" href="javascript:delete_market_banner(<?php echo @$detail['fk_i_item_id'] . ", '" . $secret ."'";?>);"><?php _e('Remove image', 'market'); ?></a>
                <div class="clear"></div>
            </div>
        <?php }; ?>
    </div>

    <br/>

    <div class="clear"></div>

<script type="text/javascript">
        function repaint_photos() {

            // move category dropdowns
            var category_div = $('div.category');
            $(category_div).css('padding-bottom', '15px');
            $('div.category').remove();
            $('div.input-title-wide').before(category_div);

            var meta_copy = $('div.meta_list');
            $('div.meta_list').remove();
            $('div.input-description-wide').before(meta_copy);
            $('textarea#meta_short-description').attr('rows', 5);

            // save elements
            var photo_container = $('.photo_container');
            $('.photo_container').remove();
            $('#market_banner').before( $(photo_container).css('display', 'block') );

            // add information
            var screenshot_info = $('<div class="jsMessage flashmessage flashmessage-info screenshot-info" />');
            $(screenshot_info).text('<?php _e('Screenshot images will appear at frontend market.osclass.org, at detail item page', 'market'); ?>');
            $(screenshot_info).css('display', 'block');
            $(screenshot_info).css('width', '480px');
            $(screenshot_info).css('margin-bottom', '15px');
            $('#photos').before(screenshot_info);
        }
        repaint_photos();

<?php if(!is_numeric(@$item_id)) { ?>
    $(document).ready(function(){
        // validate market item slug
        $('#market_slug').rules("add", {
            required: true,
            remote: '<?php echo osc_ajax_plugin_url( osc_plugin_folder(__FILE__).'check_slug.php' ); ?>',
            messages: {
                required: "<?php _e("Slug: this field is required", 'market'); ?>",
                remote: "<?php _e("Existing market slug", 'market'); ?>"
        }});
    });
<?php } ?>

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