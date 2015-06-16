<div id='panel' ng-controller='panel' ng-style="{width:width,height:height}" ng-hide="c_admin.context === 'admin'">
		<audio id="acontrols" ng-if="(size(all.a.resource[c.context.chid][c.context.pid].oaudio) > 0 && (all.a.resource[c.context.chid][c.context.pid].bvmute || all.a.resource[c.context.chid][c.context.pid].fimage.location)) || (size(all.a.resource[c.context.chid][c.context.pid].bvideo) > 0 && isTouch && !all.a.resource[c.context.chid][c.context.pid].bvmute)" controls loop>
		<source ng-if="all.a.resource[c.context.chid][c.context.pid].oaudio.a_ogg" src="{{all.a.resource[c.context.chid][c.context.pid].oaudio.a_ogg}}" type="audio/ogg">
		<source ng-if="all.a.resource[c.context.chid][c.context.pid].oaudio.a_mp3" src="{{all.a.resource[c.context.chid][c.context.pid].oaudio.a_mp3}}" type="audio/mpeg">

		<source ng-if="all.a.resource[c.context.chid][c.context.pid].bvideo.v_webm && isTouch && !all.a.resource[c.context.chid][c.context.pid].bvmute" src="{{all.a.resource[c.context.chid][c.context.pid].bvideo.v_webm}}" type="video/webm">
		<source ng-if="all.a.resource[c.context.chid][c.context.pid].bvideo.v_mp4 && isTouch && !all.a.resource[c.context.chid][c.context.pid].bvmute" src="{{all.a.resource[c.context.chid][c.context.pid].bvideo.v_mp4}}" type="video/mp4">
		<source ng-if="all.a.resource[c.context.chid][c.context.pid].bvideo.v_ogv && isTouch && !all.a.resource[c.context.chid][c.context.pid].bvmute" src="{{all.a.resource[c.context.chid][c.context.pid].bvideo.v_ogv}}" type="video/ogg">			
	</audio>
	<div id='textframe' ng-controller='textframe' ng-style="{height:height-40}" ng-hide="size(all.a.resource[c.context.chid][c.context.pid].fvideo) > 0" ng-class="{'story':c.context.chid==-1,'chapter':c.context.pid==-1,'page':c.context.pid > 0}">
		<!--<div>sid={{c.context.sid}} | chid={{c.context.chid}} | pid={{c.context.pid}} | corder={{c.context.c_order}} | porder={{c.context.p_order}}</div>-->
		<div class='inner' ng-style="style.textframe">
			<div class='story_in' ng-show='c.context.chid == -1'>
				<h1>{{all.a.story.title}}</h1>
				<p>{{all.a.story.text}}</p>
			</div>
			<div ng-show='c.context.pid == -1'>			
				<!--<div class='edit' ng-click='modal("c_tit")'>edit</div>-->
				<h1>{{all.a.chapter[c.context.c_order].title}}</h1>
				<!--<div class='edit' ng-click='modal("c_subtit")'>edit</div>-->
				<h2>{{all.a.chapter[c.context.c_order].subtitle}}</h2>
			</div>
			<div ng-show='c.context.pid > 0'>
				<!--<div class='edit' ng-click='modal("p_tit")' ng-show="all.a.chapter[c.context.c_order].page[c.context.p_order].menushow == 't'">edit</div>-->
				<h1 ng-show="all.a.chapter[c.context.c_order].page[c.context.p_order].menushow == 't'">{{all.a.chapter[c.context.c_order].page[c.context.p_order].title}}</h1>
				<!--<div class='edit' ng-click='modal("p_text")'>edit</div>-->
				<p>{{all.a.chapter[c.context.c_order].page[c.context.p_order].text}}</p>			
			</div>
			<div id="textend"></div>
		</div>
	</div>
	<div id='order_controls'>
		<div class='inner'>
			<span class='back' ng-hide='c.context.chid === -1' ng-click="pager('back')">back</span>
			<span class='scroll'>scroll</span>
			<span class='next' ng-hide = "(c.context.p_order === all.a.endpage && c.context.c_order === all.a.endchap) && c.context.chid != -1" ng-click="pager('next')">next</span>
		</div>
		<div id="textprog" ng-style="style.delaycss"></div>
	</div>
	<div id="mediafocus" ng-style="style.css" >
		<div class='inner'>
			<img class='overlay' ng-style="{width:style.awidth,height:style.aheight}" ng-if="all.a.resource[c.context.chid][c.context.pid].foverlay.location" ng-src="{{lib}}foverlay/{{all.a.resource[c.context.chid][c.context.pid].foverlay.location}}" />
			<video class="bvideo" ng-style="{width:style.awidth,height:style.aheight}" ng-if="size(all.a.resource[c.context.chid][c.context.pid].bvideo) > 0" loop  preload='auto' muted='{{all.a.resource[c.context.chid][c.context.pid].bvmute}}'>
				<source ng-if="all.a.resource[c.context.chid][c.context.pid].bvideo.v_webm && !isTouch" src="{{all.a.resource[c.context.chid][c.context.pid].bvideo.v_webm}}" type="video/webm">
				<source ng-if="all.a.resource[c.context.chid][c.context.pid].bvideo.v_mp4 && !isTouch" src="{{all.a.resource[c.context.chid][c.context.pid].bvideo.v_mp4}}" type="video/mp4">
				<source ng-if="all.a.resource[c.context.chid][c.context.pid].bvideo.v_ogv && !isTouch" src="{{all.a.resource[c.context.chid][c.context.pid].bvideo.v_ogv}}" type="video/ogg">
				Your browser does not support the video tag.	
			</video>
		</div>	
	</div>
	<div id="media" class="mediaframe" ng-class="{'animate' : c.change === 'animate'}" ng-style="style.css">
		<div class='inner'>
			<img class="pimage" ng-style="{width:style.awidth,height:style.aheight}" ng-if="all.a.resource[c.context.chid][c.context.pid].poster.location && size(all.a.resource[c.context.chid][c.context.pid].bvideo) > 0" ng-src="{{lib}}poster/{{all.a.resource[c.context.chid][c.context.pid].poster.location}}" />
			<img class="fimage" ng-style="{width:style.awidth,height:style.aheight}" ng-if="all.a.resource[c.context.chid][c.context.pid].fimage.location" ng-src="{{lib}}fimage/{{all.a.resource[c.context.chid][c.context.pid].fimage.location}}" />
			<img class='overlay' ng-style="{width:style.awidth,height:style.aheight}" ng-if="all.a.resource[c.context.chid][c.context.pid].foverlay.location" ng-src="{{lib}}foverlay/{{all.a.resource[c.context.chid][c.context.pid].foverlay.location}}" />
			<video class="video" ng-if="size(all.a.resource[c.context.chid][c.context.pid].fvideo) > 0" width="{{width-20}}" height="{{height-20}}" preload='auto'  ng-click="vidbut()" ng-mouseenter="vidfade('fade')" ng-mousemove="vidpeg('over')">
				<source ng-if="all.a.resource[c.context.chid][c.context.pid].fvideo.v_webm" src="{{all.a.resource[c.context.chid][c.context.pid].fvideo.v_webm}}" type="video/webm">
				<source ng-if="all.a.resource[c.context.chid][c.context.pid].fvideo.v_mp4" src="{{all.a.resource[c.context.chid][c.context.pid].fvideo.v_mp4}}" type="video/mp4">
				<source ng-if="all.a.resource[c.context.chid][c.context.pid].fvideo.v_ogv" src="{{all.a.resource[c.context.chid][c.context.pid].fvideo.v_ogv}}" type="video/ogg">
				Your browser does not support the video tag.
			</video>
		</div>
	</div>
	<div id='vidcontrols' ng-if="size(all.a.resource[c.context.chid][c.context.pid].fvideo) > 0" ng-mouseenter="vidpeg('peg')" ng-style="{'left':width/2-c_width/2,'width':c_width}">
		<div class='wrapper'>
			<div class='inner'>
				{{all.a.chapter[c.context.c_order].page[c.context.p_order].text}}
			</div>
			<div class='vcontrols'>
				<div id='vprogress' ng-click="seek($event)">
					<div class='lbar' ng-style="{width:vlprogress+'%'}"></div>
					<div class='bar' ng-style="{width:vprogress+'%'}"></div>
				</div>
				<div class="playbut">
					<div ng-if="!vidpaused" class="icon-pause" ng-click='vpause()'></div>
					<div ng-if="vidpaused" class="icon-play" ng-click='vpause()'></div>
				</div>
				<span class="vidtime">{{vidtime}}</span>
			</div>
		</div>
	</div>
	<div id='volume' ng-click='mute()' ng-hide="all.a.resource[c.context.chid][c.context.pid].bvmute || all.a.resource[c.context.chid][c.context.pid].fimage.location || isTouch">
		<div class='icon-volume-up inner' ng-if="!muted"></div>
		<div class='icon-volume-off inner' ng-if="muted"></div>
	</div>
	<div id="touchtrigger" ng-show="isTouch"></div>
