<html>
<head>
<?php
   $modeString = shell_exec('cat /var/www/html/kiosk/mode.flg');
   $modeString = trim($modeString);
   if($modeString == "dash") {
     echo "<meta HTTP-EQUIV=\"refresh\" CONTENT=\"10\">";
   }
   else {
     echo "<meta HTTP-EQUIV=\"refresh\" CONTENT=\"60;url=piaware.php\">";
   }
?>
  <link rel="stylesheet" type="text/css" href="index-style.css">
</head>
<title>Kiosk at PiMon1.home</title>
<body>
<center><h1>Kiosk at PiMon1.home</h1></center>
<table class="clear" width="100%" border='0'>
<tr>
<td class="clear" width="27%">
  <table width="27%">
  <tr>
  <th>Useful Links</th>
  </tr>
  <td height="200px">
  <P><a href="http://pitre.home:8080/">PiAware Skyview [FlightAware]</a></P>
  <P><a href="/apache2-default.html">Apache Default Page</a></P>
  <P><a href="/phpInfo.php">PHP Settings Page</a></P>
  </td>
  <tr>
  <th>Driveway Camera</th>
  </tr>
  <tr>
<?php
   $imageFileName = shell_exec('cat /var/www/html/kiosk/camera0/filename.dat');
   $imageFileName = trim($imageFileName);
   $imageFileSpec = "/var/www/html/kiosk/camera0/" . $imageFileName;
   echo "<td style=\"text-align:center\"><p>" . date ("F d Y H:i:s.", filectime($imageFileSpec)) . "</p>";
   echo "<img src=\"/kiosk/camera0/" . $imageFileName . "\" width=\"450\" height=\"338\"/></td>";
?>
  </tr>
  </table>
</td>
<td width="73%">
<div class="table" align="right">
<table class="data" border = '0'>
<tr>
<th><p>Status</p></th>
<th><p>-RPi-</p></th>
<th><p>Model</p></th>
<th colspan="2"><p>Kernel</p></th>
<th colspan="2"><p>Temp.</p></th>
<th><p>Up Time</p></th>
<th><p>Updated</p></th>
<th colspan="3"><p>F/S Space</p></th>
</tr>

<?php
$output = shell_exec('/home/pi/bin/lsRPis');
#echo "<pre>$output</pre>";

$piArray = explode("\n", $output);

