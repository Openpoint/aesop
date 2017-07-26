<?php

include_once(dirname(__FILE__).'/../set.php');

ini_set("log_errors", 1);
ini_set("error_log", dirname(__FILE__)."/../../log/aesop.log");


//get the parent PID for the queue system
$prid=exec('echo $PPID');

$opts  = array(
	"peg:",
	"url:",
	"command:",
	"title:",
	"ttitle:",
	"ext:",
	"context:",
	"sandbox:",
	"starttime:",
	"endtime:"
);

$options = getopt(null,$opts);

//error_log(print_r($options,true));

$peg=json_decode($options['peg']);
$url=$options['url'];
$command=$options['command'];

$context=$options['context'];
$sandbox=$options['sandbox'];
$starttime=$options['starttime']*1;
$endtime=$options['endtime']*1;

if($command === 'viddl'){
	viddl();
}
if($command === 'vidprocess'){
	$title=$options['title'];
	$ttitle=$options['ttitle'];
	$ext=$options['ext'];
	$download=$sandbox.'vidlib/'.$ttitle.'.'.$ext;
	vidprocess();
}

//proceed with the media download
function viddl(){
	global $sandbox,$url,$peg,$download,$title,$ttitle,$ext;
	
	
	//exec($sandbox.'youtube-dl -U');
	$dl = $sandbox."youtube-dl -j -4 --no-playlist ".$url;
	
	$json=exec($dl,$ouput,$err);
	//error_log(print_r($err,true));
	if($err === 0){
		$json=json_decode($json);
		//error_log(print_r($json,true));
		$title = $peg->sid.'/'.$peg->chid.'/'.$peg->pid.'/'.title($json->title,false);
		$ttitle = title($json->title,true);
		$ext=$json->ext;
		$download=$sandbox.'vidlib/'.$ttitle.'.'.$ext;
	}else{
		$sql = "UPDATE queue SET message = array_append(message, 'Download location is not valid, please try again'), status='error'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
		commit($sql);
		return;
	}

	//only download if file has not been downloaded before
	if(!file_exists ($download)){
		$cmd=$sandbox.'youtube-dl -4 --no-playlist --playlist-items 1 -o "'.$download.'" "'.$url.'"';	
		$sql = "UPDATE queue SET message = array_append(message, 'Downloading the media')  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
		commit($sql);	
		exec($cmd);
	}
	vidprocess();
}


