<?php

/* PHP Framework
 * Dejan Levec (c) 2011
 */

/*
 * FRAMEWORK REQUIREMENTS:
 * - PHP >= 5.3
 * - MySql >= 5.0.7
 */
 
//performance
$application_start_time = microtime(time); //'get as float' as of PHP 5
 
if(isset($_GET['a'])) {
error_reporting(E_ALL);
ini_set('display_errors', 1);
}

define('THECLOUD_SYSTEM', 'true');


//load framework

$framework_path = '/web/system/';
$application_path = '/web/application/';
 
include($framework_path.'core/framework.php');
 
 if(isset($_GET['a'])) {
 $framework = new Framework($framework_path, $application_path, true);
}else{
$framework = new Framework($framework_path, $application_path);
}
//load libraries
$framework->load->library('input');
$framework->load->library('db');
$framework->load->library('string');
$framework->load->library('csrf');



//routing
$routes = array();

$routes[''] = 'static_content/index';

$routes['promo'] = 'static_content/promo';

$routes['test'] = 'static_content/test';

$framework->uri->set_routes($routes);

if(!$framework->finished) {
  $framework->end_app();
}