</div>

<div id='sidebar' ng-controller='sidebar' ng-style='{height:height+px}'>
	<div id='backspacer' ng-if="locate.embedded"></div>
	<li id='home'><a class='icon-home' href='/#/home'></a></li>
	<li class='link chapter story title' ng-click='all.go(all.a.story.sid,0,0,-1,-2)'>{{all.a.story.title}}</li>
	<div ng-repeat="(key, chapter) in all.a.chapter | orderBy:'corder'" class='chapter link' ng-click="all.go(chapter.sid,key,-1,chapter.chid,-1)" ng-mouseenter="chaphover(key, chapter)"  ng-mouseleave="hover.show=null" ng-class="{'active':c.context.chid > 0 && chapter.c_order == c.context.c_order}">
		<ul class='pagelist' ng-show="hover.show == chapter.chid">
			<div class='chaphover'>
				<li class='link top' ng-click='all.go(chapter.sid,key,-1,chapter.chid,-1,$event.stopPropagation())'>
					<h3>
						Chapter {{key + 1}}
						<span class='updown' ng-if='user.authorised'>
							<a class='link' ng-hide="chapter.c_order == 0" ng-click="reorder('up',chapter.c_order,'chapter',chapter.chid,null,'swap')">up |</a><a class='link'  ng-hide="chapter.c_order == all.a.chapter.length - 1" ng-click="reorder('down',chapter.c_order,'chapter',chapter.chid,null,'swap')"> dn</a>
						</span>
					</h3>
					<h1>{{chapter.subtitle}}</h1>
				</li>
				<li ng-if='all.a.resource[chapter.chid][-1].poster.location && !all.a.resource[chapter.chid][-1].timage.location' class='image'>
					<a class='link' ng-click='all.go(chapter.sid,key,-1,chapter.chid,-1)'>
						<img ng-src='{{lib}}poster/{{all.a.resource[chapter.chid][-1].poster.location}}'>
					</a>
				</li>
				<li ng-if='all.a.resource[chapter.chid][-1].fimage.location && !all.a.resource[chapter.chid][-1].timage.location' class='image'>
					<a class='link' ng-click='all.go(chapter.sid,key,-1,chapter.chid,-1)'>
						<img ng-src='{{lib}}fimage/{{all.a.resource[chapter.chid][-1].fimage.location}}'>
					</a>
				</li>
				<li ng-if='all.a.resource[chapter.chid][-1].timage.location' class='image'>
					<a class='link' ng-click='all.go(chapter.sid,key,-1,chapter.chid,-1)'>
						<img ng-src='{{lib}}timage/{{all.a.resource[chapter.chid][-1].timage.location}}'>
					</a>
				</li>
				<li ng-if='chapter.mentitle' class='bottom'><a class='link' ng-click='all.go(chapter.sid,key,-1,chapter.chid,-1)'>{{chapter.mentitle}}</a></li> 
			</div>
			<li ng-repeat="(key2, page) in chapter.page | orderBy:'porder'" ng-show="page.menushow == 't' && page.title.length > 0"> 
				<a class='link' ng-click='$event.preventDefault(); $event.stopPropagation(); all.go(page.sid,key,key2,page.chid,page.pid)'>{{page.title}}</a>
			</li>
		</ul>
		<div ng-click='$event.stopPropagation();all.go(chapter.sid,key,-1,chapter.chid,-1,"top"); chaphover(key, chapter); add()' class="wtftag">
			<h3>Chapter {{key + 1}}</h3>
			<div class="chapsub">{{chapter.subtitle}}</div>
		</div>

	</div>
</div>
<div class='clearfix'></div>
<div id='preload'></div>

