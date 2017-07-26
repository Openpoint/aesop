<?php

ini_set("log_errors", 1);
ini_set("error_log","../log/aesop.log");


$data = json_decode(file_get_contents("php://input"));

if($data->secret === 'aesopsupersecret'){
	include_once('../php/auth.php');
}else{
	echo 'Go away you dirty scoundrel';
}


?>
