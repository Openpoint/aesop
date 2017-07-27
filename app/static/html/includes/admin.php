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
	<div id='admin_add' ng-show="c_admin.context === 'help'" class='adminbar slide_d'>
		<div class='admin_section help'>
			<div class='inner'>
				<h1>Welcome to Aesop</h1>
				<p>Aesop is structured by 'story', 'chapter' and 'page'.<br>The menu system works contextually to your current place in the story and presents only the options available for that place.</p>
				<p>Media options are video, images and audio.<br>These work in varying combinations with each other. For example, if you have a background image or video, you will be able to add overlay audio and an overlay image.<br>If the background video is not muted, the option for overlay audio will disappear and if the video becomes a featured video, the option for an overlay image will disappear. Deleting the media for any particular place in the story will open up all available media options again.</p>
				<p>From a 'chapter' level onwards, media will 'roll over' to the next page.<br>This means that when you add a new page to a chapter, it will have the same media content as the previous page until you override it. This way you can have different text per page overlaying the same media content.<br>The same principle holds true for overlay images and audio.</p>
			</div>
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
			!all.a.resource[c.context.chid][c.context.pid].foverlay.location && 
			!all.a.resource[c.context.chid][c.context.pid].oaudio &&
			c.context.chid > -1 && 
			c.context.pid !==-1 &&
			(size(all.a.resource[c.context.chid][c.context.pid].fimage) == 0 || all.a.resource[c.context.chid][c.context.pid].fcarry) && 
			(size(all.a.resource[c.context.chid][c.context.pid].bvideo) == 0 || all.a.resource[c.context.chid][c.context.pid].bcarry)
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
					<button ng-click="del_r(c_admin.subcontext,c.context.sid,c.context.pid,c.context.chid)">Delete</button>	
				</ul>
			</div>	
														
			<!-- Feature Videos -->
			<div class='fvideo' ng-if="c_admin.subcontext=='fvideo'">

				<div class='admin_subsection' ng-hide="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">					
					<div>
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
				<div class='r_exist admin_subsection' ng-show="size(all.a.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
					<h1>Delete this featured video?</h1>
					<button ng-click="del_r(c_admin.subcontext,c.context.sid,c.context.pid,c.context.chid)">Delete</button>
					<button ng-if="all.a.resource[c.context.chid][c.context.pid].fvideo.v_mp4!=='/static/resources/fvideo/loading.mp4'" ng-click='switcher("fvideo")'>Switch to background</button>										
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
					<button ng-if='
						!all.a.resource[c.context.chid][c.context.pid].oaudio && 
						c.context.pid > 0 &&  
						all.a.resource[c.context.chid][c.context.pid].bvideo.v_mp4!=="/static/resources/bvideo/loading.mp4 &&
					"
					' ng-click='switcher("bvideo")'>Switch to featured</button>									
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
		<!-- Media help section -->
		<div class='admin_section help'>
			<div class='inner' ng-show="c_admin.subcontext=='fimage'">
				<p>Images in a landscape orientation give the best results</p>				
			</div>
			<div class='inner' ng-show="c_admin.subcontext=='foverlay'">
				<p>Overlays are '.png' type image files with transparent backgrounds.<br>Please ensure that their proportions are similar to the media that they are overlaying.<br>The media gets cropped to best fit into the viewers device screen - so be careful of putting important information close to the edges.</p>				
			</div>
			<div class='inner' ng-show="c_admin.subcontext=='bvideo' || c_admin.subcontext=='fvideo'">
				<p>You can upload a video file OR grab video from the internet</p>
				<p>All popular video file formats will upload and be converted to appropriate formats.<br>Try to process uploaded videos into MP4 or WebM before uploading.<br>This will speed up processing.</p>
				<p>To grab a video from the web (eg. Youtube or Vimeo):<br>- Copy the video URL from the address bar in your browser<br>- Paste it into the field and proceed.<br>Most popular video sources will work.</p>
				<p>You can optionally specify at which time points you wish to 'slice' a video</p>			
			</div>
			<div class='inner' ng-show="c_admin.subcontext=='oaudio'">
				<p>You can upload an audio file OR grab audio from the internet</p>
				<p>All popular audio file formats will upload and be converted to appropriate formats.</p>
				<p>To grab audio from the web (eg. Soundcloud, Youtube or Vimeo):<br>- Copy the URL from the address bar in your browser<br>- Paste it into the field and proceed.<br>Most popular audio or video sources will work.</p>
				<p>You can optionally specify at which time points you wish to 'slice' the media</p>			
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
				<span class='label'>Title</span><span> (In the media frame only)</span><br>						
				<input id ='chapter_title' type='textinput' ng-model='chapter_title' required /> 
			</div>
			<div class='infield'>
				<span class='label'>Subtitle</span><span> (In the menu and media frame)</span><br>		
				<input id ='chapter_subtitle'  type='textinput' ng-model='chapter_subtitle' required />
			</div>	
			<div class='infield'>
				<span class='label'>Menu Sub-line</span><span>  (Below the menu teaser image)</span><br>			
				<input type="textinput" id ='chapter_mentitle' ng-model='chapter_mentitle'> 
			</div>		
			<div><input type='submit' value='submit' ng-disabled="editchapter.$invalid"></div>
		</form>
		
		<!-- Page -->
		<form  class='admin_section' name='editpage' ng-submit="edit('page',null,null, null, null, null, page_title, page_text, page_menshow)" ng-if="c.context.pid > 0">
			<h1>Edit the page content</h1>
			<div class='infield' ng-show="page_title">
				<input type="checkbox" name="menshow" ng-model="page_menshow"><span>  List this page in the menu?</span>							
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
<script>var aesop={};aesop.sauce = "http://{{locate.root}}/story?story={{all.a.story.title}}&request=embedded";aesop.isauce="http://{{locate.root}}/static/resources/timage/{{all.a.story.location||'placeholder.jpg'}}";aesop.title="{{all.a.story.title}}";aesop.loader="url(http://{{locate.root}}/static/css/loader.gif)";aesop.size="small";</script><script src="http://{{locate.root}}/static/js/embed.js"></script><div id="aesop_widget"></div>
			</textarea>
		</div>
		<div class='admin_section'>
			<h1>Embed a big widget</h1>
			<textarea style="height:auto;">
<script>var aesop={};aesop.sauce = "http://{{locate.root}}/story?story={{all.a.story.title}}&request=embedded";aesop.isauce="http://{{locate.root}}/static/resources/timage/{{all.a.story.location||'placeholder.jpg'}}";aesop.title="{{all.a.story.title}}";aesop.summary="{{all.a.story.text}}";aesop.loader="url(http://{{locate.root}}/static/css/loader.gif)";aesop.size="big";</script><script src="http://{{locate.root}}/static/js/embed.js"></script><div id="aesop_widget"></div>
			</textarea>
		</div>
	</div>
	<div id='bookmark' ng-if="c_admin.context != 'home' && c_admin.context != 'admin'">

		<span ng-if="c.context.pid===-2">Story Home</span>
		<span ng-if="c.context.pid==-1">Chapter {{c.context.c_order+1}} - Chapter Home</span>
		<span ng-if="c.context.pid > 0">Chapter {{c.context.c_order+1}} - Page {{c.context.p_order+1}}</span>

		<div class='closer' ng-click='c_admin.context=null'>X</div>
	</div>

