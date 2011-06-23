<?php
if(!THECLOUD_SYSTEM) exit;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset=utf-8 />
</head>
<body>
  <!-- body -->
  <div id="body">
    <div id="content">
    <?if(isset($content)):?>
    <?=$content?>
    <?endif?>
    </div>
    
    <div id="sidebar_outer">
      <div id="sidebar">
      <?if(isset($sidebar)):?>
      <?=$sidebar?>
      <?endif?>
      </div>
    </div>
    
    <div class="clear"></div>
  </div>
  <!-- /body -->
  
  <?if(isset($javascript)):?>
  <?=$javascript?>
  <?endif?>
</body>
</html>
