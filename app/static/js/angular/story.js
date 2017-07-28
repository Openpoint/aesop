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

Asp.page.story=angular.module('story', [])
.controller('story', ['$scope','$location','$cookieStore','$timeout','getter',function($scope,$location,$cookieStore,$timeout,getter) {

	$('#preload').html('');
	$scope.user=$cookieStore.get('user');
	$scope.c.context={
		sid:false
	};
	$scope.c_admin.context=null;
	$scope.c.iready=false; //set the page to 'loading'
	$scope.style.extra = 0;

	/*-------------------------------------- Get the page context id ---------------------------------*/
	var page = $location.search();
	if(page.story){
		getter.sid(page.story).then(function(data){ //get the story id from the database (asynchronous)
			if(data != 'noresult'){
				$scope.c.context.sid=data.sid*1;
				$scope.c.allowed=($scope.user?($scope.user.uid == data.owner):false);
			}else{
				window.location='/'; //go home if the story does not exist
			}
		});
	}
	if(page.request=='embedded'){ //log user out if story is being accessed through an iframe embed
		$scope.locate.embedded=true;
		if($scope.user) $scope.c.logout();
	}


	/*-------------------------------------- Load the page resources from id ---------------------------------*/

	$scope.$watch('c.context.sid',function(){ //watch for asynchronouse changes to the page id

		$scope.all.a={};

		if($scope.c.context.sid){ //ignore initial creation of 'c.context.sid'
			var sid=$scope.c.context.sid;

			getter.all(sid).then(function(data){ //fetch raw data for the context and construct the 'all' reference object. This is the semantic object that contains all the media references.


				data.resource2={};
				data.resource2[-1]={};
				data.resource2[-1][-2]={}; //the container for the opening slide

				//sort 1
				angular.forEach(data.chapter,function(value,key){
					data.resource2[data.chapter[key].chid]={};
					data.resource2[data.chapter[key].chid][-1]={};
					data.chapter[key].page=[];
					angular.forEach(data.page,function(value2,key2){
						if(data.chapter[key].chid === data.page[key2].chid){
							data.resource2[data.chapter[key].chid][data.page[key2].pid]={};
							data.chapter[key].page.push(data.page[key2]);
							delete data.page[key2];
						}
					})
				})

				//sort 2
				angular.forEach(data.resource,function(value,key){
					delete data.resource[key].sid;

					if(typeof data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type] === 'undefined'){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type]=[];
					}
					if(data.resource[key].type === 'bvideo'){
						if(data.resource[key].bvmute === 't'){
							data.resource2[data.resource[key].chid][data.resource[key].pid].bvmute = true;
						}else{
							data.resource2[data.resource[key].chid][data.resource[key].pid].bvmute = false;
						}
					}
					if(data.resource[key].location){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].location = data.resource[key].location;

						//preload the poster and featured images by dumping them into a hidden container which calls imagesready
						$('#preload').append("<img src='"+$scope.lib+[data.resource[key].type]+"/"+data.resource[key].location+"'/>");
					}
					if(data.resource[key].astop === 't'){
						data.resource2[data.resource[key].chid][data.resource[key].pid].astop = true;
					}else if(!data.resource2[data.resource[key].chid][data.resource[key].pid].astop){
						data.resource2[data.resource[key].chid][data.resource[key].pid].astop = false;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_ogv && data.resource[key].v_ogv){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_ogv = $scope.lib+[data.resource[key].type]+"/"+data.resource[key].v_ogv;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_mp4 && data.resource[key].v_mp4){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_mp4 = $scope.lib+[data.resource[key].type]+"/"+data.resource[key].v_mp4;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_webm && data.resource[key].v_webm){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].v_webm = $scope.lib+[data.resource[key].type]+"/"+data.resource[key].v_webm;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].a_ogg && data.resource[key].a_ogg){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].a_ogg = $scope.lib+[data.resource[key].type]+"/"+data.resource[key].a_ogg;
					}
					if(!data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].a_mp3 && data.resource[key].a_mp3){
						data.resource2[data.resource[key].chid][data.resource[key].pid][data.resource[key].type].a_mp3 = $scope.lib+[data.resource[key].type]+"/"+data.resource[key].a_mp3;
					}

				})
				//trigger to remove loading overlay and proceed when images have loaded
				imagesLoaded( '#preload', function() {

					$timeout(function(){

						data.resource = data.resource2;
						delete(data.resource2);

						//sort 3 assign the 'carry' states for each slide and duplicate carried media into their semantic boxes
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
						data.maxbyte=parseFloat(data.maxsize)*1048576;

						//get the end point chapter and page
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


						//----------- Just about done and ready to render ----------//

						$scope.all.a=data;

						$scope.c.iready=true; //remove the loading overlay

						//----------- have a look at the 'all' object to understand the data structure and syntax ----------//
						//console.log($scope.all.a);



						//Put a viewer back to their original position on page refresh, else to start of story
						if($cookieStore.get('context') && $cookieStore.get('context').sid == $scope.all.a.story.sid){
							$scope.c.context=$cookieStore.get('context');
						}else{

							$scope.c.context={
								'sid':$scope.all.a.story.sid*1,
								'owner':$scope.all.a.story.owner*1,
								'chid':-1,
								'c_order':0,
								'pid':-2,
								'p_order':0
							};

						}
					});
				});
			})

		}
	})
	/*-------------------------------------- Page switchers ---------------------------------*/
	//dynamic page switcher

	$scope.pager=function(dir){

		if(dir == 'next'){
			if($scope.c.context.chid == -1){
				$scope.all.go($scope.c.context.sid,0,-1,$scope.all.a.chapter[0].chid,-1);
			}else if($scope.c.context.p_order < $scope.size($scope.all.a.chapter[$scope.c.context.c_order].page)-1){
				$scope.all.go($scope.c.context.sid,$scope.c.context.c_order,$scope.c.context.p_order+1,$scope.c.context.chid,$scope.all.a.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order+1].pid);
			}else{
				$scope.all.go($scope.c.context.sid,$scope.c.context.c_order+1,-1,$scope.all.a.chapter[$scope.c.context.c_order+1].chid,-1);
			}
		}else{
			if($scope.c.context.c_order == 0 && $scope.c.context.pid == -1){
				$scope.all.go($scope.c.context.sid,0,0,-1,-2);
			}else if($scope.c.context.p_order > 0){
				$scope.all.go($scope.c.context.sid,$scope.c.context.c_order,$scope.c.context.p_order-1,$scope.c.context.chid,$scope.all.a.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order-1].pid);
			}else if($scope.c.context.p_order == 0){
				$scope.all.go($scope.c.context.sid,$scope.c.context.c_order,-1,$scope.c.context.chid,-1);
			}else{
				var end = $scope.size($scope.all.a.chapter[$scope.c.context.c_order-1].page)-1
				if(end > -1){
					$scope.all.go(
					$scope.c.context.sid,
					$scope.c.context.c_order-1,
					end,
					$scope.all.a.chapter[$scope.c.context.c_order-1].chid,
					$scope.all.a.chapter[$scope.c.context.c_order-1].page[end].pid);
				}else{
					$scope.all.go(
					$scope.c.context.sid,
					$scope.c.context.c_order-1,
					end,
					$scope.all.a.chapter[$scope.c.context.c_order-1].chid,
					-1);
				}
			}
		}
	}
	//menu page switcher
	$scope.all.go = function(sid,corder,porder,chid,pid,level){

		if(level=='top' && $scope.isTouch){
			return;
		}else{
			$scope.c.oldvid = $scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo;
			$scope.c.oldimage = $scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].fimage;
			$scope.c.oldaudio = $scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].oaudio;
			$scope.style.delaycss={"width":"0%"};
			$scope.style.delay=0;

			$scope.c.context={
				sid:sid*1,
				c_order:corder*1,
				p_order:porder*1,
				chid:chid*1,
				pid:pid*1
			}
			$cookieStore.put('context',$scope.c.context);
			//$scope.hover.show=null
		}
	}

	/*-------------------------------------- Helper Functions ---------------------------------*/

	//Mute the audio
	$scope.mute=function(){
		if($scope.muted){
			Asp.media.muted=$scope.muted=false;
			vol(false);
		}else{
			Asp.media.muted=$scope.muted=true;
			vol(true);
		}
		function vol(x){
			if(typeof Asp.media.bvideo !== 'undefined'){
				Asp.media.bvideo.muted=x;
			}
			if(typeof Asp.media.fvideo !== 'undefined'){
				Asp.media.fvideo.muted=x;
			}
		}
	}
}]);
