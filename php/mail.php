<?php
$data = json_decode(file_get_contents("php://input"));
require "Mail/PHPMailerAutoload.php";
$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'mail.piquant.ie';  // Specify main and backup server
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'michael';                            // SMTP username
$mail->Password = 'Me1th0b0b';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

$mail->From = 'noreply@piquant.ie';
$mail->FromName = 'The Opinionator';
$mail->addAddress($data->to); 
$mail->addReplyTo($data->reply, $data->from);
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $data->from.' wants your opinion on #'.$data->hash;
$css='<style type="text/css">
body{
	text-align:center;
	max-width:340px;
	padding:40px 10px;
	margin:0 auto;
}
h1{
	font-weight:bold;
	font-size:1.5em;
}
h1 a{
	text-decoration:none;
}
.quest{
	font-size:1.2em;
	line-height:1.5em;
}
</style>';
$mail->Body ="
<html lang='en'>
	<head>
		<meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
		<title>".$data->from." wants your opinion on #".$data->hash."</title>
		".$css."
	</head>
	<body>
		".$data->stat."
		<br><br>
		".$data->custom."
	</body>
</html>";
$mail->AltBody = $data->stat2."\r\n\r\n".$data->custom;

if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}

echo '<div class="esuccess">Your email to '.$data->to.' has been sent</div>';

?>
