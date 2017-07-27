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

/*--------------------------Modals------------------------------------------------*/


Aesop.factory('modal',['$cookieStore','auth',function($cookieStore,auth){
	var pegs={};
	return {
		show_modal : false,
		modals : {

		},
		toggle : function(){
			this.show_modal = this.show_modal === false ? true: false;
		},
		modal : function(context){
			this.modals={};
			if(context){
				this.modals[context]=true;
			}
			this.toggle();
		},
	}
}])

/*--------------------------Video Playback------------------------------------------------*/

Aesop.factory('video',[function(){
	var vfade;
	var vidplaying=true

	return {
		vcontrols:{
		},
		reportProgress:function(){
			this.vcontrols.vprogress=Math.floor((Asp.media.fvideo.currentTime/Asp.media.fvideo.duration)*100);
			var date = new Date((Asp.media.fvideo.duration-Asp.media.fvideo.currentTime) * 1000);
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
			this.vcontrols.vidtime = hh+":"+mm+":"+ss;
			return this.vcontrols;
		},
		reportBuffer:function(){
			if(Asp.media.fvideo.buffered.length > 0 && Math.floor((Asp.media.fvideo.buffered.end(0)/Asp.media.fvideo.duration)*100) < 100){
				this.vcontrols.vlprogress=Math.floor((Asp.media.fvideo.buffered.end(0)/Asp.media.fvideo.duration)*100)
			}else{
				this.vcontrols.vlprogress=100;
			}
			return this.vcontrols;
		},
		playvid:function(){
			var self=this;
			Asp.media.fvideo.load();


			this.vidout();
			$('#vidcontrols').hover(function(){
				self.vidpeg();
			},function(){
				self.vidpeg('over');
			})
		},
		vidout:function(){

			vfade=setTimeout(function(){
				if(!Asp.media.fvideo.paused){
					clearTimeout(vfade);
					$('#vidcontrols').animate({opacity:0},1500)
				}
			},2000)

		},

		vidpeg:function(x){
			if(!Asp.isTouch){
				clearTimeout(vfade);
				$('#vidcontrols').stop().css({opacity:1});
				if(x=='over'){
					this.vidout();
				}
			}
		},

		seek:function(e){
			var w=($('#vprogress').width());
			var p=((e.clientX-$('#vprogress').offset().left))
			var pp=p/w;
			if(Asp.media.fvideo){
				Asp.media.fvideo.currentTime=Asp.media.fvideo.duration*pp;
			}
		},
		vidfade:function(x){
			$('#vidcontrols').animate({opacity:1});
			clearTimeout(vfade);
			this.vidout()
		}
	}
}])
