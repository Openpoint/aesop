<?php

include('set.php');
$cookie=json_decode($_COOKIE["auth"]);
if(isset($cookie->uid)){
	include('auth.php');
	$token=gettoken($cookie->uid);
	if($token == $cookie->authtoken){
		$authorised = true;
	}else{
		$authorised = false;
	}
}

if($data->method == 'fileup'){
	escape($data);
	global $dbh, $p_type, $p_sid, $p_pid, $p_chid, $p_subtype;
	if ($_FILES["myFile"]["error"] > 0) {
		echo "Error: " . $_FILES["myFile"]["error"];		
	} else {
		$location = null;
		$mp4 = null;
		$webm = null;
		$ogv= null;
		$filename = dupecheck($_FILES["myFile"]["name"],"../resources/".$p_subtype."/".$_FILES["myFile"]["name"]);
		if ($_FILES["myFile"]["type"]=='video/webm'){
			$webm = $filename;
		}else if(($p_type == 'fvideo' || $p_type == 'bvideo') && $_FILES["myFile"]["type"]=='video/ogg'){
			$ogv = $filename;
		}else if ($_FILES["myFile"]["type"]=='video/mp4'){
			$mp4 = $filename;
		}else if($_FILES["myFile"]["type"]=='audio/ogg' || $_FILES["myFile"]["type"]=='video/ogg'){
			$ogg = $filename;
		}else if($_FILES["myFile"]["type"]=='audio/mpeg'){
			$mp3 = $filename;
		}else{
			$location = $filename;
		}
		
		$upload=(move_uploaded_file($_FILES["myFile"]["tmp_name"], "../resources/".$p_subtype."/".$filename));
		if ($upload){
			if(!$p_pid && !$p_chid){
				$p_pid=-1;
				$p_chid=0;
			}else if(!$p_pid){
				$p_pid=0;
			}
			if($p_chid == -1){
				$sql = "UPDATE story SET location = '".$location."' WHERE sid = ".$p_sid.";
				INSERT INTO resource (type,location,sid,pid,chid,v_ogv,v_mp4,v_webm,a_ogg,a_mp3) VALUES ('".$p_subtype."','".$location."',".$p_sid.",".$p_pid.",".$p_chid.",'".$ogv."','".$mp4."','".$webm."','".$ogg."','".$mp3."')
				";				
			}else{
				$sql = "INSERT INTO resource (type,location,sid,pid,chid,v_ogv,v_mp4,v_webm,a_ogg,a_mp3) VALUES ('".$p_subtype."','".$location."',".$p_sid.",".$p_pid.",".$p_chid.",'".$ogv."','".$mp4."','".$webm."','".$ogg."','".$mp3."')";
			}
			$result = pg_query($dbh, $sql);
			if (!$result) {				
				echo json_encode(pg_last_error($dbh));
				pg_close($dbh);
			}else{
				$returned= array(
					'message'=>'saved',
					'type'=>$p_type,
					'subtype'=>$p_subtype,
					'chid'=>$p_chid,
					'pid'=>$p_pid,
					'name'=>$location,
					'v_ogv'=>$ogv,
					'v_mp4'=>$mp4,
					'v_webm'=>$webm,
					'a_ogg'=>$ogg,
					'a_mp3'=>$mp3
				);
				echo json_encode($returned);
			}
			
		}else{
			print_r(error_get_last());
		}
	}
	
}

