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

"use strict";

Asp.page.home=angular.module('home', [])
.controller('home',['$scope','$rootScope','$cookieStore',function($scope,$rootScope,$cookieStore) {

	$scope.c.iready=false;
	$scope.style.extra = 110;

	//$rootScope.all={};
	//$rootScope.sid=null;
	$scope.c_admin.context='home';
	$scope.c.context={};
	$scope.all.a={};
	//$scope.c.context.sid=null;
	//$scope.c.context.c_order=0;
	//$scope.c.context.p_order=0;
	//$scope.c.context.chid=-1;
	//$scope.c.context.pid=-2;
	$cookieStore.put('context',$scope.c.context);

}])
.controller('frontlist', ['$scope','$cookieStore','$rootScope','getter','auth',function($scope,$cookieStore,$rootScope,getter,auth) { //controller for hover on the story tiles
	$scope.tshow=false;

	$scope.demo = function(user,password){
		$cookieStore.remove('user');
		function fetchcookie(){
			$scope.user=$cookieStore.get('user');
			if(typeof $scope.user == 'undefined'){
				$timeout(function(){
					fetchcookie();
				},10)
			}else{
				location.reload();
			}
		}
		auth.login(user,password).then(function(data){
				if(data == 'logged in'){
					fetchcookie();
				}else{
					$cookieStore.remove('user');
					alert('incorrect details');
				}
		});
	}
	getter.storylist().then(function(data){
		$scope.list=data
		$scope.c.iready=true; //hide the loading div
	})
	$scope.down=function(e){
		var elem=$(e.currentTarget).find('.storydets');
		if(e.handleObj.type=='mouseover'){
			$(elem).css({height:'auto'});
			var sheight=$(elem).height();
			$(elem).css({height:0}).stop().animate({height:sheight});
		}else{
			$(elem).stop().animate({height:0});
		}
	}

	$scope.stop=function(e){
		e.preventDefault();

	}
}])
