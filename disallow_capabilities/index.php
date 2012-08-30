<?php
/*
Plugin Name: Disallow Capabilities
Plugin URI: http://osclass.org/
Description: Disallow Capabilities.
Version: 1.0
Author: OSClass
Author URI: http://osclass.org/
Plugin update URI: 
*/
define('DISALLOW_CATEGORIES_PATH', dirname(__FILE__) . '/') ;
require_once(DISALLOW_CATEGORIES_PATH . 'class.php');
require_once(DISALLOW_CATEGORIES_PATH . 'helpers.php');

osc_com_dissallow_capability('items','settings');
osc_com_dissallow_capability('items','items_reported');
osc_com_dissallow_capability('media');
osc_com_dissallow_capability('comments');
osc_com_dissallow_capability('cfields');

osc_com_dissallow_capability('categories');

osc_com_dissallow_capability('appearance','self');
osc_com_dissallow_capability('appearance','add');
osc_com_dissallow_capability('appearance','activate');
osc_com_dissallow_capability('appearance','delete');

osc_com_dissallow_capability('plugins','self');
osc_com_dissallow_capability('plugins','add');
osc_com_dissallow_capability('plugins','uninstall');
osc_com_dissallow_capability('plugins','install');

osc_com_dissallow_capability('stats');

osc_com_dissallow_capability('settings');

osc_com_dissallow_capability('users');
osc_com_dissallow_capability('admins');

osc_com_dissallow_capability('tools');


osc_add_filter('custom_plugin_title','disallow_capabilities_title');
function disallow_capabilities_title($string){
  if(Params::getParam('page') == 'plugins' && Params::getParam('file') == basename(DISALLOW_CATEGORIES_PATH) . '/pages/disallowed.php'){
      $string = __('Permission denied');
  }
  return $string;
}
osc_add_hook('init_admin','check_disallowed_caps');

?>