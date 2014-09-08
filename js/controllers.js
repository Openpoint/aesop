'use strict';

/*
myApp.run(function($rootScope, $templateCache) {
    $rootScope.$on('$routeChangeStart', function(event, next, current) {
        if (typeof(current) !== 'undefined'){
            $templateCache.remove(current.templateUrl);
        }
    });
});
*/


/* Controllers */
myApp.controller('go_home', ['$scope','$rootScope','$location','$cookieStore','$timeout','getter',function($scope,$rootScope,$location,$cookieStore,$timeout,getter) {
	//console.log("go home");

	$scope.style.extra = 110;

	$scope.c.iready=false;
	$rootScope.all={};
	$rootScope.sid=null;
	$scope.c_admin.context='home';
	
	$scope.c.context.c_order=0;
	$scope.c.context.p_order=0;
	$scope.c.context.chid=-1;
	$scope.c.context.pid=-2;
	$cookieStore.put('context',$scope.c.context);




}])
myApp.controller('go_story', ['$scope','$rootScope','$location','getter',function($scope,$rootScope,$location,getter) {
	//console.log("go story");

	$scope.style.extra = 0;
	$scope.c.iready=false;
	var page = $location.search();
	if(page.story){
		getter.sid(page.story).then(function(data){
			if(data != 'noresult'){
				$rootScope.sid=data;
			}else{
				window.location='/';
			}
		});
	}
	if(page.request=='embedded'){
		
		if($scope.user.authorised){
			console.log($scope.user);	
			$scope.logout();
		}
		$scope.locate.embedded=true;
		setTimeout(function(){
			if(!$scope.$$phase){
				$scope.$apply();
			}			
		},10)
	}
	$scope.c.context.sid=$rootScope.sid;
}])

