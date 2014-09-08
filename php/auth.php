<?php
include_once('set.php');

$uid;	

function unique(){
	global $dbh,$p_username,$p_email;
	if(!isset($p_email)){
		$p_email=$p_username;
	}
	$sql="SELECT (SELECT COUNT(username) FROM users WHERE username='".$p_username."') AS user, (SELECT COUNT(email) FROM users WHERE email='".$p_email."') AS email";
	
	$result = pg_query($dbh, $sql);
	if (!$result) {
		die("Error in SQL query: " . pg_last_error($dbh));
	}
	return pg_fetch_all($result);

}

function adduser(){
	
	global $data;

	escape($data);

	global $dbh,$p_username,$p_email,$p_password;
	print_r($data);

	$unique = unique();
	if($unique[0]['user'] < 1 && $unique[0]['email'] < 1){
		$salt= uniqid(mt_rand(), true);
		$hash = crypt($p_password,'$6$rounds=5000$'.$salt.'$');
		
		$sql = "INSERT INTO users (username,hash,salt,email) VALUES ('".$p_username."','".$hash."','".$salt."','".$p_email."') returning *";
		
		$result = pg_query($dbh, $sql);
		if (!$result) {
			pg_close($dbh);
			die("Error in SQL query: " . pg_last_error($dbh));
		}else{
			echo('User "'.$p_username.'" has been added');
		}
	}else{
		if($unique[0]['user'] > 0){
			echo('Username "'.$p_username.'" not unique');
		}
		if($unique[0]['email'] > 0){
			echo('Email "'.$p_email.'" not unique');
		}
	}
	pg_close($dbh);
}

function login(){
	global $data;
	escape($data);
	global $dbh,$p_username,$p_password,$uid;
	$unique = unique();
	if($unique[0]['user'] > 0 || $unique[0]['email'] > 0){
		$sql="SELECT (SELECT salt FROM users WHERE username='".$p_username."' OR email='".$p_username."') AS salt, (SELECT hash FROM users WHERE username='".$p_username."' OR email='".$p_username."') AS hash, (SELECT id FROM users WHERE username='".$p_username."' OR email='".$p_username."') AS uid";
		$result = pg_query($dbh, $sql);
		if (!$result) {
			pg_close($dbh);
			die("Error in SQL query: " . pg_last_error());
		}else{
			$arr = pg_fetch_all($result);
			$hash =  crypt($p_password,'$6$rounds=5000$'.$arr[0]['salt'].'$');
			if($hash == $arr[0]['hash']){
				$uid=$arr[0]['uid'];
				$string = $_SERVER['HTTP_USER_AGENT'];
				$string .= time();
				$auth = md5($string);
				$sql="UPDATE users SET authtoken='".$auth."' WHERE id='".$uid."'";
				
				$result=pg_query($dbh, $sql);
				if (!$result) {
					pg_close($dbh);
					die("Error in SQL query: " . pg_last_error());
				}else{
					setcookie('user','{"uid":"'.$uid.'","authtoken":"'.$auth.'","authorised":true}',0,'/');
					echo('logged in');
				}
			}else{
				echo('incorrect');	
			}
		}
		
	}else{
		echo('Sorry, no such user or email');
	}
	pg_close($dbh);	
}
function gettoken() {
	global $data;
	escape($data);
	global $dbh,$p_uid;
	$sql="SELECT authtoken FROM users WHERE id=".$p_uid;
	$result = pg_query($dbh, $sql);
	if (!$result) {
		pg_close($dbh);
		die("Error in SQL query: " . pg_last_error($dbh));
	}else{
		$arr = pg_fetch_all($result);
		echo($arr[0]['authtoken']);
	}
	pg_close($dbh);	
}
//adduser();
if ($data->method == 'login'){
	login();
}
if ($data->method == 'verify'){
	gettoken(); 
}

?>
