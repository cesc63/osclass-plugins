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

// private user area 
if ( osc_is_web_user_logged_in() ) {    
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
    $user_id = osc_logged_user_id();
    if($user_id) {
        $aData = ModelMarket::newInstance()->manageMarket($p_iPage, 5, array('user_id' => $user_id) );
    } else {
        $aData = array();
    }
    
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

<a class="btn" style="float:right;" href="<?php echo osc_base_url(true).'?page=item&action=item_add';?>"><?php _e('Add new listing', 'market'); ?></a>

<div class="relative"> 
    <div class="">
        <table class="table" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead style="">
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
                <?php $item = Item::newInstance()->findByPrimaryKey( $array['fk_i_item_id'] ); ?>
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
                        <a class="" href="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'market_manage_files.php');?>?itemId=<?php echo $array['fk_i_item_id'];?>">
                            <?php _e('Manage files','market');?>
                        </a>|
                        <a class="" href="<?php echo osc_base_url(true).'?page=item&action=item_delete&id='.$array['fk_i_item_id'].'&secret='.$item['s_secret'];?>">
                            <?php _e('Delete','market');?>
                        </a>|
                        <a class="" href="<?php echo osc_base_url(true).'?page=item&action=item_edit&id='.$array['fk_i_item_id'] ;?>">
                            <?php _e('Edit','market');?>
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
</div>
<?php } else { ?>
<script>
window.location = "<?php echo osc_base_url(); ?>";
</script>
<?php } ?>