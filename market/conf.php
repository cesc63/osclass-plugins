<?php
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    if(Params::getParam('plugin_action')=='done') {
        osc_set_preference('upload_path', Params::getParam('upload_path'), 'market', 'STRING');
        osc_set_preference('allowed_ext', Params::getParam('allowed_ext'), 'market', 'STRING');
        osc_set_preference('compatible_versions', Params::getParam('compatible_versions'), 'market', 'STRING');
        
        osc_set_preference('market_categories_theme'    , '', 'market', 'STRING');
        osc_set_preference('market_categories_plugins'  , '', 'market', 'STRING');
        osc_set_preference('market_categories_languages', '', 'market', 'STRING');
        foreach(Params::getParam('plugin_category') as $key => $value) { 
            if($value == 'THEME') {
                osc_set_preference('market_categories_theme', $key, 'market', 'STRING');
            } else if($value == "PLUGIN") {
                osc_set_preference('market_categories_plugins', $key, 'market', 'STRING');
            } else if($value == "LANGUAGE") {
                osc_set_preference('market_categories_languages', $key, 'market', 'STRING');
            }
        }
        
        echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'market') . '.</p></div>' ;
        
        osc_reset_preferences();
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
</style>
    <form name="market_form" id="market_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
    <div style="float: left; width: 100%;">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>conf.php" />
    <input type="hidden" name="plugin_action" value="done" />
    
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            
            <h3><?php _e('Market Settings', 'market'); ?></h3>
            <label for="allowed_ext"><?php _e('Allowed filetypes (separated by comma)', 'market'); ?></label>
            <div><input type="text" name="allowed_ext" id="allowed_ext" value="<?php echo osc_get_preference('allowed_ext', 'market'); ?>"/></div>
            
            <label for="upload_path"><?php _e('Upload path', 'market'); ?></label>
            <div><input type="text" name="upload_path" id="upload_path" value="<?php echo osc_get_preference('upload_path', 'market'); ?>"/></div>
            
            <label for="compatible_versions"><?php _e('Compatible versions (separated by commas)', 'market'); ?></label>
            <div><input type="text" name="compatible_versions" id="compatible_versions" value="<?php echo osc_get_preference('compatible_versions', 'market'); ?>"/></div>
            
            <div style="clear:both;"></div>
            
            <br/>
            
            <?php $aCategories = PluginCategory::newInstance()->listSelected('market'); ?>
            <?php if(count($aCategories) > 0) { ?>
            <h3><?php _e('Categories plugin', 'market'); ?> <a href="<?php echo osc_admin_configure_plugin_url("market/index.php"); ?>" style="float:none;" class="btn btn-mini"><?php _e('Configure categories','market');?></a></h3><br/>
            <div class="clear"></div>
            <?php 
            $aList = array(); 
            foreach($aCategories as $catId) {
                $aux_cat = Category::newInstance()->findByPrimaryKey($catId);
                if($aux_cat['fk_i_parent_id']) {
                    $aList[$aux_cat['fk_i_parent_id']]['_sub'][] = $aux_cat;
                    $aList[$aux_cat['fk_i_parent_id']]['_sub_str'][] = $aux_cat['pk_i_id'];
                } else {
                    $aList[$aux_cat['pk_i_id']] = $aux_cat;
                    $aList[$aux_cat['pk_i_id']]['_sub_str'][] = $aux_cat['pk_i_id'];
                }
            }
            ?>
            
            <?php foreach($aList as $cat) { ?>
            <?php 
            if( is_array($cat['_sub_str']) ) {
                $nameInput = implode(',',$cat['_sub_str']);
            }
            ?>
            <div style="padding-top: 10px;">
                <a class="btn" style="cursor: default;"><?php echo $cat['locale'][osc_admin_language()]['s_name'];?></a>
                <div class="float-left" style="padding-top: 7px;">
                    <?php 
                    $aCategoryTheme    = explode(',',osc_get_preference('market_categories_theme','market') );
                    $bT = false;
                    if(in_array( $cat['pk_i_id'],$aCategoryTheme) ) { $bT = true; }
                    $aCategoryPlugin   = explode(',',osc_get_preference('market_categories_plugins','market'));
                    $bP = false;
                    if(in_array( $cat['pk_i_id'],$aCategoryPlugin) ) { $bP = true; }
                    $aCategoryLanguage = explode(',',osc_get_preference('market_categories_languages','market'));
                    $bL = false;
                    if(in_array( $cat['pk_i_id'],$aCategoryLanguage) ) { $bL = true; }
                    ?>
                    <label style="display:inline;"><input type="radio" <?php if($bT){echo 'checked="checked"';}?> name="plugin_category[<?php echo $nameInput;?>]" value="THEME"><?php _e('Themes', 'market');?></label>
                    <label style="display:inline;"><input type="radio" <?php if($bP){echo 'checked="checked"';}?> name="plugin_category[<?php echo $nameInput;?>]" value="PLUGIN"><?php _e('Plugins', 'market');?></label>
                    <label style="display:inline;"><input type="radio" <?php if($bL){echo 'checked="checked"';}?> name="plugin_category[<?php echo $nameInput;?>]" value="LANGUAGE"><?php _e('Languages', 'market');?></label>
                </div>
            </div>
            
            <div class="clear"></div>
            
            <?php if(is_array(@$cat['_sub'])) { ?>
            <div style="padding-top: 10px;" class="row-wrapper">
            <?php $auxList = $cat['_sub']; foreach($auxList as $subCat) { ?>
                <a class="btn" style="cursor: default;"><?php echo $subCat['locale'][osc_admin_language()]['s_name'];?></a>
            <?php   } ?>
            </div>
            <div class="clear"></div>
            <?php
                }
            } ?>
            
            <?php } else { ?>
            <legend><?php _e('Categories plugin', 'market'); ?></legend>
            <fieldset>
                <?php _e('Market has not categories related, you need to link plugin with categories', 'market'); ?><a href="<?php echo osc_admin_configure_plugin_url("market/index.php"); ?>" class="btn"><?php _e('Configure categories','market');?></a>
            </fieldset>
            <?php } ?>
        </div>
        <div style="clear: both;"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-submit"><?php _e('Update', 'market');?></button>
        </div>
    </div>
</div>
</form>
