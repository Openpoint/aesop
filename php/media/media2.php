<?php
ini_set("log_errors", 1);
ini_set("error_log", $_SERVER["DOCUMENT_ROOT"]."/log/aesop.log");
error_log('media2.php');
include_once(dirname(__FILE__).'/../set.php');


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

$peg=json_decode($options['peg']);
$url=$options['url'];
$command=$options['command'];
$title=$options['title'];
$ttitle=$options['ttitle'];
$ext=$options['ext'];
$context=$options['context'];
$sandbox=$options['sandbox'];
$starttime=$options['starttime']*1;
$endtime=$options['endtime']*1;


if($command === 'viddl'){
	viddl();
}
if($command === 'vidprocess'){
	vidprocess();
}


//proceed with the media download
function viddl(){

	global $title, $ttitle, $ext, $sandbox, $starttime, $endtime, $url, $peg;

			$orig=$sandbox.'vidlib/'.$ttitle.'.'.$ext;
			$target=$sandbox.$peg->type.'/'.$title.'.'.$ext;

			//only download if file has not been downloaded before
			if(!file_exists ($orig)){
				$cmd=$sandbox.'youtube-dl -4 --no-playlist --playlist-items 1 -o "'.$orig.'" "'.$url.'"';
				$sql = "UPDATE queue SET message = array_append(message, 'Downloading the media')  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
				commit($sql);
				echo 'Downloading Video';
				exec($cmd,$pid);
			}
			copy($orig,$target);

			vidprocess();
}


