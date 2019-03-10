<html>
<head>
<?php
   $modeString = shell_exec('cat /var/www/html/kiosk/mode.flg');
   $modeString = trim($modeString);
   if($modeString == "home") {
     echo "<meta HTTP-EQUIV=\"refresh\" CONTENT=\"120\">";
   }
   else {
     echo "<meta HTTP-EQUIV=\"refresh\" CONTENT=\"120;url=index.php\">";
   }
?>
  <link rel="stylesheet" type="text/css" href="index-style.css">
</head>
<title>Home Assistant at PiHome.home</title>
<body>
<div>
 <object type="text/html" data="http://piassist.home:8123" width="100%" height="100%"></object>
</div>

</body>
