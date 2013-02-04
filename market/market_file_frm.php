<?php
$item_id    = Params::getParam('itemId');
$file_id    = Params::getParam('fileId');
$aCompatible        = array();
if(!isset($item_id) || !is_numeric($item_id)) {
    // show error, you miss item id
} else {
    // everything ok ...
    $isEdit     = false;
    $market     = ModelMarket::newInstance()->findByItemId( $item_id );
    $aError     = array();
    $error      = false;
    $success    = false;
    // clear variables used to refill inputs
    $aCompatible        = array();
    $file_version       = '';
    $file_download_url  = '';

    $title      = __('Add new market file', 'market');
    $submit_txt = __('Add new file', 'market');

    //  submited form INSERT
    if(Params::getParam('plugin_action')=='done') {
        // insert new market file
        market_admin_add_file($item_id, $market, $error, $aError, $file_version, $file_download_url, $aCompatible);
        if($error) {
            $str_error = implode('<br/>', $aError);

        } else {
            $success     = true;
            $str_success = __('The file was added successfully', 'market');
            // clear variables used to refill inputs
            $aCompatible        = array();
            $file_version       = '';
            $file_download_url  = '';
        }
    } else if(Params::getParam('plugin_action')=='edit') {
        $isEdit = true;
        $file_info  = ModelMarket::newInstance()->marketFileByPrimaryKey( $file_id );
        // update file market
        market_admin_update_file($item_id, $file_id, $market, $error, $aError, $file_version, $file_download_url, $aCompatible, $file_info['s_file']);
        if($error) {
            $str_error = implode('<br/>', $aError);
        } else {
            $success     = true;
            $str_success = __('The file was edited successfully', 'market');
            // clear variables used to refill inputs
            $aCompatible        = array();
            $file_version       = '';
            $file_download_url  = '';
        }
    }

    $market_files = ModelMarket::newInstance()->getFilesFromItem($item_id); // existent market files  ONLY ADMIN
    $market_versions = explode(",", osc_get_preference('compatible_versions', 'market'));
    $item_info  = Item::newInstance()->findByPrimaryKey( $item_id );
    $secret = $item_info['s_secret'];
    if(isset($file_id) && is_numeric($file_id)) {
        // updating
        $file_info  = ModelMarket::newInstance()->marketFileByPrimaryKey( $file_id );
        // clear variables used to refill inputs
        $aCompatible        = explode(',', $file_info['s_compatible']);
        $file_version       = $file_info['s_version'];
        $file_download_url  = $file_info['s_download'];
        $file_download_file = $file_info['s_file'];

        $isEdit     = true;
        $title = __('Edit market file', 'market');
        $submit_txt = __('Edit file', 'market');
    } else {
        $title = __('Add new market file', 'market');
        $submit_txt = __('Add new file', 'market');
    }

?>
    <style>
        label {
            display: block;
            font-size: 14px;
            color: #616161;
            margin-top: 10px;
            margin-bottom: 5px;
        }

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

    <h3><?php echo $title; ?></h3>
    <h4><?php echo $item_info['locale'][osc_admin_language()]['s_title'];?></h4>

    <form style="width: 860px;" id="market_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>market_file_frm.php" />
        <input type="hidden" name="itemId" value="<?php echo Params::getParam('itemId'); ?>" />
        <input type="hidden" name="plugin_action" value="<?php if($isEdit) { echo "edit"; } else { echo "done"; } ?>"/>
        <table>
            <tr>
                <td valign="top">
                    <div id="market_files">
                        <div>
                            <label><?php _e('File version', 'market'); ?></label>
                            <?php if($isEdit) { ?>
                            <span class="input"><?php echo @$file_version; ?></span>
                            <input type="hidden" name="market_version_new" id="market_version_new" value="<?php echo osc_esc_html( @$file_version ); ?>" />
                            <input type="hidden" name="fileId" id="fileId" value="<?php echo osc_esc_html( @$file_id ); ?>" />
                            <?php } else { ?>
                            <input type="text" name="market_version_new" id="market_version_new" value="<?php echo osc_esc_html( @$file_version ); ?>" />
                            <?php } ?>
                        </div>
                        <div>
                            <label><?php _e('Compatible with', 'market'); ?></label>
                            <div>
                                <?php foreach($market_versions as $v) { ?>
                                <?php
                                $checked = false;
                                if(array_search($v, $aCompatible) !== false) {
                                    $checked = true;
                                }
                                ?>
                                <label style="display:inline;padding-right:8px;"><input <?php if($checked) { echo "checked";} ?> type="checkbox" name="market_new_comp_versions[<?php echo $v; ?>]" value="1" /> <?php echo $v; ?></label>
                                <?php }; ?>
                            </div>
                        </div>

                        <div>
                            <label><?php _e('Download file'); ?></label>
                        </div>
                        <div id="tabs_upload">
                            <ul>
                                <?php if(isset($file_download_file) && !empty($file_download_file) && file_exists($file_download_file) ) { ?>
                                <li><a href="#market-url"><?php _e('Download URL', 'market'); ?></a></li>
                                <li class="ui-tabs-selected ui-state-active"><a href="#market-file"><?php _e('Download file', 'market'); ?></a></li>
                                <?php } else { ?>
                                <li class="ui-tabs-selected ui-state-active"><a href="#market-url"><?php _e('Download URL', 'market'); ?></a></li>
                                <li><a href="#market-file"><?php _e('Download file', 'market'); ?></a></li>
                                <?php } ?>
                            </ul>
                            <div id="market-url" class="row-wrapper">
                                <label><?php _e('Download URL','market'); ?></label>
                                <input type="text" name="market_download_url" id="market_download_url" value="<?php echo osc_esc_html(@$file_download_url);?>" />
                                <label><?php _e('If \'Download URL\' is set,  cannot be uploaded files' , 'market'); ?></label>
                            </div>
                            <div id="market-file" class="row-wrapper">
                                <?php if(isset($file_download_file) && !empty($file_download_file) && file_exists($file_download_file) ) { ?>
                                <a target="_blank" href="<?php echo osc_base_url() . 'oc-content/plugins/market/download.php?code=';?><?php echo $market['s_slug'];?>@<?php echo $file_version;?>"><?php _e('Download attachment', 'market');?></a>
                                <input type="hidden" name="market_file_exist" value="1"/>
                                <?php } ?>
                                <label><?php printf(__('Allowed extensions are %s. Any other file will not be uploaded', 'market'), osc_get_preference('allowed_ext', 'market')) ; ?></label>
                                <input type="file" name="market_file_new" />
                                <label><?php _e('If \'Download URL\' is set,  cannot be uploaded files' , 'market'); ?></label>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>

            <?php if($market_files != null && is_array($market_files) && count($market_files) > 0) { ?>

                <h3><?php _e('Market files', 'market'); ?></h3>
                <div id="tabs_files" class="row-wrapper">
                    <ul>
                        <?php foreach($market_files as $_r) { ?>
                        <li><a href="#<?php echo str_replace(array('.'), '', $_r['s_version']); ?>"><?php echo $_r['s_version']; ?></a></li>
                        <?php } ?>
                    </ul>
                    <?php
                    foreach($market_files as $_r) {
                        $slug_file = str_replace(array('.'), '', $_r['s_version']);  ?>
                    <div id="<?php echo $slug_file;?>" style="padding: 10px;background-color: #F3F3F3;border: solid 1px #DDD;border-radius: 4px;-webkit-border-radius: 4px;">
                        <div style="">
                            <div>
                                <?php if($_r['s_download']!='') { ?>
                                    <label><?php _e('Download URL','market'); ?></label>
                                    <div>
                                        <?php echo $_r['s_download'];?> <br />
                                        <a target="_blank" href="<?php echo osc_base_url() . 'oc-content/plugins/market/download.php?code=';?><?php echo $market['s_slug'];?>@<?php echo $_r['s_version'];?>"><?php _e('Download', 'market');?></a>
                                    </div>
                                <?php } else { ?>
                                    <?php $tmp = explode("/", $_r['s_file'] ); ?>
                                    <?php if( $tmp[count($tmp)-1] ) { ?>
                                    <label><?php _e('Download URL','market'); ?></label>
                                    <div>
                                        <?php echo $tmp[count($tmp)-1]; ?><br />
                                        <a target="_blank" href="<?php echo osc_base_url() . 'oc-content/plugins/market/download.php?code=';?><?php echo $market['s_slug'];?>@<?php echo $_r['s_version'];?>"><?php _e('Download attachment', 'market');?></a>
                                    </div>
                                    <?php } else { ?>
                                    <?php _e('No download file');?>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div>
                                <label><?php _e('Version','market'); ?></label>
                                <div>
                                    <a class="btn" style="cursor:default;"><?php echo $_r['s_version']; ?></a>
                                </div>
                            </div>

                            <div class="clear"></div>

                            <div>
                                <label><?php _e('Compatible versions:', 'market');?></label>
                                <div>
                                <?php foreach(explode(',',$_r['s_compatible']) as $v) { ?>
                                    <a class="btn" style="cursor:default;"><?php echo $v; ?></a>
                                <?php }; ?>
                                </div>
                            </div>
                            <div class="clear" style="padding-top:10px;"></div>
                            <div>
                                <a class="btn btn-submit float-left" href="javascript:delete_market_file(<?php echo $_r['pk_i_id'] . ", " . $_r['fk_i_item_id'] . ", '" . $secret . "', '".$slug_file."'" ;?>);"  class="delete"><?php _e('Delete', 'market') ; ?></a>
                                <?php if($_r['b_enabled']==1) { ?>
                                <a title="<?php _e('Disable listing', 'market');?>" href="javascript:disable(<?php echo $_r['pk_i_id']; ?>);" class="btn btn-green float-right"><?php _e('Enabled', 'market'); ?></a>
                                <?php } else { ?>
                                <a title="<?php _e('Enable listing', 'market');?>" href="javascript:enable(<?php echo $_r['pk_i_id']; ?>);" class="btn btn-red float-right"><?php _e('Disabled', 'market'); ?></a>
                                <?php } ?>
                                <a class="btn float-right" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'market_file_frm.php')?>?itemId=<?php echo $item_id;?>&fileId=<?php echo $_r['pk_i_id'];?>"><?php _e('Edit', 'market'); ?></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </td>
            <td valign="top"  width="330"><div class="well" style="margin-left:30px;"><?php _e('Slug', 'market'); ?><br/><b><?php echo $market['s_slug']; ?></b><br /><?php _e('Preview', 'market'); ?><br/><b><?php echo $market['s_preview']; ?></b></div></td>
            </tr>
        </table>

        <div class="form-actions">
            <a href="javascript:history.go(-1)" class="btn"><?php _e('Cancel', 'market')?></a>
            <input type="submit" value="<?php echo $submit_txt;?>" class="btn btn-submit">
        </div>
    </form>

    <script type="text/javascript">

        <?php if($error) { ?>
        $(".jsMessage > p").html('<?php echo $str_error;?>');
        $(".jsMessage").removeClass('flashmessage-info');
        $(".jsMessage").removeClass('flashmessage-ok');
        $(".jsMessage").addClass('flashmessage-error');
        $(".jsMessage").slideDown('slow');//.delay(3000).slideUp('slow');
        scrollTo(0,0);
        <?php } else if ($success) { ?>
        $(".jsMessage > p").html('<?php echo $str_success;?>');
        $(".jsMessage").removeClass('flashmessage-info');
        $(".jsMessage").removeClass('flashmessage-error');
        $(".jsMessage").addClass('flashmessage-ok');
        $(".jsMessage").slideDown('slow');//.delay(3000).slideUp('slow');
        scrollTo(0,0);

        setTimeout(function() {
            window.location = "<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'list_market.php'); ?>";
        },1250);

        <?php } ?>


        $(function() {
            $( "#tabs_files" ).tabs();
            $( "#tabs_upload" ).tabs();
        });

        var ufIndex = 0;
        function gebi(id) { return document.getElementById(id); }
        function ce(name) { return document.createElement(name); }
        function re(id) {
            var e = gebi(id);
            e.parentNode.removeChild(e);
        }


        function delete_market_file(id, item_id, secret, slug) {
            var result = confirm('<?php echo __('This action can\\\'t be undone. Are you sure you want to continue?', 'market'); ?>');
            if(result) {
                $.ajax({
                    type: "POST",
                    url: '<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>&paction=delete&id='+id+'&item='+item_id+'&secret='+secret,
                    dataType: 'json',
                    success: function(data){
                        if(data.error==0) {
                            $('#tabs_files').tabs( "remove" , slug );
                        }

                        $(".jsMessage > p").html(data.msg);
                        $(".jsMessage").removeClass('flashmessage-info');
                        $(".jsMessage").removeClass('flashmessage-error');
                        $(".jsMessage").addClass('flashmessage-ok');
                        $(".jsMessage").slideDown('slow');//.delay(3000).slideUp('slow');
                        scrollTo(0,0);
                    }
                });
            }
        }

        function disable(file_id) {
            var url = "<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>&paction=disable&fileId="+file_id;
            changeStatus( file_id, url );
        }

        function enable(file_id) {
            var url = "<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>&paction=enable&fileId="+file_id;
            changeStatus( file_id, url );
        }

        function changeStatus(file_id, url) {
            $.ajax({
                type: "POST",
                url: url,
                dataType: 'json',
                success: function(data){
                    $(".jsMessage > p").html(data.msg);
                    if(data.error == 0) {
                        $(".jsMessage").removeClass('flashmessage-info');
                        $(".jsMessage").removeClass('flashmessage-error');
                        $(".jsMessage").addClass('flashmessage-ok');
                    } else {
                        $(".jsMessage").removeClass('flashmessage-info');
                        $(".jsMessage").removeClass('flashmessage-ok');
                        $(".jsMessage").addClass('flashmessage-error');
                    }

                    $(".jsMessage").slideDown('slow');//.delay(3000).slideUp('slow');
                    scrollTo(0,0);

                    setTimeout(function() {
                        window.location = "<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'market_file_frm.php');?>?itemId=<?php echo $item_id;?>";
                    },1250);
                }
            });
        }
        $(".flashmessage .ico-close").click(function(){$(this).parent().hide();});
    </script>

<?php } ?>

<?php
/*
 *  functions
 */

/*
 * Insert market file
 */
function market_admin_add_file($item_id, $market, &$error, &$aError, &$file_version, &$file_download_url, &$aCompatible)
{
    _market_add_file($item_id, $market, $error, $aError, $file_version, $file_download_url, $aCompatible, 1);
//    _market_add_file($item_id, $market, &$error, &$aError, &$file_version, &$file_download_url, &$aCompatible, 1);
}

/*
 * Update market file
 */
function market_admin_update_file($item_id, $file_id, $market, &$error, &$aError, &$file_version, &$file_download_url, &$aCompatible, $path)
{
    _market_update_file($item_id, $file_id, $market, $error, $aError, $file_version, $file_download_url, $aCompatible, $path);
}
?>