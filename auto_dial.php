#!/usr/bin/php -q
<?php
require 'phpagi-asmanager.php';
error_reporting (E_ALL);

//connect to DB
$hostname = '192.168.50.2' ;
$username = 'voipdb' ;
$password = 'Voip123456789' ;
$dbname = 'essentialmode' ;
$conn = new mysqli($hostname,$username,$password,$dbname);
$conn->set_charset("utf8");
$asm = new AGI_AsteriskManager();
//set time zone to local
date_default_timezone_set('Asia/Tehran');

//set parameters for routing success calls
$context = 'from-internal';
$exten = 'paradiserp';
$priority = '1';
$trunk = 'SIP/Asiatech-gatway';

//set number of concurrent calls
$number_of_concurrent_calls = '4';



while(true){

    $conn = new mysqli($hostname,$username,$password,$dbname);
	$conn->set_charset("utf8");
	

//fetch numbers for call
$query = "SELECT *  FROM register_verification WHERE  `status`='0' AND `method`='call' LIMIT $number_of_concurrent_calls";
$results = $conn->query($query);
$rawnum = mysqli_num_rows($results);

echo "Finde ". $rawnum ." number for call \r\n";



 if($rawnum>0){

if(!($asm->connect('127.0.0.1','admin','masihadmin9131569602','off'))){
	echo "can not connect to ami";
	$conn->close();
	exit;
}

//originate
while(($result = $results->fetch_assoc())){
	echo "calling to :". $number ." number for call \r\n";
	$id = $result['id'];
    $number = $result['number'];
	//$datetime = date("Y-m-d H:i:s");
	$call = $asm->send_request('Originate',
		array('Channel'=>"$trunk/$number",
			  'Context'=>"$context",
			  'priority'=>"$priority",
			  'exten'=>"$exten",
			  //'timeout'=>"30000",
			  'Async'=>'yes',
			  'ActionID'=>'voiping_auto_dial_'."$id",
			  'callerid'=>"$number"));
	//update data in DB
	$query2 = "UPDATE register_verification SET `status`='5' WHERE  `id` = '$id' ";
	$results2 = $conn->query($query2);
}
$asm->disconnect();

 }

$conn->close();
sleep(5);
}


exit();

?>