if(!aesop) var aesop={};

aesop.make = function(loc,size,title,sum,image,url){
	
	size==='small'?size={w:'300px',h:'160px'}:size={w:'940px',h:'528px'};
	
	var widget = document.createElement('div');
	var iframe = document .createElement('iframe');
	var closer = document .createElement('div');
	var outer = document .createElement('div');
	var img = document .createElement('img');
	var text = document.createElement('div');
	var tit = document.createElement('div');
	var enter = document.createElement('div');

	
	img.src = url+"static/resources/timage/"+image;
	img.style.width = '100%';
	img.style.height = 'auto';
	
	enter.innerHTML = 'View';
	tit.innerHTML = title;
	text.appendChild(tit);
	text.appendChild(enter);
	if(sum){
		var summary = document.createElement('div');
		summary.innerHTML = sum;
		summary.style.padding = '8px';
		summary.style.background='rgba(0,0,0,.5)';
		summary.style.padding = '8px';
		summary.style.marginTop = '8px';
		summary.style.maxWidth = '50%';
		summary.style.fontSize='14px';
		summary.style.lineHeight='20px';
		text.appendChild(summary);
	}	
	outer.appendChild(closer);
	outer.appendChild(iframe);
	widget.appendChild(outer);
	widget.appendChild(img);
	widget.appendChild(text);
	
	enter.style.position='absolute';
	enter.style.bottom='12px';
	enter.style.right='12px';
	enter.style.backgroundColor='rgba(0,0,0,.5)';
	enter.style.borderRadius='50%';
	enter.style.width = '40px';
	enter.style.height='14px';
	enter.style.padding='23px 10px';
	enter.style.textAlign='center';
	
	
	tit.style.fontSize='18px';
	tit.style.textTransform='uppercase';
	tit.style.textShadow='2px 2px 2px black';
	tit.style.padding = '8px';
	
	text.style.position = 'absolute';
	text.style.top = 0;
	text.style.left= 0;
	text.style.width = '100%';
	text.style.height = '100%';
	text.style.color = 'white';
	text.style.fontFamily='Arial, sans-serif';
	
	outer.style.position='absolute';
	outer.style.width=0;
	outer.style.height=0;
	outer.style.top=0;
	outer.style.left=0;
	outer.style.opacity=0;
	outer.style.backgroundColor = "#527489";
	outer.style.transition = "width 2s, height 2s, opacity 2s"
	outer.style.zIndex = 2;
	
	widget.style.width=size.w;
	widget.style.height=size.h;
	widget.style.position="relative";
	widget.style.overflow="hidden";
	widget.style.backgroundColor = "#527489";
	widget.style.display='flex';
	widget.style.alignItems='center';
	widget.style.cursor = 'pointer';
	
	
	closer.innerHTML = 'Close';
	closer.style.position='absolute';
	closer.style.top = '2px';
	closer.style.right = '2px';
	closer.style.cursor = 'pointer';
	closer.style.padding = '12px';
	closer.style.fontSize='16px';
	closer.style.color = 'white';
	closer.style.backgroundColor = 'rgba(0,0,0,.5)';
	closer.style.fontFamily='Arial, sans-serif';	
	

	
	iframe.style.width = '100%';
	iframe.style.height = '100%';
	iframe.style.border='none';
	
	var source = url+'story?story='+encodeURIComponent(title)+'&request=embedded';
	
	

	closer.onclick = function(e){
		e.stopPropagation();
		aesop.close(iframe,outer,closer);
	}
	widget.onclick=function(e){
		e.stopPropagation();
		aesop.open(iframe,outer,closer,source);
	}
	
	loc.parentElement.insertBefore(widget,loc);
}
aesop.open = function(iframe,outer,closer,source){	
	var w = window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth;
	var h= window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight;
	w+='px';
	h+='px';
	console.log(source);
	outer.style.position="fixed";
	outer.style.opacity=1;
	outer.style.width = w;
	outer.style.height = h;
	iframe.src = source;	
}
aesop.close=function(iframe,outer,closer){

	outer.style.opacity=0;
	outer.style.width = 0;
	outer.style.height = 0;
	setTimeout(function(){
		iframe.src='about:blank';
		outer.style.position="absolute";		
	},2000)
}
