<?php
$adminManager = Admin::newInstance();
$admin = $adminManager->findByPrimaryKey(osc_logged_admin_id());
?>
<div id="general-settings">
    <form name="corporateboard_form" method="post">
        <input type="hidden" name="subaction" value="update-settings">
        <fieldset>
            <div class="form-horizontal">
                <h2 class="render-title"><?php _e('General Settings') ; ?></h2>
                <div class="form-row">
                    <div class="form-label"><?php _e('Company name', 'corporateboard') ; ?></div>
                    <div class="form-controls"><input type="text" class="xlarge" name="pageTitle" value="<?php echo osc_esc_html( osc_page_title() ); ?>" /></div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Contact email') ; ?></div>
                    <div class="form-controls"><input type="text" class="large" name="contactEmail" value="<?php echo osc_esc_html( osc_contact_email() ) ; ?>" /></div></div>
                <h2 class="render-title separate-top"><?php _e('Edit admin user') ; ?></h2>
                <div class="form-row clear">
                    <div class="form-label"><?php _e('Name <em>(required)</em>') ; ?></div>
                    <div class="form-controls">
                        <?php AdminForm::name_text($admin) ; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Username <em>(required)</em>') ; ?></div>
                    <div class="form-controls"><?php AdminForm::username_text($admin) ; ?></div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Current password') ; ?></div>
                    <div class="form-controls">
                        <?php AdminForm::old_password_text($admin) ; ?>
                        <p class="help-inline"><em><?php _e('If you want to change your password, type your current password here. Otherwise, leave this blank.') ; ?></em></p>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('New password') ; ?></div>
                    <div class="form-controls">
                        <?php AdminForm::password_text($admin) ; ?>
                    </div>
                    <div class="form-controls">
                        <?php AdminForm::check_password_text($admin) ; ?>
                        <p class="help-inline"><em><?php _e('Type your new password again') ; ?></em></p>
                    </div>
                </div>
                <h2 class="render-title separate-top"><?php _e('Google analytics') ; ?></h2>
                <div class="form-row clear">
                    <div class="form-label"><?php _e('Tracking ID') ; ?></div>
                    <div class="form-controls">
                        <input type="text" class="xlarge" name="googleanalytics_trackingid" value="<?php echo osc_esc_html( osc_get_preference('googleanalytics_trackingid', 'corporateboard') ); ?>" />
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" id="save_changes" value="<?php echo osc_esc_html( __('Save changes', 'corporateboard') ) ; ?>" class="btn btn-submit" />
                </div>
            </div>
        </fieldset>
    </form>
</div>