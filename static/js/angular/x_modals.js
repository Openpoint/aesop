Asp.modal=angular.module('modal',[])
.controller('modal', ['$scope','$rootScope','$location','$q','$cookieStore','setter','auth',function($scope,$rootScope,$location,$q,$cookieStore,setter,auth) {
	//console.log("modal");
	$scope.show_modal=false;
	$scope.modals = {'c_tit':false,'c_subtit':false,'p_tit':false,'p_text':false}
	$scope.toggle = function(){
		$scope.show_modal = $scope.show_modal === false ? true: false;
		if ($scope.show_modal == false) {
			$scope.modals = {'c_tit':false,'p_tit':false,'p_text':false}
		}
	}
	$scope.modal=function(context){
		$scope.toggle();
		$scope.modals[context]=true;
		if(context == 's_tit'){
			$scope.story_title=$scope.all.a.story.title;
		}
		if(context == 's_text'){
			$scope.story_text=$scope.all.a.story.text;
		}
		if(context == 'c_tit'){
			$scope.chapter_title=$scope.all.a.chapter[$scope.c.context.c_order].title;
		}
		if(context == 'c_subtit'){
			console.log($scope.all.a.chapter[$scope.c.context.c_order].subtitle);
			$scope.chapter_subtitle=$scope.all.a.chapter[$scope.c.context.c_order].subtitle;
		}
		if(context == 'p_tit'){
			$scope.page_title=$scope.all.a.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].title;
		}
		if(context == 'p_text'){
			$scope.page_text=$scope.all.a.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].text;
		}
	}
	//delete a page, chapter or story
	$scope.del=function(target){
		if(target=='story'){
			setter.deletestory($scope.c.context.sid).then(function(data){
				$cookieStore.remove('context');
				location.reload();
			});
		}
		if(target=='chap'){
			setter.deletechap($scope.c.context.chid,$scope.c.context.sid).then(function(data){
				if(data=='success'){
					setter.order('up',$scope.c.context.c_order,'chapter',$scope.c.context.sid,$scope.c.context.chid,null,null).then(function(data){
						if (data == 'success'){
							$cookieStore.remove('context');
							location.reload();
						}else{
							console.log(data);
						}												
					})
				}else{
					console.log(data);
				}
			});
		}
		if(target=='page'){
			setter.deletepage($scope.c.context.pid,$scope.c.context.chid,$scope.c.context.sid).then(function(data){
				if(data=='success'){
					setter.order('up',$scope.c.context.p_order,'page',$scope.c.context.sid,$scope.c.context.chid,$scope.c.context.pid,null).then(function(data){
						if (data == 'success'){
							$cookieStore.remove('context');
							location.reload();
						}else{
							console.log(data);
						}												
					})
				}else{
					console.log(data);
				}
			});
		}
	}
	//edit page content
	$scope.edit=function(type, element, value){
		if(element == "menushow" && value){
			value = 't';
		}else if(element == "menushow" && !value){
			value = 'f';			
		}
		setter.edit(type, element, value, $scope.c.context).then(function(data){
			//update the url if page title changes and reload
			if(data.result=="success"){
				if(data.type=="story" && data.element=="title"){
					$location.url("story?story="+data.title)
					setTimeout(function(){
						location.reload();
					},10)
						
				}else{
					location.reload();
				}
			}
		})
	}
	$scope.bvchange=function(x){
		if(x){
			var value='t';
		}else{
			var value='f';
		}
		$scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute=x;
		setter.edit('resource', 'bvmute', value, $scope.c.context).then(function(data){
			if(data.result=="success"){
				$scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute=x;
				$scope.notice('message','Please reload your browser to ensure audio changes to take effect');
			}
		})
	}
	$scope.astop=function(x){
		if(x){
			var value='t';
		}else{
			var value='f';
		}
		$scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].astop=x;
		setter.edit('resource', 'astop', value, $scope.c.context).then(function(data){
			if(data.result=="success"){
				$scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].astop=x;
				$scope.notice('message','Please reload your browser to ensure audio changes to take effect');
			}
		})
	}
	
}])
