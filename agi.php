#!/usr/bin/php -q
<?php

include_once("phpagi.php");
error_reporting(E_ALL);
$con=mysqli_connect("192.168.50.2","voipdb","Voip123456789","essentialmode");
 
 // Check connection
 if (mysqli_connect_errno($con))
   {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }


$agi = new AGI();
$agi->answer();
// get caller id
$data = $agi->get_variable("CDR(src)");
$callerid = $data["data"];
//play cod taid


$agi->stream_file("paradiserp/cod-taid-shoma");
// get data from database
$result = mysqli_query($con,"SELECT * FROM register_verification WHERE number=$callerid AND `status`='5' AND `method`='call'");
$row = mysqli_fetch_array($result);
$id =$row['id'];
mysqli_query($con,"UPDATE register_verification SET `status`='1' WHERE  `id`=$id");
mysqli_close($con);



//fatch data base query



//play code number
$agi->say_digits($row['code']);
$agi->stream_file("paradiserp/Tekrar-mishavad");
$agi->say_digits($row['code']);



//play final sound
$agi->stream_file("paradiserp/ba-tashakor");

$agi->hangup();

?>