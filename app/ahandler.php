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
ini_set("error_log","../log/aesop.log");


if (!isset($_SESSION)) {
    session_start();
}

$headers = apache_request_headers();

if($headers['CsrfToken'] === $_SESSION['token']){
	include_once('../php/auth.php');
}else{
	echo 'Go away you dirty scoundrel!';
}

?>
