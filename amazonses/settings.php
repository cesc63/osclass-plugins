<h2 class="render-title"><?php _e('Amazon SES', 'amazonses'); ?></h2>
<form action="<?php echo osc_admin_render_plugin_url('amazonses/settings.php'); ?>" method="post">
    <input type="hidden" name="amazonses_hidden" value="configuration" />
    <fieldset>
        <div class="form-horizontal">
            <div class="form-row">
                <div class="form-label"><?php _e('AWS Access Key ID', 'amazonses') ?></div>
                <div class="form-controls"><input type="text" class="xlarge" name="amazonses_awsaccesskeyid" value="<?php echo osc_esc_html( osc_get_preference('amazonses_awsaccesskeyid', 'amazonses') ); ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('AWS Secret Key', 'amazonses') ?></div>
                <div class="form-controls"><input type="text" class="xlarge" name="amazonses_awssecretkey" value="<?php echo osc_esc_html( osc_get_preference('amazonses_awssecretkey', 'amazonses') ); ?>"></div>
            </div>
            <div class="form-actions">
                <input type="submit" value="<?php echo osc_esc_html(__('Save changes', 'amazonses')); ?>" class="btn btn-submit">
            </div>
        </div>
    </fieldset>
</form>