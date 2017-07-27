<!doctype html>

<?php
	session_start();
	if (empty($_SESSION['token'])) {
		if (function_exists('mcrypt_create_iv')) {
			$_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		}else{
			$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
		}
	}
	$token = $_SESSION['token'];
?>

<html lang="en" ng-app="Aesop">
<head>
	<meta charset="utf-8">
	<!--
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
	-->
	<link rel="shortcut icon" type="image/png" href="static/css/favicon.png">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	<meta name="csrf-token" content="<?= $token ?>">
	
	<title>Aesop | Create a story</title>
	<base href="/">
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="static/css/normalize.css"/>
	<link rel="stylesheet" href="static/css/fontello.css"/>
	<link rel="stylesheet" href="static/css/app.css"/>
	<link rel="stylesheet" href="static/css/fluidmedia.css"/>
	<script language="javascript" type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
	<script language="javascript" type="text/javascript" src="node_modules/imagesloaded/imagesloaded.pkgd.min.js"></script>
	<script language="javascript" type="text/javascript" src="node_modules/jquery-mousewheel/jquery.mousewheel.js"></script>
</head>
<?php

	if(!file_exists('../settings.php')){
		header('Location: install/install.php');
		exit;
	}
	include_once('../settings.php');
	if(!isset($installed)){
		header('Location: install/install.php');
		exit;
	}
	if(is_writable('../settings.php')){
		die('<div class="container">Please remove write permissions from /settings.php and reload the page</div>');
	}
	if(isset($_GET['method']) && $_GET['method']==='setpass'){
		setcookie('user','{"uid":"'.$_GET['uid'].'","authtoken":"'.$_GET['token'].'","method":"reset"}',0,'/');
	}
	if(isset($_COOKIE['user'])){
		$User = $_COOKIE['user'];
		$User = json_decode($User);
	}else{
		$User = false;
	}
?>
<body>

	<div id='mainwrapper' ng-controller="common">
		<div ng-hide='portrait'>
			<div id='maininner'>
				<div ng-controller='admin' id='admin' class='admin'>
					<?php if($User){ ?>
					<div  ng-if='user.authorised'>
						<div class='topmen'>
							<ul class='activities'>
								<li ng-click="add('add',null)" ng-class="{'active' : c_admin.context=='add'}">NEW <span class='denied' ng-if = '!c.allowed&&c.context.sid'>&lt;--Acess denied, create your own story</span></li>

								<li ng-click="add('help',null)" ng-class="{'active' : c_admin.context=='help'}" ng-if='all.a.story.sid'>Help</li>
								<li ng-click="add('resource',null)" ng-class="{'active' : c_admin.context=='resource'}" ng-if='all.a.story.sid && c.allowed'>Media</li>
								<li ng-click="add('content',null)" ng-class="{'active' : c_admin.context=='content'}" ng-if='all.a.story.sid && c.allowed'>Text</li>
								<li ng-click="add('order',null)" ng-class="{'active' : c_admin.context=='order'}" ng-if='all.a.story.sid && c.allowed'>Order</li>
								<li ng-click="add('delete',null)" ng-class="{'active' : c_admin.context=='delete'}" ng-if='all.a.story.sid && c.allowed'>Delete</li>
								<li ng-click="add('embed',null)" ng-class="{'active' : c_admin.context=='embed'}" ng-if='all.a.story.sid'>Embed Code</li>
							</ul>
							<ul class='user'>
								<li class='message bubb' ng-if="c_admin.queue.running.length+c_admin.queue.queued.length > 0" ng-click="add('admin',null)">{{c_admin.queue.running.length+c_admin.queue.queued.length}}</li>
								<li class='success bubb' ng-if="c_admin.queue.complete.length > 0" ng-click="reload()">{{c_admin.queue.complete.length}}</li>
								<li class='error bubb' ng-if="c_admin.queue.error.length > 0"  ng-click="add('admin',null)">{{c_admin.queue.error.length}}</li>
								<li ng-click="add('admin',null)">Admin</li>
								<li ng-click="c.logout()">Logout</li>
							</ul>
							<div class='clearfix'></div>
						</div>
						<div id='adminwrap' ng-show="c_admin.context && c_admin.context !== 'admin'" style="width:{{width+style.extra}}px">
							<?php include("./static/html/includes/admin.php") ?>
						</div>
						<?php include("./static/html/includes/admin2.php") ?>
					</div>
					<?php } ?>

					<div id = 'modal' class='modal' ng-show='modal.show_modal' ng-click='modal.toggle()'>
						<div class='inner'>
							<div class='content' ng-click='$event.stopPropagation()'>
								<?php
									include('./static/html/includes/modals.php');
								?>
							</div>
						</div>
					</div>

					<div id='login' ng-if="!user.authorised && !locate.embedded"> <!-- Remove login link if being viewed through an embedded iframe -->
						<a ng-click='modal.modal("login")'>Login</a>
					</div>
				</div>
				<div id='viewport' ng-style="{height:height}" ng-view='viewport'></div>
				<div id='loader' class='loader' ng-hide='c.iready'></div>

			</div>

		</div>
		<div ng-if='portrait'>
			<div id="landscape">
				<div class='inner'>
					<div class='content'>
						<h1>Please set your device to a landscape orientation</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id='footer'>
		<div class='container inner'>
			<a id='openpoint' href='http://openpoint.ie' target='_blank'>
				<img src='static/css/openpoint.png' />
				<span>Aesop is an Openpoint project<br>Please support by providing contract work</span>
			</a>
		</div>
	</div>
	<script language="javascript" type="text/javascript" src="node_modules/angular/angular.js"></script>
	<script language="javascript" type="text/javascript" src="node_modules/ng-file-upload/dist/ng-file-upload-shim.min.js"></script>
	<script language="javascript" type="text/javascript" src="node_modules/ng-file-upload/dist/ng-file-upload.min.js"></script>
	<script language="javascript" type="text/javascript" src="node_modules/angular-route/angular-route.min.js"></script>
	<script language="javascript" type="text/javascript" src="aesop.js"></script>
	<script language="javascript" type="text/javascript" src="static/js/angular/services/services.js"></script>
	<script language="javascript" type="text/javascript" src="static/js/angular/factories/factory.js"></script>
	<script language="javascript" type="text/javascript" src="static/js/angular/filters/filters.js"></script>
	<script language="javascript" type="text/javascript" src="node_modules/angular-animate/angular-animate.min.js"></script>
	<script language="javascript" type="text/javascript" src="node_modules/angular-cookies/angular-cookies.min.js"></script>
	<script language="javascript" type="text/javascript" src="node_modules/moment/moment.js"></script>
	<script language="javascript" type="text/javascript" src="node_modules/angular-moment/angular-moment.js"></script>

	<script language="javascript" type="text/javascript" src="static/js/angular/home.js"></script>
	<script language="javascript" type="text/javascript" src="static/js/angular/story.js"></script>

	<script language="javascript" type="text/javascript" src="static/js/angular/controllers/controller.admin.js"></script>
	<script language="javascript" type="text/javascript" src="static/js/angular/controllers/controller.story.js"></script>
	<script language="javascript" type="text/javascript" src="static/js/angular/animations/animations.js"></script>

</body>
</html>
