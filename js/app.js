'use strict';
var bvideo;
var fvideo;
var oaudio;
var firstload = true;
var bvposter;
var bvmute;
var mute;
var iplaceholder;
// Declare app level module which depends on filters, and services
var myApp=angular.module('myApp', [
	'ngRoute',
	'animated',
	'ngCookies',
	'angularFileUpload',
]).
config(['$routeProvider', function($routeProvider) {
	$routeProvider.when('/home', {templateUrl: 'partials/home.html', controller: 'go_home',reloadOnSearch: false});
	$routeProvider.when('/story', {templateUrl: 'partials/story.html', controller: 'go_story',reloadOnSearch: true});
	$routeProvider.otherwise({redirectTo: '/home'}); 
}])



