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

//set time zone to local
date_default_timezone_set('Asia/Tehran');

//connect to asterisk ami
$asm = new AGI_AsteriskManager();
if(!($asm->connect('127.0.0.1','admin','masihadmin9131569602'))){
	echo "can not connect to ami";
	$conn->close();
	exit;
}





//add OrginateResponse Event handler
$asm->add_event_handler("OriginateResponse", "callback");

//callback function
function callback($event,$data)
{
	$status = $data['Response'];
	$reason = $data['Reason'];
	$id = explode('_', $data['ActionID']);
	$id = $id[3];
	switch($reason){
//answered
		case '4':
		$call_result = '5';
		break;
		default:
		$call_result = "-1";
	}

        	//update data in DB
        $query = "UPDATE register_verification SET  `status`=$call_result  WHERE  `id`=$id ";
	$results = $GLOBALS['conn']->query($query);


}

//waite until get response
$asm->wait_response();
$conn->close();
$asm->disconnect();
exit();

?>