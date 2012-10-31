<?php
$adminManager = Admin::newInstance();
$admin = $adminManager->findByPrimaryKey(osc_logged_admin_id());
?>
<div id="general-settings">
    <form name="corporateboardmenu_form" method="post">
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>settings.php" />
        <input type="hidden" name="subaction" value="update-settings">
        <fieldset>
            <div class="form-horizontal">
                <h2 class="render-title"><?php _e('General Settings', 'corporateboardmenu') ; ?></h2>
                <div class="form-row">
                    <div class="form-label"><?php _e('Company name', 'corporateboardmenu') ; ?></div>
                    <div class="form-controls"><input type="text" class="xlarge" name="pageTitle" value="<?php echo osc_esc_html( osc_page_title() ); ?>" /></div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Contact email', 'corporateboardmenu') ; ?></div>
                    <div class="form-controls"><input type="text" class="large" name="contactEmail" value="<?php echo osc_esc_html( osc_contact_email() ) ; ?>" /></div></div>
                <h2 class="render-title separate-top"><?php _e('Edit admin user', 'corporateboardmenu') ; ?></h2>
                <div class="form-row clear">
                    <div class="form-label"><?php _e('Name <em>(required)</em>', 'corporateboardmenu') ; ?></div>
                    <div class="form-controls">
                        <?php AdminForm::name_text($admin) ; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Username <em>(required)</em>', 'corporateboardmenu') ; ?></div>
                    <div class="form-controls"><?php AdminForm::username_text($admin) ; ?></div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Current password', 'corporateboardmenu') ; ?></div>
                    <div class="form-controls">
                        <?php AdminForm::old_password_text($admin) ; ?>
                        <p class="help-inline"><em><?php _e('If you want to change your password, type your current password here. Otherwise, leave this blank.', 'corporateboardmenu') ; ?></em></p>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('New password', 'corporateboardmenu') ; ?></div>
                    <div class="form-controls">
                        <?php AdminForm::password_text($admin) ; ?>
                    </div>
                    <div class="form-controls">
                        <?php AdminForm::check_password_text($admin) ; ?>
                        <p class="help-inline"><em><?php _e('Type your new password again', 'corporateboardmenu') ; ?></em></p>
                    </div>
                </div>
                <h2 class="render-title separate-top"><?php _e('Google analytics', 'corporateboardmenu') ; ?></h2>
                <div class="form-row clear">
                    <div class="form-label"><?php _e('Tracking ID', 'corporateboardmenu') ; ?></div>
                    <div class="form-controls">
                        <input type="text" class="xlarge" name="googleanalytics_trackingid" value="<?php echo osc_esc_html( osc_get_preference('googleanalytics_trackingid', 'corporateboardmenu') ); ?>" />
                    </div>
                </div>
                <h2 class="render-title separate-top"><?php _e('Other settings', 'corporateboardmenu') ; ?></h2>
                <div class="form-row clear">
                    <div class="form-label"><?php _e('Show in OSClass.com', 'corporateboardmenu') ; ?></div>
                    <div class="form-controls">
                        <div class="form-label-checkbox">
                            <label>
                                <input type="checkbox" <?php echo ( osc_get_preference('show_in_osclass','corporateboardmenu')==0 ? 'checked="checked"' : '' ) ; ?> name="show_in_osclass" value="notshow" /> <?php _e('I do not want my ads to appear in OSClass.com\'s home', 'corporateboardmenu') ; ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" id="save_changes" value="<?php echo osc_esc_html( __('Save changes', 'corporateboardmenu') ) ; ?>" class="btn btn-submit" />
                </div>
            </div>
        </fieldset>
    </form>
</div>