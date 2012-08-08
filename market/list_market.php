<?php
    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */
    
    // ------------------------------------------------------
    //       search 
    // ------------------------------------------------------
    $start   = Params::getParam('iDisplayStart');
    $limit   = Params::getParam('iDisplayLength');
    $p_iPage = 1;
    
    // default values
    if( !isset($start) ) {
        $start = 0 ;
    } else {
        $start = Params::getParam('iDisplayStart');
    }
    Params::setParam('iDisplayStart', $start);
    // ------------------------------------------------------
    if( !isset($limit) ) {
        $limit = 10 ;
    } else {
        $limit = Params::getParam('iDisplayLength');
    }
    Params::setParam('iDisplayLength', $limit);
    // ------------------------------------------------------
    if( !is_numeric(Params::getParam('iPage')) || Params::getParam('iPage') < 1 ) {
        $p_iPage = 1; 
    } else {
        $p_iPage = Params::getParam('iPage') ;
    }
    Params::setParam('iPage', $p_iPage );
    // ------------------------------------------------------
    $aData = ModelMarket::newInstance()->manageMarket($p_iPage, $limit);
    ?>
<script type="text/javascript">
    // autocomplete users
    $(document).ready(function(){
        // dialog delete
        $("#dialog-item-delete").dialog({
            autoOpen: false,
            modal: true,
            title: '<?php echo osc_esc_js( __('Delete listing', 'market') ); ?>'
        });
    });

    // dialog delete function
    function delete_dialog(item_id) {
        $("#dialog-item-delete input[name='id[]']").attr('value', item_id);
        $("#dialog-item-delete").dialog('open');
        return false;
    }
</script>

<h2 class="render-title"><?php _e('Market listings') ; ?></h2>
<div class="relative">
    <div id="listing-toolbar">
        <div class="float-right">
            <form method="get" action="<?php echo osc_admin_base_url(true); ?>"  class="inline select-items-per-page">
                <?php foreach( Params::getParamsAsArray('get') as $key => $value ) { ?>
                <?php if( $key != 'iDisplayLength' ) { ?>
                <input type="hidden" name="<?php echo $key; ?>" value="<?php echo osc_esc_html($value); ?>" />
                <?php } } ?>
                <select name="iDisplayLength" class="select-box-extra select-box-medium float-left" onchange="this.form.submit();" >
                    <option value="10"><?php printf(__('%d Listings'), 10); ?></option>
                    <option value="25" <?php if( Params::getParam('iDisplayLength') == 25 ) echo 'selected'; ?> ><?php printf(__('%d Listings'), 25); ?></option>
                    <option value="50" <?php if( Params::getParam('iDisplayLength') == 50 ) echo 'selected'; ?> ><?php printf(__('%d Listings'), 50); ?></option>
                    <option value="100" <?php if( Params::getParam('iDisplayLength') == 100 ) echo 'selected'; ?> ><?php printf(__('%d Listings'), 100); ?></option>
                </select>
            </form>
        </div>
    </div>    
    <div class="">
        <table class="table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e('Title') ; ?></th>
                    <th><?php _e('Slug'); ?></th>
                    <th><?php _e('Updated'); ?></th>
                    <th><?php _e('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if(count($aData['aaData'])>0) { ?>
            <?php foreach( $aData['aaData'] as $array) { ?>
                <tr>
                    <td>
                    <?php echo $array['s_title'];?>
                    </td>
                    <td>
                    <?php echo $array['s_update_url'];?>
                    </td>
                    <td>
                    <?php echo $array['dt_mod_date'];?>
                    </td>
                    <td>
                        <a class="btn btn-green" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'market_file_frm.php')?>?itemId=<?php echo $array['fk_i_item_id'];?>">
                            <?php _e('Add/Edit files','market');?>
                        </a>
                        <a class="btn" onclick="return delete_dialog('<?php echo $array['fk_i_item_id']; ?>');" href="<?php echo osc_admin_base_url(true).'?page=items&action=delete&id='.$array['fk_i_item_id'] ;?>">
                            <?php _e('Delete','market');?>
                        </a>
                        <a class="btn" href="<?php echo osc_admin_base_url(true).'?page=items&action=item_edit&id='.$array['fk_i_item_id'] ;?>">
                            <?php _e('Edit','market');?>
                        </a>
                        <a class="btn btn-blue" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php')?>?itemId=<?php echo $array['fk_i_item_id'];?>">
                            <?php _e('Show stats','market');?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="9" class="text-center">
                    <p><?php _e('No data available in table') ; ?></p>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php
    osc_show_pagination_admin($aData);
?>
<form id="dialog-item-delete" method="get" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide">
    <input type="hidden" name="page" value="items" />
    <input type="hidden" name="action" value="delete" />
    <input type="hidden" name="id[]" value="" />
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Are you sure you want to delete this listing? All market files will be deleted.', 'market'); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
            <a class="btn" href="javascript:void(0);" onclick="$('#dialog-item-delete').dialog('close');"><?php _e('Cancel', 'market'); ?></a>
            <input id="item-delete-submit" type="submit" value="<?php echo osc_esc_html( __('Delete', 'market') ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>