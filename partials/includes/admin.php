
	<div id='notification' ng-show='size(notification.message) > 0' ng-click='notice("clear")'  style='width:{{width}}px'>
		<div ng-repeat="mes in notification.message" ng-class="mes.class" class='inner'>
			{{mes.message}}
		</div>					
	</div>
	<!-- Main menu add a story,chapter,page -->
	<div id='admin_add' ng-show="c_admin.context == 'add'" class='adminbar slide_d'>
		<div>
			<!-- Story -->
			<form  class='admin_section' name='addstory' ng-submit='submit("story",astory_name,null,null,null)' ng-hide="achap_name || apage_name" novalidate>
				<h1>New Story</h1>
				<div class='infield'>
					<input id ='story_name' type='textinput' ng-model='astory_name' required /> 
					<label ng-hide='astory_name' for='story_name'>Story Name</label>
				</div>
				<input type='submit' value='submit' ng-disabled="addstory.$invalid">
			</form>
			
			<!-- Chapter -->
			<form  class='admin_section' name='addchap' ng-submit='submit("chapter",achap_name,c.context.sid,null,null)' ng-hide="astory_name || apage_name || !all.story.sid" novalidate>
				<h1>New Chapter</h1>
				<div class='infield'>
					<input id ='chap_name' type='textinput' ng-model='achap_name' required /> 
						<label ng-hide='achap_name' for='chap_name'>Chapter Title</label>
					</div>
				<input type='submit' value='submit' ng-disabled="addchap.$invalid">
			</form>
			
			<!-- Page -->
			<form  class='admin_section' name='addpage' ng-submit='submit("page",apage_name,c.context.sid,c.context.chid,null)' ng-hide="astory_name || achap_name || !all.story.sid || c.context.chid==-1" novalidate>
				<h1>New Page</h1>
				<div class='infield'>
					<input id ='page_name' type='textinput' ng-model='apage_name' required /> 
					<label ng-hide='apage_name' for='page_name'>Page Title</label>
				</div>
				<input type='submit' value='submit' ng-disabled="addpage.$invalid">
			</form>
		</div>
	</div>

	<!-- Main menu edit a resource -->
	<div id='admin_resource' ng-show="c_admin.context == 'resource'" class='adminbar slide_d' ng-controller="FileUp">
		<ul class='r_type admin_section'>
			<li ng-class="{'active' : c_admin.subcontext=='fimage'}" class='first' ng-show="
			
			size(all.resource[c.context.chid][c.context.pid].fvideo) == 0 && (size(all.resource[c.context.chid][c.context.pid].bvideo) == 0 || all.resource[c.context.chid][c.context.pid].bcarry || all.resource[c.context.chid][c.context.pid].fcarry) && (!all.resource[c.context.chid][c.context.pid].poster || all.resource[c.context.chid][c.context.pid].bcarry) 
			
			" ng-click="add('resource','fimage'); resetInputFile()">Feature Image</li>
			
			<li ng-class="{'active' : c_admin.subcontext=='foverlay'}" ng-show="
			
			c.context.chid > -1 && size(all.resource[c.context.chid][c.context.pid].fvideo) == 0 && (!all.resource[c.context.chid][c.context.pid].poster || all.resource[c.context.chid][c.context.pid].bcarry) && (size(all.resource[c.context.chid][c.context.pid].bvideo) > 0 || all.resource[c.context.chid][c.context.pid].fimage.location);
			
			" ng-click="add('resource','foverlay'); resetInputFile()">Feature Overlay</li>
			<li ng-class="{'active' : c_admin.subcontext=='fvideo'}" ng-show="
			!all.resource[c.context.chid][c.context.pid].foverlay.location && c.context.chid > -1 && (size(all.resource[c.context.chid][c.context.pid].fimage) == 0 || all.resource[c.context.chid][c.context.pid].fcarry) && (size(all.resource[c.context.chid][c.context.pid].bvideo) == 0 || all.resource[c.context.chid][c.context.pid].bcarry)
			" ng-click="add('resource','fvideo'); resetInputFile()">Feature Video</li>
			
			<li ng-class="{'active' : c_admin.subcontext=='bvideo'}" ng-show="
			size(all.resource[c.context.chid][c.context.pid].fvideo) == 0 && (size(all.resource[c.context.chid][c.context.pid].fimage) == 0 || (all.resource[c.context.chid][c.context.pid].fcarry && size(all.resource[c.context.chid][c.context.pid].foverlay) == 0))" ng-click="add('resource','bvideo'); resetInputFile()
			">Background Video</li>
			
			<li ng-class="{'active' : c_admin.subcontext=='oaudio'}" ng-hide="
			size(all.resource[c.context.chid][c.context.pid].fvideo) > 0 || (size(all.resource[c.context.chid][c.context.pid].bvideo) > 0 && !all.resource[c.context.chid][c.context.pid].bvmute) || c.context.chid < 0" ng-click="add('resource','oaudio'); resetInputFile()
			">Overlay Audio</li>
		
			<li ng-class="{'active' : c_admin.subcontext=='timage'}" ng-show="c.context.pid < 0" ng-click="add('resource','timage'); resetInputFile()">Teaser Image</li>							
		</ul>
		<div class="upload-div admin_section">
								
			<!-- 							Submit forms										-->
								
			<!-- Feature Images -->
			<div class='fimage ' ng-show="c_admin.subcontext=='fimage'">
				<div class='admin_subsection' ng-show ="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) == 0 || all.resource[c.context.chid][c.context.pid].fcarry">
					<h1>Upload a featured image</h1>
					<div><input ng-model='fimage' type="file" ng-file-select="onFileSelect($files,'fimage')" ng-click="resetInputFile()"></div>
					<?php include("progress.html"); ?>
				</div>
				<ul class='r_exist admin_subsection' ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 && !all.resource[c.context.chid][c.context.pid].fcarry">
					<h1>Remove the featured image:</h1>
					<li ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location,null,c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location" ng-class="{carry: all.resource[c.context.chid][c.context.pid].fcarry}">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location}}</li>
				</ul>
				<ul class='r_exist admin_subsection' ng-show="all.resource[c.context.chid][c.context.pid].fcarry">
					<h1>Carried From Previous:</h1>
					<li ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location" ng-class="{carry: all.resource[c.context.chid][c.context.pid].fcarry}">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location}}</li>
				</ul>
			</div>
			<!-- Overlay Images -->
			<div class='foverlay' ng-show="c_admin.subcontext=='foverlay'">
				<div class='admin_subsection' ng-hide="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
					<h1>Upload a overlay image</h1>
					<div><input ng-model='foverlay' type="file" ng-file-select="onFileSelect($files,'foverlay')" ng-click="resetInputFile()"></div>
					<?php include("progress.html"); ?>
				</div>
				<ul class='r_exist admin_subsection' ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
					<h1>Remove the overlay image:</h1>
					<li ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location,null,c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location}}</li>
				</ul>
			</div>	
														
			<!-- Feature Videos -->
			<div class='fvideo' ng-show="c_admin.subcontext=='fvideo'">
				<div class='admin_subsection' ng-hide="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 2">
					<div ng-hide="(placeholder[c_admin.subcontext].selectedFiles.length == 0 || all.resource[c.context.chid][c.context.pid].poster.location) && !all.resource[c.context.chid][c.context.pid].bcarry">
						<h1>Upload a video poster</h1>
						<input  ng-model='tvideo' type="file" ng-file-select="onFileSelect($files,'poster')" ng-click="resetInputFile()">
					</div>
					
					<div ng-if="all.resource[c.context.chid][c.context.pid].poster.location && !all.resource[c.context.chid][c.context.pid].bcarry">
						<h1>Upload video files</h1>
						<div ng-hide="placeholder[c_admin.subcontext].selectedFiles.length > 2"><input  ng-model='fvideo' type="file" ng-file-select="onFileSelect($files,'fvideo')" multiple ng-click="resetInputFile()"></div>					
					</div>
					<?php include("progress.html"); ?>
				</div>
				<ul class='r_exist admin_subsection' ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 || (all.resource[c.context.chid][c.context.pid].poster.location && !all.resource[c.context.chid][c.context.pid].bcarry)">
					<h1>Remove:</h1>
					<div ng-show="all.resource[c.context.chid][c.context.pid].poster.location && size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) == 0">
						<h2>Poster</h2>				
						<li ng-click="del_r('poster',all.resource[c.context.chid][c.context.pid].poster.location,null,c.context.pid,c.context.chid)">{{all.resource[c.context.chid][c.context.pid].poster.location}}</li>
					</div>
					<div ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
						<h2>Videos</h2>
						<li ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_mp4,'v_mp4',c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_mp4">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_mp4}}</li>									
						<li ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_ogv,'v_ogv',c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_ogv">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_ogv}}</li>									
						<li ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_webm,'v_webm',c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_webm">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_webm}}</li>
					</div>
				</ul>
			</div>
								
			<!-- Background Videos -->
			<div class='bvideo' ng-show="c_admin.subcontext=='bvideo'">
				<div ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 && !all.resource[c.context.chid][c.context.pid].bcarry && size(all.resource[c.context.chid][c.context.pid].oaudio) == 0">
					<input type="checkbox" name="bvmute" ng-model="c.bvmute" ng-change="bvchange(c.bvmute)"><span>  Mute the background video?</span>
				</div>
				<div class='admin_subsection' ng-hide="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 2">
					<div ng-hide="(placeholder[c_admin.subcontext].selectedFiles.length == 0 || all.resource[c.context.chid][c.context.pid].poster.location) && !all.resource[c.context.chid][c.context.pid].bcarry">
						<h1>Upload a video poster</h1>
						<input  ng-model='tvideo' type="file" ng-file-select="onFileSelect($files,'poster')" ng-click="resetInputFile()">
					</div>
					<div ng-if="all.resource[c.context.chid][c.context.pid].poster.location && !all.resource[c.context.chid][c.context.pid].bcarry">
						<h1>Upload video files</h1>
						<div ng-hide="placeholder[c_admin.subcontext].selectedFiles.length > 2"><input  ng-model='bvideo' type="file" ng-file-select="onFileSelect($files,'bvideo')" multiple ng-click="resetInputFile()"></div>
					</div>
					<?php include("progress.html"); ?>
				</div>
				<ul class='r_exist admin_subsection' ng-show="(size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 || all.resource[c.context.chid][c.context.pid].poster.location) && !all.resource[c.context.chid][c.context.pid].bcarry">
					<h1>Remove:</h1>
					<div ng-show="all.resource[c.context.chid][c.context.pid].poster.location && size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) == 0">
						<h2>Poster</h2>				
						<div class='file' ng-click="del_r('poster',all.resource[c.context.chid][c.context.pid].poster.location,null,c.context.pid,c.context.chid)" ng-class="{carry: all.resource[c.context.chid][c.context.pid].bcarry}">{{all.resource[c.context.chid][c.context.pid].poster.location}}</div>
					</div>

					<div ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
						<h2>Videos</h2>
						<div class='file' ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_mp4,'v_mp4',c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_mp4" ng-class="{carry: all.resource[c.context.chid][c.context.pid].bcarry}">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_mp4}}</div>									
						<div class='file' ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_ogv,'v_ogv',c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_ogv" ng-class="{carry: all.resource[c.context.chid][c.context.pid].bcarry}">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_ogv}}</div>									
						<div class='file' ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_webm,'v_webm',c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_webm" ng-class="{carry: all.resource[c.context.chid][c.context.pid].bcarry}">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_webm}}</div>
					</div>
				</ul>
				<ul class='r_exist admin_subsection' ng-show="all.resource[c.context.chid][c.context.pid].bcarry">
					<h1>Carried From Previous:</h1>
						<div ng-show="all.resource[c.context.chid][c.context.pid].poster.location">
						<h2>Poster</h2>				
						<div class='file' ng-class="{carry: all.resource[c.context.chid][c.context.pid].bcarry}">{{all.resource[c.context.chid][c.context.pid].poster.location}}</div>
					</div>
					<div ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
						<h2>Videos</h2>
						<div class='file' ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_mp4" ng-class="{carry: all.resource[c.context.chid][c.context.pid].bcarry}">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_mp4}}</div>									
						<div class='file' ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_ogv" ng-class="{carry: all.resource[c.context.chid][c.context.pid].bcarry}">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_ogv}}</div>									
						<div class='file' ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_webm" ng-class="{carry: all.resource[c.context.chid][c.context.pid].bcarry}">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].v_webm}}</div>
					</div>
				</ul>
			</div>
			
			<!-- Audio Overlay -->
			<div class='oaudio' ng-show="c_admin.subcontext=='oaudio'">
				<div ng-hide="size(all.resource[c.context.chid][c.context.pid].oaudio) > 0 && !all.resource[c.context.chid][c.context.pid].acarry"</div>
					<input type="checkbox" name="astop" ng-model="c.astop" ng-change="astop(c.astop)"><span>  Stop the background audio here?</span>
				</div>
				<div class='admin_subsection' ng-hide="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 1 || all.resource[c.context.chid][c.context.pid].acarry">					
					<div>
						<h1>Upload audio files</h1>
						<div ng-hide="placeholder[c_admin.subcontext].selectedFiles.length > 1"><input  ng-model='oaudio' type="file" ng-file-select="onFileSelect($files,'oaudio')" multiple ng-click="resetInputFile()"></div>
					</div>
					<?php include("progress.html"); ?>
				</div>
				<ul class='r_exist admin_subsection' ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0 && !all.resource[c.context.chid][c.context.pid].acarry">
					<h1>Remove:</h1>
					<div ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
						<h2>Audio</h2>
						<li ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_mp3,'a_mp3',c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_mp3">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_mp3}}</li>									
						<li ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_ogg,'a_ogg',c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_ogg">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_ogg}}</li>									
					</div>
				</ul>
				<ul class='r_exist admin_subsection' ng-show="all.resource[c.context.chid][c.context.pid].acarry">
					<h1>Carried From Previous:</h1>
					<div ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
						<h2>Audio</h2>
						<li class='carry' ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_mp3">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_mp3}}</li>
						<li class='carry' ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_ogg">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].a_ogg}}</li>	
					</div>
				</ul>
			</div>
			
			<!-- Teaser Images -->
			<div class='timage' ng-show="c_admin.subcontext=='timage'">
				<div class='admin_subsection' ng-hide="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
					<h1>Upload a teaser image</h1>
					<div><input ng-model='timage' type="file" ng-file-select="onFileSelect($files,'timage')" ng-click="resetInputFile()"></div>
					<?php include("progress.html"); ?>
				</div>
				<ul class='r_exist admin_subsection' ng-show="size(all.resource[c.context.chid][c.context.pid][c_admin.subcontext]) > 0">
					<h1>Remove the teaser image:</h1>
					<div class='file' ng-click="del_r(c_admin.subcontext,all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location,null,c.context.pid,c.context.chid)" ng-show="all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location">{{all.resource[c.context.chid][c.context.pid][c_admin.subcontext].location}}</div>
				</ul>
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
			<p>Your webserver is restricted to a maximum upload size of: {{all.maxsize}}</p>
		</div>
	</div>
	<!-- Main menu edit content -->
	<div id='admin_content' ng-show="c_admin.context == 'content'" class='adminbar slide_d'>
		
		<!-- Story -->
		<form  class='admin_section' name='editstory' ng-submit="edit('story','title',story_name);edit('story','text',story_text)" ng-show="c.context.chid == -1" novalidate>
			<h1>Edit the story content</h1>
			<div><label>Story Name</label></div>
			<input id ='story_name' type='textinput' ng-model='story_name' required /> 
			
			<div><label>Edit Story Summary</label></div>							
			<textarea id ='story_text' ng-model='story_text' required> </textarea>
			<div><input type='submit' value='submit' ng-disabled="editstory.$invalid"></div>
		</form>
		<!-- Chapter -->
		<form  class='admin_section' name='editchapter' ng-submit="edit('chapter','title',chapter_title);edit('chapter','subtitle',chapter_subtitle);edit('chapter','mentitle',chapter_mentitle)" ng-show="c.context.pid == -1" novalidate>
			<h1>Edit the chapter content</h1>
			<div><label><strong>Title</strong> (In the main frame)</label></div>							
			<input id ='chapter_title' type='textinput' ng-model='chapter_title' required /> 

			<div><label><strong>Subtitle</strong>  (In the menu and main frame)</label></div>			
			<input id ='chapter_subtitle'  type='textinput' ng-model='chapter_subtitle' required />
				

			<div><label><strong>Menu Title</strong>  (Below the menu teaser image)</label></div>				
			<input type="textinput" id ='chapter_mentitle' ng-model='chapter_mentitle'> 
				
			<div><input type='submit' value='submit' ng-disabled="editchapter.$invalid"></div>
		</form>
		<!-- Page -->
		<form  class='admin_section' name='editpage' ng-submit="edit('page','title',page_title);edit('page','text',page_text);edit('page','menushow',page_menshow)" ng-show="c.context.pid > 0" novalidate>
			<h1>Edit the page content</h1>
			<input type="checkbox" name="menshow" ng-model="page_menshow"><span>  Show the title?</span>							
			
			<div><label>Title</label></div>
			<input id ='page_title' type='textinput' ng-model='page_title' required /> 
			
			<div><label>Edit Page Text</label></div>				
			<textarea id ='page_text' ng-model='page_text' required></textarea>
			

			<div><input type='submit' value='submit' ng-disabled="editpage.$invalid"></div>
		</form>
	</div>
	<!-- Main menu edit page order -->
	<div id='admin_order' ng-show="c_admin.context == 'order' && c.context.chid !=-1" class='adminbar slide_d'>
		<ul class='pagelist admin_section' ng-show="show == chapter.chid">
			<li ng-repeat="(key, page) in all.chapter[c.context.c_order].page | orderBy:'p_order*1'" > 
				<a class='link' ng-click='go(page.sid,c.context.c_order,key,page.chid,page.pid)'>{{page.title}}</a>
				<span class='updown'>
					<a class='link' ng-hide="page.p_order == 0" ng-click="reorder('up',page.p_order,'page',c.context.chid,page.pid,'swap')">up |</a>
					<a class='link' ng-hide="page.p_order == all.chapter[c.context.c_order].page.length - 1" ng-click="reorder('down',page.p_order,'page',c.context.chid,page.pid,'swap')"> dn</a>
				</span>
			</li>
		</ul>
	</div>
	<!-- Main menu delete a story,chapter,page -->
	<div id='admin_delete' style="width:{{width}}px" ng-show="c_admin.context == 'delete'" class='adminbar slide_d'>
		<div class='admin_section'>
			<h1>Delete</h1>
			<div class='file' ng-click='modal("story")'>Story</div>	
			<div class='file' ng-hide="c.context.chid == -1" ng-click='modal("chap")'>Chapter</div>
			<div class='file' ng-hide="c.context.pid == -1 || c.context.chid == -1" ng-click='modal("page")'>Page</div>
		</div>
	</div>
	<!-- Embed Code -->
	<div id='admin_embed' style="width:{{width}}px" ng-show="c_admin.context == 'embed'" class='adminbar slide_d'>
		<div class='admin_section'>
			<h1>Embed a small widget</h1>
			<textarea style="height:auto;">
