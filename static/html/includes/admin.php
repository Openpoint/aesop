	<div id='notification' ng-if='size(notification.message) > 0 && c_admin.context !== "admin" && !modal.show_modal' ng-click='notice("clear")'  style='width:{{width}}px'>
		<div ng-repeat="mes in notification.message" ng-class="mes.class" class='inner'>
			{{mes.message}}
		</div>					
	</div>
	<!-- Main menu add a story,chapter,page -->
	<div id='admin_add' ng-show="c_admin.context === 'add'" class='adminbar slide_d'>
		<div>
			<!-- Story -->
			<form  class='admin_section' name='addstory' ng-submit='submit("story",c_admin.astory_name,null,null,null)' ng-hide="c_admin.achap_name || c_admin.apage_name" novalidate>
				<h1>New Story</h1>
				<div class='infield'>
					<input class='story' id ='story_name' type='textinput' ng-model='c_admin.astory_name' required /> 
					<label ng-hide='c_admin.astory_name' for='story_name'>Story Name</label>
				</div>
				<input type='submit' value='submit' ng-disabled="addstory.$invalid">
			</form>
			
			<!-- Chapter -->
			<form  class='admin_section' name='addchap' ng-submit='submit("chapter",c_admin.achap_name,c.context.sid,null,null)' ng-hide="c_admin.astory_name || c_admin.apage_name || !all.a.story.sid" novalidate>
				<h1>New Chapter</h1>
				<div class='infield'>
					<input class='chapter'  id ='chap_name' type='textinput' ng-model='c_admin.achap_name' required /> 
						<label ng-hide='c_admin.achap_name' for='chap_name'>Chapter Title</label>
					</div>
				<input type='submit' value='submit' ng-disabled="addchap.$invalid">
			</form>
			
			<!-- Page -->
			<form  class='admin_section' name='addpage' ng-submit='submit("page",c_admin.apage_name,c.context.sid,c.context.chid,null)' ng-hide="c_admin.astory_name || c_admin.achap_name || !all.a.story.sid || c.context.chid==-1" novalidate>
				<h1>New Page</h1>
				<div class='infield'>
					<input class='page'  id ='page_name' type='textinput' ng-model='c_admin.apage_name' required /> 
					<label ng-hide='c_admin.apage_name' for='page_name'>Page Title</label>
				</div>
				<input type='submit' value='submit' ng-disabled="addpage.$invalid">
			</form>
		</div>
	</div>

	<!-- Main menu edit a resource -->
	<div id='admin_resource' ng-show="c_admin.context === 'resource'" class='adminbar slide_d' ng-controller="FileUp">
		<ul class='r_type admin_section'>
			<li ng-class="{'active' : c_admin.subcontext=='fimage'}" class='first' ng-show="			
			size(all.a.resource[c.context.chid][c.context.pid].fvideo) == 0 
			&& (size(all.a.resource[c.context.chid][c.context.pid].bvideo) == 0 
			|| all.a.resource[c.context.chid][c.context.pid].bcarry 
			|| all.a.resource[c.context.chid][c.context.pid].fcarry) 
			&& (!all.a.resource[c.context.chid][c.context.pid].poster || all.a.resource[c.context.chid][c.context.pid].bcarry) 			
			" 
			ng-click="add('resource','fimage'); resetInputFile()">Feature Image</li>
			
			<li ng-class="{'active' : c_admin.subcontext=='foverlay'}" ng-show="			
			c.context.chid > -1 
			&& size(all.a.resource[c.context.chid][c.context.pid].fvideo) == 0 
			&& (!all.a.resource[c.context.chid][c.context.pid].poster 
			|| all.a.resource[c.context.chid][c.context.pid].bcarry) 
			&& (size(all.a.resource[c.context.chid][c.context.pid].bvideo) > 0 || all.a.resource[c.context.chid][c.context.pid].fimage.location);			
			" 
			ng-click="add('resource','foverlay'); resetInputFile()">Feature Overlay</li>
			<li ng-class="{'active' : c_admin.subcontext=='fvideo'}" ng-show="
			!all.a.resource[c.context.chid][c.context.pid].foverlay.location && c.context.chid > -1 && (size(all.a.resource[c.context.chid][c.context.pid].fimage) == 0 || all.a.resource[c.context.chid][c.context.pid].fcarry) && (size(all.a.resource[c.context.chid][c.context.pid].bvideo) == 0 || all.a.resource[c.context.chid][c.context.pid].bcarry)
			" ng-click="add('resource','fvideo'); resetInputFile()">Feature Video</li>
			
			<li ng-class="{'active' : c_admin.subcontext=='bvideo'}" ng-show="
			size(all.a.resource[c.context.chid][c.context.pid].fvideo) == 0 && (size(all.a.resource[c.context.chid][c.context.pid].fimage) == 0 || (all.a.resource[c.context.chid][c.context.pid].fcarry && size(all.a.resource[c.context.chid][c.context.pid].foverlay) == 0))" ng-click="add('resource','bvideo'); resetInputFile()
			">Background Video</li>
			
			<li ng-class="{'active' : c_admin.subcontext=='oaudio'}" ng-hide="
			size(all.a.resource[c.context.chid][c.context.pid].fvideo) > 0 || (size(all.a.resource[c.context.chid][c.context.pid].bvideo) > 0 && !all.a.resource[c.context.chid][c.context.pid].bvmute) || c.context.chid < 0" ng-click="add('resource','oaudio'); resetInputFile()
			">Overlay Audio</li>
		
			<li ng-class="{'active' : c_admin.subcontext=='timage'}" ng-show="c.context.pid < 0" ng-click="add('resource','timage'); resetInputFile()">Teaser Image</li>							
		</ul>
		<div class="upload-div admin_section">
								
			<!-- 							Submit forms										-->
								
			<!-- Feature Images -->
			<div class='fimage ' ng-if="c_admin.subcontext=='fimage'">
				<div class='admin_subsection' ng-show ="
				size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) == 0 
				|| all.a.resource[c.context.chid][c.context.pid].fcarry
				">
					<h1>Upload a featured image</h1>
					<div><input class='fimage' ng-model='fimage' type="file" ngf-select="onFileSelect($files,'fimage')"></div>
					<?php include("progress.html"); ?>
				</div>
				<div class='r_exist admin_subsection' ng-show="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 && !all.a.resource[c.context.chid][c.context.pid].fcarry">
					<h1>Delete this featured image?</h1>
					<button ng-click="del_r(c_admin.subcontext,c.context.sid,c.context.pid,c.context.chid)">Delete</button>									
				</div>
				<ul class='r_exist admin_subsection' ng-show="all.a.resource[c.context.chid][c.context.pid].fcarry">
					<h1>Carried From Previous:</h1>
					<li ng-show="all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext].location" ng-class="{carry: all.a.resource[c.context.chid][c.context.pid].fcarry}">{{all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext].location}}</li>
				</ul>
			</div>
			<!-- Overlay Images -->
			<div class='foverlay' ng-if="c_admin.subcontext=='foverlay'">
				<div class='admin_subsection' ng-hide="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
					<h1>Upload a overlay image</h1>
					<div><input class='foverlay' ng-model='foverlay' type="file" ngf-select="onFileSelect($files,'foverlay')"></div>
					<?php include("progress.html"); ?>
				</div>
				<ul class='r_exist admin_subsection' ng-show="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
					<h1>Remove the overlay image:</h1>
					<li ng-click="del_r(c_admin.subcontext,all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext].location,null,c.context.pid,c.context.chid)" ng-show="all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext].location">{{all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext].location}}</li>
				</ul>
			</div>	
														
			<!-- Feature Videos -->
			<div class='fvideo' ng-if="c_admin.subcontext=='fvideo'">

				<div class='admin_subsection' ng-hide="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">					
					<div ng-show="!all.a.resource[c.context.chid][c.context.pid].fcarry">
						<h1>Add a featured video</h1>
						<div class='infield' ng-hide="placeholder[c_admin.subcontext].selectedFiles.length > 0">														
							<input class='bvideourl' id="fvideourl" ng-model='placeholder[c_admin.subcontext].vidi.vurl' type="text" size="40">
							<label ng-if="!placeholder[c_admin.subcontext].vidi.vurl" for="fvideourl">Enter a URL</label>							
						</div>
						<div ng-hide="placeholder[c_admin.subcontext].vidi.vurl || placeholder[c_admin.subcontext].selectedFiles.length > 0">Or upload a file</div>
						<div ng-hide="placeholder[c_admin.subcontext].selectedFiles.length > 0 || placeholder[c_admin.subcontext].vidi.vurl">
							<input class='fvideo' ng-model='fvideo' type="file" ngf-select="onFileSelect($files,'fvideo')">							
						</div>					
					</div>
					<?php include("progress.html"); ?>
				</div>
				<div class='r_exist admin_subsection' ng-show="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 && !all.a.resource[c.context.chid][c.context.pid].fcarry">
					<h1>Delete this featured video?</h1>
					<button ng-click="del_r(c_admin.subcontext,c.context.sid,c.context.pid,c.context.chid)">Delete</button>
					<button ng-click='switcher("fvideo")'>Switch to background</button>										
				</div>
			</div>
								
			<!-- Background Videos -->

			<div class='bvideo' ng-if="c_admin.subcontext === 'bvideo'">
				<div ng-show=" 
					!all.a.resource[c.context.chid][c.context.pid].bcarry && 
					size(all.a.resource[c.context.chid][c.context.pid].oaudio) == 0 &&
					size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0
					">
					<input type="checkbox" name="bvmute" ng-model="c.bvmute" ng-change="bvchange(c.bvmute)"><span>  Mute the background video?</span>
				</div>

				<div class='admin_subsection' ng-hide="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 && !all.a.resource[c.context.chid][c.context.pid].bcarry">
					<div>
						<h1>Add a <span ng-if="all.a.resource[c.context.chid][c.context.pid].bcarry">different </span>background video</h1>
						<div class='infield' ng-hide="placeholder[c_admin.subcontext].selectedFiles.length > 0">
							<input class='bvideourl' id="bvideourl" ng-model='placeholder[c_admin.subcontext].vidi.vurl' type="text" size="40">
							<label ng-if="!placeholder[c_admin.subcontext].vidi.vurl" for="bvideourl">Enter a URL</label>							
						</div>
						<div ng-hide="placeholder[c_admin.subcontext].vidi.vurl || placeholder[c_admin.subcontext].selectedFiles.length > 0">Or upload a file</div>
						<div ng-hide="placeholder[c_admin.subcontext].selectedFiles.length > 0 || placeholder[c_admin.subcontext].vidi.vurl">
							<input class='bvideo' ng-model='bvideo' type="file" ngf-select="onFileSelect($files,'bvideo')">
						</div>
						
					</div>
					<?php include("progress.html"); ?>
				</div>
				<div class='r_exist admin_subsection' ng-show="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 && !all.a.resource[c.context.chid][c.context.pid].bcarry">
					<h1>Delete this background video?</h1>
					<button ng-click="del_r(c_admin.subcontext,c.context.sid,c.context.pid,c.context.chid)">Delete</button>
					<button ng-if='!all.a.resource[c.context.chid][c.context.pid].oaudio && c.context.pid!==-2' ng-click='switcher("bvideo")'>Switch to featured</button>									
				</div>
				<ul class='r_exist admin_subsection' ng-show="all.a.resource[c.context.chid][c.context.pid].bcarry">
					<h1>Carried From Previous:</h1>
					<div class='teaser'>
						<img class='teaser' ng-src="{{lib}}poster/{{all.a.resource[c.context.chid][c.context.pid].poster.location}}" />
					</div>
				</ul>
			</div>
			
			<!-- Audio Overlay -->
			<div class='oaudio' ng-if="c_admin.subcontext=='oaudio'">
				<div ng-hide="
					(!all.a.resource[c.context.chid][c.context.pid].acarry && !all.a.resource[c.context.chid][c.context.pid].astop)||
					(all.a.resource[c.context.chid][c.context.pid].oaudio && !all.a.resource[c.context.chid][c.context.pid].acarry)
				"</div>
					<input type="checkbox" name="astop" ng-model="c.astop" ng-change="astop(c.astop)"><span>  Stop the carried overlay audio?</span>
				</div>
				<div class='admin_subsection' ng-hide="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 1 || all.a.resource[c.context.chid][c.context.pid].acarry">					
					<div>
						<h1>Add overlay audio</h1>
						<div class='infield' ng-hide="placeholder[c_admin.subcontext].selectedFiles.length > 0">														
							<input class='bvideourl' id="oaudurl" ng-model='placeholder[c_admin.subcontext].vidi.vurl' type="text" size="40">
							<label ng-if="!placeholder[c_admin.subcontext].vidi.vurl" for="oaudurl">Enter a URL</label>
						</div>
						<div ng-hide="placeholder[c_admin.subcontext].vidi.vurl || placeholder[c_admin.subcontext].selectedFiles.length > 0">Or upload a file</div>
						<div ng-hide="placeholder[c_admin.subcontext].selectedFiles.length > 0 || placeholder[c_admin.subcontext].vidi.vurl">
							<input class='oaudio' ng-model='oaudio' type="file" ngf-select="onFileSelect($files,'oaudio')">
						</div>
					</div>
					<?php include("progress.html"); ?>
				</div>
				<div class='r_exist admin_subsection' ng-show="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 && !all.a.resource[c.context.chid][c.context.pid].acarry">
					<h1>Delete this background audio?</h1>
					<button ng-click="del_r(c_admin.subcontext,c.context.sid,c.context.pid,c.context.chid)">Delete</button>									
				</div>
				<ul class='r_exist admin_subsection' ng-show="all.a.resource[c.context.chid][c.context.pid].acarry">
					<h1>Audio is carried from the previous page</h1>
				</ul>
			</div>
			
			<!-- Teaser Images -->
			<div class='timage' ng-if="c_admin.subcontext=='timage'">
				
				<div class='admin_subsection' ng-hide="all.a.story.location || c.context.pid !== -2">
					<h1>Upload a teaser image for the story</h1>
					<div><input class='timage' ng-model='timage' type="file" ngf-select="onFileSelect($files,'timage')"></div>
					<?php include("progress.html"); ?>
				</div>
				<div class='r_exist admin_subsection' ng-show="all.a.story.location && c.context.pid === -2">
					<h1>Delete the story's teaser image:</h1>
					<div class='teaser'>
						<img class='teaser' ng-src="{{lib}}timage/{{all.a.story.location}}" />
					</div>
					<button ng-click="del_r(c_admin.subcontext,c.context.sid,c.context.pid,c.context.chid)">Delete</button>	
				</div>
				
				
				
				<div class='admin_subsection' ng-hide="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 || c.context.pid === -2">
					<h1>Override the default menu teaser image</h1>
					<div><input class='timage' ng-model='timage' type="file" ngf-select="onFileSelect($files,'timage')"></div>
					<?php include("progress.html"); ?>
				</div>
				<div class='r_exist admin_subsection' ng-show="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
					<h1>Remove the custom menu teaser image</h1>
					<div class='teaser'>
						<img class='teaser' ng-src="{{lib}}timage/{{all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext].location}}" />
					</div>
					<button ng-click="del_r(c_admin.subcontext,c.context.sid,c.context.pid,c.context.chid)">Delete</button>	
				</div>
				
				
				
				
			</div>							
		</div>
		<div class='admin_section help'>
			<div class='inner' ng-show="c_admin.subcontext=='fimage'">
				<p>Upload a featured image of type:<br />.png, .jpg, .gif<br />Please ensure that the image is in landscape orientation</p>				
			</div>
			<div class='inner' ng-show="c_admin.subcontext=='foverlay'">
				<p>Upload an overlay image of type:<br />.png<br />Please ensure that the image is the same size as the featured image or background video.<br>Please ensure that the image has a transparent background.</p>				
			</div>
			<div class='inner' ng-show="c_admin.subcontext=='bvideo' || c_admin.subcontext=='fvideo'">
				<p><strong>1) </strong>Upload a video poster of type:<br />.png, .jpg<br />Please ensure that the poster is the same dimensions as the video</p>
				<p><strong>2) </strong>Upload a video in the following formats:<br />.ogv, .mp4, .webm</p>				
			</div>
			<p>Your webserver is restricted to a maximum upload size of: {{all.a.maxsize}}</p>
		</div>
	</div>
	<!-- Main menu edit content -->
	<div id='admin_content' ng-show="c_admin.context == 'content'" class='adminbar slide_d'>
		
		<!-- Story -->
		<form  class='admin_section' name='editstory' ng-submit="edit('story',story_name,story_text, null, null, null, null, null, null)" ng-if="c.context.chid == -1">
			<h1>Edit the story content</h1>
			
			<div  class='infield'>
				<div class='label'>Story Name:</div>
				<input id ='story_name' type='textinput' ng-model='story_name' value='' required /> 
			</div>
			<div  class='infield'>
				<div class='label'>Story Summary</div>						
				<textarea id ='story_text' ng-model='story_text' required></textarea>
			</div>
			<div><input type='submit' value='submit' ng-disabled="editstory.$invalid"></div>
		</form>
		
		<!-- Chapter -->
		<form  class='admin_section' name='editchapter' ng-submit="edit('chapter',null,null, chapter_title, chapter_subtitle, chapter_mentitle, null, null, null)" ng-if="c.context.pid == -1">
			<h1>Edit the chapter content</h1>
			<div class='infield'>
				<div class='label'>Title</div>							
				<input id ='chapter_title' type='textinput' ng-model='chapter_title' required /> 
			</div>
			<div class='infield'>
				<span class='label'>Subtitle</span><span>(In the menu and main frame)</span><br>		
				<input id ='chapter_subtitle'  type='textinput' ng-model='chapter_subtitle' required />
			</div>	
			<div class='infield'>
				<span class='label'>Menu Title</span><span>  (Below the menu teaser image)</span><br>			
				<input type="textinput" id ='chapter_mentitle' ng-model='chapter_mentitle'> 
			</div>		
			<div><input type='submit' value='submit' ng-disabled="editchapter.$invalid"></div>
		</form>
		
		<!-- Page -->
		<form  class='admin_section' name='editpage' ng-submit="edit('page',null,null, null, null, null, page_title, page_text, page_menshow)" ng-if="c.context.pid > 0">
			<h1>Edit the page content</h1>
			<div class='infield' ng-show="page_title">
				<input type="checkbox" name="menshow" ng-model="page_menshow"><span>  Show the title in the menu?</span>							
			</div>
			<div class='infield'>
				<div class='label'>Title</div>
				<input id ='page_title' type='textinput' ng-model='page_title' /> 
			</div>
			<div class='infield'>
				<div class='label'>Page Text</div>				
				<textarea id ='page_text' ng-model='page_text' required></textarea>
			</div>
			

			<div><input type='submit' value='submit' ng-disabled="editpage.$invalid"></div>
		</form>
	</div>
	<!-- Main menu edit order -->
	<div id='admin_order' ng-show="c_admin.context == 'order'" class='adminbar slide_d'>
		<ul class='pagelist admin_section' ng-show="show == chapter.chid">
			<h1>Chapter Order</h1>
			<li ng-repeat="chap in all.a.chapter | orderBy:'c_order*1'" >
				<a class='link' ng-click='all.go(chap.sid,chap.c_order,-1,chap.chid,-1)' ng-class="{message: c.context.chid==chap.chid}">{{chap.title}}</a>
				<span class='updown'>
					<a class='link' ng-hide="chap.c_order == 0" ng-click="reorder('up',chap.c_order,'chapter',chap.chid,page.pid,'swap')">up |</a>
					<a class='link' ng-hide="chap.c_order == all.a.chapter.length - 1" ng-click="reorder('down',chap.c_order,'chapter',chap.chid,page.pid,'swap')"> dn</a>
				</span>
			</li>
		</ul>		
		<ul class='pagelist admin_section' ng-show="show == chapter.chid && c.context.chid !=-1">
			<h1>Chap {{c.context.c_order*1+1}} Page Order</h1>
			<li ng-repeat="(key, page) in all.a.chapter[c.context.c_order].page | orderBy:'p_order*1'" > 
				<a class='link' ng-click='all.go(page.sid,c.context.c_order,key,page.chid,page.pid)' ng-class="{message: c.context.pid==page.pid}">{{page.title}}</a>
				<span class='updown'>
					<a class='link' ng-hide="page.p_order == 0" ng-click="reorder('up',page.p_order,'page',c.context.chid,page.pid,'swap')">up |</a>
					<a class='link' ng-hide="page.p_order == all.a.chapter[c.context.c_order].page.length - 1" ng-click="reorder('down',page.p_order,'page',c.context.chid,page.pid,'swap')"> dn</a>
				</span>
			</li>
		</ul>
	</div>
	<!-- Main menu delete a story,chapter,page -->
	<div id='admin_delete' style="width:{{width}}px" ng-show="c_admin.context == 'delete'" class='adminbar slide_d'>
		<div class='admin_section'>
			<h1>Delete</h1>
			<div class='file' ng-click='modal.modal("story")'>Story</div>	
			<div class='file' ng-hide="c.context.chid == -1" ng-click='modal.modal("chap")'>Chapter</div>
			<div class='file' ng-hide="c.context.pid == -1 || c.context.chid == -1" ng-click='modal.modal("page")'>Page</div>
		</div>
	</div>
	<!-- Embed Code -->
	<div id='admin_embed' style="width:{{width}}px" ng-show="c_admin.context == 'embed'" class='adminbar slide_d'>
		<div class='admin_section'>
			<h1>Embed a small widget</h1>
			<textarea style="height:auto;">
