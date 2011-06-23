<?php
if(!THECLOUD_SYSTEM) exit;

class static_content extends Controller {
  function __construct() {
    parent::__construct();
  }
  
  function index() {
    
    $this->response->redirect('/promo');
  }
  
  function promo() {
    $this->response->view('static_content/promo');
  }
  
  function users_index() {
    $data = array();
    //echo $this->db->sql_query('SELECT * FROM users')->num_rows();
    $content = $this->response->view('static_content/index', $data, true);
    
    $data['content'] = $content;
    
    $this->response->view('base', $data);
  }
}