foreach ($piArray as $pi) {
  if(!empty($pi)) {

   echo "<tr class=\"data\">";
   # COL: up/down
   #  note: many of our RPi's have two interfaces,
   #    if any of them are up, mark the device UP
   #    else mark the device down if there is a down!
   $status = shell_exec('cat /var/status/' . $pi . '/up.dat');
   $imageName = 'ledOn.jpg';
   $status = trim($status);
   if(empty($status)) {
   	$status = shell_exec('cat /var/status/' . $pi . '/down.dat');
   	$status = trim($status);
        $imageName = 'ledOff.jpg';
   }
   #echo "<td  style=\"text-align:center\"><p>" . $status . "</p></td>";
   echo "<td  style=\"text-align:center\"><img src=\"images/" . $imageName . "\" width=\"52\" height=\"27\"/></td>";

   # COL: HOSTNAME
   echo "<td class=\"major\" style=\"text-align:center\"><P class=\"major\">" . $pi . "</P></td>";

   # COL: RPi Model
   $model = shell_exec('cat /var/status/' . $pi . '/model.txt');
   $model = trim($model);
   $note = shell_exec('cat /var/status/' . $pi . '/note.txt');
   $note = trim($note);
   $prefix = 'Raspberry ';
   if(substr($model, 0, strlen($prefix)) == $prefix) {
     $model = "R" . substr($model, strlen($prefix));
   }
   if(!empty($note)) {
     echo "<td><p>" . $model . "<br><span class=\"note\">(" . $note . ")</span></p></td>";
   }
   else {
     echo "<td><p>" . $model . "</p></td>";
   }

   # COL: Linux Version - Rls Name
   $build = shell_exec('cat /var/status/' . $pi . '/build.txt');
   $build = trim($build);
   $rlsName = shell_exec('cat /var/status/' . $pi . '/rls-name.txt');
   $rlsName = trim($rlsName);
   if(!empty($build)) {
     echo "<td><p>" . $build . "</p></td>";
     echo "<td><p>" . $rlsName . "</p></td>";
   }
   else {
     echo "<td>" . "</td>";
     echo "<td>" . "</td>";
   }

   # COL: System Temp. in C
   if($status == "DOWN") {
     echo "<td  style=\"text-align:center\"><p>" . "---" . "</p></td>";
     echo "<td  style=\"text-align:center\"><p>" . "---" . "</p></td>";
   }
   else {
     $temp = shell_exec('cat /var/status/' . $pi . '/systemp.txt');
     $prefix = 'temp=';
     if(substr($temp, 0, strlen($prefix)) == $prefix) {
       $temp = substr($temp, strlen($prefix));
     }
     $temp = trim($temp);
     if(empty($temp)) {
       echo "<td>" . "</td>";
       echo "<td>" . "</td>";
     }
     else {
       $tempDigits = substr($temp, 0, -2); # remove last two chars
       $tempValue = floatval($tempDigits);
       $imageName = 'ledGreenOff@3x.gif';
       if($tempValue > 80) {
         $imageName = 'ledRedOn@3x.gif';
       }
       else if($tempValue > 60) {
         $imageName = 'ledYellowOn@3x.gif';
       }
       echo "<td><p>" . $temp . "</p></td>";
       #echo "<td  style=\"text-align:center\"><p>" . $temp . " <img src=\"images/" . $imageName . "\" width=\"21\" height=\"21\"/></p></td>";
       echo "<td  style=\"text-align:center\"><p><img src=\"images/" . $imageName . "\" width=\"21\" height=\"21\"/></p></td>";
     }
   }

   # COL: Up Time
   if($status == "DOWN") {
     echo "<td  style=\"text-align:center\"><p>" . "---" . "</p></td>";
   }
   else {
     $uptime = shell_exec('cat /var/status/' . $pi . '/uptime.txt');
     $uptimeArray = explode(',', $uptime);
       $temp = trim($uptimeArray[0]);
       $uptimeUsefulArray = explode(' ', $temp);
       $tempDigits = substr($uptimeUsefulArray[0], 0, -3); # remove last three chars
       $uptimeUsefulArray[0] = $tempDigits;
       $uptimeShort = implode(' ', $uptimeUsefulArray);
     echo "<td><p>" . $uptimeShort . "</p></td>";
   }

   # COL: Date last updated
   $lastUpdated = shell_exec('cat /var/status/' . $pi . '/lastupd.txt');
  if(!empty($lastUpdated)) {
   $lastYMD = substr($lastUpdated, 0, 6);
   $parsedDate = date('dMy', strtotime('20' . $lastYMD));
   echo "<td><p>" . $parsedDate . "</p></td>";
  }
  else {
   echo "<td>" . "</td>";
  }

   # COL: file-system space
   $fsSpace = shell_exec('cat /var/status/' . $pi . '/df.txt | grep root');
   if(!empty($fsSpace)) {
     $fsSpaceAr = preg_split("/\s+/", $fsSpace);
     $kBlocks = intval($fsSpaceAr[1]);
     $mBlocks = $kBlocks / 1024;
     $gBlocks = $mBlocks / 1024;
     $gBlocksStr = number_format($gBlocks, 0);
     $sizeStr = "??";
     if($gBlocks > 32) {
       $sizeStr = "64";
     }
     else if($gBlocks > 16) {
       $sizeStr = "32";
     }
     else if($gBlocks > 8) {
       $sizeStr = "16";
     }
     else if($gBlocks > 4) {
       $sizeStr = "8";
     }
     else if($gBlocks > 2) {
       $sizeStr = "4";
     }
     else if($gBlocks > 1) {
       $sizeStr = "2";
     }
     #echo "<td><p>" . "32GB 30%" . "</p></td>";
     #echo "<td><p>" . sizeof($fsSpaceAr) . "</p></td>";
     #echo "<td><p>" . $fsSpaceAr[1] . " " . $fsSpaceAr[4] . "</p></td>";
     #echo "<td><p>" . $sizeStr . "GB " . $fsSpaceAr[4] . "</p></td>";
     $percentValue = substr($fsSpaceAr[4], 0, -1); # remove last char (%)
     $imageName = 'ledGreenOff@3x.gif';
     if($percentValue >= 90) {
       $imageName = 'ledRedOn@3x.gif';
     }
     else if($percentValue >= 70) {
       $imageName = 'ledYellowOn@3x.gif';
     }
     #echo "<td  style=\"text-align:center\"><p>" . $sizeStr . "GB " . $fsSpaceAr[4] . " <img src=\"images/" . $imageName . "\" width=\"24\" height=\"24\"/></p></td>";
     echo "<td  style=\"text-align:right\"><p>" . $sizeStr . "GB</p></td>";
     echo "<td  style=\"text-align:right\"><p>" . $fsSpaceAr[4] . "</p></td>";
     echo "<td  style=\"text-align:center\"><img src=\"images/" . $imageName . "\" width=\"24\" height=\"24\"/></td>";
   }
   else {
     echo "<td>" . "</td>";
     echo "<td>" . "</td>";
     echo "<td>" . "</td>";
   }

   echo "</tr>";
  }
}

?>
</table>
</td>
</tr>
</table>
</div>

</body>
</html>
