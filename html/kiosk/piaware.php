<html>
<head>
<?php
   $modeString = shell_exec('cat /var/www/html/kiosk/mode.flg');
   $modeString = trim($modeString);
   if($modeString == "flight") {
     echo "<meta HTTP-EQUIV=\"refresh\" CONTENT=\"120\">";
   }
   else {
     echo "<meta HTTP-EQUIV=\"refresh\" CONTENT=\"120;url=index.php\">";
   }
?>
  <link rel="stylesheet" type="text/css" href="index-style.css">
</head>
<title>piAware at PiTre.home</title>
<body>
<div>
 <object type="text/html" data="http://pitre.home:8080" width="100%" height="100%"></object>
</div>

</body>
