<?php
if(!THECLOUD_SYSTEM) exit;

class input {
  
  function get() {
    
  }
  
  function post() {
    
  }
  
  function cookie($name) {
    if(isset($_COOKIE[$name])) {
      return $_COOKIE[$name];
    }else{
      return "";
    }
  }
  
  function session($name, $value=null) {
    if($value != null) {
      $_SESSION[$name] = $value;
    }
    
    if(isset($_SESSION[$name])) {
      return $_SESSION[$name];
    }else{
      return "";
    }
  }
  
  
  
  
}
