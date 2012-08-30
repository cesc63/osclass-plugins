<?php 
/*
Plugin Name: Validate email
Plugin URI: http://www.osclass.org/
Description: Validate email domain at new user
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: validate_email
Plugin update URI: validate_email
*/

function validate_email_check_domain_js() {
    $section  = osc_get_osclass_section() ;
    $location = osc_get_osclass_location() ;
    
    /*
     * ADD ITEM
     */
    if( $section == 'item_add' && $location == 'item' ) {
    ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#contactEmail').rules("add", {
        required: true,
        email: true,
        remote: '<?php echo osc_base_url() . 'oc-content/plugins/validate_email/emails.php';?>',
        messages: {
            required: "<?php _e("Email: this field is required", 'validate_email'); ?>",
            remote: "<?php _e("Invalid email address", 'validate_email'); ?>"
        }});
    }) ;
</script>
    <?php
    }
    
    /*
     * SHARE ITEM
     */
    if( $section == 'send_friend' && $location == 'item') {
        ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#yourEmail').rules("add", {
        required: true,
        email: true,
        remote: '<?php echo osc_base_url() . 'oc-content/plugins/validate_email/emails.php';?>',
        messages: {
            required: "<?php _e("Email: this field is required", 'validate_email'); ?>",
            remote: "<?php _e("Invalid email address", 'validate_email'); ?>"
        }});
        $('#friendEmail').rules("add", {
        required: true,
        email: true,
        remote: '<?php echo osc_base_url() . 'oc-content/plugins/validate_email/emails.php';?>',
        messages: {
            required: "<?php _e("Email: this field is required", 'validate_email'); ?>",
            remote: "<?php _e("Invalid email address", 'validate_email'); ?>"
        }});
    }) ;
</script>
    <?php 
    }
    /*
     * CONTACT ITEM PAGE
     */
    if( $section == '' && $location == 'item' || 
        $section == 'contact' && $location == 'item' ) {
        ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#contact-dialog form input[name="yourEmail"]').rules("add", {
            required: true,
            email: true,
            remote: '<?php echo osc_base_url() . 'oc-content/plugins/validate_email/emails.php';?>',
            messages: {
                required: "<?php _e("Email: this field is required", 'validate_email'); ?>",
                remote: "<?php _e("Invalid email address", 'validate_email'); ?>"
        }});
        $('form#contact_form input[name="yourEmail"]').rules("add", {
            required: true,
            email: true,
            remote: '<?php echo osc_base_url() . 'oc-content/plugins/validate_email/emails.php';?>',
            messages: {
                required: "<?php _e("Email: this field is required", 'validate_email'); ?>",
                remote: "<?php _e("Invalid email address", 'validate_email'); ?>"
        }});
    }) ;
</script>
    <?php 
    }
    
    /*
     * REGISTER NEW USER
     */
    if( $section == 'register' && $location == 'register' ) {
        ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#s_email').rules("add", {
        required: true,
        email: true,
        remote: '<?php echo osc_base_url() . 'oc-content/plugins/validate_email/emails.php';?>',
        messages: {
            required: "<?php _e("Email: this field is required", 'validate_email'); ?>",
            remote: "<?php _e("Invalid email address", 'validate_email'); ?>"
        }});
    }) ;
</script>
    <?php 
    }
    
    /*
     * REGISTER NEW USER
     */
    if( $section == 'change_email' && $location == 'user' ) {
        ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#new_email').rules("add", {
        required: true,
        email: true,
        remote: '<?php echo osc_base_url() . 'oc-content/plugins/validate_email/emails.php';?>',
        messages: {
            required: "<?php _e("Email: this field is required", 'validate_email'); ?>",
            remote: "<?php _e("Invalid email address", 'validate_email'); ?>"
        }});
    }) ;
</script>
    <?php 
    }
    
}

osc_add_hook('footer', 'validate_email_check_domain_js') ;

?>