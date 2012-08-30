<?php
if( !function_exists('redirect_to_url') ) {
      function redirect_to_url($url) {
            header('Location: ' . $url);
            exit;
      }
}
function check_disallowed_caps(){
      $caps = osc_com_DissallowCapability::newInstance();
      $caps->checkCapabilities();
}
function osc_com_dissallow_capability($capability, $subaction = false){
	$caps = osc_com_DissallowCapability::newInstance();
	$caps->addCapability($capability, $subaction);
}
?>