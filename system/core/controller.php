<?php
if(!THECLOUD_SYSTEM) exit;

class Controller {
  function __construct() {
    global $framework;
    
    foreach($framework->libraries->list_libraries() as $lib) {
      $this->$lib = &$framework->$lib;
    }
  }
  
}
