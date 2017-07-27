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

aesop.biginnards='<iframe id="aesop_iframe" style="position:absolute;height:100%;width:100%;top:0;left:0" frameBorder = "0" src=""></iframe><img id="aesop_image" style="position:absolute;width:100%;top:0;left:0" src="" /><div id="aesop_loader" style="position:absolute;width:100%;height:100%;top:0;left:0;background-repeat:no-repeat;background-position:center center;display:none;"></div><div id="aesop_innards" style="position:relative;width:100%;height:100%" ><div id="aesop_title" style="font-size:16px;font-weight:bold;text-transform:uppercase;padding:5px;margin:5px;background:rgba(255,255,255,.4);color:white;display:inline-block"></div><p id="aesop_summary" style="font-size:12px;padding:5px;margin:5px;color:white"></p> <div style="position:absolute;padding:5px;margin:5px;background:#527489;bottom:0;left:0;color:white;font-size:12px">ENTER</div></div><div id="aesop_back" style="display:none;position:fixed;right:0;top:0;width:90px;margin:5px;padding:5px;background:#8B9FAC;color:white;text-align:center" onclick="aesop.animateout();event.stopPropagation()">close</div>';
aesop.smallinnards='<iframe id="aesop_iframe" style="position:absolute;height:100%;width:100%;top:0;left:0" frameBorder = "0" src=""></iframe><img id="aesop_image" style="position:absolute;width:100%;top:0;left:0" src="" /><div id="aesop_loader" style="position:absolute;width:100%;height:100%;top:0;left:0;background-repeat:no-repeat;background-position:center center;display:none;"></div><div id="aesop_innards" style="position:relative;width:100%;height:100%" ><div id="aesop_title" style="font-size:16px;font-weight:bold;text-transform:uppercase;padding:5px;margin:5px;background:rgba(255,255,255,.4);color:white;display:inline-block"></div><div style="position:absolute;padding:5px;margin:5px;background:#527489;bottom:0;left:0;color:white;font-size:12px">ENTER</div></div><div id="aesop_back" style="display:none;position:fixed;right:0;top:0;width:90px;margin:5px;padding:5px;background:#8B9FAC;color:white;text-align:center" onclick="aesop.animateout();event.stopPropagation()">close</div>'
aesop.load=function(){

	if(document.getElementById("aesop_widget")){
		clearTimeout(i);

		aesop.w=document.getElementById("aesop_widget");
		aesop.w.style.position='relative';
		aesop.w.style.fontFamily='Helvetica,Sans-serif';
		aesop.w.style.overflow='hidden';
		aesop.w.style.cursor='pointer';
		if(aesop.size==='big'){
			aesop.w.innerHTML=aesop.biginnards;

			aesop.w.style.width='940px';
			aesop.w.style.height='528px';
		}else{
			aesop.w.innerHTML=aesop.smallinnards;

			aesop.w.style.width='300px';
			aesop.w.style.height='160px';

		}
		aesop.w.onclick=function(){aesop.open()};

		aesop.n=document.getElementById("aesop_iframe");

		aesop.i=document.getElementById("aesop_image");


		document.getElementById("aesop_title").innerHTML=aesop.title;
		if(typeof aesop.summary!=='undefined'){
			document.getElementById("aesop_summary").innerHTML=aesop.summary;
		}

		aesop.in=document.getElementById("aesop_innards");
		aesop.l=document.getElementById("aesop_loader");
		aesop.l.style.backgroundImage=aesop.loader;
		aesop.b=document.getElementById("aesop_back");

		aesop.vp = aesop.w.getBoundingClientRect();
		aesop.ww = aesop.w.offsetWidth;
		aesop.wh = aesop.w.offsetHeight;
		aesop.rat = aesop.ww/aesop.wh;
		aesop.wt = aesop.vp.top;
		aesop.wl = aesop.vp.left;
		aesop.winw=window.innerWidth;
		aesop.winh=window.innerHeight;

		aesop.i.onload=function(){
			if(aesop.i.height>=aesop.wh){
				aesop.i.style.top=(aesop.wh-aesop.i.height)/2;
			}else{
				aesop.i.style.height='100%';
				aesop.i.style.width='auto';
				aesop.i.style.left=(aesop.ww-aesop.i.width)/2;
			}
		}
		aesop.i.src=aesop.isauce;
	}else{
		var i=setTimeout(function(){
			aesop.load();
		},10);
	}

}
aesop.load();
aesop.animateout=function(){
	aesop.w.onclick=function(){aesop.open()};
	clearTimeout(aesop.to);
	aesop.state='closed'
	aesop.n.onload=null;
	aesop.i.style.width="100%";
	aesop.i.style.height="auto";
	aesop.i.style.display='block';
	aesop.i.style.opacity=1;
	aesop.n.src="";
	aesop.b.style.display="none";
	aesop.l.style.display="none";
	aesop.in.style.display='block';
	aesop.w.style.width=aesop.ww;
	aesop.w.style.height=aesop.wh;
	aesop.w.style.position='relative';
	aesop.w.style.top=0;
	aesop.w.style.left=0;
}
aesop.open=function(){
	aesop.w.onclick=null;
	aesop.state='open';
	aesop.in.style.display='none';
	aesop.n.style.display='block';
	var r=aesop.ww;

	if((aesop.winw-aesop.ww)>(aesop.winh-aesop.wh)){
		var dif=(aesop.winw-aesop.ww);
	}else{
		var dif=(aesop.winh-aesop.wh);
	}
	var x=dif*0.02;
	var sx = aesop.wl/((aesop.winw-aesop.ww)/x);
	var sy = aesop.wt/((aesop.winw-aesop.ww)/x);
	aesop.w.style.position='fixed';
	aesop.w.style.left=aesop.wl;
	aesop.w.style.top=aesop.wt;
	var wt=aesop.wt;
	var wl=aesop.wl;
	function t(){
		var i=setTimeout(function(){

			if(r >= aesop.ww && r < aesop.winw){
				aesop.w.style.width=r+"px";

				if(wl > 0 && sx < wl){
					wl=wl-sx;
				}else{
					wl=0;
				}
				aesop.w.style.left=wl+"px";
				if(wt > 0 && sy < wt){
					wt=wt-sy;
				}else{
					wt=0;
				}
				aesop.w.style.top=wt+"px";
				if(r/aesop.rat < aesop.winh){
					aesop.w.style.height=r/aesop.rat+"px";
				}
			}
			r=r+x;
			if(r-aesop.winw <= x || r-aesop.winh <= x){
				t()
			}else{
				clearTimeout(i);
				delete i;
				aesop.w.style.width='100%';
				aesop.w.style.height='100%';
				aesop.w.style.top=0;
				aesop.w.style.left=0;

				aesop.l.style.display='block';
				//aesop.n.src="about: blank";
				aesop.n.src=aesop.sauce;
				aesop.n.onload=function(){
					aesop.n.onload=null;
					var i =100;
					function fade(){
						i=i-2;
						aesop.i.style.opacity=i/100;
						if(i > 0 && aesop.state=='open'){
							aesop.to=setTimeout(function(){
								fade();
							},10)
						}else{
							clearTimeout(aesop.to);
							if(aesop.state=='open'){
								aesop.i.style.display='none';
								aesop.l.style.display='none';
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
