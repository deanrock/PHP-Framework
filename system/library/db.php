<?php
if(!THECLOUD_SYSTEM) exit;

class db {
  var $fw;
  var $mysqli;
  
  function __construct(&$fw) {
    $this->fw = $fw;
    
    $this->connect();
    
    $this->queries = array();
  }
  
  function e() {echo"ss";}
  
  /* Private functions */
  private function connect() {
    if($this->fw->performance) {
      $s_t = $this->fw->performance->get_time();
    }
    
  
    $this->mysqli = new mysqli(
                        $this->fw->config->get('mysql_host'),
                        $this->fw->config->get('mysql_user'),
                        $this->fw->config->get('mysql_password'),
                        $this->fw->config->get('mysql_database')
                        );
    
    if($this->mysqli->connect_error) {
      die('Database error');
    }
    
    $this->mysqli->set_charset('utf8');
    
    if($this->fw->performance) {
      $e_t = $this->fw->performance->get_time();
      
      $this->fw->performance->db->difference($s_t, $e_t);
    }
  }
  
  private function protect($string) {
      $string = mysql_escape_string($string);
      return $string;
    }
  
  private function execute($query) {
    
    if($this->fw->performance) {
      $s_t = $this->fw->performance->get_time();
    }
    
    $r = $this->mysqli->query($query);
    
    if($this->fw->performance) {
      $e_t = $this->fw->performance->get_time();
      
      $this->fw->performance->db->difference($s_t, $e_t);
    }
    
    if($this->fw->debug) {
      $this->queries[] = array($query, ($e_t-$s_t));
      
      if(!$r) {
        echo "DB error: ".$this->mysqli->error."<br />";
      }
    }
    
    return $r;
  }
  
  /* Public functions */
  
  function query($query, $variables=array()) {
    //$clean_variables = array();
    
    if(count($variables) > 0) {
      foreach($variables as $key => $value) {
        //$clean_variables[$this->protect($key)] = $this->protect($value);
        
        $query = str_replace("@".$this->protect($key), "'".$this->protect($value)."'", $query);
      }
      
      $variables = null;
    }

    
    
    //execute sql
    $r = $this->execute($query);
    
    
    
    
    return new QueryResult($r);
  }
  
  function select() {
    return new Query($this);
  }
  
  function insert($table, $columns = array()) {
    
    
  }
  
  function insert_id() {
    return $this->mysqli->insert_id();
  }
  
  function get_where($table, $where = array()) {
    
  }
}

class Query {

}

class QueryResult {
  var $r;
  
  function __construct(&$r) {
    $this->r = $r;
    
    if($this->r) {
      return true;
    }else{
      return false;
    }
  }
  
  function row() {
    return $this->r->fetch_object();
  }
  
  function num_rows() {
    if(!$this->r) {
      return 0;
    }
    
    return $this->r->num_rows();
  }
  
  function close() {
    $this->r->close();
  }
}
