<?php 
$item_id    = Params::getParam('itemId');
$file_id    = Params::getParam('fileId');

if ( osc_is_web_user_logged_in() && $item_id != '' && is_numeric($item_id)) {
    // item belongs to logged user
    $item_info = Item::newInstance()->findByPrimaryKey( $item_id );
    $valid_file = true;
    if( $file_id != '' && is_numeric($file_id) ) {
        $marketFile = ModelMarket::newInstance()->marketFileByPrimaryKey($file_id);
        $auxFile    = ModelMarket::newInstance()->getFileFromItem($item_id);
        if( @$marketFile['fk_i_market_id'] != @$auxFile['fk_i_market_id'] ) {
            $valid_file = false;
        }
    }
    if(@$item_info['fk_i_user_id'] == osc_logged_user_id() && $valid_file) {
        // file exist & belongs to an user item ?
        $aCompatible        = array();

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
            market_front_add_file($item_id, $market, &$error, &$aError, &$file_version, &$file_download_url, &$aCompatible);
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
            market_front_update_file($item_id, $file_id, $market, &$error, &$aError, &$file_version, &$file_download_url, &$aCompatible, $file_info['s_file']);
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

        $market_versions = explode(",", osc_get_preference('compatible_versions', 'market'));
        $item_info       = Item::newInstance()->findByPrimaryKey( $item_id );
        if(isset($file_id) && is_numeric($file_id)) {
            // updating
            $file_info  = ModelMarket::newInstance()->marketFileByPrimaryKey( $file_id );
            // clear variables used to refill inputs
            $aCompatible        = explode(',', $file_info['s_compatible']);
            $file_version       = $file_info['s_version'];
            $file_download_url  = $file_info['s_download'];
            $file_download_file = $file_info['s_file'];

            $isEdit     = true;
            $title      = __('Edit market file', 'market');
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
<div class="wrapper" style="padding-top:10px;">
    <h3><?php echo $title; ?></h3>
    <h4><?php echo $item_info['locale'][osc_language()]['s_title'];?></h4>
    <form style="width: 830px;" id="market_form" action="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'market_file_frm_front.php'); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="itemId" value="<?php echo Params::getParam('itemId'); ?>" />
        <input type="hidden" name="plugin_action" value="<?php if($isEdit) { echo "edit"; } else { echo "done"; } ?>"/>
        <table>
            <tr>
                <td valign="top"> 
                    <div id="market_files">
                        <div>
                            <label><?php _e('Version','market'); ?></label>
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
                                <label style="display:inline;padding-right:8px;"><input style="width: 12px;display: inline-block;" <?php if($checked) { echo "checked";} ?> type="checkbox" name="market_new_comp_versions[<?php echo $v; ?>]" value="1" /> <?php echo $v; ?></label>
                                <?php }; ?>
                            </div>
                        </div>

                        <div>
                            <label><?php _e('Download file'); ?></label>
                        </div>
                        
                        <ul class="nav nav-tabs" id="tabs_upload">
                            <li class="active"><a data-toggle="tab" href="#market-url"><?php _e('Download URL', 'market'); ?></a></li>
                            <li><a data-toggle="tab" href="#market-file"><?php _e('Download file', 'market'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="market-url" class="tab-pane active">
                                <label><?php _e('Download URL','market'); ?></label>
                                <input type="text" name="market_download_url" id="market_download_url" value="<?php echo osc_esc_html(@$file_download_url);?>" />
                                <label><?php _e('If \'Download URL\' is set,  cannot be uploaded files' , 'market'); ?></label>
                            </div>
                            <div id="market-file" class="tab-pane">
                                <?php if(isset($file_download_file) && !empty($file_download_file) && file_exists($file_download_file) ) { ?>
                                <a href="<?php echo osc_base_url() . 'oc-content/plugins/market/download.php?code=';?><?php echo $market['s_slug'];?>@<?php echo $file_version;?>"><?php _e('Download attachment', 'market');?></a>
                                <input type="hidden" name="market_file_exist" value="1"/>
                                <?php } ?>
                                <label><?php printf(__('Allowed extensions are %s. Any other file will not be uploaded', 'market'), osc_get_preference('allowed_ext', 'market')) ; ?></label>
                                <input type="file" name="market_file_new" />
                                <label><?php _e('If \'Download URL\' is set,  cannot be uploaded files' , 'market'); ?></label>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
            </td>
            <td valign="top"  width="330">
                <div class="well" style="margin-left:30px;">
                    <?php _e('Slug', 'market'); ?>
                    <br/><b><?php echo $market['s_slug']; ?></b>
                    <br/><?php _e('Preview', 'market'); ?><br/>
                    <b><?php echo $market['s_preview']; ?></b>
                    <?php if(isset($file_info)) { ?>
                    <br/><br/><?php _e('File Status', 'market'); ?><br/>
                    <?php if($file_info['b_enabled']==1) { ?>
                    <b><?php _e('Enabled', 'market'); ?></b>
                    <?php } else { ?>
                    <b><?php _e('Disabled', 'market'); ?></b>
                    <?php } ?>
                    <?php } ?>
                </div></td>
            </tr>
        </table>
        
        <div class="form-actions">
            <a href="javascript:history.go(-1)" class="btn"><?php _e('Cancel', 'market')?></a>
            <input type="submit" value="<?php echo $submit_txt;?>" class="btn btn-submit">
        </div>
    </form>
    
    <script type="text/javascript">
        
        $('#tabs_upload a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
        })
        
        <?php if($error) { $str_error = '<a class="btn ico btn-mini ico-close">x</a>'.$str_error; ?>
        $("#flashmessage").html('<?php echo $str_error;?>');
        $("#flashmessage").removeClass('flashmessage-info');
        $("#flashmessage").removeClass('flashmessage-ok');
        $("#flashmessage").addClass('flashmessage-error');
        $("#flashmessage").slideDown('slow');//.delay(3000).slideUp('slow');
        scrollTo(0,0);
        <?php } else if ($success) { $str_success = '<a class="btn ico btn-mini ico-close">x</a>'.$str_success; ?>
        $("#flashmessage").html('<?php echo $str_success;?>');
        $("#flashmessage").removeClass('flashmessage-info');
        $("#flashmessage").removeClass('flashmessage-error');
        $("#flashmessage").addClass('flashmessage-ok');
        $("#flashmessage").slideDown('slow');//.delay(3000).slideUp('slow');
        scrollTo(0,0);
        
        setTimeout(function() {
            window.location = "<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'market_manage_files.php');?>?itemId=<?php echo $item_id;?>";
        },1250);
        
        <?php } ?>
        
        
    </script>
</div>    
<?php } ?>
    <div class="wrapper" style="padding-top:10px;">
    <br/>
    <h1><?php _e('Sorry, we don\'t have any listings with that ID', 'market'); ?></h1>
    <br/>
    </div>
<?php
}
?>

    
<?php 
/*
 *  functions
 */

/*
 * Insert market file
 */
function market_front_add_file($item_id, $market, $error, $aError, $file_version, $file_download_url, $aCompatible) {
    _market_add_file($item_id, $market, &$error, &$aError, &$file_version, &$file_download_url, &$aCompatible, 0);
}

/*
 * Update market file
 */
function market_front_update_file($item_id, $file_id, $market, &$error, &$aError, &$file_version, &$file_download_url, &$aCompatible, $path)
{
// TODO
}

?>