//media has been uploaded/downloaded - now proceed to process it
function vidprocess(){

	global $download,$title,$ext,$context,$starttime,$endtime,$dbh,$peg,$cores;
	$source=$download;

	//Get the media info
	$info='mediainfo "--Inform=General;%InternetMediaType%" '.$source;
	$info=exec($info);
	$info=explode('/',$info);

	if($info[0]==='audio'){
		$vinfo='mediainfo "--Inform=Audio;{\"format\":\"%Format%\",\"duration\":\"%Duration%\"}" '.$source;
	}else{
		$vinfo='mediainfo "--Inform=Video;{\"width\":%Width%,\"height\":%Height%,\"format\":\"%Format%\",\"duration\":\"%Duration%\",\"bitrate\":\"%BitRate%\",\"framerate\":\"%FrameRate%\"}" '.$source;
	}
	$vinfo=exec($vinfo);
	$vinfo=json_decode($vinfo);
	$vinfo->duration=floor($vinfo->duration/1000);
	$vinfo->size = $vinfo->width*$vinfo->height;
	$vinfo->ratio = $vinfo->height/$vinfo->width;
	if($vinfo->width > 1280){
		$vinfo->newwidth = 1280;
		$vinfo->newheight = round(($vinfo->newwidth*$vinfo->ratio)/2)*2;
	}else{
		$vinfo->newwidth = $vinfo->width;
		$vinfo->newheight = $vinfo->height;
	}
	


	//Process the video time slices into hh:mm:ss format

	if ($endtime > $vinfo->duration){
		$endtime = $vinfo->duration;
	}
	function maketime($time){
		$hours = floor($time / 3600);
		if($hours < 10){
			$hours='0'.$hours;
		}
		$mins = floor(($time - ($hours*3600)) / 60);
		if($mins < 10){
			$mins='0'.$mins;
		}
		$secs = floor($time % 60);
		if($secs < 10){
			$secs='0'.$secs;
		}
		return $hours.':'.$mins.':'.$secs;
	}
	$startcrop = maketime($starttime);
	if($endtime > $starttime){
		$endcrop = maketime($endtime-$starttime);
	}
	$endtime = maketime($endtime);

	
	$title=$title.'_cropped';
	if($peg->type==='bvideo' || $peg->type==='fvideo'){
		$sql = "UPDATE queue SET message = array_append(message, 'Converting video - this could take a while'), title = '".$title.".mp4'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
	}else{
		$sql = "UPDATE queue SET message = array_append(message, 'Converting audio'), title = '".$title.".mp3'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
	}
	commit($sql);

	//Slice the media at times and process
	if(isset($endcrop)){
		if($peg->type==='bvideo' || $peg->type==='fvideo'){
			$target=$context.$peg->type.'/'.$title.'_temp.mp4';
			$keyframe = 'avconv -y -i '.$source.' -vcodec copy -acodec copy -force_key_frames '.$startcrop.','.$endtime.' '.$target;
			exec($keyframe);
			error_log($keyframe);
			$ksource = $target;
			$target = $context.$peg->type.'/'.$title.'.mp4';
			$crop='avconv -y -ss '.$startcrop.' -i '.$ksource.' -t '.$endcrop.' -c:v libx264 -c:a libmp3lame -s '.$vinfo->newwidth.'X'.$vinfo->newheight.' -threads '.$cores.' '.$target;
			error_log($crop);
		}else{
			$target = $context.$peg->type.'/'.$title.'.mp3';
			$crop='avconv -y -ss '.$startcrop.' -i '.$source.' -t '.$endcrop.' -c:a libmp3lame '.$target;
		}

	}else if($starttime > 0){

		if($peg->type==='bvideo' || $peg->type==='fvideo'){
			$target=$context.$peg->type.'/'.$title.'_temp.mp4';
			$keyframe = 'avconv -y -i '.$source.' -vcodec copy -acodec copy -force_key_frames '.$startcrop.' '.$target;
			error_log($keyframe);
			exec($keyframe);
			$ksource = $target;
			$target = $context.$peg->type.'/'.$title.'.mp4';
			$crop='avconv -y -ss '.$startcrop.' -i '.$ksource.' -c:v libx264 -c:a libmp3lame -s '.$vinfo->newwidth.'X'.$vinfo->newheight.' -threads '.$cores.' '.$target;
			error_log($crop);
		}else{
			$target = $context.$peg->type.'/'.$title.'.mp3';
			$crop='avconv -y -ss '.$startcrop.' -i '.$source.' -c:a libmp3lame '.$target;
		}

	}else{
		if($peg->type==='bvideo' || $peg->type==='fvideo'){
			$target = $context.$peg->type.'/'.$title.'.mp4';
			$crop='avconv -y -i '.$source.' -c:v libx264 -c:a libmp3lame -s '.$vinfo->newwidth.'X'.$vinfo->newheight.' -threads '.$cores.' '.$target;
			error_log($crop);
		}else{
			$target = $context.$peg->type.'/'.$title.'.mp3';
			$crop='avconv -y -i '.$source.' -c:a libmp3lame '.$target;
		}
	}
	exec($crop,$mess,$err);
	if(isset($ksource)){
		unlink($ksource);
	}
	if($peg->type==='bvideo' || $peg->type==='fvideo'){
		$poster='avconv -y -i '.$target.' -vf "select=eq(n\,0)" -q:v 1 '.$context.'poster/'.$title.'.jpg';
		exec($poster);
	}
	done();
}

function done(){
	global $title, $peg, $sandbox, $prid, $dbh, $cores;

	if($peg->type==='oaudio'){
		$sql="
			UPDATE resource SET a_mp3='".$title.".mp3', a_ogg='".$title.".ogg' WHERE type='oaudio' AND sid=".$peg->sid." AND pid=".$peg->pid." AND chid=".$peg->chid;
	}else{
		$sql="
			UPDATE resource SET location='".$title.".jpg' WHERE type='poster' AND sid=".$peg->sid." AND pid=".$peg->pid." AND chid=".$peg->chid.";
			UPDATE resource SET v_mp4='".$title.".mp4', v_webm='".$title.".webm' WHERE type='".$peg->type."' AND sid=".$peg->sid." AND pid=".$peg->pid." AND chid=".$peg->chid;
	}
	$sql=$sql.";UPDATE queue SET status='complete' WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
	commit($sql);
	qprocess();
}
?>