//delete a static resource
if($data->method == 'delres'){
	escape($data);
	global $dbh, $p_name, $p_type, $p_sub, $p_pid, $p_chid;
	if($p_type=='fvideo' || $p_type=='bvideo' || $p_type=='oaudio'){
		$sql = "
		DELETE FROM resource WHERE ".$p_sub." = '".$p_name."' AND type = '".$p_type."' AND pid = ".$p_pid." RETURNING chid,pid;
		";
	}
	if($p_type=='fimage' || $p_type=='foverlay' || $p_type=='poster' || $p_type=='timage'){
		$sql = "
		DELETE FROM resource WHERE location = '".$p_name."' AND type = '".$p_type."' AND pid = ".$p_pid." RETURNING chid,pid;
		";	
	}
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		$arr = pg_fetch_all($result);
		if($arr){
			$path = '../resources/'.$p_type.'/'.$p_name; 
			unlink($path);
			$returned= array(
				'message'=>'deleted',
				'type'=>$p_type,
				'chid'=>$arr[0]['chid'],
				'pid'=>$arr[0]['pid'],
				'name'=>$p_name
			);
		}else{
			$returned= array(
				'message'=>'notdeleted',
				'type'=>$p_type,
				'chid'=>$p_chid,
				'pid'=>$p_pid,
				'name'=>$p_name	
			);		
		}

		echo json_encode($returned);
	}
}
if($data->method == 'validate' && $authorised){
	echo('valid');
}
if($data->method == 'getsid'){
	
	escape($data);
	global $p_title,$dbh;
	$sql = "SELECT sid FROM story WHERE title= '".$p_title."'";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		$arr = pg_fetch_all($result);
		if($arr[0]['sid']){
			echo($arr[0]['sid']);
		}else{
			echo('noresult');
		}
	}
}
if($data->method == 'new_story'){
	escape($data);
	global $p_title,$dbh;
	$sql = "INSERT INTO story (title) VALUES ('".$p_title."') returning sid";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		$return = array(
			"result"=>pg_last_error($dbh),
		);
		echo json_encode($return);
		pg_close($dbh);
	}else{
		$arr = pg_fetch_all($result);
		$sid=$arr[0]['sid'];
		$sql = "INSERT INTO chapter (sid,c_order,title) VALUES (".$sid.",0,'Please add a chapter title') returning chid";
		$result = pg_query($dbh, $sql);
		if (!$result) {
			$return = array(
				"result"=>pg_last_error($dbh),
			);
			echo json_encode($return);
			pg_close($dbh);
		}else{
			$arr = pg_fetch_all($result);
			$chid=$arr[0]['chid'];
			$sql = "INSERT INTO page (sid,chid,p_order,title) VALUES (".$sid.",".$chid.",0,'Please add a page title') returning chid";
			$result = pg_query($dbh, $sql);		
			if (!$result) {
				$return = array(
					"result"=>pg_last_error($dbh),
				);
				echo json_encode($return);
				pg_close($dbh);
			}else{	
				$return = array(
					"result"=>"success story",
					"title"=>$p_title,
				);
				echo json_encode($return);
				pg_close($dbh);
			}	
		}
	}
}
if($data->method == 'new_chapter'){
	escape($data);
	global $p_title,$p_sid,$dbh,$p_corder,$p_porder;
	$sql = "INSERT INTO chapter (title,sid,c_order) VALUES ('".$p_title."',".$p_sid.",".$p_corder.") returning chid";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		$return = array(
			"result"=>pg_last_error($dbh),
		);
		echo json_encode($return);
		pg_close($dbh);
	}else{
		$arr = pg_fetch_all($result);
		$chid=$arr[0]['chid'];
		$sql = "INSERT INTO page (sid,chid,p_order,title) VALUES (".$p_sid.",".$chid.",0,'Please add a page title') returning chid";
		$result = pg_query($dbh, $sql);		
		if (!$result) {
			$return = array(
				"result"=>pg_last_error($dbh),
			);
			echo json_encode($return);
			pg_close($dbh);
		}else{
			$return = array(
				"result"=>"success chapter",
			);
			echo json_encode($return);	
			pg_close($dbh);
		}	
	}	
}
if($data->method == 'new_page'){
	escape($data);
	global $p_title,$p_sid,$dbh,$p_chid,$p_porder;
	$sql = "INSERT INTO page (title,sid,p_order,chid) VALUES ('".$p_title."',".$p_sid.",".$p_porder.",".$p_chid.")";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		$return = array(
			"result"=>pg_last_error($dbh),
		);
		echo json_encode($return);
		pg_close($dbh);
	}else{
		$return = array(
			"result"=>"success page",
		);
		echo json_encode($return);	
		pg_close($dbh);
	}		
}
if($data->method == 'getall'){
	
	escape($data);
	global $p_sid,$dbh;
	$maxsize = ini_get('post_max_size');
	$all=array();
	$all['maxsize'] = $maxsize;
	$sql = "SELECT * FROM story WHERE sid = ".$p_sid;
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		$arr = pg_fetch_all($result);
		$all['story']=$arr[0];
	}
	$sql = "SELECT * FROM chapter WHERE sid = ".$p_sid." ORDER BY c_order";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		$arr = pg_fetch_all($result);
		$all['chapter']=$arr;
	}
	$sql = "SELECT * FROM page WHERE sid = ".$p_sid." ORDER BY p_order";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		$arr = pg_fetch_all($result);
		$all['page']=$arr;
	}
	$sql = "SELECT * FROM resource WHERE sid = ".$p_sid;
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		$arr = pg_fetch_all($result);
		$all['resource']=$arr;
	}
	echo json_encode($all);
	pg_close($dbh);
}
if($data->method == 'getstories'){
	global $dbh;
	$sql = "SELECT * FROM story";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		$arr = pg_fetch_all($result);
		echo json_encode($arr);
	}
}
if($data->method == 'delstory'){
	escape($data);
	global $dbh, $p_sid;
	$sql = "
	DELETE FROM story WHERE sid = ".$p_sid.";
	DELETE FROM chapter WHERE sid = ".$p_sid.";
	DELETE FROM page WHERE sid = ".$p_sid.";
	DELETE FROM resource WHERE sid = ".$p_sid;

	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		echo ('success');
	}
}

