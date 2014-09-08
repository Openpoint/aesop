var aesop_n;
var aesop_w;
var aesop_i;
var aesop_in;
var aesop_l;
var aesop_b;
var aesop_vp;
var aesop_vp;
var aesop_n;
var aesop_w;
var aesop_ww;
var aesop_wh;
var aesop_rat;
var aesop_wt;
var aesop_wl;				
var aesop_winw;
var aesop_winh;
var aesop_state;
var aesop_to;	
function load(){
		
	if(document.getElementById("aesop_iframe") && document.getElementById("aesop_widget") && document.getElementById("aesop_image") && document.getElementById("aesop_innards") &&document.getElementById("aesop_loader") && document.getElementById("aesop_back")){
		clearTimeout(i);
		aesop_n=document.getElementById("aesop_iframe");
		aesop_w=document.getElementById("aesop_widget");
		aesop_i=document.getElementById("aesop_image");
		aesop_in=document.getElementById("aesop_innards");
		aesop_l=document.getElementById("aesop_loader");
		aesop_b=document.getElementById("aesop_back");
			
		aesop_vp = aesop_w.getBoundingClientRect();
		aesop_ww = aesop_w.offsetWidth;
		aesop_wh = aesop_w.offsetHeight;
		aesop_rat = aesop_ww/aesop_wh;
		aesop_wt = aesop_vp.top;
		aesop_wl = aesop_vp.left;				
		aesop_winw=window.innerWidth;
		aesop_winh=window.innerHeight;
								
	}else{
		var i=setTimeout(function(){
			load();
		},10);
	}
	
}
load();
function aesop_animateout(){
	clearTimeout(aesop_to);
	aesop_state='closed'
	aesop_n.onload=null;
	aesop_i.style.width="100%";
	aesop_i.style.height="auto";
	aesop_i.style.display='block';
	aesop_i.style.opacity=1;
	aesop_n.src="";
	aesop_b.style.display="none";
	aesop_l.style.display="none";			
	aesop_in.style.display='block';
	aesop_w.style.width=aesop_ww;
	aesop_w.style.height=aesop_wh;
	aesop_w.style.position='relative';
	aesop_w.style.top=0;
	aesop_w.style.left=0;
}
function aesop_open(){
	aesop_state='open';
	aesop_in.style.display='none';
	aesop_n.style.display='block';
	/*
	if(aesop_winw/aesop_winh < aesop_rat){
		aesop_i.style.height='100%';
		aesop_i.style.width='auto';
	}else{
		aesop_i.style.width='100%';
		aesop_i.style.height='auto';			
	}
	*/
	var r=aesop_ww;
	
	if((aesop_winw-aesop_ww)>(aesop_winh-aesop_wh)){
		var dif=(aesop_winw-aesop_ww);
	}else{
		var dif=(aesop_winh-aesop_wh);
	}
	var x=dif*0.02;
	var sx = aesop_wl/((aesop_winw-aesop_ww)/x);
	var sy = aesop_wt/((aesop_winw-aesop_ww)/x);
	aesop_w.style.position='fixed';
	aesop_w.style.left=aesop_wl;
	aesop_w.style.top=aesop_wt;
	var wt=aesop_wt;
	var wl=aesop_wl;
	function t(){	
		var i=setTimeout(function(){
				
			if(r >= aesop_ww && r < aesop_winw){
				aesop_w.style.width=r+"px";					
					
				if(wl > 0 && sx < wl){
					wl=wl-sx;
				}else{
					wl=0;
				}
				aesop_w.style.left=wl+"px";
				if(wt > 0 && sy < wt){
					wt=wt-sy;
				}else{
					wt=0;
				}
				aesop_w.style.top=wt+"px";
				if(r/aesop_rat < aesop_winh){
					aesop_w.style.height=r/aesop_rat+"px";
				}
			}
			r=r+x;
			if(r-aesop_winw <= x || r-aesop_winh <= x){
				t()
			}else{
				clearTimeout(i);
				delete i;
				aesop_w.style.width='100%';
				aesop_w.style.height='100%';
				aesop_w.style.top=0;
				aesop_w.style.left=0;
					
				aesop_l.style.display='block';
				//aesop_n.src="about: blank";
				aesop_n.src=aesop_sauce;
				aesop_n.onload=function(){
					aesop_n.onload=null;
					var i =100;
					function fade(){
						i=i-2;
						aesop_i.style.opacity=i/100;
						if(i > 0 && aesop_state=='open'){
							aesop_to=setTimeout(function(){
								fade();
							},10)
						}else{
							clearTimeout(aesop_to);
							if(aesop_state=='open'){
								aesop_i.style.display='none';
								aesop_l.style.display='none';
							}
						}
					}
					fade();

				}
				var p=document.getElementById("aesop_back");
				p.style.display="block";
				delete p;						
			}
		},10)
	}				
	t()
}
