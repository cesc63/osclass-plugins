<h2><?php _e("Universe", 'universe') ; ?></h2>
<div class="box">
    <div class="box universe_files">
        <div class="row">
            <label for="universe_type"><?php _e('Type','universe'); ?></label>
            <select name="universe_type" id="universe_type">
                <option value="PLUGIN" <?php if(@$universe_files[0]['e_type']=='PLUGIN') {echo 'selected="selected"';}; ?>><?php _e('Plugin'); ?></option>
                <option value="THEME" <?php if(@$universe_files[0]['e_type']=='THEME') {echo 'selected="selected"';}; ?>><?php _e('Theme'); ?></option>
                <option value="LANGUAGE" <?php if(@$universe_files[0]['e_type']=='LANGUAGE') {echo 'selected="selected"';}; ?>><?php _e('Language'); ?></option>
            </select>
            <?php if(osc_is_admin_user_logged_in()) { ?> 
            <br/>
            <label><?php _e('Slug','universe'); ?></label>
            <input type="text" name="universe_slug" id="universe_slug" value="<?php echo @$universe_files[0]['s_slug']; ?>" />
            <?php }; ?>
        </div>
            <?php
            if($universe_files != null && is_array($universe_files) && count($universe_files) > 0) {
                foreach($universe_files as $_r) { ?>
                    <div id="<?php echo $_r['pk_i_id'] ; ?>" fkid="<?php echo $_r['fk_i_item_id'];?>" name="<?php echo $_r['s_name'];?>">
                        <?php $tmp = explode("/", $_r['s_file']);?>
                        <p><?php echo $tmp[count($tmp)-1] ; ?> 
                            <br/>
                            <label><?php _e('Version','universe'); ?></label>
                            <input type="text" name="universe_version[<?php echo $_r['pk_i_id']; ?>]" id="universe_version_<?php echo $_r['pk_i_id']; ?>" value="<?php echo $_r['s_version']; ?>" />
                            <br/>
                            <a href="javascript:delete_universe_file(<?php echo $_r['pk_i_id'] . ", " . $_r['fk_i_item_id'] . "', '" . $secret . "'" ;?>);"  class="delete"><?php _e('Delete', 'universe') ; ?></a><br/>
                            <?php if(osc_is_admin_user_logged_in()) { ?> 
                            <label><?php _e('Enabled', 'universe'); ?></label><input type="checkbox" name="universe_enabled[<?php echo $_r['pk_i_id']; ?>]" value="1" <?php if($_r['b_enabled']==1){ echo 'checked'; };?>/>
                            <?php }; ?>
                            </p>
                    </div>
                <?php }
            } ?>
        <div id="universe_files">
            <div class="row">
                <p><?php printf(__('Allowed extensions are %s. Any other file will not be uploaded', 'universe'), osc_get_preference('allowed_ext', 'universe')) ; ?></p>
            </div>
            <div class="row">
                <input type="file" name="universe_file_new" />
                <br/>
                <label><?php _e('Version','universe'); ?></label>
                <input type="text" name="universe_version_new" id="universe_version_new" value="" />
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


    function delete_universe_file(id, item_id, secret) {
        var result = confirm('<?php echo __('This action can\\\'t be undone. Are you sure you want to continue?', 'universe'); ?>');
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