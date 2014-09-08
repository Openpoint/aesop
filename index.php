<!doctype html>
<html lang="en" ng-app="myApp">
<head>
	<link rel="icon" type="image/png" href="">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	<meta charset="utf-8">
	<title>Story</title>	
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/normalize.css"/>
	<link rel="stylesheet" href="css/fontello.css"/>
	<link rel="stylesheet" href="css/app.css"/>
	<link rel="stylesheet" href="css/fluidmedia.css"/>
</head>

<body>
	
	<div id='mainwrapper' ng-controller="main">
		<div ng-controller='modal'>
			<div id = 'modal' class='modal' ng-show='show_modal' ng-click='toggle()'>
				<div class='inner'>
					<div class='content' ng-click='$event.stopPropagation()'>
						<?php include('partials/includes/modals.html'); ?>
					</div>
				</div>
			</div>
			<div id="landscape" ng-show="!landscape">
				<div class='inner'>
					<div class='content'>
						<h1>Please set your device to a landscape orientation</h1>
					</div>
				</div>
			</div>
			<div id='maininner' ng-show="landscape">			
				<div ng-controller='admin' id='admin' ng-if='user.authorised'>
					<div class='topmen'>
						<ul class='activities'>
							<li ng-click="add('add',null)" ng-class="{'active' : c_admin.context=='add'}">Add</li>
							<li ng-click="add('resource',null)" ng-class="{'active' : c_admin.context=='resource'}" ng-show='all.story.sid'>Edit Resources</li>
							<li ng-click="add('content',null)" ng-class="{'active' : c_admin.context=='content'}" ng-show='all.story.sid'>Edit Content</li>
							<li ng-click="add('order',null)" ng-class="{'active' : c_admin.context=='order'}" ng-show='all.story.sid && c.context.chid !=-1'>Edit Page Order</li>
							<li ng-click="add('delete',null)" ng-class="{'active' : c_admin.context=='delete'}" ng-show='all.story.sid'>Delete</li>
							<li ng-click="add('embed',null)" ng-class="{'active' : c_admin.context=='embed'}" ng-show='all.story.sid'>Embed</li>
						</ul>
						<ul class='user'>
							<li ng-click="logout()">Logout</li>
						</ul>
						<div class='clearfix'></div>
					</div>
					<div id='adminwrap' style="width:{{width+style.extra}}px">
					<?php include("partials/includes/admin.php") ?>	
					</div>
			
				</div>			
				<div id='viewport' ng-view='viewport'></div>
			</div>
		</div>
		<div id='loader' class='loader' ng-hide='c.iready'>
		</div>
	</div>

	<!-- In production use:
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular.min.js"></script>
	-->  
	<script src="js/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.mousewheel.min.js"></script>
	<script src="js/jquery.mobile.custom.min.js"></script>
	<script src="js/imagesloaded.pkgd.min.js"></script>
	<script src="fileupload/angular-file-upload-shim.min.js"></script>
	<script src="angular.js"></script>
	<script src="fileupload/angular-file-upload.min.js"></script>
	<script src="angular-route.js"></script>
	<script src="angular-animate.min.js"></script>
	<script src="angular-cookies.min.js"></script>
	<script src="js/app.js"></script>
	<script src="js/services.js"></script>
	<script src="js/controllers.js"></script>
	<script src="js/filters.js"></script>
	<script src="js/directives.js"></script>
	<script src="js/animations.js"></script>
</body>
</html>