<script>var aesop_sauce = "http://{{locate.root}}/#{{locate.path}}?story={{all.a.story.title}}&request=embedded"</script><script src="http://{{locate.root}}/js/embed.min.js"></script><div style="position:relative;width:300px;height:160px;font-family:Helvetica,Sans-serif;overflow:hidden;cursor:pointer" id="aesop_widget" onclick="aesop_open();event.stopPropagation()"><iframe id="aesop_iframe" style="position:absolute;height:100%;width:100%;top:0;left:0" frameBorder = "0" src=""></iframe><img id="aesop_image" style="position:absolute;width:100%;top:0;left:0" src="http://{{locate.root}}/resources/timage/{{all.a.story.location}}" /><div id="aesop_loader" style="position:absolute;width:100%;height:100%;top:0;left:0;background-image: url(http://{{locate.root}}/css/loader.gif);background-repeat:no-repeat;background-position:center center;display:none;"></div><div id="aesop_innards" style="position:relative;width:100%;height:100%" ><div style="font-size:16px;font-weight:bold;text-transform:uppercase;padding:5px;margin:5px;background:rgba(255,255,255,.4);color:white;display:inline-block">{{all.a.story.title}}</div><div style="position:absolute;padding:5px;margin:5px;background:#527489;bottom:0;left:0;color:white;font-size:12px">ENTER</div></div><div id='aesop_back' style="display:none;position:fixed;right:0;top:0;width:90px;margin:5px;padding:5px;background:#8B9FAC;color:white;text-align:center" onclick="aesop_animateout();event.stopPropagation()">close</div></div>						
			</textarea>
		</div>
		<div class='admin_section'>
			<h1>Embed a big widget</h1>
			<textarea style="height:auto;">
