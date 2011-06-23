<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class auth {
  var $status;
  var $data;
  var $fw; //framework
  
  function __construct(&$fw) {
    $this->fw = $fw;
    
    //recognize user
    
    $this->status = 0;
    
    
    
    //Auto login
    if(!$this->fw->input->session('user_id') && $this->fw->input->cookie('auto_hash')) {
      
      $auto_hash = $this->fw->input->cookie('auto_hash');
      
      $data = $this->fw->db->get_where('users', array('auto_hash' => $auto_hash));
      
      if($data->num_rows() > 0) {
        $this->fw->input->session('logged_in', '1');
        $this->fw->input->session('user_id', $data->row()->id);
      }
    }
    
    //Login
    if($this->fw->session('user_id') && $this->fw->session('logged_in') == 1) {
      $id = $this->fw->session('user_id');
      $query = $this->fw->db->query("SELECT * FROM users WHERE u.id=@user_id AND u.is_active = 1", array('user_id' => $id));

      
      if($query->num_rows() > 0) {
        $this->status = 1;
        
        $this->data = $query->row();
      }
    }
  }
  
  function is_authenticated() {
    return ($this->status==1) ? true : false;
  }
  
  function user_info() {
    return $this->data;
  }
  
  function email_exists($email) {
    $query = $this->fw->db->get_where('users', array('email' => $email));
    
    if($query->num_rows() > 0) {
      return true;
    }else{
      return false;
    }
  }
  
  function username_exists($email) {
    $query = $this->fw->db->get_where('username', array('username' => $email));
    
    if($query->num_rows() > 0) {
      return true;
    }else{
      return false;
    }
  }
}

?>