<script>var aesop_sauce = "http://{{locate.root}}/#{{locate.path}}?story={{all.story.title}}&request=embedded"</script><script src="http://{{locate.root}}/js/embed.min.js"></script><div style="position:relative;width:300px;height:160px;font-family:Helvetica,Sans-serif;overflow:hidden;cursor:pointer" id="aesop_widget" onclick="aesop_open();event.stopPropagation()"><iframe id="aesop_iframe" style="position:absolute;height:100%;width:100%;top:0;left:0" frameBorder = "0" src=""></iframe><img id="aesop_image" style="position:absolute;width:100%;top:0;left:0" src="http://{{locate.root}}/resources/timage/{{all.story.location}}" /><div id="aesop_loader" style="position:absolute;width:100%;height:100%;top:0;left:0;background-image: url(http://{{locate.root}}/css/loader.gif);background-repeat:no-repeat;background-position:center center;display:none;"></div><div id="aesop_innards" style="position:relative;width:100%;height:100%" ><div style="font-size:16px;font-weight:bold;text-transform:uppercase;padding:5px;margin:5px;background:rgba(255,255,255,.4);color:white;display:inline-block">{{all.story.title}}</div><div style="position:absolute;padding:5px;margin:5px;background:#527489;bottom:0;left:0;color:white;font-size:12px">ENTER</div></div><div id='aesop_back' style="display:none;position:fixed;right:0;top:0;width:90px;margin:5px;padding:5px;background:#8B9FAC;color:white;text-align:center" onclick="aesop_animateout();event.stopPropagation()">close</div></div>						
			</textarea>
		</div>
		<div class='admin_section'>
			<h1>Embed a big widget</h1>
			<textarea style="height:auto;">
