<?php
if(!THECLOUD_SYSTEM) exit;

class csrf {
  var $fw;
  var $token;
  
  function __construct(&$fw) {
    $this->fw = $fw;
    
    $this->check_session();
    
    $this->fw->response->add_variables(array('csrf_token' => $this->token));
  }
  
  function check_session() {
    if($this->fw->input->session('csrf_token')) {
      $this->token = $this->fw->input->session('csrf_token');
    }else{
      //generate CSRF token
      $this->generate_token();
    }
  }
  
  private function generate_token() {
    $this->token = $this->fw->string->randomcode(40);
    
    $this->fw->input->session('csrf_token', $this->token);
  }
}
  