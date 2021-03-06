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


ini_set("log_errors", 1);
ini_set("error_log","../../log/aesop.log");


if(!file_exists('../../settings.php')){
	die('<div class="container">Please copy /settings.php.temp to /settings.php and make it writable to start the installation</div>');
}else{
	include('../../settings.php');
	if(!is_writable('../../settings.php')){
		if(isset($installed) && $installed){
			header('Location: /');
			exit;
		}else{
			die('<div class="container">Please make /settings.php writable and refresh the page to start the installation</div>');
		}
	}

	if(isset($db)){
		$conn_string = "host=".$db->host." port=".$db->port." dbname=".$db->name." user=".$db->user." password=".$db->pass;
		$dbh = pg_connect($conn_string);
		$dbconn = true;

	}
}
if(isset($_POST['database'])) {
	//get the domain name for email invites
	$domainname=$_SERVER['HTTP_HOST'];
	$domainname=explode('.',parse_url($domainname)['path']);
	array_shift($domainname);
	$domainname=implode(".",$domainname);

	$conn_string = "host=".$_POST['dbloc']." port=".$_POST['dbport']." dbname=".$_POST['dbname']." user=".$_POST['dbuser']." password=".$_POST['dbpass'];
	$dbh = pg_connect($conn_string);
	if (!$dbh) {
		$dbconn='fail';
	}else{
		$tofile="
\$domainname='".$domainname."';\n
\$db=(object) array(
	'name'=>'".$_POST['dbname']."',
	'host'=>'".$_POST['dbloc']."',
	'user'=>'".$_POST['dbuser']."',
	'port'=>'".$_POST['dbport']."',
	'pass'=>'".$_POST['dbpass']."'
); \n
?>";
		$lines = file('../../settings.php');
		array_pop($lines);
		$tofile = join('',$lines).$tofile;
		file_put_contents('../../settings.php',$tofile);
		include('./sql.php');
		$result = pg_query($dbh, $sql);
		if (!$result) {
			die(json_encode(pg_last_error($dbh)));
		}else{
			sleep(5);
			header('Location:'.$_SERVER['PHP_SELF']);
			exit;
		}
	}
}
if(isset($_POST['superuser'])) {
	if($_POST['upass1']!==$_POST['upass2']){
		$passerr='nomatch';
	}else{
		$lines = file('../../settings.php');
		array_pop($lines);
		$tofile = join('', $lines)."
\n
\$installed=true;\n
?>";
		file_put_contents('../../settings.php',$tofile);
		include_once('../../php/auth.php');
		$salt= uniqid(mt_rand(), true);
		$hash = crypt($_POST['upass1'],'$6$rounds=5000$'.$salt.'$');
		$token=authtoken();
		$sql = "INSERT INTO settings (pname) VALUES ('".$_POST['pname']."');INSERT INTO users (username,hash,salt,email,authtoken,role,verified) VALUES ('".$_POST['uname']."','".$hash."','".$salt."','".$_POST['umail']."','".$token."','admin','1') RETURNING id";
		$result = pg_query($dbh, $sql);
		if (!$result) {
			die(json_encode(pg_last_error($dbh)));
		}else{
			$arr = pg_fetch_all($result);
			setcookie('user','{"uid":"'.$arr[0]['id'].'","authtoken":"'.$token.'","authorised":true,"role":"admin"}',0,'/');
			sleep(5);
			header('Location: /');
			exit;
		}
	}
}
?>

<!doctype html>
<html lang="en" ng-app="Aesop">
<head>
	<link rel="icon" type="image/png" href="">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	<meta charset="utf-8">
	<title>Aesop Install</title>
	<link rel="stylesheet" href="/static/css/normalize.css"/>
	<style media="screen" type="text/css">
		.container{
			width:960px;
			margin:0 auto;
			text-align:center;
			margin-top:40px;
		}
		input{
			margin:5px 0;
		}
		.error{
			color:red;
		}
		.midmess{
			width:50%;
			margin:20px 25%;
		}
	</style>
</head>

<body class='container'>
	<?php if(!isset($dbconn) || $dbconn==='fail'){ ?>
	<h1>Database</h1>
	<div>Aesop requires a Postgresql 9+ database</div>
	<form id='database' name='database' method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type='text' value='' placeholder="database name" name="dbname" required size='40' /><br>
		<input type='text' value='5432' placeholder="database port" name="dbport" required size='40' /><br>
		<input type='text' value='localhost' placeholder="database location" name="dbloc" required size='40' /><br>
		<input type='text' value='' placeholder="database user" name="dbuser" required size='40' /><br>
		<input type='text' value='' placeholder="database password" name="dbpass" required size='40' /><br>
		<input type='submit' name="database" value='save' />
	</form>
	<?php }else if(!isset($passerr) || $passerr==='nomatch'){ ?>
		<h1>Admin User</h1>
		<div>Create the admin user</div>
		<form id='database' name='superuser' method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type='text' value='' placeholder="User Name" name="uname" required size='40' /><br>
			<input type='email' value='' placeholder="User Email" name="umail" required size='40' /><br>
			<input type='password' value='' placeholder="User Password" name="upass1" required size='40' /><br>
			<input type='password' value='' placeholder="Confirm the password" name="upass2" required size='40' /><br>
			<div class='midmess'>Please give your project a name. This will be used in outgoing mail subjects.</div>
			<input type='text' value='' placeholder="Project Name" name="pname" required size='40' /><br>
			<input type='submit' name="superuser" value='save' />
		</form>
	<?php }
		if(isset($dbconn) && $dbconn==='fail'){
			echo "<div class='error'>The connection details are incorrect. Please try again.</div>";
		}
		if(isset($passerr) && $passerr==='nomatch'){
			echo "<div class='error'>The passwords do not match. Please try again.</div>";
		}
	?>
</body>
