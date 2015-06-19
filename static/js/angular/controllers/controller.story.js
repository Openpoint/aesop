'use strict';

Asp.page.story.controller('panel', ['$scope','$sce','$timeout','video',function($scope,$sce,$timeout,video) {
	
	//Watches for change to context and controls the trnasition between pages
	$scope.$watchCollection('c.context',function(){
		if(typeof $scope.c.context.pid !== 'undefined' && $scope.all.a && $scope.all.a.resource){ //ignore watch until everything is ready	

			//move the old image into object for fade transition
			if($('#media .fimage').length){
				Asp.media.old=$('#media .fimage').clone();
				Asp.media.old.attr('ng-style',null);
			}else if($('#media .pimage').length){
				Asp.media.old=$('#media .pimage').clone();
				Asp.media.old.attr('ng-style',null);
			}else{
				Asp.media.old=false;
			}

			if($scope.c_admin.context==='admin'){
				$scope.c_admin.context=null;//close the admin page
			};
			

			$scope.c.ready=false;
			$timeout(function(){
				$scope.c.ready=true; //set delay on scroller functioning to sane heuristic		
			},1000)
		
			$scope.style.delaycss={"width":"0%"};//set the scroller progress bar to 0
					
			//-----------------if new page has background audio--------------------------//
			if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].oaudio && $scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].oaudio !== $scope.c.oldaudio){
					$timeout(function(){
						Asp.media.oaudio = $('#acontrols')[0];
						Asp.media.oaudio.load();
						if(!$scope.muted){							
							Asp.media.oaudio.play();
						}else{
							Asp.media.oaudio.pause();
						}	
						
					})
			}			
			//-----------------if new page is background video--------------------------//
			
			if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo){ 
				$('#textframe .inner').css({display:'none'}); //hide the old page text
				//if the bgvideo has not carried from the previous page
				if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo && $scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo !== $scope.c.oldvid){
										
					pause();
					
					//Load and render the new page bvideo resource	

					$('#media .pimage').clone().prependTo($('#mediafocus .inner')); // copy active to top to avoid 'DOM flash' in Chrome
					$('#media .fimage').clone().prependTo($('#mediafocus .inner'));
					$('#media').css({opacity:0})																
					$timeout(function(){
						Asp.media.bvideo = $('#mediafocus .bvideo')[0];	
						if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute){
							Asp.media.bvideo.muted=true;
							
						}else if(!$scope.muted){
							Asp.media.bvideo.muted=false;
						}							
						Asp.media.bvposter=$scope.lib+"poster/"+$scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].poster.location;							
						if($("#media .pimage")[0].complete){
							setimage($("#media .pimage"));
						}else{
							$("#media .pimage").bind("load", function () {
								
								setimage($("#media .pimage"));
							})
						}
					})												

				}else{
					textfade(); //animate the text if only a text update
				}
				
			//-----------------if new page is featured video--------------------------//
			
			}else if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].fvideo){
				
				pause();
				if(Asp.media.old){
					$(Asp.media.old).prependTo('#mediafocus .inner');
				}
				Asp.media.old=false;

				$timeout(function(){
					Asp.media.fvideo=$('#media .video')[0];
					Asp.media.fvideo.poster=$scope.lib+"poster/"+$scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].poster.location;
					Asp.media.fvideo.ontimeupdate=function(){
						var p=video.reportProgress();
						$scope.vidtime=p.vidtime;
						$scope.vprogress=p.vprogress;
						$scope.$apply();
					}
					Asp.media.fvideo.onprogress=function(){
						$scope.vlprogress=video.reportBuffer().vlprogress;
						$scope.$apply();
					}
					Asp.media.fvideo.onended=function(){
						if($scope.c.context.p_order !== $scope.all.a.endpage && $scope.c.context.c_order !== $scope.all.a.endchap){
							$('#media').animate({opacity:0},function(){
									$scope.pager('next');
									$scope.$apply();
									$('#media').css({opacity:1})
							});
						}
					}
					$('#media').css({opacity:0});
					$('#mediafocus').animate({opacity:0},1000,function(){
						$('#mediafocus .pimage, #mediafocus .fimage').remove();
						$scope.style.css=null;
						video.playvid();
						if($scope.muted){
							Asp.media.fvideo.muted=true;
						}
						$scope.vidpaused=true;
						$scope.vpause();
						$('#media').animate({opacity:1},1000);

					})	
				});			
			//-----------------if new page is featured image--------------------------//
			
						
			}else if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].fimage){
				
				pause();
				
				//if the featured image has not carried from the previous page
				if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].fimage && $scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].fimage !== $scope.c.oldimage){

					//Load and render the new featured image resource
					
					$('#media .fimage').clone().prependTo($('#mediafocus .inner')); // copy active to top to avoid 'DOM flash' in Chrome
					$('#media .pimage').clone().prependTo($('#mediafocus .inner'));
					$('#media').css({opacity:0});
					$timeout(function(){
						if($("#media .fimage")[0].complete){
							setimage($("#media .fimage"));
						}else{
							$("#media .fimage")[0].bind("complete", function () {
								setimage($("#media .fimage"));
							})
						}						
					})
				}else{
					textfade();
				}
			}

		}
		
		//----Helper functions--//	
		function setimage(foo){ //set the media to best fit in container and trigger transition animation

			$('#media').css({zIndex:0,opacity:1});
			$('#mediafocus').css({zIndex:1,opacity:1});
			Asp.media.oldstyle=Asp.media.newstyle;			
			var width=foo.width();
			var height=foo.height();
			$scope.style.ratio = width/height;
			$scope.isize();
			Asp.media.offset={
				top:Asp.media.oldstyle.top-Asp.media.newstyle.top+'px',
				left:Asp.media.oldstyle.left-Asp.media.newstyle.left+'px',
				position:'relative'
			}
			$('#mediafocus .fimage, #mediafocus .pimage').remove(); // remove the temp cover that prevents 'DOM flash'
			if(Asp.media.old){
				$(Asp.media.old).css(Asp.media.offset).prependTo('#mediafocus .inner'); //move the new image for fade into the cover					
			}							
			$scope.c.change='animate'; //triggers fade animation on the media container via class and ng-animate														
		}
		
		function pause(){ //Pause any previously playing media			
			if(Asp.media.bvideo){	
				Asp.media.bvideo.pause();							
			}
			if(Asp.media.fvideo){	
				Asp.media.fvideo.pause();
			}
		}						
	})
	Asp.anireset=function(){
		$scope.c.change=false; //reset fade animations to start	
	}
	function textfade(){
		$('#textframe .inner').stop().css({opacity:0,display:'block'}).animate({opacity:1});
	}

	/* Video cannot autostart on mobile and mute control is FUBAR. Just disable background video for mobile.
	$('#mediafocus').on("tap",function(){
		if(bvideo.paused){			
			bvideo.play();
		}
	})
	*/
		

	//video controls fade
	/*
	$('#panel').on('tap',function(){
		if($('.video').html()){
			video.vidfade();
		}
		$scope.hover.show=null;
	})
	* */
	
	
	//video play and pause
	$scope.vpause=function(){
		if(!$scope.vidpaused){
			$scope.vidpaused=true;
			Asp.media.fvideo.pause();
		}else{
			$scope.vidpaused=false;
			Asp.media.fvideo.play();
		}
	}
	$scope.seek=video.seek;



}])
.controller('textframe', ['$scope','$location',function($scope,$location) {

	
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
						
						if($scope.c.context.c_order != $scope.all.a.endchap || $scope.c.context.p_order != $scope.all.a.endpage || $scope.c.context.chid == -1){
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
.controller('sidebar', ['$scope',function($scope) {

	$scope.chaphover=function(key, chapter){
		$scope.hover.show=chapter.chid;		
	}

}])