<script>var aesop_sauce = "http://{{locate.root}}/#{{locate.path}}?story={{all.a.story.title}}&request=embedded"</script><script src="http://{{locate.root}}/js/embed.min.js"></script>
<div style="position:relative;width:940px;height:705px;font-family:Helvetica,Sans-serif;overflow:hidden;cursor:pointer" id="aesop_widget" onclick="aesop_open();event.stopPropagation()"><iframe id="aesop_iframe" style="position:absolute;height:100%;width:100%;top:0;left:0" frameBorder = "0" src=""></iframe><img id="aesop_image" style="position:absolute;width:100%;top:0;left:0" src="http://{{locate.root}}/resources/timage/{{all.a.story.location}}" /><div id="aesop_loader" style="position:absolute;width:100%;height:100%;top:0;left:0;background-image: url(http://{{locate.root}}/css/loader.gif);background-repeat:no-repeat;background-position:center center;display:none;"></div>
<div id="aesop_innards" style="position:relative;width:100%;height:100%" >
	<div style="font-size:16px;font-weight:bold;text-transform:uppercase;padding:5px;margin:5px;background:rgba(255,255,255,.4);color:white;display:inline-block">{{all.a.story.title}}</div>
	<p style="font-size:12px;padding:5px;margin:5px;color:white">{{all.a.story.text}}</p> 
	<div style="position:absolute;padding:5px;margin:5px;background:#527489;bottom:0;left:0;color:white;font-size:12px">ENTER</div></div><div id='aesop_back' style="display:none;position:fixed;right:0;top:0;width:90px;margin:5px;padding:5px;background:#8B9FAC;color:white;text-align:center" onclick="aesop_animateout();event.stopPropagation()">close</div></div>						
			</textarea>
		</div>
	</div>
	<div id='bookmark' ng-if="c_admin.context != 'home' && c_admin.context != 'admin'">{{all.a.resource[c.context.chid][c.context.pid].astop}}<br>{{all.a.resource[c.context.chid][c.context.pid].acarry}}<br>{{c.context}}</div>

