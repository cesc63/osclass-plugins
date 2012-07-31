<h2 class="render-title"><?php _e('Contact params', 'contact_params'); ?></h2>
<form action="<?php echo osc_admin_render_plugin_url('contact_params/render.php'); ?>" method="post">
    <input type="hidden" name="option" value="submit" />
    <fieldset>
        <div class="form-horizontal">
            <div class="form-row">
                <div class="form-label"><?php _e('Contact emails', 'contact_params') ?></div>
                <div class="form-controls"><input type="text" class="xlarge" name="contact_emails" value="<?php echo osc_esc_html( osc_get_preference('contact_emails', 'contact_params') ); ?>"></div>
            </div>
            <div class="form-actions">
                <input type="submit" value="Save changes" class="btn btn-submit">
            </div>
        </div>
    </fieldset>
</form>