'use strict';


var Asp={
	page:{},
	lib:'/static/resources/',
	media:{}
};

// Declare main app level module
var Aesop=angular.module('Aesop', [
	'ngAnimate',
	'ngRoute',
	'ngCookies',
	'ngFileUpload',
	'angularMoment',
	'home',
	'story'
])
.config(['$routeProvider','$locationProvider', function($routeProvider,$locationProvider) {
	$routeProvider.when(
		'/', {
		redirectTo: '/home'
	})
	.when('/story', {
		templateUrl: '/static/html/story.php',
		controller:'story'
	})
	.when('/home', {
		templateUrl: '/static/html/home.php',
		controller:'home'
	})
	.otherwise({redirectTo: '/home'});
	$locationProvider.html5Mode(true);
	
}]).constant('angularMomentConfig', {	
    timezone: 'UTC'
})
.controller('common',['$scope','$location','$timeout',function($scope,$location,$timeout){

	//$scope.firstload = false;
	$scope.lib='/static/resources/' //root location of static media files
	$scope.c_admin={};
	$scope.locate={
		root:$location.host(),
		path:$location.path()
	};
	$scope.c={
		context:{}
	};
	$scope.hover={};
	$scope.style={};
	$scope.all={};

	
	
	/*-------------------------- Various top level screen container size functions -----------------------------------*/

	$scope.portrait=true;
	
	$scope.wsize= function(){ //the size of outer containers
		$timeout(function(){		
			$scope.width=$(window).width()-110;
			if($scope.width > 550){
				$scope.c_width = 500;
			}else{
				$scope.c_width = 300;
			}
			if($(window).height() < $(window).width()){
				$scope.portrait=false;
			}else{
				$scope.portrait=true;
			}
			$scope.height=$(window).height()-$('#admin').outerHeight();
			$scope.ratio = $scope.width/$scope.height;
			$scope.isize()
			$('#mediafocus .bvideo, #mediafocus .pimage').width($('#media .pimage').width()).height($('#media .pimage').height());
			$('.fteaser').height($('.fteaser').width()*.56);
		})
	}
	
	
	$scope.isize= function(){ // the proportion of media containers
		
		if($scope.style.ratio > $scope.ratio){
			
			$scope.style.aheight = $scope.height;
			$scope.style.awidth = 'auto';

			if(!$('#media .video').html()){
				if($('#media .fimage').length > 0){
					var foo = $('#media .fimage').width();
				}else if($('#media .pimage').length > 0){
					var foo = $('#media .pimage').width();
				}
				var left=(foo-$scope.width)/2*-1;
				
				$scope.style.css={left:left+"px",top:0};
				Asp.media.newstyle={left:left,top:0};
			}
							
		}else{
			
			$scope.style.aheight = 'auto';	
			$scope.style.awidth = $scope.width;

			if(!$('#media .video').html()){
				if($('#media .fimage').length > 0){
					var foo = $('#media .fimage');
				}else if($('#media .pimage').length > 0){
					var foo = $('#media .pimage');
				}				
				var top=($(foo).height()-$scope.height)/2*-1;
				$scope.style.css={top:top+"px",left:0};
				Asp.media.newstyle={top:top,left:0};
			}	
		}
	
	}
	$(document).ready(function(){
		Asp.isTouch = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));
		$scope.wsize();		
	})

	$(window).resize(function(){
		$scope.wsize();
		if(!$scope.$$phase){
			$scope.$apply();
		}
	});	
	
	/*-------------------------------------- Helper Functions ---------------------------------*/
	//get the size of an object	
	$scope.size = function(obj) {
		var size = 0, key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) size++;
		}
		return size;
	};
		
}])
