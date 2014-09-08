'use strict';

var story_ani = angular.module('animated', ['ngAnimate']);

story_ani.animation('.slide_d', function() {
	var heightfix;
	var topheight;
	function adminmen(active){

		heightfix=setTimeout(function(){
			$('.admin_section').height('auto');
			$(active).height('auto');
			$(active).children('.admin_section').each(function(){
				if($(this).outerHeight() < $(active).height()){
					$(this).outerHeight($(active).height())
				};
			})			
			adminmen(active);
		},100)
	}
	var animateUp = function(element, className, done){
		element.css({
			opacity:1
		});
		$(element).stop().animate({height:0,opacity:0})
		clearTimeout(heightfix);
	};
	var animateDown = function(element, className, done){
		$('.admin_section').height('100%');
		element.stop().css({
			height:'auto',
			opacity:0
		});
		var height = $(element).height();

		$(element).height(0);
		
		$(element).animate({height:height, opacity:1},function(){
				adminmen($(element));
				//$(element).height('auto');				
		})		
	};
	 return {
		beforeAddClass: animateUp,
		removeClass: animateDown
	};
})
story_ani.animation('.pagelist', function() {
	var show = function(element, className, done){
		$(element).stop().animate({opacity:1},function(){
			//done();
		});
		var os=($(window).height()-($(element).offset().top+$(element).outerHeight()));	
		if(os < 0){
			$(element).css({top:os});
		}else{
			$(element).css({top:0});
		}	
	};
	var hide = function(element, className, done){
		$(element).stop().css({opacity:0,top:0})	
	};
	 return {
		addClass: hide,
		removeClass: show
	};
})

story_ani.animation('.animate', function() {
	
	function background(){
		if(fvideo){
			fvideo.pause();
		}
		if(bvideo){
			bvideo.pause();
		}	
		if($('#mediafocus .bvideo').html()){
			$('#mediafocus .pimage').remove();
			$('#media .pimage').clone().insertBefore('#mediafocus .bvideo');
			
			bvideo.poster=bvposter;
			
			bvideo.load();
			bvideo.controls=true;
			bvideo.play();
			bvideo.controls=false;		
		}else{
			$('#mediafocus .pimage').remove();
		}
		//$('#mediafocus').attr('style',($('#media').attr('style')));
		if($('#media .fimage').length == 0){
			$('#mediafocus .fimage').remove();
			$('#media').css({opacity:0});
			$('#mediafocus').css({zIndex:1});									
			$('#media').css({zIndex:0});
			$('#mediafocus').css({zIndex:1});
		}else{
			$('#media .fimage').clone().appendTo('#mediafocus .inner');
			$('#media').css({zIndex:1});
			$('#mediafocus').css({zIndex:0});
		}
		
	}
	var animate = function(element, className, done){
		//detect and control the transition conditions
		if($('#media .pimage').length && $('#mediafocus .pimage').length){
			//alert('bvideo to bvideo');
			$('#media').css({zIndex:0,opacity:1})		
			$('#mediafocus').css({zIndex:1}).animate({opacity:0},2000,function(){				
				background();
				$('#mediafocus').css({opacity:1});
			})
			//$('#mediafocus').css({opacity:1,zIndex:0})
			
		}else if($('#media .pimage').length){
			//alert('something to bvideo');
			//$('#media .pimage').clone().insertBefore($('#mediafocus .bvideo'));
			//$('#mediafocus .bvideo').addClass('temp');
			$('#mediafocus').css({opacity:0}).animate({opacity:1},2000)
			$('#media').animate({opacity:0},2000,function(){
				
				$('#mediafocus').css({zIndex:1})
				$('#media').css({zIndex:0})
				background()
			})
			
		}else if($('#media .fimage').length){
			//alert('something to fimage');
			$('#media').animate({opacity:1},2000)
			$('#mediafocus').animate({opacity:0},2000,function(){
				$('#media').css({zIndex:1});
				$('#mediafocus').css({zIndex:0});
				$('#mediafocus .pimage').remove();
				//$('#mediafocus .inner').append($('#media .fimage'));
				
			})
		}

		if($(element).hasClass('up')){
			$('#textframe .inner').css({
				marginTop:'-100%',
				display:'block'
			})
			$('#textframe .inner').stop().animate({
				marginTop:0	
			},1000);

		}else if($(element).hasClass('down')){
			$('#textframe .inner').css({
				marginTop:'100%',
				display:'block'
			})
			$('#textframe .inner').stop().animate({
				marginTop:0	
			},1000);					
		}else{
			$('#textframe .inner').css({
				marginTop:0,
				display:'block'
			})
		}				
	};

	return {
		addClass: animate,
	};
})
story_ani.animation('.loader', function() {
	var animate = function(element, className, done){
		$(element).animate({opacity:0},300,function(){$(element).addClass('ng-hide').css({opacity:1})});
	}
	return {
		beforeAddClass: animate,
	};
})
story_ani.animation('.modal', function() {
	var fadein = function(element, className, done){
		$(element).css({opacity:0}).animate({opacity:1});
	}
	var fadeout = function(element, className, done){
		$(element).animate({opacity:0},function(){done()});
	}
	return {
		removeClass: fadein,
		beforeAddClass: fadeout,
	};
})
story_ani.animation('.fteaserwrap', function() {
	var teaser = function(element, className, done){
		$('.fteaser').height($('.fteaser').width()*.56);
		$('.fteaser img').each(function(){
			$(this).on('load',function(){
				$(this).parents('.fteaser').animate({opacity:1});
			})		
		})
	}

	return {
		removeClass:teaser,
	};
})

