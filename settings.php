<?php
$maxq=4; // The maximum jobs to run in parallel
$cores=exec('nproc')*4; //The amount of cores to give to each media processing job
$domainname=$_SERVER['HTTP_HOST'];
$domainname=explode('.',parse_url($domainname)['path']);
array_shift($domainname);
$domainname=implode(".",$domainname);

$db=(object) array(
	'name'=>'aesop',
	'host'=>'localhost',
	'user'=>'michaeladmin',
	'port'=>'5432',
	'pass'=>'Me1th0b0b'		
); 




$installed=true;

?>