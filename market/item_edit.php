<?php $market_versions = explode(",", osc_get_preference('compatible_versions', 'market')); ?>
<h2><?php _e("Market", 'market') ; ?></h2>
<div class="box">
    <div class="box market_files">
        <div class="row">
            <?php if(osc_is_admin_user_logged_in()) { ?> 
            <br/>
            <label><?php _e('Slug','market'); ?></label>
            <input type="text" name="market_slug" id="market_slug" value="<?php echo @$market_files[0]['s_slug']; ?>" />
            <?php }; ?>
        </div>
            <?php
            if($market_files != null && is_array($market_files) && count($market_files) > 0) {
                foreach($market_files as $_r) { ?>
                    <div id="<?php echo $_r['pk_i_id'] ; ?>" fkid="<?php echo $_r['fk_i_item_id'];?>" name="<?php echo $_r['s_name'];?>">
                        <?php $tmp = explode("/", $_r['s_file']);?>
                        <p><?php echo $tmp[count($tmp)-1] ; ?> 
                            <br/>
                            <label><?php _e('Version','market'); ?></label>
                            <input type="text" name="market_version[<?php echo $_r['pk_i_id']; ?>]" id="market_version_<?php echo $_r['pk_i_id']; ?>" value="<?php echo $_r['s_version']; ?>" />
                            <br/>
                            <?php foreach($market_versions as $v) { ?>
                                <input type="checkbox" <?php echo ( market_is_checked($v, $_r['s_compatible']) ? 'checked="checked"' : '' ) ; ?> name="market_comp_versions[<?php echo $_r['pk_i_id']; ?>][<?php echo $v; ?>]" value="1" />
                                <?php echo $v; ?>
                            <?php }; ?>
                            <?php if(osc_is_admin_user_logged_in()) { ?> 
                            <label><?php _e('Enabled', 'market'); ?></label><input type="checkbox" name="market_enabled[<?php echo $_r['pk_i_id']; ?>]" value="1" <?php if($_r['b_enabled']==1){ echo 'checked'; };?>/>
                            <?php }; ?>
                            <br/>
                            <a href="javascript:delete_market_file(<?php echo $_r['pk_i_id'] . ", " . $_r['fk_i_item_id'] . "', '" . $secret . "'" ;?>);"  class="delete"><?php _e('Delete', 'market') ; ?></a><br/>
                            </p>
                    </div>
                <?php }
            } ?>
        <div id="market_files">
            <div class="row">
                <p><?php printf(__('Allowed extensions are %s. Any other file will not be uploaded', 'market'), osc_get_preference('allowed_ext', 'market')) ; ?></p>
            </div>
            <div class="row">
                <input type="file" name="market_file_new" />
                <br/>
                <label><?php _e('Version','market'); ?></label>
                <input type="text" name="market_version_new" id="market_version_new" value="" />
                <br/>
                <label><?php _e('Compatible with', 'market'); ?></label>
                <br/>
                <?php foreach($market_versions as $v) { ?>
                    <input type="checkbox" name="market_new_comp_versions[<?php echo $v; ?>]" value="1" />
                    <?php echo $v; ?>
                <?php }; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ufIndex = 0;
    function gebi(id) { return document.getElementById(id); }
    function ce(name) { return document.createElement(name); }
    function re(id) {
        var e = gebi(id);
        e.parentNode.removeChild(e);
    }


    function delete_market_file(id, item_id, secret) {
        var result = confirm('<?php echo __('This action can\\\'t be undone. Are you sure you want to continue?', 'market'); ?>');
        if(result) {
            $.ajax({
                type: "POST",
                url: '<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>&id='+id+'&item='+item_id+'&secret='+secret,
                dataType: 'json',
                success: function(data){
                    var class_type = "error";
                    if(data.success) {
                        $("div[name="+name+"]").remove();
                        class_type = "ok";
                    }
                    var flash = $("#flash_js");
                    var message = $('<div>').addClass('pubMessages').addClass(class_type).attr('id', 'FlashMessage').html(data.msg);
                    flash.html(message);
                    $("#FlashMessage").slideDown('slow').delay(3000).slideUp('slow');
                }
            });
        }
    }
</script>