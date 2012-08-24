<?php
// private user area 
if ( osc_is_web_user_logged_in() ) {
    
    $paction = Params::getParam('paction');
    
    if($paction == 'done') {
        $user_id = Params::getParam('userId');
        $secret  = Params::getParam('secret');
        
        $res  = false;
        $user = User::newInstance()->findByPrimaryKey($user_id);
        if( !empty ($user) && $user['s_secret'] == $secret) {
            error_log("trying ");
            $res = User::newInstance()->deleteUser( $user_id );
        } 
    } else {
        $userId = osc_logged_user_id();
        $user   = User::newInstance()->findByPrimaryKey($userId);
    }
?>
<?php if($paction != 'done') { ?>
<div>
    <p><?php _e('Are you unsubcribing your account, all your listings and alerts will be removed.', 'remove_users'); ?></p>
    <p><?php _e('Are you sure?', 'remove_users'); ?></p>
    <form action="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'confirm_unsubscribe.php');?>"  method="POST">
        <input type="hidden" value="<?php echo $userId; ?>" name="userId"/>
        <input type="hidden" value="<?php echo $user['s_secret']; ?>" name="secret"/>
        <input type="hidden" value="done" name="paction"/>
        <input type="submit" <?php _e( 'I\'m sure, unsubscribe me', 'remove_users');?>/>
        <a href="<?php echo osc_user_dashboard_url();?>"><?php _e( 'Cancel', 'remove_users');?></a>
    </form>
</div>
<?php } else { ?>
    <?php if($res) { ?>
    <?php _e("Unsubscribed succesfully.", 'remove_users'); ?>
    <?php } else {?>
    <?php _e('Cannot unsubscribe user.', 'remove_users'); ?>
    <?php } ?>

<?php } ?>

<?php } ?>