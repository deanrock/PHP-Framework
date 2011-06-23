<?php
if(!THECLOUD_SYSTEM) exit;

class performance {
  function __construct() {
    $this->db = new performance_item(); //database
    $this->app = new performance_item(); //application
  }
  function get_time() {
    return microtime(true); //get as float
  }
  
  function get_difference($a, $b) {
    return ($b-$a);
  }
}

class performance_item {
  var $time;
  var $starttime;
  var $endtime;
  
  function __construct() {
    $this->time = 0;
    $this->starttime = 0;
    $this->endtime = 0;
  }
  
  function set_starttime($time) {
    $this->starttime = $time;
  }
  
  function set_endtime($time) {
    $this->endtime = $time;
  }
  
  function difference($a, $b) {
    $this->time += ($b-$a);
  }
  
  function get_time() {
    if($this->time == 0) {
    $this->time = $this->endtime - $this->starttime;
    }
    
    return $this->time;
  }
}
