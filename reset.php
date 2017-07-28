<?php
/*
Copyright 2017 Michael Jonker (http://openpoint.ie)

This file is part of Aesop.

Aesop is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Aesop is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Aesop.  If not, see <http://www.gnu.org/licenses/>.
*/
$root = dirname(__FILE__);

ini_set("log_errors", 1);
ini_set("error_log",$root."/log/reset_error.log");

include_once('php/set.php');


function undo($sid){
	global $dbh,$mtypes,$root;
	$sql = "
	DELETE FROM queue;
	DELETE FROM story WHERE sid = ".$sid.";
	DELETE FROM chapter WHERE sid = ".$sid.";
	DELETE FROM page WHERE sid = ".$sid.";
	DELETE FROM resource WHERE sid = ".$sid;

	pg_query($dbh, $sql);

	
	foreach($mtypes as $type){
		$path = $root.'/app/static/resources/'.$type.'/'.$sid;
		if(file_exists ($path)){
			$cmd = 'rm -r '.$path;
			exec($cmd);
		}
	}
}
exec('pkill avconv');
$files = array_diff(scandir($root.'/utils/vidlib'),array('..', '.','.gitignore'));
if(count($files)){
	$cmd = 'rm ';
	foreach($files as $file){
		$cmd = $cmd.$root.'/utils/vidlib/'.$file.' ';
	}
	exec($cmd);	
}


$sql = "SELECT * FROM story";
$result = pg_query($dbh, $sql);
$arr = pg_fetch_all($result);
foreach ($arr as $key => $value){
	if($arr[$key]['owner']!= 1){
		undo($arr[$key]['sid']);
	}
}
?>
