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
escape($data);
global $p_hid,$p_answered;
if($data->answered || $authorised){
	if(!$authorised){
		$except="WHERE questions.id IN (".implode(',',$p_answered).") AND questions.hashid=".$p_hid." ";
	}else{
		$except="WHERE  questions.hashid=".$p_hid." ";
	}
}else{
	echo('[]');
	pg_close($dbh);
	die();
}

$sql = "SELECT questions.id, questions.text, COUNT(answers.id) AS votes, COUNT(CASE WHEN answers.no = 'y' THEN 1 ELSE NULL END) AS no, COUNT(CASE WHEN answers.meh = 'y' THEN 1 ELSE NULL END) AS meh, COUNT(CASE WHEN answers.yes = 'y' THEN 1 ELSE NULL END) AS yes FROM questions INNER JOIN answers ON questions.id=answers.id ".$except."GROUP BY questions.id, questions.text";

$result = pg_query($dbh, $sql);
if($result){
	$arr = pg_fetch_all($result);
	echo json_encode($arr);
	//file_put_contents('log.txt', print_r($arr,TRUE));
}else{
	die("Error in SQL query: " . pg_last_error());
}
pg_close($dbh);
?>

