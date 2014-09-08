<?php
if(file_get_contents("php://input")){
	$data = json_decode(file_get_contents("php://input"));
	
}else{
	$data = new stdClass();
	$data2 = $_POST;
	//$data2 = $_GET;
	foreach($data2 as $key => $value){
		$data->$key = $value;
	}
}
//$data->username='michaeladmin';
//$data->email='michael@piquant.ie';
//$data->password='Me1th0b0b';


$conn_string = "host=localhost port=5432 dbname=story user=website password=WebsiteatPostgres";
$dbh = pg_connect($conn_string);
if (!$dbh) {
	die("Error in connection: " . pg_last_error($dbh));
}else{
	//echo('including');
	//include("install/install.php");
}

function escape($data_batch){
	foreach($data_batch as $key => $value){
		global ${'p_'.$key};
		${'p_'.$key}=pg_escape_string($value);
	}
}

function dupecheck($filename,$filelocation){
	$path = explode("/",$filelocation);
	unset($path[count($path)-1]);
	$path = implode("/", $path)."/";
	function subcheck($checkfile,$checklocation,$i,$path,$origfile){
		if(file_exists ($checklocation)){
			$checkfile=explode(".",$origfile);
			$newfile = $checkfile[0].'('.$i.').'.$checkfile[count($checkfile)-1];
			$i++;			
			return subcheck($newfile,$path.$newfile,$i,$path,$origfile);
		}else{
			return $checkfile;
		}
	}
	return subcheck($filename,$filelocation,1,$path,$filename);
}
?>
