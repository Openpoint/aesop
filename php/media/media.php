<?php
ini_set("log_errors", 1);
ini_set("error_log", "../log/aesop.log");

//create the poster directory
if($p_type==='bvideo' || $p_type==='fvideo'){
	$cmd='mkdir -p '.$context.'poster/'.$dir;
	error_log($cmd);
	exec($cmd);
}


function setfile(){
	global $dbh, $title, $ext, $sandbox, $context, $starttime, $endtime, $p_vidi, $vtemp, $p_type, $p_sid, $p_chid, $p_porder, $p_corder, $p_pid, $peg, $dir;
	$starttime=($p_vidi->start['h']*60*60)+($p_vidi->start['m']*60)+($p_vidi->start['s']);
	$endtime=($p_vidi->end['h']*60*60)+($p_vidi->end['m']*60)+($p_vidi->end['s']);

	if(isset($p_vidi->vurl) && $p_vidi->vurl!=='null'){
		viddl();
	}else if(isset($vtemp)){
		//process uploaded media
		$type=explode("/", $vtemp['type']);

		if($type[0]==='video'||$type[0]==='audio'){
			$title=explode(".", $vtemp['name']);
			$ext = array_pop($title);
			$title=implode('',$title);
			$ttitle=title($title,true);
			$title=$dir.title($title,false);
			$orig=$sandbox.'vidlib/'.$ttitle.'.'.$ext;

			// need to design client side solution to not upload file again, but use library instead
			if(!file_exists ($orig)){
				move_uploaded_file($vtemp['tmp_name'], $orig);
			}


			//echo $orig."\n";
			//echo $target;
			//return;
			$cmd='nohup php '.$_SERVER["DOCUMENT_ROOT"].'/../php/media/media2.php --peg \''.$peg.'\' --url false --command "vidprocess" --title "'.$title.'" --ttitle "'.$ttitle.'" --ext "'.$ext.'" --context "'.$context.'" --sandbox "'.$sandbox.'" --starttime '.$starttime.' --endtime '.$endtime.' > /tmp/console.log 2>&1 & echo $!';

			loading();
			//exec($cmd);
			queue($cmd,null,$p_sid,$p_chid,$p_pid,$p_porder,$p_corder,$p_type,$title);

		}else{
			echo json_encode(makemess('warning','The file is not of a supported type. Please try again'));
			return;
		}
	}
}

//download media from the web
function viddl(){
	global $title, $ext, $sandbox, $p_vidi,$starttime, $endtime, $peg, $context, $p_type, $p_sid, $p_chid, $p_porder, $p_corder, $p_pid, $dir;
	$url=$p_vidi->vurl;
	$cmd='nohup php '.$_SERVER["DOCUMENT_ROOT"].'/../php/media/media2.php --peg \''.$peg.'\' --url "'.$url.'" --command "viddl" --context "'.$context.'" --sandbox "'.$sandbox.'" --starttime '.$starttime.' --endtime '.$endtime.' > /tmp/console.log 2>&1 & echo $!';

	loading();
	queue($cmd,null,$p_sid,$p_chid,$p_pid,$p_porder,$p_corder,$p_type,$title);
}



//check if local version of youtube-dl exists and download if not
if(file_exists($_SERVER["DOCUMENT_ROOT"].'/../utils/youtube-dl')){
	setfile();
}else{
	exec("wget 'https://yt-dl.org/downloads/latest/youtube-dl' -O '".$_SERVER["DOCUMENT_ROOT"]."/../utils/youtube-dl'");
	exec("chmod a+rx '".$_SERVER["DOCUMENT_ROOT"]."/../utils/youtube-dl'");
	setfile();
}

//set the loading animations before proceeding
function loading(){
	global $context, $p_type, $p_sid, $p_chid, $p_pid;
	if($p_type==='oaudio'){
		$sql="
			INSERT INTO resource (type,sid,pid,chid,a_mp3,a_ogg) VALUES ('".$p_type."',".$p_sid.",".$p_pid.",".$p_chid.",'loading.mp3','loading.ogg');
		";
	}else{
		$sql="
			INSERT INTO resource (type,sid,pid,chid,v_mp4,v_webm) VALUES ('".$p_type."',".$p_sid.",".$p_pid.",".$p_chid.",'loading.mp4','loading.webm');
			INSERT INTO resource (type,sid,pid,chid,location) VALUES ('poster',".$p_sid.",".$p_pid.",".$p_chid.",'loading.jpg');
		";
	}
	commit($sql);
	echo json_encode(makemess('success',"The media is being processed. This can take some time.\nVideo can take about 10 times the playtime of itself. Audio will be quicker."));
}
?>
