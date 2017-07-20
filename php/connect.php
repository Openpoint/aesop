<?php
ini_set("log_errors", 1);
ini_set("error_log", $_SERVER["DOCUMENT_ROOT"]."/log/aesop.log");


include_once('set.php');
if($data->method === 'fileup'){
	error_log('connect.php');
	error_log(print_r($data,true));
}

if(isset($_COOKIE["auth"])){
	$cookie=json_decode($_COOKIE["auth"]);
}
if(isset($cookie->uid)){
	include('auth.php');
	$token=gettoken($cookie->uid);
	if($token == $cookie->authtoken){
		$authorised = true;
	}else{
		$authorised = false;
	}
}

$sandbox=$_SERVER["DOCUMENT_ROOT"].'/utils/';
$context=$_SERVER["DOCUMENT_ROOT"].'/static/resources/'; //create the base path for file uploads
$mtypes=['bvideo','fvideo','fimage','foverlay','oaudio','poster','timage'];

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

//Process file uploads

if($data->method === 'fileup'){

	global $dbh, $p_type, $p_sid, $p_pid, $p_chid, $vtemp, $context, $dir;

	$dir=$p_sid.'/'.$p_chid.'/'.$p_pid.'/'; //create the path for data store file uploads
	$cmd='mkdir -p '.$context.$p_type.'/'.$dir.';chmod -R 774 '.$context.$p_type.';mkdir -p '.$sandbox.$p_type.'/'.$dir.';chmod -R 774 '.$sandbox.$p_type;

	exec($cmd); //create the destination and sandbox directories

	//peg gets passed to <asyncmediaprocessor>.php in a BASH param
	$peg=(object)array(
		"type"=>$p_type,
		"sid"=>$p_sid,
		"pid"=>$p_pid,
		"chid"=>$p_chid
	);
	$peg=json_encode($peg);
	$p_vidi = (object) $data->vidi;

	error_log(print_r($_FILES,true));

	//return a error if there was a problem with the file upload
	if (isset($_FILES['file']) && $_FILES['file']['error'] > 0) {
		echo json_encode(makemess('error','There was an unknown error with the file upload. Please advise your system admin.'));
	} else {

		//continue processing the file
		if($p_type==='fvideo' || $p_type==='bvideo' || $p_type==='oaudio'){ //handle for videos and audio

			error_log(print_r($p_vidi,true));
			if(!$p_vidi->vurl){
				$vtemp=$_FILES['file'];
			}
			include('media/media.php');
			return;
		}
		if($p_type==='timage' || $p_type==='fimage' || $p_type==='foverlay'){ //handle for images

			$filename=$_FILES['file']['name'];
			$title=explode(".", $filename);
			array_pop($title);
			$title=implode('',$title);
			$title=title($title,true);
			$title=$dir.$title;


			$source=$sandbox.$p_type.'/'.$dir.$filename;
			if($p_type==='foverlay'){
				$target=$context.$p_type.'/'.$title.'.png';
			}else{
				$target=$context.$p_type.'/'.$title.'.jpg';
			}

			$upload=(move_uploaded_file($_FILES['file']['tmp_name'], $source));
			if($upload){
				$cmd='chmod 774 '.$source;
				exec($cmd);
				if($p_type==='timage'){
					$cmd='convert "'.$source.'" -resize 960 -quality 80 '.$target;

					exec($cmd);

				}
				if($p_type==='fimage' || $p_type==='foverlay'){
					$cmd='convert "'.$source.'" -ping -format %w info:';
					$width=exec($cmd);
					if($width > 1920){
						$cmd='convert "'.$source.'" -resize 1920 -quality 80 '.$target;
					}else{
						$cmd='convert -quality 80 "'.$source.'" '.$target;
					}
					$cmd=$cmd.';rm -r '.$sandbox.$p_type.'/'.$dir;
					exec($cmd);
				}

				if($p_type==='foverlay'){
					$sql="INSERT INTO resource (type,sid,pid,chid,location) VALUES ('".$p_type."',".$p_sid.",".$p_pid.",".$p_chid.",'".$title.".png')";
				}else if($p_type==='timage' && $p_pid==-2){
					$sql="UPDATE story SET location='".$title.".jpg' WHERE sid=".$p_sid;
				}else{
					$sql="INSERT INTO resource (type,sid,pid,chid,location) VALUES ('".$p_type."',".$p_sid.",".$p_pid.",".$p_chid.",'".$title.".jpg')";
				}

				commit($sql);
				echo json_encode(makemess('success','The image was saved'));
				return;
			}else{
				echo json_encode(makemess('error',print_r(error_get_last())));
			}
		}

	}
}

