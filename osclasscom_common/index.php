<?php
/*
Plugin Name: Osclass.com common
Plugin URI: http://www.osclass.org/
Description: Osclass common
Version: 0.9
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: osclasscom_common
Plugin update URI:
*/

    define('OSCLASSCOM_COMMON_VERSION', '0.9');

    function osclasscom_common_favicon($favicons) {
        $fav   = array();
        $fav[] = array(
            'rel'   => 'shortcut icon',
            'sizes' => '',
            'href'  => osc_plugin_url(__FILE__) . 'favicons/favicon-48.png'
        );
        $fav[] = array(
            'rel'   => 'apple-touch-icon-precomposed',
            'sizes' => '144x144',
            'href'  => osc_plugin_url(__FILE__) . 'favicons/favicon-144.png'
        );
        $fav[] = array(
            'rel'   => 'apple-touch-icon-precomposed',
            'sizes' => '114x114',
            'href'  => osc_plugin_url(__FILE__) . 'favicons/favicon-114.png'
        );
        $fav[] = array(
            'rel'   => 'apple-touch-icon-precomposed',
            'sizes' => '72x72',
            'href'  => osc_plugin_url(__FILE__) . 'favicons/favicon-72.png'
        );
        $fav[] = array(
            'rel'   => 'apple-touch-icon-precomposed',
            'sizes' => '',
            'href'  => osc_plugin_url(__FILE__) . 'favicons/favicon-57.png'
        );

        return $fav;
    }
    osc_add_filter('admin_favicons', 'osclasscom_common_favicon');

    function osclass_common_favicon_theme() {
        $favicons = osc_apply_filter('admin_favicons', array());
        ?>
        <!-- favicons
        ================================================== -->
<?php
        foreach($favicons as $f) { ?>
        <link <?php if($f['rel'] !== '') { ?>rel="<?php echo $f['rel']; ?>" <?php } if($f['sizes'] !== '') { ?>sizes="<?php echo $f['sizes']; ?>" <?php } ?>href="<?php echo $f['href']; ?>">
    <?php }
    }
    osc_add_hook('header', 'osclass_common_favicon_theme');
    osc_add_hook('admin_login_header', 'osclass_common_favicon_theme');

    function osclass_common_generator() { ?>
        <meta name="generator" content="Osclass.com" />
    <?php }
    osc_add_hook('header', 'osclass_common_generator');
    osc_remove_hook('header', 'osc_meta_generator');

    function osclass_common_admin_js() {
        $scripts = AdminThemes::newInstance()->getScripts();
        echo '        <!-- js -->' . PHP_EOL;
        foreach($scripts as $s) {
            $s_path = str_replace(osc_base_url(), osc_base_path(), $s);
            $s_date = date("YmdHis", filemtime($s_path));
            echo '        <script type="text/javascript" src="' . $s . '?' . $s_date . '"></script>' . PHP_EOL;
        }
    }
    osc_add_hook('admin_header', 'osclass_common_admin_js');
    osc_remove_hook('admin_header', 'admin_theme_js');

    function osclass_common_admin_css() {
        $styles = AdminThemes::newInstance()->getStyles();
        echo '        <!-- css -->' . PHP_EOL;
        foreach($styles as $s) {
            $s_path = str_replace(osc_base_url(), osc_base_path(), $s);
            $s_date = date("YmdHis", filemtime($s_path));
            echo '        <link href="' . $s . '?' . $s_date . '" rel="stylesheet" type="text/css" />' . PHP_EOL;
        }
        echo '        <!-- /css -->' . PHP_EOL;
    }
    osc_add_hook('admin_header', 'osclass_common_admin_css');
    osc_remove_hook('admin_header', 'admin_theme_css');

    function osclass_common_footer() { ?>
    <div class="float-left">
        <?php printf(__('Powered by <a href="%s" target="_blank">Osclass</a>', 'osclasscom_common'), 'http://osclass.com/'); ?> -
        <a title="<?php _e('Contact', 'osclasscom_common'); ?>" href="http://osclass.com/contact" target="_blank"><?php _e('Contact', 'osclasscom_common'); ?></a>
    </div>
    <div class="clear"></div>
    <?php }
    osc_add_hook('admin_content_footer', 'osclass_common_footer');

    function osclass_common_ga_tracking() { ?>
<script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-17910966-6']);
        _gaq.push(['_trackPageview']);
            (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/u/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
    <?php }
    osc_add_hook('admin_footer', 'osclass_common_ga_tracking');

    /* admin login */
    function osclass_common_admin_url($url) {
        return 'http://osclass.com/';
    }
    osc_add_filter('login_admin_url', 'osclass_common_admin_url');
    function osclass_common_admin_title($title) {
        return 'Osclass.com';
    }
    osc_add_filter('login_admin_title', 'osclass_common_admin_title');
    function osclass_common_admin_image($image_url) {
        return osc_plugin_url(__FILE__) . 'images/osclass-logo.gif';
    }
    osc_add_filter('login_admin_image', 'osclass_common_admin_image');
    function osclass_common_admin_login_css() {
        echo '<link type="text/css" href="' . osc_plugin_url(__FILE__) . 'css/login.css' . '" media="screen" rel="stylesheet" />' . PHP_EOL;
    }
    osc_add_hook('admin_login_header', 'osclass_common_admin_login_css');

    function osclass_common_titles($title) {
        $title = preg_replace('|osclass$|i', 'Osclass.com', $title);
        return $title;
    }
    osc_add_filter('admin_title', 'osclass_common_titles', 10);

    function osclasscom_common_init_admin() {
        osc_remove_hook('admin_content_footer', 'admin_footer_html');
    }
    osc_add_hook('init_admin', 'osclasscom_common_init_admin');

    function osclasscom_common_bcc_email($mail) {
        $mail->AddBCC('jobboard.notifications@osclass.com');
        return $mail;
    }
    osc_add_filter('pre_send_mail', 'osclasscom_common_bcc_email');

    /* file end: osclasscom_common/index.php */