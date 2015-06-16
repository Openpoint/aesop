<?php
include_once('set.php');

$uid;	

function unique(){
	global $dbh,$p_username,$p_usermail;

	if(!isset($p_usermail)){
		$p_usermail=$p_username;
	}
	$sql="SELECT (SELECT COUNT(username) FROM users WHERE username='".$p_username."') AS user, (SELECT COUNT(email) FROM users WHERE email='".$p_usermail."') AS email";
	
	$result = pg_query($dbh, $sql);
	if (!$result) {
		die("Error in SQL query unique: " . pg_last_error($dbh));
	}
	return pg_fetch_all($result);

}
function authtoken(){
	$string = $_SERVER['HTTP_USER_AGENT'];
	$string .= time();
	return md5($string);	
}

function login(){

	global $dbh,$p_username,$p_password,$uid;
	$unique = unique();
	if($unique[0]['user'] > 0 || $unique[0]['email'] > 0){
		$sql="SELECT salt,hash,id,role FROM users WHERE username='".$p_username."' OR email='".$p_username."'";
		$result = pg_query($dbh, $sql);
		if (!$result) {
			pg_close($dbh);
			die("Error in SQL query1: " . pg_last_error());
		}else{
			$arr = pg_fetch_all($result);
			$hash =  crypt($p_password,'$6$rounds=5000$'.$arr[0]['salt'].'$');
			if($hash == $arr[0]['hash']){
				$uid=$arr[0]['id'];
				$role=$arr[0]['role'];
				$auth = authtoken();
				$sql="UPDATE users SET authtoken='".$auth."' WHERE id='".$uid."'";
				$result=pg_query($dbh, $sql);
				if (!$result) {
					pg_close($dbh);
					die("Error in SQL query2: " . pg_last_error());
				}else{
					setcookie('user','{"uid":"'.$uid.'","authtoken":"'.$auth.'","authorised":true,"role":"'.$role.'"}',0,'/');
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

function randomPassword() {
$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}

function gettoken() {

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
function mailuser($to,$subject,$message) {
	global $domainname,$sitename,$uid,$token,$mess,$p_usermail;
	$headers = '
	From: noreply@'.$domainname." \r\n" .
	'Reply-To: noreply@'.$domainname." \r\n" .
	'X-Mailer: PHP/' . phpversion();

	$headers   = array();
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-type: text/plain; charset=iso-8859-1";
	$headers[] = "From: ".$sitename." <noreply@".$domainname.">";
	$headers[] = "Reply-To: ".$sitename." <noreply@".$domainname.">";
	$headers[] = "Subject: {$subject}";
	$headers[] = "X-Mailer: PHP/".phpversion();

	$message=$message.
	'http://'.$_SERVER['HTTP_HOST']."?method=setpass&uid=".$uid."&token=".$token;

	$mail=mail($to, $subject, $message,implode("\r\n", $headers));

	if($mail){
		$m=(object) array(
			'class'=>'success',
			'message'=>'Further instructions have been emailed to '.$p_usermail
		);
		array_push ($mess,$m);
		
	}else{
		$m=(object) array(
			'class'=>'error',
			'message'=>'The system failed to send an email to '.$p_usermail.'. Please set their password manually and notify them.'
		);
		array_push ($mess,$m);	
	}
	echo json_encode($mess);
}

if ($data->method == 'login'){
	login();
}

if ($data->method == 'verify'){
	gettoken(); 
}
if($data->method === 'newpass'){

	global $dbh,$p_user,$mess, $sitename,$p_usermail,$uid,$token;
	$mess=array();
	
	$sql="SELECT id,email from users WHERE username='".$p_user."' OR email='".$p_user."'";

	$result = pg_query($dbh, $sql);
	$arr = pg_fetch_all($result);

	if(!$arr){		
		$m=(object) array(
			'class'=>'error',
			'message'=>'There is no such user or email address'
		);
		array_push ($mess,$m);
		echo json_encode($mess);		
	}else{
		$token=authtoken();
		$sql="UPDATE users SET authtoken='".$token."' WHERE id=".$arr[0]['id'];
		commit($sql);
		$message='Please follow the link below to reset your password';
		$uid=$arr[0]['id'];
		$p_usermail=$arr[0]['email'];
		mailuser($arr[0]['email'],'Password reset for '.$sitename,$message);
	}
}

if($data->method === 'newuser'){

	global $dbh,$p_username,$p_usermail,$p_mess,$uid,$token,$mess;
	$unique = unique();	
	$mess=array();
	if($unique[0]['user'] < 1 && $unique[0]['email'] < 1){
				
		$salt= uniqid(mt_rand(), true);
		$password=randomPassword();
		$hash = crypt($password,'$6$rounds=5000$'.$salt.'$');
		$token=authtoken();
		
		$sql = "INSERT INTO users (username,hash,salt,email,authtoken) VALUES ('".$p_username."','".$hash."','".$salt."','".$p_usermail."','".$token."') RETURNING id";

		$result = pg_query($dbh, $sql);
		if (!$result) {
			pg_close($dbh);
			die("Error in SQL query3: " . pg_last_error($dbh));
		}else{
			$arr = pg_fetch_all($result);
			$uid=$arr[0]['id'];
			$m=(object) array(
				'class'=>'success',
				'message'=>'User "'.$p_username.'" has been added'
			);
			array_push ($mess,$m);
			$p_mess=$p_mess."\r\n\r\n".
			"Your username is ".$p_username."\r\n\r\n".
			"Please follow the link below to set your password:"."\r\n";
			mailuser($p_usermail,'New Account',$p_mess);
		}
	}else{
		if($unique[0]['user'] > 0){
			$m=(object) array(
				'class'=>'error',
				'message'=>'Username "'.$p_username.'" not unique'
			);
			array_push ($mess,$m);
		}
		if($unique[0]['email'] > 0){
			$m=(object) array(
				'class'=>'error',
				'message'=>'Email "'.$p_usermail.'" not unique'
			);
			array_push ($mess,$m);
		}
		echo json_encode($mess);
	}
	pg_close($dbh);
}
if($data->method === 'setpass'){
	global $dbh,$p_uid,$p_token,$p_pass;
	$salt= uniqid(mt_rand(), true);
	$hash = crypt($p_pass,'$6$rounds=5000$'.$salt.'$');
	$token=authtoken();
	
	$sql="UPDATE users SET hash='".$hash."', salt='".$salt."', authtoken='".$token."', verified='1' WHERE authtoken='".$p_token."' AND id=".$p_uid;

	$result = pg_query($dbh, $sql);
	if (!$result) {
		pg_close($dbh);
		die("Error in SQL updating password: " . pg_last_error($dbh));
	}else{
		echo 'success';
	}
}
?>