//delete a static resource
if($data->method === 'delres'){

	global $dbh, $p_type, $p_sid, $p_pid, $p_chid;

	if($p_type==='timage' && $p_pid==-2){
		$sql = "SELECT location FROM story WHERE sid = ".$p_sid;

	}else{
		$sql = "DELETE FROM resource WHERE type = '".$p_type."' AND sid = ".$p_sid." AND chid = ".$p_chid." AND pid = ".$p_pid." RETURNING *;";
	}
	echo $sql."\n";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		if($p_type==='bvideo' || $p_type==='fvideo'){
			foreach ($arr as $item){
				if($item['v_webm']!=='loading.webm' && $item['v_mp4']!=='loading.mp4'){
					$path = $context.$p_type.'/'.$item['v_webm'];
					unlink($path);
					$path = $context.$p_type.'/'.$item['v_mp4'];
					unlink($path);
				}
			}
			$sql = "DELETE FROM resource WHERE type = 'poster' AND sid = ".$p_sid." AND chid = ".$p_chid." AND pid = ".$p_pid." RETURNING location;";
			$result = pg_query($dbh, $sql);
			if ($result) {
				$arr = pg_fetch_all($result);
				foreach ($arr as $item){
					if($item['location']!=='loading.jpg'){
						$path = $context.'poster/'.$item['location'];
						unlink($path);
					}
				}
				delq();
				return;
			}
		}
		if($p_type==='fimage' || $p_type==='foverlay' || $p_type==='timage'){
			foreach ($arr as $item){
				$path = $context.$p_type.'/'.$item['location'];
				unlink($path);
			}
			if($p_type==='timage' && $p_pid==-2){
				$sql="UPDATE story SET location = NULL WHERE sid = ".$p_sid;
				commit($sql);
			}
		}
		if($p_type==='oaudio'){
			foreach ($arr as $item){
				if($item['a_mp3']!=='loading.mp3' && $item['a_ogg']!=='loading.ogg'){
					$path = $context.$p_type.'/'.$item['a_mp3'];
					unlink($path);
					$path = $context.$p_type.'/'.$item['a_ogg'];
					unlink($path);
					delq();
				}
			}
		}

	}
}
if($data->method === 'validate' && $authorised){
	echo('valid');
}
if($data->method === 'getsid'){

	global $p_title,$dbh;
	$sql = "SELECT sid FROM story WHERE title= '".$p_title."'";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		if($arr[0]['sid']){
			echo($arr[0]['sid']);
		}else{
			echo('noresult');
		}
	}
}
if($data->method === 'new_story'){

	global $p_title,$dbh;
	$sql = "INSERT INTO story (title) VALUES ('".$p_title."') RETURNING sid";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		$return = array(
			"result"=>pg_last_error($dbh),
		);
		echo json_encode($return);
	}else{
		$arr = pg_fetch_all($result);
		$sid=$arr[0]['sid'];
		$sql = "INSERT INTO chapter (sid,c_order,title) VALUES (".$sid.",0,'Chapter title') RETURNING chid";
		$result = pg_query($dbh, $sql);
		if (!$result) {
			$return = array(
				"result"=>pg_last_error($dbh),
			);
			echo json_encode($return);
		}else{
			$arr = pg_fetch_all($result);
			$chid=$arr[0]['chid'];
			$sql = "INSERT INTO page (sid,chid,p_order,title) VALUES (".$sid.",".$chid.",0,'Page title') RETURNING chid";
			$result = pg_query($dbh, $sql);
			if (!$result) {
				$return = array(
					"result"=>pg_last_error($dbh),
				);
				echo json_encode($return);
			}else{
				$return = array(
					"result"=>"success story",
					"title"=>$p_title,
					"sid"=>$sid
				);
				echo json_encode($return);
			}
		}
	}
}
if($data->method === 'new_chapter'){

	global $p_title,$p_sid,$dbh,$p_corder,$p_porder;
	$sql = "UPDATE chapter SET c_order=c_order + 1 WHERE sid=".$p_sid." AND c_order >= ".$p_corder."; INSERT INTO chapter (title,sid,c_order) VALUES ('".$p_title."',".$p_sid.",".$p_corder.") RETURNING chid";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		$return = array(
			"result"=>pg_last_error($dbh),
		);
		echo json_encode($return);
	}else{
		$arr = pg_fetch_all($result);
		$chid=$arr[0]['chid'];
		$sql = "INSERT INTO page (sid,chid,p_order,title) VALUES (".$p_sid.",".$chid.",0,'Page title')";
		$result = pg_query($dbh, $sql);
		if (!$result) {
			$return = array(
				"result"=>pg_last_error($dbh),
			);
			echo json_encode($return);
		}else{
			$return = array(
				"result"=>"success chapter",
				"chid"=>$chid,
				"c_order"=>$p_corder
			);
			echo json_encode($return);
		}
	}
}
if($data->method === 'new_page'){

	global $p_title,$p_sid,$dbh,$p_chid,$p_porder;
	$sql = "UPDATE page SET p_order=p_order + 1 WHERE sid=".$p_sid." AND chid=".$p_chid." AND p_order >= ".$p_porder."; INSERT INTO page (title,sid,p_order,chid) VALUES ('".$p_title."',".$p_sid.",".$p_porder.",".$p_chid.") RETURNING pid";

	$result = pg_query($dbh, $sql);
	if (!$result) {
		$return = array(
			"result"=>pg_last_error($dbh),
		);
		echo json_encode($return);
	}else{
		$arr = pg_fetch_all($result);
		$return = array(
			"result"=>"success page",
			"p_order"=>$p_porder,
			"pid"=>$arr[0]['pid']
		);
		echo json_encode($return);
	}
}

