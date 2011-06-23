<?php
if(!THECLOUD_SYSTEM) exit;


session_start();

class Framework {
  var $config;
  
  function __construct($framework_path, $application_path, $debug=false) {
    global $application_start_time;
    
    $this->config = array();
    
    $this->debug = $debug;
    $this->finished = false;
    
    $this->load = new Loader($this);
    
    
    $this->config = new Config();
    
    //libraries
    $this->libraries = new Libraries();
    
    //Request
    $this->request = new Request();
    
    //Response
    $this->response = new Response($this);
    
    //load settings
    $this->config->set('framework_path', $framework_path);
    $this->config->set('application_path', $application_path);
    
    require_once($this->config->get('framework_path').'settings.php');
    
    //load core files
    require_once($this->config->get('framework_path').'core/controller.php');
    
    if(isset($settings)) {
      foreach($settings as $setting => $value) {
        $this->config->set($setting, $value);
      }
      $settings = array();
    }
    
    $this->uri = new Uri($this);
    $this->libraries->add('load');
    $this->libraries->add('uri');
    $this->libraries->add('config');
    $this->libraries->add('response');
    $this->libraries->add('request');
    
    $this->load->library('performance');
    $this->performance->app->set_starttime($application_start_time);
  }

  function end_app() {
    $this->finished = true;
    //Runs at the end of the web page execution
    $this->performance->app->set_endtime($this->performance->get_time());
    
    if($this->debug) {
      echo "Application: ".$this->performance->app->get_time()." s<br />".
      "DB: ".$this->performance->db->get_time()." s";
      
      if(is_array($this->db->queries)) {
        echo "<br />";
        
        foreach($this->db->queries as $query) {
          echo $query[0]." ". $query[1]." s<br />";
        }
      }
    }
  }
}

class Libraries {
  var $lib;
  function __construct() {
    $this->lib = array();
  }
  
  function add($name) {
    $this->lib[]=$name;
  }
  
  function list_libraries() {
    
    return $this->lib;
  }
}

class Config {
  var $settings;
  function __construct() {
    $this->settings=array();
  }
  
  function set($name, $value) {
    $this->settings[$name] = $value;
  }
  
  function get($name) {
    if(isset($this->settings[$name])) {
      return $this->settings[$name];
    }
  }
}


class Loader {
  var $fw;
  
  function __construct(&$fw) {
   $this->fw = $fw; 
  }
  
  function controller() {
    //Load controller
    
    $name = $this->fw->request->controller;
    $method = $this->fw->request->method;
    
    require_once($this->fw->config->get('application_path')."controllers/".$name.".php");
    
    $this->fw->controller = new $name();
    $this->fw->controller->$method();
  }
  
  function library($name) {
    require_once($this->fw->config->get('framework_path')."library/".$name.".php");
    
    $this->fw->$name = new $name($this->fw);
    
    $this->fw->libraries->add($name);
  }
}

class Uri {
  var $fw;
  var $routes;
  var $uri;
  var $segments;
  
  function __construct(&$fw) {
    $this->fw = $fw;
    
    $this->segments = array();
    $this->routes = array();
    
    //Get uri
    $this->uri = substr(strstr($_SERVER["REQUEST_URI"],'/'),1);
    
    //Remove query string
    if(strstr($_SERVER["REQUEST_URI"],'?')) {
    $this->uri = strstr($this->uri,'?', true);
    }
    
    //Remove trailing slash
    if($this->uri{strlen($this->uri)-1} == "/") {
      $this->uri = substr($this->uri, 0, -1);
    }
    
    $this->segments = explode("/",$this->uri);
    
    
  }
  
  function get_uri() {
    return $this->uri;
  }
  
  function set_routes($array) {
    $this->routes = $array;
    
    $this->find_controller();
  }
  
  private function find_controller() {
    $found = false;
    
    foreach($this->routes as $key => $value) {
      $key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));
      
      if(preg_match('#^'.$key.'$#', $this->uri)) {
        $x = explode('/', $value);
        
        if(count($x) != 2) {
          $this->fw->response->redirect();
        }
        
        $this->fw->request->set_controller($x[0]);
        $this->fw->request->set_method($x[1]);
        
        $found = true;
        
        $this->fw->load->controller();
      }
    }
    
    if(!$found) {
      $this->fw->response->redirect();
    }
  }
}

class Response {
  var $fw;
  var $variables;
  
  function __construct(&$fw) {
        global $framework;
    $this->fw = &$framework;
    
    $this->variables = array();
  }
  
  function redirect($path=null) {
    if($path == null) {
      $path = "/";
    }
    
    header('Location: ' . $path) ;
    
    $this->fw->end_app();
    exit;
  }
  
  function add_variables($variables) {
    $this->variables = array_merge($this->variables, $variables);
  }
  
  function clean($a) {
    if(is_array($a)) {
      return array_map(array('self','clean'), $a);
    }else{
      return $this->fw->string->protect_xss($a);
    }
  }
  
  //$vars: YOU MAUST PASS AN ARRAY! OBJECTS are not allowed.
  function view($template, $vars = null, $return=false) {
    //CLEAN VARIABLES!!!!!
    
    if(is_array($vars)) {
      $this->variables = array_merge($this->variables, $vars);
    }
    
    $vars = $this->clean($this->variables);

    //Map variables
    if(is_array($vars)) {
      foreach($vars as $key => $value) {
        if($key != "this" && $key != '$_GLOBALS') {
          $$key = $value;
        }
      }
    }
    
    if($return) {
      ob_start();
      
      include($this->fw->config->get('application_path')."views/".$template.".php");
      
      $r = ob_get_clean();
      
      return $r;//$r;
      
    }else{
      include($this->fw->config->get('application_path')."views/".$template.".php");
      
      $this->fw->end_app();
    }
  }
}

class Request {
  var $controller;
  var $method;
  
  function set_controller($string) {
    $this->controller = $string;
  }
  
  function set_method($string) {
    $this->method = $string;
  }
}
