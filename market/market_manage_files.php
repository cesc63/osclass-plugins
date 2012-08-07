<?php 
$item_id = Params::getParam('itemId');
// private user area 
if ( osc_is_web_user_logged_in() && $item_id != '' ) {
    // item belongs to logged user
    $item_info = Item::newInstance()->findByPrimaryKey( $item_id );
    
    if(@$item_info['fk_i_user_id'] == osc_logged_user_id() && is_numeric($item_id)) {
        $market_files = ModelMarket::newInstance()->getFilesFromItem($item_id);
        $market_versions = explode(",", osc_get_preference('compatible_versions', 'market'));
        $secret = $item_info['s_secret'];
    
?>

<style>
    thead {
        text-align: left;
    }
    tr {
        height: 20px;
    }
</style>
<div class="wrapper" style="padding-top:10px;">
<h2 class="render-title" style="float:left;"><?php _e('Market listings') ; ?></h2>

<a class="btn" style="float:right;" href="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'market_file_frm_front.php');?>&itemId=<?php echo $item_id;?>"><?php _e('Add new file', 'market'); ?></a>

<br/>

<div class="relative"> 
    <div class="">
        <table class="table" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead style="">
                <tr>
                    <th><?php _e('Title', 'market') ; ?></th>
                    <th><?php _e('Version', 'market'); ?></th>
                    <th><?php _e('Status', 'market'); ?></th>
                    <th><?php _e('Actions', 'market'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if(count($market_files)>0) { ?>
            <?php foreach( $market_files as $array) { ?>
                <tr>
                    <td>
                    <?php echo $item_info['locale'][osc_language()]['s_title'];?>
                    </td>
                    <td>
                    <?php echo $array['s_version'];?>
                    </td>
                    <td>
                    <?php if($array['b_enabled']==1) { ?>
                    <span><?php _e('Enabled', 'market'); ?></span>
                    <?php } else { ?>
                    <span><?php _e('Disabled', 'market'); ?></span>
                    <?php } ?>
                    </td>
                    <td>
                        <a class="" href="javascript:delete_market_file(<?php echo $array['pk_i_id'] . ", " . $array['fk_i_item_id'] . ", '" . $secret . "', '".str_replace(array('.',','), '', $array['s_version'])."'" ;?>);">
                            <?php _e('Delete','market');?>
                        </a>|
                        <a class="" href="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'market_file_frm_front.php');?>&itemId=<?php echo $array['fk_i_item_id'];?>&fileId=<?php echo $array['pk_i_id'];?>">
                            <?php _e('Edit','market');?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="4" class="text-center">
                    <p><?php _e('No data available in table') ; ?></p>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
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

    function delete_market_file(id, item_id, secret, slug) {
        var result = confirm('<?php echo __('This action can\\\'t be undone. Are you sure you want to continue?', 'market'); ?>');
        if(result) {
            $.ajax({
                type: "POST",
                url: '<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>&paction=delete&id='+id+'&item='+item_id+'&secret='+secret,
                dataType: 'json',
                success: function(data){
                    var $close = $('<a class="btn ico btn-mini ico-close">x</a>').click(function(){
                        $(this).parent().hide();
                    });
                    $("#flashmessage").html(data.msg).append($close);
                    $("#flashmessage").removeClass('flashmessage-info');
                    $("#flashmessage").removeClass('flashmessage-error');
                    $("#flashmessage").addClass('flashmessage-ok');
                    $("#flashmessage").slideDown('slow');//.delay(3000).slideUp('slow');
                    scrollTo(0,0);

                    if(data.error==0) {
                        setTimeout(function() {
                            window.location = "<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'market_manage_files.php');?>?itemId=<?php echo $item_id;?>";
                        },1250);
                    }
                }
            });
        }
    }
</script>
<?php 
    } else {
?>
<div class="wrapper" style="padding-top:10px;">
    <br/>
    <h1><?php _e('Sorry, we don\'t have any listings with that ID', 'market'); ?></h1>
    <br/>
</div>
<?php
    }} else { 
?>
<script>
window.location = "<?php echo osc_base_url(); ?>";
</script>
<?php } ?>