if($data->method == 'delchap'){
	escape($data);
	global $dbh, $p_chid, $p_sid;
	$sql = "
	DELETE FROM chapter WHERE chid = ".$p_chid." AND sid = ".$p_sid.";
	DELETE FROM page WHERE chid = ".$p_chid." AND sid = ".$p_sid.";
	DELETE FROM resource WHERE chid = ".$p_chid." AND sid = ".$p_sid;

	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		echo ('success');
	}
}
if($data->method == 'delpage'){
	escape($data);
	global $dbh, $p_pid, $p_chid, $p_sid;
	$sql = "
	DELETE FROM page WHERE pid = ".$p_pid." AND chid = ".$p_chid." AND sid = ".$p_sid.";
	DELETE FROM resource WHERE pid = ".$p_pid." AND chid = ".$p_chid." AND sid = ".$p_sid;

	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		echo ('success');
	}
}
if($data->method == 'edit'){
	
	escape($data);
	global $p_type,$p_element,$p_value,$p_chid,$p_pid,$p_porder,$p_corder,$dbh;
	if($p_type == 'story'){
		$sql = "UPDATE ".$p_type." SET ".$p_element." = '".$p_value."' WHERE sid = ".$p_sid;
	}
	if($p_type == 'chapter'){
		$sql = "UPDATE ".$p_type." SET ".$p_element." = '".$p_value."' WHERE sid =".$p_sid." AND chid =".$p_chid;
	}
	if($p_type == 'page'){
		$sql = "UPDATE ".$p_type." SET ".$p_element." = '".$p_value."' WHERE sid = ".$p_sid." AND chid = ".$p_chid." AND pid = ".$p_pid;
	}
	if($p_type == 'resource'){
		if($p_element != 'astop'){
			$sql = "UPDATE ".$p_type." SET ".$p_element." = '".$p_value."' WHERE sid = ".$p_sid." AND chid = ".$p_chid." AND pid = ".$p_pid;
		}else if($p_value == 't'){
			$sql = "INSERT INTO resource (type,sid,pid,chid,astop) VALUES ('astop',".$p_sid.",".$p_pid.",".$p_chid.",'".$p_value."')";
		}else{
			$sql = "DELETE FROM resource WHERE pid = ".$p_pid." AND chid = ".$p_chid." AND sid = ".$p_sid." AND type = 'astop'";
		}
	}
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		$return = array(
			"result"=>"success",
			"title"=>$p_value,
			"type"=>$p_type,
			"element"=>$p_element,
			"sql"=>$sql,
			"rows"=>$result
		);
		echo json_encode($return);
		pg_close($dbh);
	}
}
if($data->method == 'order'){
	
	escape($data);
	global $p_direction,$p_position,$p_context,$p_sid,$p_chid,$p_pid,$p_swap,$dbh;
	if ($p_direction == 'up'){
		$sum = -1;
	}else{
		$sum = 1;
	}

	if($p_context == 'chapter'){
		if(!$p_swap){
			$sql = "UPDATE ".$p_context." SET c_order = c_order + ".$sum." WHERE sid =".$p_sid." AND c_order > ".$p_position.";";
		}else{

			$sql = "
			UPDATE ".$p_context." SET c_order = -2 WHERE sid =".$p_sid." AND c_order = ".$p_position.";
			UPDATE ".$p_context." SET c_order = c_order - ".$sum." WHERE sid =".$p_sid." AND c_order = ".($p_position + $sum).";
			UPDATE ".$p_context." SET c_order = ".($p_position + $sum)." WHERE sid =".$p_sid." AND c_order = -2;
			";
		}
	}
	if($p_context == 'page'){
		if(!$p_swap){
			$sql = "UPDATE ".$p_context." SET p_order = p_order + ".$sum." WHERE sid =".$p_sid." AND chid=".$p_chid." AND p_order > ".$p_position.";";
		}else{
			$sql = "
			UPDATE ".$p_context." SET p_order = -2 WHERE sid =".$p_sid." AND chid=".$p_chid." AND p_order = ".$p_position.";
			UPDATE ".$p_context." SET p_order = p_order - ".$sum." WHERE sid =".$p_sid." AND chid=".$p_chid." AND p_order = ".($p_position + $sum).";	
			UPDATE ".$p_context." SET p_order = ".($p_position + $sum)." WHERE sid =".$p_sid." AND chid=".$p_chid." AND p_order = -2;		
			";			
		}
	}
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
		pg_close($dbh);
	}else{
		echo('success');
	}
}
?>
