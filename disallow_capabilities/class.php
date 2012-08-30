<?php
class osc_com_DissallowCapability
      {
      private $capabilities ;
      private $hide_menu ;
      private static $instance ;

      public static function newInstance()
      {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
      }
      function __construct() {
            $this->capabilities = array();
            $this->hide_menu = false;
      }
      function updateHideMenu($value = false)
      {
            $this->hide_menu = $value;
      }
      function addCapability($capability, $subaction = false)
      {
            $currentCapabilities = $this->capabilities;
            if(!isset($currentCapabilities[$capability])){
                  $currentCapabilities[$capability] = array();
            }
            if($subaction){
                  $currentCapabilities[$capability][$subaction] = true;
            }
            $this->capabilities = $currentCapabilities;
      }
      function removeCapability($capability = false, $subaction = false)
      {
            $currentCapabilities = $this->capabilities;
            if($subaction){
                  unset($currentCapabilities[$capability][$subaction]);
            } else {
                  if($capability){
                        unset($currentCapabilities[$capability]);    
                  }
            }
            $this->capabilities = $currentCapabilities;
      }
      function checkCapabilities()
      {
            if($this->hide_menu){
                  $this->hideMenu();
            }
            //get current capabilities
            $page = Params::getParam('page');
            $action = Params::getParam('action');
            $file = Params::getParam('file');
            $currentCapabilities = $this->capabilities;
            $redirect = false;
            if (isset($currentCapabilities[$page]) && $file != 'disallow_capabilities/pages/disallowed.php') {
                if(count($currentCapabilities[$page]) == 0){
                  $redirect = true;
                } else{
                  if(isset($currentCapabilities[$page][$action])){
                        $redirect = true;
                  }
                  if(isset($currentCapabilities[$page]['self']) && $action == ''){
                        $redirect = true;
                  }
                }
            }
            if($redirect){
                  redirect_to_url(osc_admin_render_plugin_url('disallow_capabilities/pages/disallowed.php'));
            }
      }
      function hideMenu(){
            return felse;
      }
      function getCapabilities()
      {
            return $this->capabilities;
      }
}
?>