//media has been uploaded/downloaded - now proceed to process it
function vidprocess(){
	global $title, $ext, $sandbox, $context, $starttime, $endtime, $dbh, $peg, $cores;

	$source=$sandbox.$peg->type.'/'.$title.'.'.$ext;




	//Get the media info
	$vinfo='mediainfo "--Inform=General;%InternetMediaType%" '.$source;
	$vinfo=exec($vinfo);
	$vinfo=explode('/',$vinfo);
	if($vinfo[0]==='audio'){
		$vinfo='mediainfo "--Inform=Audio;{\"format\":\"%Format%\",\"duration\":\"%Duration%\"}" '.$source;
	}else{
		$vinfo='mediainfo "--Inform=Video;{\"width\":%Width%,\"height\":%Height%,\"format\":\"%Format%\",\"duration\":\"%Duration%\",\"bitrate\":\"%BitRate%\",\"framerate\":\"%FrameRate%\"}" '.$source;
	}
	$vinfo=exec($vinfo);
	$vinfo=json_decode($vinfo);


	//$vinfo->ratio=$vinfo->height/$vinfo->width;
	$vinfo->duration=floor($vinfo->duration/1000);



	//Process the video time slices into hh:mm:ss format

	if ($endtime > $vinfo->duration){
		$endtime = $vinfo->duration;
	}
	$hours = floor($starttime / 3600);
	if($hours < 10){
		$hours='0'.$hours;
	}
	$mins = floor(($starttime - ($hours*3600)) / 60);
	if($mins < 10){
		$mins='0'.$mins;
	}
	$secs = floor($starttime % 60);
	if($secs < 10){
		$secs='0'.$secs;
	}
	$startcrop=$hours.':'.$mins.':'.$secs;

	if($endtime > $starttime){
		$crop=$endtime-$starttime;

		$hours = floor($crop / 3600);
		if($hours < 10){
			$hours='0'.$hours;
		}
		$mins = floor(($crop - ($hours*3600)) / 60);
		if($mins < 10){
			$mins='0'.$mins;
		}
		$secs = floor($crop % 60);
		if($secs < 10){
			$secs='0'.$secs;
		}
		$endcrop=$hours.':'.$mins.':'.$secs;
	}


	//Slice the media at times before processing
	if(isset($crop)){

		$title=$title.'_cropped';
		$sql = "UPDATE queue SET title = '".$title."'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
		commit($sql);
		$target=$sandbox.$peg->type.'/'.$title.'_temp.'.$ext;
		$crop='avconv -y -i '.$source.' -ss '.$startcrop.' -t '.$endcrop.' -vcodec copy -acodec copy '.$target;

		exec($crop);
		unlink($source);
		$source=$target;

	}else if($starttime > 0){
		$title=$title.'_cropped';
		$sql = "UPDATE queue SET title = '".$title."'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
		commit($sql);
		$target=$sandbox.$peg->type.'/'.$title.'_temp.'.$ext;
		$crop='avconv -y -i '.$source.' -ss '.$startcrop.' -vcodec copy -acodec copy '.$target;
		exec($crop);
		unlink($source);
		$source=$target;
	}else{
		rename($source , $sandbox.$peg->type.'/'.$title.'_temp.'.$ext);
		$source=$sandbox.$peg->type.'/'.$title.'_temp.'.$ext;
	}

	//Convert the video to MP4 if the input is neither webm or mp4
	if(($peg->type==='bvideo' || $peg->type==='fvideo')&&(($vinfo->format !== 'AVC' && $vinfo->format !== 'VP8' && $vinfo->format !== 'VP9') || (strcasecmp($ext, 'mp4') !== 0 && strcasecmp($ext, 'webm') !== 0))){

		$purify='avconv -y -i '.$source.' -c:v libx264 -preset slow -tune film -r 25 -c:a libvo_aacenc -threads '.$cores.' '.$context.$peg->type.'/'.$title.'.mp4';

		$sql = "UPDATE queue SET message = array_append(message, 'Creating a mp4 file')  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
		commit($sql);

		exec($purify,$mess,$err);
		if($err > 0){
			$sql = "UPDATE queue SET message = array_append(message, 'Error creating a mp4 file'), status='error'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
			commit($sql);
			errlog('ERROR in initial convert to mp4');
			unlink($sandbox.$peg->type.'/'.$title.'.mp4');
			unlink($source);
			return;
		}else{
			$vinfo->format = 'AVC';
			unlink($source);
			$ext='mp4';
			$source=$context.$peg->type.'/'.$title.'.'.$ext;
		}
	//Strip the audio to mp3 and ogg
	}else if($peg->type==='oaudio'){
		$purify='avconv -i '.$source.' -vn -b:a 128k -c:a libmp3lame '.$context.$peg->type.'/'.$title.'.mp3;avconv -i '.$source.' -vn -b:a 128k -c:a libvorbis -threads '.$cores.' '.$context.$peg->type.'/'.$title.'.ogg';

		$sql = "UPDATE queue SET message = array_append(message, 'Converting to mp3 and ogg audio')  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
		commit($sql);

		exec($purify,$mess,$err);
		if($err > 0){
			$sql = "UPDATE queue SET message = array_append(message, 'Error converting to mp3 and ogg audio'), status='error'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
			commit($sql);
			errlog('ERROR in audio conversion');
			unlink($source);
			return;
		}else{
			done();
		}
	//Put the video through avconv with no encoding to repair any container issues
	}else{
		$purify='avconv -y -i '.$source.' -vcodec copy -acodec copy '.' '.$context.$peg->type.'/'.$title.'.'.$ext;

		//echo '<br>Purify mp4.<br>';
		$sql = "UPDATE queue SET message = array_append(message, 'Auditing the video container')  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
		commit($sql);

		exec($purify,$mess,$err);
		if($err > 0){
			$sql = "UPDATE queue SET message = array_append(message, 'Error auditing the video container'), status='error'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
			commit($sql);
			errlog('ERROR in purify mp4');
			unlink($source);
			unlink($sandbox.$peg->type.'/'.$title.'.'.$ext);
			return;
		}else{
			unlink($source);
			$source=$context.$peg->type.'/'.$title.'.'.$ext;
		}
	}

	//convert to required formats
	$webm='avconv -y -i '.$source.' -c:v libvpx -crf 4 -b:v 1M -c:a libvorbis -threads '.$cores.' '.$context.$peg->type.'/'.$title.'.webm';
	$mp4='avconv -y -i '.$source.' -c:v libx264 -preset slow -tune film -r 25 -c:a libvo_aacenc -threads '.$cores.' '.$context.$peg->type.'/'.$title.'.mp4';
	$poster='avconv -ss 00.00:00 -i '.$source.'  -t 1 -f image2 '.$context.'poster/'.$title.'.jpg';
error_log($poster);
	//$getmp4='bash -c "exec nohup setsid '.$mp4.' > /dev/null 2>&1 &"';
	//$getwebm='bash -c "exec nohup setsid '.$webm.' > /dev/null 2>&1 &"';
	//$getposter='bash -c "exec nohup setsid '.$poster.' > /dev/null 2>&1 &"';

	exec($poster);



	if($vinfo->format === 'AVC'){
		$sql = "UPDATE queue SET message = array_append(message, 'Creating a webm file')  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
		commit($sql);
		exec($webm,$mess,$err);
		if($err > 0){
			$sql = "UPDATE queue SET message = array_append(message, 'Error creating a webm file'), status='error'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
			commit($sql);
			errlog('ERROR in conversion to webm');
			return;
		}else{
			done();
		}

	}else{
		$sql = "UPDATE queue SET message = array_append(message, 'Creating a mp4 file')  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
		commit($sql);
		exec($mp4,$mess,$err);
		if($err > 0){
			$sql = "UPDATE queue SET message = array_append(message, 'Error creating a mp4 file'), status='error'  WHERE sid=".$peg->sid." AND chid=".$peg->chid." AND pid=".$peg->pid." AND type='".$peg->type."'";
			commit($sql);
			errlog('ERROR in conversion to mp4');
			return;
		}else{
			done();
		}
	}
}

function done(){
	global $title, $peg, $sandbox, $prid, $dbh, $cores;

	$cmd='rm -r '.$sandbox.$peg->type.'/'.$peg->sid.'/'.$peg->chid.'/'.$peg->pid; //clean up
	exec($cmd);
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