if($data->method === 'getall'){

	function return_bytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}

	global $p_sid,$dbh;
	$maxsize1 = ini_get('post_max_size');
	$maxsize2 = ini_get('upload_max_filesize');
	$all=array();
	if(return_bytes($maxsize1) > return_bytes($maxsize2)){
		$all['maxsize'] = $maxsize2;
	}else{
		$all['maxsize'] = $maxsize1;
	}

	$sql = "SELECT * FROM story WHERE sid = ".$p_sid;
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		$all['story']=$arr[0];
	}
	$sql = "SELECT * FROM chapter WHERE sid = ".$p_sid." ORDER BY c_order";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		$all['chapter']=$arr;
	}
	$sql = "SELECT * FROM page WHERE sid = ".$p_sid." ORDER BY p_order";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		$all['page']=$arr;
	}
	$sql = "SELECT * FROM resource WHERE sid = ".$p_sid;
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		$all['resource']=$arr;
	}
	echo json_encode($all);
}
if($data->method === 'getstories'){
	global $dbh;
	$sql = "SELECT * FROM story";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		echo json_encode($arr);
	}
}
if($data->method === 'delstory'){

	global $dbh, $p_sid;
	$sql = "
	DELETE FROM story WHERE sid = ".$p_sid.";
	DELETE FROM chapter WHERE sid = ".$p_sid.";
	DELETE FROM page WHERE sid = ".$p_sid.";
	DELETE FROM resource WHERE sid = ".$p_sid;

	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		foreach($mtypes as $type){
			$cmd=$cmd.'rm -r '.$context.$type.'/'.$p_sid.'/;';
		}
		exec($cmd);
		echo ('success');
	}
}

