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

$mtypes=['bvideo','fvideo','fimage','foverlay','oaudio','poster','timage'];
if(isset($_COOKIE['user'])){
	$User = $_COOKIE['user'];
	$User = json_decode($User);
}else{
	$User = false;
}

require(dirname(__FILE__).'/../settings.php');
if(file_get_contents("php://input")){
	$data = json_decode(file_get_contents("php://input"));
}else{
	$data = new stdClass();
	$data2 = $_POST;
	$data->method=null;
	foreach($data2 as $key => $value){
		$data->$key = $value;
	}
}


if(isset($data)){
	escape($data);
}else{
	$data=(object) array(
		'method'=>null
	);
}


$conn_string = "host=".$db->host." port=".$db->port." dbname=".$db->name." user=".$db->user." password=".$db->pass;
$dbh = pg_connect($conn_string);
if (!$dbh) {
	die("Error in connection: " . pg_last_error($dbh));
}

$sql="SELECT pname FROM settings";
$result = pg_query($dbh, $sql);
$arr = pg_fetch_all($result);
$sitename=$arr[0]['pname'];
function earray($array){
	foreach($array as $key=>$value){
		if(is_array($value)){
			$array[$key] = earray($value);
		}else{
			$array[$key] = pg_escape_string($value);
			//$array[$key] = 'test';
		}
	}
	return $array;
}
function escape($data_batch){
	foreach($data_batch as $key => $value){

		global ${'p_'.$key};
		if(is_array($value)){
			${'p_'.$key} = earray($value);;
		}else{
			${'p_'.$key} = pg_escape_string($value);
		}
		if($value !== 'poller'){
			//error_log(print_r(${'p_'.$key},true));
		}
	}
}

function commit($sql){
	global $dbh;
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}
}

function errlog($message){
	$log = dirname(__FILE__)."/../utils/error.log";
	$fh = fopen($log, 'a') or die("can't open file");
	fwrite($fh, $message."\n");
	fclose($fh);
}

//helper to construct a singleton return message
function makemess($type,$mess){
	$message=array();
	$message[0]=(object) array(
		'class'=>$type,
		'message'=>$mess
	);
	return $message;
}
//Queue processing
function queue($cmd,$mess,$p_sid,$p_chid,$p_pid,$p_porder,$p_corder,$p_type,$title){

	global $dbh;

	//submit the command to queue
	$sql="INSERT INTO queue (time,command,status,sid,chid,pid,porder,corder,type,title) VALUES (current_timestamp,".pg_escape_literal($cmd).",'queued',".$p_sid.",".$p_chid.",".$p_pid.",".$p_porder.",".$p_corder.",'".$p_type."','".$title."')";

	commit($sql);
	qprocess();
}

function qprocess(){

	global $dbh,$cores,$maxq;

	$running=array();
	$queued=array();

	$sql="SELECT * FROM queue ORDER BY time ASC";
	$result = pg_query($dbh, $sql);
	$arr = pg_fetch_all($result);

	if(count($arr) > 0){

		foreach($arr as $item){

			if($item['status']==='queued'){
				array_push($queued,$item);
			}else if($item['status']!=='complete'){
				array_push($running,$item);
			}
		}
		$slots=$maxq - count($running);
		if(count($queued) > 0 && $slots > 0){
			foreach($queued as $item){
				if($slots > 0){
					$slots--;
					$cmd=$item['command'];
					exec($cmd,$prid);
					$sql="UPDATE queue SET  prid=".$prid[0].", status='running' WHERE qid=".$item['qid']*1;
					commit($sql);
				}else{
					break;
				}
			}

		}
	}
	pg_close($dbh);
}
//delete an item from the queue
function delq(){
	global $p_type,$p_sid,$p_chid,$p_pid;
	$sql="DELETE FROM queue WHERE type='".$p_type."' AND  sid = ".$p_sid." AND chid = ".$p_chid." AND pid = ".$p_pid;
	//echo "\n".$sql."\n";
	commit($sql);
}

//helper to sanitize titles
function title($tit,$notime){
	global $starttime, $endtime;
	$title = str_replace(' ','-',$tit);
	$title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
	if(!$notime){
		return $title.'_'.$starttime.'_'.$endtime;
	}else{
		return $title;
	}
}
?>
