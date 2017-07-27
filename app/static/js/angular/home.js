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
.controller('frontlist', ['$scope','$rootScope','getter',function($scope,$rootScope,getter) { //controller for hover on the story tiles
	$scope.tshow=false;

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