if($data->method === 'delchap'){

	global $dbh, $p_chid, $p_sid, $p_corder;
	$sql = "
	DELETE FROM chapter WHERE chid = ".$p_chid." AND sid = ".$p_sid.";
	DELETE FROM page WHERE chid = ".$p_chid." AND sid = ".$p_sid.";
	DELETE FROM resource WHERE chid = ".$p_chid." AND sid = ".$p_sid.";
	UPDATE chapter SET c_order = c_order - 1 WHERE sid=".$p_sid." AND c_order > ".$p_corder;

	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		foreach($mtypes as $type){
			$cmd=$cmd.'rm -r '.$context.$type.'/'.$p_sid.'/'.$p_chid.'/;';
		}
		exec($cmd);
		echo ('success');
	}
}
if($data->method === 'delpage'){


	global $dbh, $p_pid, $p_chid, $p_sid, $p_porder;
	$sql = "
	DELETE FROM page WHERE pid = ".$p_pid." AND chid = ".$p_chid." AND sid = ".$p_sid.";
	DELETE FROM resource WHERE pid = ".$p_pid." AND chid = ".$p_chid." AND sid = ".$p_sid.";
	UPDATE page SET p_order=p_order - 1 WHERE sid=".$p_sid." AND chid=".$p_chid." AND p_order > ".$p_porder;

	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		foreach($mtypes as $type){
			$cmd=$cmd.'rm -r '.$context.$type.'/'.$p_sid.'/'.$p_chid.'/'.$p_pid.'/;';
		}
		exec($cmd);
		echo ('success');
	}
}
if($data->method === 'edit'){

	global $p_type,$p_value,$p_chid,$p_pid,$p_porder,$p_corder,$p_story_name,$p_story_text,$p_chapter_title,$p_chapter_subtitle,$p_chapter_mentitle,$p_page_title,$p_page_text,$p_page_menshow,$dbh;
	if($p_type == 'story'){
		$sql = "UPDATE ".$p_type." SET title = '".$p_story_name."', text = '".$p_story_text."' WHERE sid = ".$p_sid;
	}
	if($p_type == 'chapter'){
		$sql = "UPDATE ".$p_type." SET title = '".$p_chapter_title."', subtitle = '".$p_chapter_subtitle."', mentitle = '".$p_chapter_mentitle."' WHERE sid =".$p_sid." AND chid =".$p_chid;
	}
	if($p_type == 'page'){
		$sql = "UPDATE ".$p_type." SET title = '".$p_page_title."', text = '".$p_page_text."', menushow = '".$p_page_menshow."' WHERE sid = ".$p_sid." AND chid = ".$p_chid." AND pid = ".$p_pid;
	}
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{

		$return = (object)array(
			"result"=>"success"
		);

		echo (json_encode($return));
	}
}
if($data->method === 'redit'){

	global $p_type,$p_element,$p_value,$p_chid,$p_pid,$p_porder,$p_corder,$dbh;

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
	}else{
		$return = array(
			"result"=>"success"
		);
		echo json_encode($return);
	}
}
if($data->method == 'order'){

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
			UPDATE ".$p_context." SET c_order = ".($p_position + $sum)." WHERE sid =".$p_sid." AND c_order = -2 RETURNING c_order,chid;
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
			UPDATE ".$p_context." SET p_order = ".($p_position + $sum)." WHERE sid =".$p_sid." AND chid=".$p_chid." AND p_order = -2 RETURNING chid,p_order,pid;
			";
		}
	}
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result)[0];

		if($p_context==='page'){
			$p_porder=$arr['p_order'];
			$p_pid=$arr['pid'];
		}else{
			$p_pid='undefined';
			$p_porder='undefined';
		}
		if($p_context==='chapter'){
			$p_corder=$arr['c_order'];
		}else{
			$p_corder='undefined';
		}
		$p_chid=$arr['chid'];
		$return=(object) array(
			'status'=>'success',
			'sid'=>$p_sid,
			'chid'=>$p_chid,
			'c_order'=>$p_corder,
			'pid'=>$p_pid,
			'p_order'=>$p_porder
		);
		echo json_encode($return);
	}
}
if($data->method === 'getqueue'){
	global $dbh,$p_uid;

	$sql = "SELECT type,sid,porder,corder,pid,chid,status,time,title,array_to_json(message) AS message FROM queue WHERE NOT (".$p_uid." = ANY (seen)) ORDER BY time ASC;";

	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		if($arr){
			echo json_encode($arr);
		}
	}
}
if($data->method === 'poller'){
	global $dbh,$p_uid;

	$sql = "SELECT COUNT (*) FROM queue WHERE status !='complete';";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		echo $arr[0]['count'];
	}
}
if($data->method === 'seenqueue'){
	global $p_uid;

	$sql = "UPDATE queue SET seen = seen || ".$p_uid."  WHERE NOT (".$p_uid." = ANY (seen)) AND status = 'complete';";
	commit($sql);
}
if($data->method === 'switcher'){
	global $p_type,$p_sid,$p_chid,$p_pid,$context;
	if($p_type==='bvideo'){
		$newtype='fvideo';
	}else{
		$newtype='bvideo';
	}
	$sql = "UPDATE resource SET type='".$newtype."' WHERE type='".$p_type."' AND sid=".$p_sid." AND chid=".$p_chid." AND pid=".$p_pid;
	commit($sql);
	$path='/'.$p_sid.'/'.$p_chid.'/'.$p_pid;
	$cmd='
	mkdir -p '.$context.$newtype.$path.';
	mv '.$context.$p_type.$path.'/* '.$context.$newtype.$path.';
	rm '.$context.$p_type.$path.';
	chmod -R 774 '.$context.$newtype;
	echo $cmd;
	exec($cmd);
}
if($data->method === 'users'){
	global $dbh;

	$sql = "SELECT username,role,id,verified FROM users ORDER BY username ASC;";
	$result = pg_query($dbh, $sql);
	if (!$result) {
		echo json_encode(pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		echo json_encode($arr);
	}
}
if($data->method === 'setpass'){
echo 'setpass';
}
pg_close($dbh);
?>