//The main outside scope wrapper
myApp.controller('main', ['$scope','$rootScope','$cookieStore','$location','getter','setter','auth',function($scope,$rootScope,$cookieStore,$location,getter,setter,auth) {
	//console.log("main");
	
	$scope.locate={};
	$scope.locate.root=$location.host();
	$scope.locate.path=$location.path();
	$scope.hover={};
	$scope.style={};
	$scope.style2={};
	$scope.video={};
	$scope.c={};
	$scope.c.context={};
	$scope.c.iready=false;
	$scope.notification={};
	$scope.notification.message=null;
	$scope.notification.type=null;
	$rootScope.notification={};
	$rootScope.notification.message=null;
	$rootScope.notification.type=null;
	
	//verify that the cookie is the real thing by checking authtoken
	if($cookieStore.get('user')){
		var foou=$cookieStore.get('user');
		auth.verify(foou.uid).then(function(data){
			if(data == foou.authtoken){
				$scope.user=foou;
				setTimeout(function(){
					$scope.wsize();
				},10);
				if(!$scope.$$phase){
					$scope.$apply();
				}
			}else{
				$scope.user={}
				$cookieStore.remove('user');
			}
		})

	}else{
		$scope.user={};
	}
	$rootScope.$watch('notification.message',function(){
		$scope.notification=$rootScope.notification;
	})
	$scope.size = function(obj) {
		var size = 0, key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) size++;
		}
		return size;
	};
		
	//dynamic page switcher
	var endchap;
	var endpage;
	$scope.pager=function(dir){
		
		if(dir == 'next'){
			if($scope.c.context.chid == -1){
				$scope.go($scope.c.context.sid,0,-1,$scope.all.chapter[0].chid,-1);
			}else if($scope.c.context.p_order < $scope.size($scope.all.chapter[$scope.c.context.c_order].page)-1){
				$scope.go($scope.c.context.sid,$scope.c.context.c_order,$scope.c.context.p_order+1,$scope.c.context.chid,$scope.all.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order+1].pid);
			}else{
				$scope.go($scope.c.context.sid,$scope.c.context.c_order+1,-1,$scope.all.chapter[$scope.c.context.c_order+1].chid,-1);
			}
		}else{
			if($scope.c.context.c_order == 0 && $scope.c.context.pid == -1){
				$scope.go($scope.c.context.sid,0,0,-1,-2);
			}else if($scope.c.context.p_order > 0){
				$scope.go($scope.c.context.sid,$scope.c.context.c_order,$scope.c.context.p_order-1,$scope.c.context.chid,$scope.all.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order-1].pid);
			}else if($scope.c.context.p_order == 0){
				$scope.go($scope.c.context.sid,$scope.c.context.c_order,-1,$scope.c.context.chid,-1);			
			}else{
				var end = $scope.size($scope.all.chapter[$scope.c.context.c_order-1].page)-1
				if(end > -1){
					$scope.go(
					$scope.c.context.sid,
					$scope.c.context.c_order-1,
					end,
					$scope.all.chapter[$scope.c.context.c_order-1].chid,
					$scope.all.chapter[$scope.c.context.c_order-1].page[end].pid);
				}else{
					$scope.go(
					$scope.c.context.sid,
					$scope.c.context.c_order-1,
					end,
					$scope.all.chapter[$scope.c.context.c_order-1].chid,
					-1);					
				}	
			}			
		}
	}
	//menu page switcher
	$scope.go = function(sid,corder,porder,chid,pid,level){
		if(level=='top' && $scope.isTouch){
			return;
		}else{
			$scope.c.oldvid = $scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo;
			$scope.c.oldimage = $scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].fimage;
			$scope.style.delaycss={"width":"0%"};
			$scope.style.delay=0;
			if(!$scope.$$phase){
				$scope.$apply();
			}

			$scope.c.context.sid=sid;
			$scope.c.context.c_order=corder;
			$scope.c.context.p_order=porder;
			$scope.c.context.chid=chid;
			$scope.c.context.pid=pid;
			$cookieStore.put('context',$scope.c.context);
			$scope.hover.show=null
		}
	}
	
	//change the order of a chapter or page
	$scope.reorder = function(direction,order,type,chid,pid,action){
		setter.order(direction,order,type,$scope.c.context.sid,chid,pid,action).then(function(data){
			if (data == 'success'){
				location.reload();
			}else{
				console.log(data);
			}												
		})				
	}
	
		
	//switcher for the admin menu
	$scope.c_admin={};
	$scope.c_admin.subcontext='fimage';
	$scope.placeholder={};
	$scope.placeholder.fimage={};
	
	
	$scope.add = function(type,sub){
		if(!sub){
			if($scope.c_admin.context!=type){
				$scope.c_admin.context=type;
			}else{
				$scope.c_admin.context=null;
			}
			if(type == 'resource'){
				if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].fimage){					
					$scope.c_admin.subcontext='fimage';
				};
				if($scope.size($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].fvideo) > 0){
					$scope.c_admin.subcontext='fvideo';
				};
				if($scope.size($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo) > 0){					
					$scope.c_admin.subcontext='bvideo';
				};
			}
		}else if(!type){
			$scope.c_admin.context=null;
		}else{
			$scope.c_admin.subcontext=sub;
			
			if(!$scope.placeholder[$scope.c_admin.subcontext]){
				$scope.placeholder[$scope.c_admin.subcontext]={};
			}
		}
	}
	
	//adjust container sizes for screen resizing
	$scope.wsize= function(){
		
		$scope.width=$(window).width()-110;
		if($scope.width > 550){
			$scope.c_width = 500;
		}else{
			$scope.c_width = 300;
		}
		if($(window).height() < $(window).width()){
			$scope.landscape=true;
		}else{
			$scope.landscape=false;
		}
		$scope.height=$(window).height()-$('#admin').outerHeight();
		$scope.ratio = $scope.width/$scope.height;
		$scope.isize()
		$('#mediafocus .bvideo, #mediafocus .pimage').width($('#media .pimage').width());
		$('#mediafocus .bvideo, #mediafocus .pimage').height($('#media .pimage').height());
		$('.fteaser').height($('.fteaser').width()*.56);
	}

	$scope.isize= function(){
		
		if($scope.style.ratio > $scope.ratio){
			
			$scope.style.aheight = $scope.height;
			$scope.style.awidth = 'auto';
			if(!$scope.$$phase){
				$scope.$apply();
			}
			if(!$('#media .video').html()){
				if($('#media .fimage').length > 0){
					var foo = $('#media .fimage').width();
				}else if($('#media .pimage').length > 0){
					var foo = $('#media .pimage').width();
				}
				var left=(foo-$scope.width)/2*-1;
				
				$scope.style.css={left:left+"px",top:0};
				if(!$scope.$$phase){
					$scope.$apply();
				}
			}				
		}else{
			
			$scope.style.aheight = 'auto';	
			$scope.style.awidth = $scope.width;
			if(!$scope.$$phase){
				$scope.$apply();
			}
			if(!$('#media .video').html()){
				if($('#media .fimage').length > 0){
					var foo = $('#media .fimage');
				}else if($('#media .pimage').length > 0){
					var foo = $('#media .pimage');
				}				
				var top=($(foo).height()-$scope.height)/2*-1;
				$scope.style.css={top:top+"px",left:0};
				if(!$scope.$$phase){
					$scope.$apply();
				}
			}	
		}	
	}
	$(document).ready(function(){
		$scope.isTouch = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));
		$scope.wsize();		
	})

	$(window).resize(function(){
		$scope.wsize();
		if(!$scope.$$phase){
			$scope.$apply();
		}
	});



	//watch for story changes and update the main context object
	$rootScope.$watch('sid',function(){
		if($rootScope.sid){
			//fetch all the content for the context and construct the 'all' reference object
			getter.all($rootScope.sid).then(function(data){
				data.resource2={};
				data.resource2[-1]={};
				data.resource2[-1][-2]={};
				angular.forEach(data.chapter,function(value,key){
					data.resource2[data.chapter[key].chid]={};
					data.resource2[data.chapter[key].chid][-1]={};
					data.chapter[key].page=new Array();
					angular.forEach(data.page,function(value2,key2){
						if(data.chapter[key].chid == data.page[key2].chid){
							data.resource2[data.chapter[key].chid][data.page[key2].pid]={};
							data.chapter[key].page.push(data.page[key2]);
							delete data.page[key2];
						}
					})
				})

				angular.forEach(data.resource,function(value,key){
					delete data.resource[key].sid;

					if(typeof data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type] == 'undefined'){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type]=new Array();							
					}
					if(data.resource[key].type == 'bvideo'){
						if(data.resource[key].bvmute == 't'){
							data.resource2[data.resource[key].chid][data.resource[key].pid].bvmute = true;
						}else{
							data.resource2[data.resource[key].chid][data.resource[key].pid].bvmute = false;							
						}
					}
					if(data.resource[key].location){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].location = data.resource[key].location;
						//preload the poster and featured images
						$('#preload').append("<img src='/resources/"+[data.resource[key].type]+"/"+data.resource[key].location+"'/>")
					}
					if(data.resource[key].astop == 't'){
						console.log(data.resource2[data.resource[key].chid][data.resource[key].pid])
						data.resource2[data.resource[key].chid][data.resource[key].pid].astop = true;
					}else if(!data.resource2[data.resource[key].chid][data.resource[key].pid].astop){
						data.resource2[data.resource[key].chid][data.resource[key].pid].astop = false;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_ogv && data.resource[key].v_ogv){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_ogv = "/resources/"+[data.resource[key].type]+"/"+data.resource[key].v_ogv;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_mp4 && data.resource[key].v_mp4){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_mp4 = "/resources/"+[data.resource[key].type]+"/"+data.resource[key].v_mp4;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_webm && data.resource[key].v_webm){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_webm = "/resources/"+[data.resource[key].type]+"/"+data.resource[key].v_webm;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].a_ogg && data.resource[key].a_ogg){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].a_ogg = "/resources/"+[data.resource[key].type]+"/"+data.resource[key].a_ogg;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].a_mp3 && data.resource[key].a_mp3){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].a_mp3 = "/resources/"+[data.resource[key].type]+"/"+data.resource[key].a_mp3;
					}
					
				})
				//trigger to remove loading overlay and proceed when images have loaded
				imagesLoaded( '#preload', function() {
					$scope.$apply(function(){
						$scope.c.iready=true;


						data.resource = data.resource2;
						delete(data.resource2);

						angular.forEach(data.chapter,function(chapter,key){					
							angular.forEach(chapter.page,function(page,key2){
								if(key2 == 0){
									var prev = -1;
								}else{
									var prev = chapter.page[key2-1].pid;
								}
								if(!data.resource[chapter.chid][page.pid].bvideo && !data.resource[chapter.chid][page.pid].fvideo && !data.resource[chapter.chid][page.pid].fimage && !data.resource[chapter.chid][page.pid].poster && data.resource[chapter.chid][prev].bvideo){
									data.resource[chapter.chid][page.pid].bvideo=data.resource[chapter.chid][prev].bvideo
									data.resource[chapter.chid][page.pid].bvmute=data.resource[chapter.chid][prev].bvmute
									data.resource[chapter.chid][page.pid].poster=data.resource[chapter.chid][prev].poster
									data.resource[chapter.chid][page.pid].bcarry=true;
								}else{
									data.resource[chapter.chid][page.pid].bcarry=false;
								}
								if(!data.resource[chapter.chid][page.pid].bvideo && !data.resource[chapter.chid][page.pid].fvideo && !data.resource[chapter.chid][page.pid].fimage && !data.resource[chapter.chid][page.pid].poster && data.resource[chapter.chid][prev].fimage){
									data.resource[chapter.chid][page.pid].fimage=data.resource[chapter.chid][prev].fimage
									data.resource[chapter.chid][page.pid].fcarry=true;
								}else{
									data.resource[chapter.chid][page.pid].fcarry=false;
								}
								if(!data.resource[chapter.chid][page.pid].oaudio 
								&& !data.resource[chapter.chid][page.pid].fvideo 
								&& data.resource[chapter.chid][prev].oaudio 
								&& !data.resource[chapter.chid][page.pid].astop 
								&& !(data.resource[chapter.chid][page.pid].bvideo && !data.resource[chapter.chid][page.pid].bvmute)){
									data.resource[chapter.chid][page.pid].oaudio=data.resource[chapter.chid][prev].oaudio
									data.resource[chapter.chid][page.pid].acarry=true;
								}else{
									data.resource[chapter.chid][page.pid].acarry=false;
								}
							})
						})

						delete data.page;				
						console.log(data);

						data.maxbyte=parseFloat(data.maxsize)*1048576;
						if(data.chapter.length){
							data.endchap=data.chapter.length-1;
						}else{
							data.endchap=0
						}
						if(data.chapter[data.endchap] && data.chapter[data.endchap].page){
							data.endpage=data.chapter[data.endchap].page.length-1;
						}else{
							data.endpage=0
						}
						$rootScope.all=data;

						if(!$scope.c.context.sid){
							if($cookieStore.get('context') && $cookieStore.get('context').sid == $rootScope.sid){		
								$scope.c.context=$cookieStore.get('context');
							}else{
								$scope.c.context={'sid':$rootScope.all.story.sid,'chid':-1,'c_order':0,'pid':-2,'p_order':0};
							}					
						}
					});
				});								
			})

		}
	})	
	$rootScope.$watch('all',function(){
		$scope.all=$rootScope.all;
		if($scope.all && $scope.all.chapter){
				$scope.endchap=$scope.size($scope.all.chapter)-1;
				$scope.endpage=$scope.size($scope.all.chapter[$scope.endchap].page)-1;
			}else{
				$scope.endchap=0;
				$scope.endpage=0;
			}			
	})
	//the message alert system - types are message, warning, error
	$scope.notice=function(action,message){
		if (action == 'clear'){
			$rootScope.notification.message={};
		}else{
			if(typeof message == 'string'){
				var mes = new Array();
				mes[0]={};
				mes[0]['message']=message;
				mes[0]['class']=action;
				message = mes;
			}
			$rootScope.notification.message=message;
		}
	}
}])
myApp.controller('admin', ['$scope','$rootScope','setter','$cookieStore',function($scope,$rootScope,setter,$cookieStore) {
	//console.log("admin");	


	//delete a resource from the database and filesystem
	$scope.del_r = function(type,name,sub,pid,chid){
				
		setter.deleteres(type,name,sub,pid,chid).then(function(data){
			
			console.log(data);
			//delete a resource from the active scope
			if(data.type != 'fvideo' && data.type != 'bvideo'){
				delete $scope.all.resource[data.chid][data.pid][data.type];
				if(data.type == 'poster' || data.type == 'fimage'){
					$scope.notice('message',"Please refresh your browser to re-instate carried media");
				}
				if(!$scope.$$phase){
					$scope.$apply();
				}
			}else{
				if(bvideo){
					bvideo.pause();
				}
				if(fvideo){
					fvideo.pause();
				}
				//check to find the correct video item to remove			
				var array=$scope.all.resource[data.chid][data.pid][data.type];
				for (var key in array) {
					 
					if (array.hasOwnProperty(key)) {
						var cname = array[key].split("/");
						cname = cname[cname.length-1]; 
						if(data.name == cname){
							if(data.type == "fvideo"){
								delete $scope.all.resource[data.chid][data.pid].fvideo[key];
								if(!$scope.$$phase){
									$scope.$apply();
								}
							}
							if(data.type == "bvideo"){
								delete $scope.all.resource[data.chid][data.pid].bvideo[key];
								if(!$scope.$$phase){
									$scope.$apply();
								}
							}
						}					
					}
					
				}			
			}			
		})
	}

	//add a new story, chapter or page
	$scope.submit=function(context,title,sid,chid,pid){
		if($scope.all.chapter){
			c_order=$scope.all.chapter.length;
			if(typeof ($scope.all.chapter[$scope.c.context.c_order].page) !='undefined'){
				var p_order=$scope.all.chapter[$scope.c.context.c_order].page.length;
			}else{
				var p_order=0;
			}			
		}else{
			var c_order=0;
			var p_order=0;
		}
		setter.add(context,title,sid,chid,pid,c_order,p_order).then(function(data){
			if (data.result.indexOf("duplicate key value violates unique constraint") > -1){
				//$rootScope.notification.message="'"+title+"' already exists - please add an unique title";
				//$rootScope.notification.type="warning";
				$scope.notice('warning',"'"+title+"' already exists - please add an unique title");
			}else{
				if(data.result == 'success chapter'){
					location.reload();
				}
				if(data.result == 'success story'){
					window.location='/#/story?story='+data.title;
					location.reload();
				} 
				if(data.result == 'success page'){
					location.reload();
				} 
			}
		});
	}
	//set values for content edit forms
	$scope.$watch("c.context.sid",function(){
		if($scope.c.context.chid == -1 && $scope.all && $scope.all.story){
			$scope.story_name=$scope.all.story.title;
			$scope.story_text=$scope.all.story.text;
		}
	})
	$scope.$watch("c.context.chid",function(){
		if($scope.c.context.pid == -1 && $scope.all && $scope.all.story){
			$scope.chapter_title=$scope.all.chapter[$scope.c.context.c_order].title;
			$scope.chapter_subtitle=$scope.all.chapter[$scope.c.context.c_order].subtitle;
			$scope.chapter_mentitle=$scope.all.chapter[$scope.c.context.c_order].mentitle;
		}
	})
	$scope.$watch("c.context.pid",function(){
		if($scope.c.context.pid > 0 && $scope.all && $scope.all.story){
			$scope.page_title=$scope.all.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].title;
			$scope.page_text=$scope.all.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].text;
			if($scope.all.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].menushow == 't'){
				$scope.page_menshow=true;
			}else{
				$scope.page_menshow=false;
			}
		}
	})
	//set misc contexts in the forms

	$scope.$watch("c.context.pid",function(){
		if($scope.all && $scope.all.story && typeof $scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute != 'undefined'){
			$scope.c.bvmute=$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute;
		}
		if($scope.all && $scope.all.story && typeof $scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].astop != 'undefined'){
			$scope.c.astop=$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].astop;
		}
	})
		
}])
myApp.controller('modal', ['$scope','$rootScope','$location','$q','$cookieStore','setter','auth',function($scope,$rootScope,$location,$q,$cookieStore,setter,auth) {
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
			$scope.story_title=$scope.all.story.title;
		}
		if(context == 's_text'){
			$scope.story_text=$scope.all.story.text;
		}
		if(context == 'c_tit'){
			$scope.chapter_title=$scope.all.chapter[$scope.c.context.c_order].title;
		}
		if(context == 'c_subtit'){
			console.log($scope.all.chapter[$scope.c.context.c_order].subtitle);
			$scope.chapter_subtitle=$scope.all.chapter[$scope.c.context.c_order].subtitle;
		}
		if(context == 'p_tit'){
			$scope.page_title=$scope.all.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].title;
		}
		if(context == 'p_text'){
			$scope.page_text=$scope.all.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].text;
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
		$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute=x;
		setter.edit('resource', 'bvmute', value, $scope.c.context).then(function(data){
			if(data.result=="success"){
				$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute=x;
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
		$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].astop=x;
		setter.edit('resource', 'astop', value, $scope.c.context).then(function(data){
			if(data.result=="success"){
				$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].astop=x;
				$scope.notice('message','Please reload your browser to ensure audio changes to take effect');
			}
		})
	}
	
	//login
	$scope.login = function(user,password){
		$cookieStore.remove('user');
		function fetchcookie(){
			$scope.user=$cookieStore.get('user');
			if(typeof $scope.user == 'undefined'){
				setTimeout(function(){
					fetchcookie();
				},10)
			}else{
				setTimeout(function(){
					console.log('login');
					$scope.wsize();
					return null;
				},10);
				if(!$scope.$$phase){
					$scope.$apply();
				}
			}
		}
		auth.login(user,password).then(function(data){		
				if(data == 'logged in'){
					$scope.toggle();
					fetchcookie();
				}else{
					$cookieStore.remove('user');
					alert('incorrect details');
				}
		});

	}
	$scope.logout = function(){
		$cookieStore.remove('user');
		$scope.user={};
		if(!$scope.$$phase){
			$scope.$apply();
		}
		setTimeout(function(){
			console.log('logout');
			$scope.wsize();
		},10);
	}
	
}])

