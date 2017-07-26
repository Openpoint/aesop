<?php

ini_set("log_errors", 1);
ini_set("error_log","../log/aesop.log");

if (!isset($_SESSION)) {
    session_start();
}

$headers = apache_request_headers();

if($headers['CsrfToken'] === $_SESSION['token']){
	include_once('../php/auth.php');
}else{
	echo 'Go away you dirty scoundrel!';
}

?>