<script>var aesop_sauce = "http://{{locate.root}}/#{{locate.path}}?story={{all.story.title}}&request=embedded"</script><script src="http://{{locate.root}}/js/embed.min.js"></script>
<div style="position:relative;width:940px;height:705px;font-family:Helvetica,Sans-serif;overflow:hidden;cursor:pointer" id="aesop_widget" onclick="aesop_open();event.stopPropagation()"><iframe id="aesop_iframe" style="position:absolute;height:100%;width:100%;top:0;left:0" frameBorder = "0" src=""></iframe><img id="aesop_image" style="position:absolute;width:100%;top:0;left:0" src="http://{{locate.root}}/resources/timage/{{all.story.location}}" /><div id="aesop_loader" style="position:absolute;width:100%;height:100%;top:0;left:0;background-image: url(http://{{locate.root}}/css/loader.gif);background-repeat:no-repeat;background-position:center center;display:none;"></div>
<div id="aesop_innards" style="position:relative;width:100%;height:100%" >
	<div style="font-size:16px;font-weight:bold;text-transform:uppercase;padding:5px;margin:5px;background:rgba(255,255,255,.4);color:white;display:inline-block">{{all.story.title}}</div>
	<p style="font-size:12px;padding:5px;margin:5px;color:white">{{all.story.text}}</p> 
	<div style="position:absolute;padding:5px;margin:5px;background:#527489;bottom:0;left:0;color:white;font-size:12px">ENTER</div></div><div id='aesop_back' style="display:none;position:fixed;right:0;top:0;width:90px;margin:5px;padding:5px;background:#8B9FAC;color:white;text-align:center" onclick="aesop_animateout();event.stopPropagation()">close</div></div>						
			</textarea>
		</div>
	</div>

