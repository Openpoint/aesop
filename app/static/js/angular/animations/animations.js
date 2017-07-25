'use strict';



/*----------------------- Story Page ---------------------------------------------------*/
Asp.page.story.animation('.animate', function() {
	
	function background(){	
		
		$('#mediafocus .pimage, #mediafocus .fimage').remove();
		Asp.anireset(); //reset the trigger for fade
		if($('#mediafocus .bvideo').length > 0){		
			Asp.media.bvideo.poster=Asp.media.bvposter;			
			Asp.media.bvideo.load();
			Asp.media.bvideo.controls=true;
			Asp.media.bvideo.play();
			Asp.media.bvideo.controls=false;		
		}
		
	}
	var animate = function(element, className, done){
				
		$('#mediafocus').stop().animate({opacity:0},1000,function(){				
			background();
			$('#mediafocus').css({opacity:1});
		})

		if($(element).hasClass('up')){
			$('#textframe .inner').css({
				marginTop:'-100%',
				display:'block',
				opacity:0
			}).stop().animate({
				marginTop:0,
				opacity:1	
			},1000);

		}else if($(element).hasClass('down')){
			$('#textframe .inner').css({
				marginTop:'100%',
				display:'block',
				opacity:0
			}).stop().animate({
				marginTop:0,
				opacity:1	
			},1000);					
		}else{
			$('#textframe .inner').css({
				marginTop:0,
				display:'block',
				opacity:0
			}).stop().animate({
				opacity:1	
			});
		}				
	};

	return {
		addClass: animate,
	};
})
.animation('.pagelist', function() {
	var show = function(element, className, done){
		$(element).stop().animate({opacity:1},function(){
			//done();
		});
		var os=($(window).height()-($(element).offset().top+$(element).outerHeight()));	
		if(os < 0){
			$(element).css({top:os,display:'block'});
		}else{
			$(element).css({top:0,display:'block'});
		}	
	};
	var hide = function(element, className, done){
		$(element).stop().css({opacity:0,top:0,display:'none'})	
	};
	 return {
		addClass: hide,
		removeClass: show
	};
})


/*----------------------- Front Page ---------------------------------------------------*/
Asp.page.home.animation('.fteaserwrap', function() {
	var teaser = function(element, className, done){
		var theight=$('.fteaser').height($('.fteaser').width()*.56).height();
		$('.fteaser img').each(function(){
			$(this).on('load',function(){
				$(this).parents('.fteaser').animate({opacity:1});
				this.style.position='relative';
				this.style.top=(theight-this.height)/2+'px';
			})		
		})
	}

	return {
		removeClass:teaser,
	};
})



/*----------------------- Admin ---------------------------------------------------*/
Aesop.animation('.slide_d', function() {
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

/*----------------------- Common ---------------------------------------------------*/
.animation('.loader', function() {
	var animate = function(element, className, done){
		$(element).animate({opacity:0},300,function(){$(element).hide().css({opacity:1})});
	}
	var restore = function(element, className, done){
		$(element).show();
	}
	return {
		beforeAddClass: animate,
		removeClass: restore,
	};
})
.animation('.modal', function() {
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