myApp.controller('panel', ['$scope','$rootScope','$sce',function($scope,$rootScope,$sce) {
	//console.log("panel");
	
	$scope.muted=false;
	function textfade(){
		$('#textframe .inner').css({opacity:0,display:'block'}).animate({opacity:1});
	}
	function reportProgress(){
		$scope.vprogress=Math.floor((fvideo.currentTime/fvideo.duration)*100);
		
		var date = new Date((fvideo.duration-fvideo.currentTime) * 1000);
		var hh = date.getUTCHours();
		var mm = date.getUTCMinutes();
		var ss = date.getSeconds();
		// This line gives you 12-hour (not 24) time
		if (hh > 12) {hh = hh - 12;}
		// These lines ensure you have two-digits
		if (hh < 10) {hh = "0"+hh;}
		if (mm < 10) {mm = "0"+mm;}
		if (ss < 10) {ss = "0"+ss;}
		// This formats your string to HH:MM:SS
		$scope.vidtime = hh+":"+mm+":"+ss;
		if(!$scope.$$phase){
			$scope.$apply();
		}
	}
	function playvid(){
		
		fvideo = $('#media .video')[0];
		fvideo.addEventListener("pause", function(){
			$scope.vidpause()
		});
		fvideo.addEventListener("play", function(){
			$scope.vidplay()
		});
		fvideo.poster="/resources/poster/"+$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].poster.location;
		fvideo.load();
		fvideo.addEventListener("timeupdate", reportProgress, false);
		fvideo.addEventListener("progress", function(){

			if(fvideo.buffered.length > 0 && Math.floor((fvideo.buffered.end(0)/fvideo.duration)*100) < 100){
				$scope.vlprogress=Math.floor((fvideo.buffered.end(0)/fvideo.duration)*100)
			}else{
				$scope.vlprogress=100;
			}
			if(!$scope.$$phase){
				$scope.$apply();
			}
		})	
		
		$('#media').animate({opacity:1})
		fvideo.muted=$scope.muted;
		fvideo.play();
		if(fvideo.paused){
			$scope.vidplaying=false;
		}
		vidout();
							
	}
	

	/* Video cannot autostart on mobile and mute control is FUBAR. Just disable background video for mobile.
	$('#mediafocus').on("tap",function(){
		if(bvideo.paused){			
			bvideo.play();
		}
	})
	*/

	mute=function(x){

		

		if(!x){
			if($scope.muted){
				$scope.muted=false;
				
			}else{
				$scope.muted=true;
			}
			if(bvideo){
				bvideo.muted=$scope.muted;
			}
			if(fvideo){
				fvideo.muted=$scope.muted;
			}
		}else if(!$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute || $('.acontrols').html()){
			$scope.muted=x.target.muted;
			if(!$scope.$$phase){
				$scope.$apply();
			}						
		}
		
	}
	//video controls fade
	$('#panel').on('tap',function(){
		if($('.video').html()){
			$scope.vidfade();
		}
		$scope.hover.show=null;
		if(!$scope.$$phase){
			$scope.$apply();
		}
	})
	function vidout(){
		
		vfade=setTimeout(function(){
			if(!fvideo.paused){
				clearTimeout(vfade);
				$('#vidcontrols').animate({opacity:0},1500)
			}
		},2000)
		
	}
	var vfade;
	$scope.vidpeg=function(x){
		if(!$scope.isTouch){
			clearTimeout(vfade);
			$('#vidcontrols').stop().css({opacity:1});
			if(x=='over'){
				vidout();
			}
		}
	}

	$scope.seek=function(e){
		var w=($('#vprogress').width());
		var p=((e.clientX-$('#vprogress').offset().left))
		var pp=p/w;
		if(fvideo){
			fvideo.currentTime=fvideo.duration*pp;
		}
		//alert($('#vprogress').offset().left);
	}
	$scope.vidfade=function(x){
		$('#vidcontrols').animate({opacity:1});
		clearTimeout('vfade');		
		vidout()
	}

	$scope.vidbut=function(){
		if (fvideo.paused){
			fvideo.play();
			vidout();
		}else{
			fvideo.pause();
		}
	}
	$scope.vidplaying=true
	$scope.vidpause=function(){
		$scope.vidplaying=false;
		$scope.$apply();
	}
	$scope.vidplay=function(){
		$scope.vidplaying=true;
		$scope.$apply();	
	}

	//Watched for change to context and controls the sizing and positioning of the media container
	$scope.$watchCollection('c.context',function(){
		$scope.c_admin.context=null;
		function setimage(foo){
			$scope.style.width=foo.width();
			$scope.style.height=foo.height();
			$scope.style.ratio = $scope.style.width/$scope.style.height;
			if(!$scope.$$phase){
				$scope.$apply();
			}
			$scope.isize();	
			if(!firstload){	
				$scope.c.change='animate';
			}							
			if(!$scope.$$phase){
				$scope.$apply();
			}													
		}		

		$scope.c.change=true;
		if(!$scope.$$phase){
			$scope.$apply();
		}
		$scope.c.ready=false;
		setTimeout(function(){
			$scope.$apply(function(){
				$scope.c.ready=true;
			})
			
		},100)
		$scope.style.delaycss={"width":"0%"};
		if($scope.all && $scope.all.resource){
			if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].oaudio || $scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo){
				
				setTimeout(function(){
					$('.acontrols').css({opacity:1})
					setTimeout(function(){
						$('.acontrols').animate({opacity:.3},2000)
					},3000)
					
					if($('#acontrols').html()){
						oaudio = $('#acontrols')[0];
						if(!firstload){
							oaudio.muted=$scope.muted;
						}else{
							oaudio.muted=true;
							oaudio.muted=false;
						}
					}
				},10)
			}
			if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo){
				
				if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo && $scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo != $scope.c.oldvid){
					
					$('#textframe .inner').css({display:'none'});
					
					if(!firstload){
						$('#media')[0].style.zIndex=1;
						$('#mediafocus')[0].style.zIndex=0;
												
					}else{
						$('#media')[0].style.zIndex=0
						$('#mediafocus')[0].style.zIndex=1
						textfade();								
					}
					
					$('#media').css({opacity:0});
					
					setTimeout(function(){
						
						if($('#acontrols').html()){							
							oaudio = $('#acontrols')[0];
							oaudio.load();
							oaudio.play();							
						}
					},10)
					if(bvideo){	
						bvideo.pause();
						if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute){
							bvideo.muted=true;
							if(oaudio){
								oaudio.muted=true;
							}
							
						}
							
					}
					if(fvideo){	
						fvideo.pause();
					}
					if(!$scope.$$phase){
						$scope.$apply();
					}						
					setTimeout(function(){
										
						$scope.$apply(function(){
							if(firstload){
								bvideo = $('#mediafocus .bvideo')[0];						
								bvideo.poster="/resources/poster/"+$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].poster.location;
								if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute){
									bvideo.muted=true;
									//$scope.muted=true;
								}else {
									bvideo.muted=false;
									//$scope.muted=false;								
								}

								bvideo.load();											
								bvideo.play();
								
								$('#media').css({zIndex:0});
								$('#mediafocus').css({zIndex:1});
								$('#media .pimage').clone().insertBefore($('#mediafocus .bvideo'));
								textfade();
								if($("#media .pimage")[0].complete){
									setimage($("#media .pimage"));
									firstload=false;
								}else{
									$("#media .pimage").bind("load", function () {
										setimage($("#media .pimage"));
										firstload=false;											

									})
								}
							}else{

								bvideo = $('#mediafocus .bvideo')[0];
								
								bvposter="/resources/poster/"+$scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].poster.location;
								if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute){
									bvmute=true;
									var mutehold=$scope.muted;
									bvideo.muted=true;
									setTimeout(function(){
										$scope.muted=mutehold;
									},10)
									
								}else{
									bvmute=false;
									bvideo.muted=$scope.muted;								
								}
								if($("#media .pimage")[0].complete){
									setimage($("#media .pimage"));
								}else{
									$("#media .pimage").bind("load", function () {
										setimage($("#media .pimage"));
									})
								}
							}
						})												
					},10);
				}else{
					textfade();
				}
			}else if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].fvideo){
				//alert('something to video');
				if(fvideo){	
					fvideo.pause();
				}
				if($('#mediafocus .pimage').length || $('#media .fimage').length){
					
					if($('#media .fimage').length){
						$('#media .fimage').clone().appendTo($('#mediafocus .inner'));
						$('#mediafocus').css({opacity:1,zIndex:1})
						$('#media').css({opacity:1,zIndex:0})
					}
					$('#mediafocus').animate({opacity:0},1000,function(){
						$('#mediafocus .pimage').remove();
						$('#mediafocus .fimage').remove();
						$('#media').stop().css({zIndex:1});
						$('#mediafocus').stop().css({zIndex:0,opacity:0});
						if(bvideo){	
							bvideo.pause();
						}
						$scope.style.css=null;

						setTimeout(function(){
							playvid();
						},10);
						
					});
				}else{
					$scope.style.css=null;
					if(!firstload){
						$('#media').animate({opacity:0},1000,function(){
							playvid();
							$('#media').animate({opacity:1},1000)
						})
					}else{
						setTimeout(function(){
							playvid();
						},10)
					}
					
					
				}
				
				firstload=false;

				$('#textframe .inner').css({display:'none'});
				if(!$scope.$$phase){
						$scope.$apply();
					}
				


				setTimeout(function(){									

				},10);			
			}else if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].fimage){
				//$('#media').css({zIndex:1});
				//$('#mediafocus').css({zIndex:0});
				
				firstload=false;
				if(bvideo){	
					bvideo.pause();
				}
				if(fvideo){	
					fvideo.pause();
				}
				if($scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].fimage && $scope.all.resource[$scope.c.context.chid][$scope.c.context.pid].fimage != $scope.c.oldimage){
					
					$('#textframe .inner').css({display:'none'});
					iplaceholder=$('#mediafocus .pimage');					
					setTimeout(function(){
						$('#mediafocus .inner').prepend($(iplaceholder));
						iplaceholder=$('#media fimage');
						if($("#media .fimage")[0].complete){
							setimage($("#media .fimage"));
						}else{
							$("#media .fimage").bind("load", function () {
								setimage($("#media .fimage"));
							})
						}
					},10)
				}else{
					textfade();
				}
			}else{
				$('#mediafocus').animate({opacity:0},function(){
						$('#mediafocus .pimage').remove();
						$('#mediafocus .fimage').remove();
				});
				firstload=false;
				setTimeout(function(){
					textfade();
				},10)
			}

		}
			if($scope.c.change != 'animate'){
				//console.log($scope.c.context);
				//console.log('no animate')
			}else{
				//console.log($scope.c.context);
				//console.log('animate')
				setTimeout(function(){
					$scope.c.change=true;
				},10);				
			}
	})

}])
myApp.controller('textframe', ['$scope','$rootScope','$location',function($scope,$rootScope,$location) {
	//console.log("textframe");


	
	//control the delayed mouse paging
	var textop=0;
	var oldevent =-1;
	
	$scope.style.delay=0;
	$scope.style.reverse=true;
	$scope.relay=true;

	var touchfactorY;
	var touchfactorX;


		$('#touchtrigger')[0].addEventListener('touchstart', function(e){	
			touchfactorY=e.changedTouches[0].pageY
			touchfactorX=e.changedTouches[0].pageX
		}, false)
		
		$('#touchtrigger')[0].addEventListener('touchmove', function(e){
			e.preventDefault();
			var distanceY = touchfactorY-e.changedTouches[0].pageY
			var distanceX = touchfactorX-e.changedTouches[0].pageX
			if(Math.abs(distanceY) > Math.abs(distanceX)){
				if(distanceY > 0){
					var delta = -1;
				}else{
					var delta = 1
				};
				if($scope.relay){
					scroller(delta,Math.abs(distanceY/2),1)
				}
				
			}
		},false)


	$(document).keydown(function(e){
		if (e.keyCode == 38) { 
			scroller(1,43);
			return false;
		}
		if (e.keyCode == 40) { 
			scroller(-1,43);
			return false;
		}
	});
	$('#panel').mousewheel(function (event) {		
		scroller(event.deltaY,event.deltaFactor)
	})
	function scroller(delta,factor,dfactor){

		if(!dfactor){
			dfactor=2;
		}
		
		if($scope.c_admin.context != 'home'){
			if($scope.c.ready){
				if(delta > 0 && $scope.style.reverse){
					$('#media').addClass('up');
					$('#media').removeClass('down');
				}else if($scope.style.reverse){
					$('#media').addClass('down');
					$('#media').removeClass('up');
					
				}
				//delay direction reversals to prevent scroll wheel jitter
				var timer
				if(delta != oldevent){
					$scope.style.reverse=false;
					timer=setTimeout(function(){				
						$scope.$apply(function(){

							$scope.style.reverse=true;
							$scope.style.delay=0;
							$scope.style.delaycss={"width":"0%"};
						})
					},100)							
				}else{
					clearTimeout('timer');
				}
				oldevent=delta

				
				if(textop <= 0){
					if($('#textend').offset().top - $scope.height > 0 && delta < 0){
						textop=textop+(delta*factor);
					}else if(delta > 0){
						textop=textop+(delta*factor);
						if(textop > 0){
							textop=.1;
						}

					}else if($scope.style.reverse){
						//going forward
						
						if($scope.c.context.c_order != $scope.all.endchap || $scope.c.context.p_order != $scope.all.endpage || $scope.c.context.chid == -1){
							if($scope.relay){
								
								$scope.style.delay=Math.ceil($scope.style.delay-(delta*dfactor));
								$scope.style.delaycss={"width":$scope.style.delay*10+"%","float":"left"};
								if(!$scope.$$phase){
									$scope.$apply();
								}

								if($scope.style.delay > 10){
									$scope.relay=false;
									$scope.$apply
									setTimeout(function(){
										$scope.relay=true;
										$scope.$apply
									},1100)							
									$scope.style.delay=0;
									$scope.style.delaycss={"width":0};
									textop=0;
									$scope.pager('next');
									$scope.style.textframe={"top":textop+"px"};
									if(!$scope.$$phase){
										$scope.$apply();
									}					
								}
							}
						}				
					}
					//going back
					
					if(textop > 0  && $scope.style.reverse){
						textop=0;
						if($scope.c.context.chid !=-1){
							if($scope.relay){
								$scope.style.delay=$scope.style.delay+(delta*2);
								$scope.style.delaycss={"width":$scope.style.delay*10+"%","float":"right"};
								if(!$scope.$$phase){
									$scope.$apply();
								}
								if($scope.style.delay > 10){
									$scope.relay=false;
									$scope.$apply
									setTimeout(function(){
										$scope.relay=true;
										$scope.$apply
									},1100)	
									$scope.style.delaycss={"width":0};							
									$scope.style.delay=0;
									textop=0;
									$scope.pager('back');
									$scope.style.textframe={"top":textop+"px"};
									if(!$scope.$$phase){
										$scope.$apply();
									}					
								}
							}
						}
					}
					
					$scope.style.textframe={"top":textop+"px"};
					if(!$scope.$$phase){
						$scope.$apply();
					}
				}else{
					textop=0;
				}
			}
		}
	}
	
}])
myApp.controller('sidebar', ['$scope','$rootScope','$cookieStore','getter','setter',function($scope,$rootScope,$cookieStore,getter,setter) {
	//console.log("sidebar");

	$scope.chaphover=function(key, chapter){
		$scope.hover.show=chapter.chid;		
	}

}])
myApp.controller('frontlist', ['$scope','$rootScope','getter',function($scope,$rootScope,getter) {
	//console.log("frontlist");
	$scope.tshow=false;
	
	getter.storylist().then(function(data){
		$scope.list=data
		$scope.c.iready=true;
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

