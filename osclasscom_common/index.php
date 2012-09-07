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

    function osclass_common_generator() { ?>
        <meta name="generator" content="Osclass.com" />
    <?php }
    osc_add_hook('header', 'osclass_common_generator');
    osc_remove_hook('header', 'osc_meta_generator');

    function osclass_common_footer() { ?>
    <div class="float-left">
        <?php printf(__('Powered by <a href="%s" target="_blank">Osclass</a>'), 'http://osclass.org/'); ?> -
        <a title="<?php _e('Contact'); ?>" href="#contact"><?php _e('Contact'); ?></a>
    </div>
    <div class="clear"></div>
    <?php }
    osc_add_hook('admin_content_footer', 'osclass_common_footer');
    osc_remove_hook('admin_footer', 'admin_content_footer');
    /* file end: show_ip_manage_listings/index.php */