<?php
if(!THECLOUD_SYSTEM) exit;

class string {
    function RandomCode($size = 40) {
    $chars = array('a', 'b', 'c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','v','z','0','1','2','3',
'4','5','6','7','8','9','0');

    $r = rand();
    $string = "";
    for($i = 0; $i < $size; $i++)
    {
    $string .= $chars[rand(0,(count($chars)-1))];
    }
    return $string;
  }
    
    function protect_xss($string) {
     return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
  }
}
