"use strict";

// controller for the admin menu
Aesop.controller('admin', ['$scope','$cookieStore','$timeout','$location','setter','getter','modal','auth',function($scope,$cookieStore,$timeout,$location,setter,getter,modal,auth) {

	$scope.modal=modal;

	$scope.placeholder={

	};

	$scope.notification={};
	$scope.notification.message=null;
	$scope.notification.type=null;

	//login
	$scope.login = function(user,password){
		$cookieStore.remove('user');
		function fetchcookie(){
			$scope.user=$cookieStore.get('user');
			if(typeof $scope.user == 'undefined'){
				$timeout(function(){
					fetchcookie();
				},10)
			}else{
				$timeout(function(){
					if(!$scope.locate.embedded){
						$scope.c_admin.getqueue();
					}
					$scope.wsize();
					return null;
				},10);
			}
		}
		auth.login(user,password).then(function(data){
				if(data == 'logged in'){
					modal.toggle();
					fetchcookie();
				}else{
					$cookieStore.remove('user');
					alert('incorrect details');
				}
		});

	}

	//logout
	$scope.c.logout = function(){
		$timeout(function(){
			//if(typeof $scope.user!=='undefined' && $scope.user.authorised){
				$cookieStore.remove('user');
				$scope.user={};
				$scope.c_admin.context=null;
				$scope.wsize();

			//}
		});
	}
	//reset a password from url token
	$scope.setpass=function(){

		setter.setpass($scope.user.uid,$scope.user.authtoken,$scope.c_admin.pass1).then(function(data){
			if(data==='success'){
				$scope.modal.modals.reset=false;
				$scope.modal.modals.login=true;
			}
		})
	}
	//reset a password from login
	$scope.newpass=function(usern){
		setter.newpass(usern).then(function(data){
			$scope.notification.message=data;
		})
	}
	/*-------------------------- Maintain login over sessions -----------------------------------*/
	//verify that the cookie is the real thing by checking authtoken
	if($cookieStore.get('user')){
		var foou=$cookieStore.get('user');

		auth.verify(foou.uid).then(function(data){
			if(data == foou.authtoken){
				$scope.user=foou;
				if(foou.method==='reset'){
					$cookieStore.remove('user');
					modal.show_modal=true;
					modal.modals.reset=true;
					$timeout(function(){
						$location.url($location.path());
					})
				}
				$scope.c.user=$scope.user.authorised;
				$scope.c_admin.seenqueue();
				$scope.wsize();

			}else{
				$scope.user={}
				$cookieStore.remove('user');
			}
		})


	}else{
		$scope.user={};
	}


	/*-------------------------- Various editorial functions -----------------------------------*/

	//delete a resource from the database and filesystem
	$scope.del_r = function(type,sid,pid,chid){

		setter.deleteres(type,sid,pid,chid).then(function(data){
			$scope.reload();
		})
	}
	$scope.kill_r = function(prid){
		setter.killprocess(prid);
	}
	//retry a failed job
	$scope.retry = function(rid){
		setter.retry(rid);
	}
	//delete a story, chapter or page
	$scope.del=function(item){
		if(item==='page'){
			setter.deletepage($scope.c.context.p_order,$scope.c.context.pid,$scope.c.context.chid,$scope.c.context.sid).then(function(data){
				if(data==='success'){
					var contex=JSON.parse(JSON.stringify($scope.c.context));
					if(contex.p_order===0){
						contex.pid=-1;
					}else{
						contex.pid=$scope.all.a.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order-1].pid;
					}
					contex.p_order--;
					$cookieStore.put('context',contex);
					$scope.modal.modal();
					$scope.reload();

				}
			})
		}
		if(item==='chapter'){
			setter.deletechapter($scope.c.context.c_order,$scope.c.context.chid,$scope.c.context.sid).then(function(data){
				if(data==='success'){
					var contex=JSON.parse(JSON.stringify($scope.c.context));
					if(contex.c_order===0){
						contex={
							'sid':$scope.c.context.sid,
							'c_order':0,
							'chid':-1,
							'p_order':0,
							'pid':-2
						}
					}else{
						contex.chid=$scope.all.a.chapter[$scope.c.context.c_order-1].chid;
						contex.c_order=$scope.c.context.c_order-1;
						contex.pid=-1;
						contex.p_order=-1;
					}
					$cookieStore.put('context',contex);
					$scope.modal.modal();
					$scope.reload();

				}
			})
		}
		if(item==='story'){
			setter.deletestory($scope.c.context.sid).then(function(data){
				if(data==='success'){
					$cookieStore.remove('context');
					$scope.modal.modal();
					window.location='/#/home';

				}
			})
		}
	}


	//add a new story, chapter or page
	$scope.submit=function(context,title,sid,chid,pid){

		$scope.c_admin.apage_name='';
		$scope.c_admin.astory_name='';
		$scope.c_admin.achap_name='';
		/*
		if($scope.all.a && $scope.all.a.chapter){
			var c_order=$scope.all.a.chapter.length;
			if(typeof ($scope.all.a.chapter[$scope.c.context.c_order].page) !='undefined'){
				var p_order=$scope.all.a.chapter[$scope.c.context.c_order].page.length;
			}else{
				var p_order=0;
			}
		}else{
			var c_order=0;
			var p_order=0;
		}
		* */
		if (context==='chapter'){
			var c_order=$scope.c.context.c_order+1;
			var p_order=-1;
		}else if(context==='page'){
			var c_order=$scope.c.context.c_order;
			var p_order=$scope.c.context.p_order+1;
		}
		setter.add(context,title,sid,chid,pid,c_order,p_order).then(function(data){


			if (data.result.indexOf("duplicate key value violates unique constraint") > -1){
				$scope.notice('warning',"'"+title+"' already exists - please add an unique title");
			}else{
				if(data.result == 'success chapter'){
					var contex=JSON.parse(JSON.stringify($scope.c.context));
					contex.pid=-1;
					contex.p_order=-1;
					contex.c_order=data.c_order*1;
					contex.chid=data.chid*1;
					$cookieStore.put('context',contex);
					$scope.reload();
				}
				if(data.result == 'success story'){
					window.location='/#/story?story='+data.title;
					$scope.c.context.sid=data.sid;
				}
				if(data.result == 'success page'){

					var contex=JSON.parse(JSON.stringify($scope.c.context));
					contex.pid=data.pid*1;
					contex.p_order=data.p_order*1;
					$cookieStore.put('context',contex);
					$scope.reload();
				}
			}
		});
	}

	//change the background video mute state
	$scope.bvchange=function(x){
		if(x){
			var value='t';
		}else{
			var value='f';
		}
		//$scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute=x;
		setter.redit('resource', 'bvmute', value, $scope.c.context).then(function(data){

			if(data.result==="success"){
				$scope.reload();
			}
		})
	}

	//stop carrying the background audio
	$scope.astop=function(x){
		if(x){
			var value='t';
		}else{
			var value='f';
		}
		setter.redit('resource', 'astop', value, $scope.c.context).then(function(data){
			if(data.result=="success"){
				$scope.reload();
			}
		})
	}

	//edit page content
	$scope.edit=function(type,story_name,story_text, chapter_title, chapter_subtitle, chapter_mentitle, page_title, page_text, page_menshow){

		if(page_menshow && type == "page"){
			page_menshow = 't';
		}else if(type == "page"){
			page_menshow = 'f';
		}

		setter.edit(type,story_name,story_text, chapter_title, chapter_subtitle, chapter_mentitle, page_title, page_text, page_menshow, $scope.c.context).then(function(data){
			if(data.result==="success"){
				$scope.reload();
			}
		})
	}
	//switch between featured/background video
	$scope.switcher=function(type){
		setter.switcher(type,$scope.c.context.sid,$scope.c.context.chid,$scope.c.context.pid).then(function(){
			$scope.reload();
		})
	}
	//change the order of a chapter or page
	$scope.reorder = function(direction,order,type,chid,pid,action){
		setter.order(direction,order,type,$scope.c.context.sid,chid,pid,action).then(function(data){
			if (data.status == 'success'){

				delete(data.status);


				if(data.c_order === 'undefined'){
					data.c_order=$scope.c.context.c_order;
				};
				if(data.p_order === 'undefined'){
					data.p_order=$scope.c.context.p_order;
				};
				if(data.pid === 'undefined'){
					data.pid=$scope.c.context.pid;
					if(data.pid==-2){
						data.pid=-1;
					}
				};
				for (var key in data) {
					data[key]=data[key]*1;
				}
				$cookieStore.put('context',data);
				$scope.reload();
			}else{
				console.log(data);
			}
		})
	}
	//set values for content edit forms
	$scope.$watch("all.a.story.title",function(){

		if($scope.all.a && $scope.all.a.story && $scope.all.a.story.title){
			subcon();
			$scope.story_name=$scope.all.a.story.title;
			$scope.story_text=$scope.all.a.story.text;
		}
	})
	$scope.$watch("c.context.chid",function(){
		if($scope.all.a && $scope.c.context.chid && $scope.c.context.c_order >=0){
			subcon();
			$scope.chapter_title=$scope.all.a.chapter[$scope.c.context.c_order].title;
			$scope.chapter_subtitle=$scope.all.a.chapter[$scope.c.context.c_order].subtitle;
			$scope.chapter_mentitle=$scope.all.a.chapter[$scope.c.context.c_order].mentitle;
		}
	})
	$scope.$watch("c.context.pid",function(){
		if($scope.all.a && $scope.c.context.pid && $scope.c.context.p_order >= 0){
			subcon();
			$scope.page_title=$scope.all.a.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].title;
			$scope.page_text=$scope.all.a.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].text;
			if($scope.all.a.chapter[$scope.c.context.c_order].page[$scope.c.context.p_order].menushow == 't'){
				$scope.page_menshow=true;
			}else{
				$scope.page_menshow=false;
			}
		}
		//set audio contexts in the forms
		if(typeof $scope.c.context.pid !== 'undefined' && $scope.all.a && $scope.all.a.story && typeof $scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute != 'undefined'){
			$scope.c.bvmute=$scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvmute;
		}
		if(typeof $scope.c.context.pid !== 'undefined' && $scope.all.a && $scope.all.a.story && typeof $scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].astop != 'undefined'){
			$scope.c.astop=$scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].astop;
		}
	})
	//helper to set the subcontext on page change
	function subcon(){
		if($scope.all.a.resource[$scope.c.context.chid]){
			//console.log($scope.c.context.chid);
			//console.log($scope.c.context.pid);
			if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].bvideo){
				$scope.c_admin.subcontext='bvideo';
			}else if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].fvideo){
				$scope.c_admin.subcontext='fvideo';
			}else if($scope.all.a.resource[$scope.c.context.chid][$scope.c.context.pid].fimage){
				$scope.c_admin.subcontext='fimage';
			}else{
				$scope.c_admin.subcontext='fimage';
			}
			if($scope.c_admin.subcontext==='bvideo'||$scope.c_admin.subcontext==='fvideo'||$scope.c_admin.subcontext==='oaudio'){
				$scope.c_admin.setvid();
			}
		}
	}

	//set the context markers for admin menu sub-types on click
	$scope.add = function(type,sub){
		if(!sub){
			if($scope.c_admin.context!=type){
				$scope.c_admin.context=type;
			}else{
				$scope.c_admin.context=null;
			}
			if(type == 'resource'){
				subcon();
			}
			if(type == 'admin'){
				if(sub){
					$scope.c_admin.subcontext=sub;
				}else{
					$scope.c_admin.subcontext='jobs';
				}
			}
		}else if(!type){
			$scope.c_admin.context=null;
		}else{
			$scope.c_admin.subcontext=sub;
			$scope.placeholder[$scope.c_admin.subcontext]={};
			if(sub==='bvideo'||sub==='fvideo'||sub==='oaudio'){
				$scope.c_admin.setvid();
			}
		}
	}


	//the message alert system - types are message, warning, error, success
	$scope.notice=function(type,message){

		if (type === 'clear'){
			$scope.notification.message={};
		}else{
			if(typeof message === 'string'){
				var mes = [];
				mes[0]={};
				mes[0]['message']=message;
				mes[0]['class']=type;
				message = mes;
			}
			$scope.notification.message=message;
		}
	}
	//clear the messages on context change
	$scope.$watch('c_admin.context',function(){
		$scope.notification.message=null;
	})
	//helper to reset the video forms
	$scope.c_admin.setvid=function(){
		if(typeof $scope.placeholder[$scope.c_admin.subcontext] === 'undefined'){
			$scope.placeholder[$scope.c_admin.subcontext]={};
		}

		$scope.placeholder[$scope.c_admin.subcontext].vidi={
			start:{
				h:0,
				m:0,
				s:0
			},
			end:{
				h:0,
				m:0,
				s:0
			},
			vurl:null
		}
	}

	//reload story details after admin function
	$scope.reload=function(home){
		$scope.c_admin.seenqueue();
		var contex={}
		if(!home){
			contex.sid=$scope.c.context.sid;
		}
		$scope.c.context={};
		$timeout(function(){
			$scope.c.context=contex;
		});
	}

	//get the process queue
	$scope.c_admin.getqueue=function(){
		$timeout.cancel(Asp.poller);
		$timeout.cancel(Asp.poller2);
		$scope.c_admin.polling0=false;
		getter.getqueue($scope.user.uid).then(function(data){

			$scope.c_admin.queue={
				running:[],
				queued:[],
				complete:[],
				error:[]
			};
			for (var i = 0; i < data.length; ++i) {
				if(data[i].status==='error'){
					data[i].message=JSON.parse(data[i].message);
					$scope.c_admin.queue.error.push(data[i]);
				}else if(data[i].status==='complete'){

					$scope.c_admin.queue.complete.push(data[i]);
				}else if(data[i].status==='queued'){
					$scope.c_admin.queue.queued.push(data[i]);
				}else{
					data[i].message=JSON.parse(data[i].message);
					$scope.c_admin.queue.running.push(data[i]);
				}
			}
			if($scope.c_admin.queue.running.length > 0 || $scope.c_admin.queue.queued.length > 0){
				$scope.c_admin.polling0=false;
				$timeout.cancel(Asp.poller);
				$timeout.cancel(Asp.poller2);
				Asp.poller2=$timeout(function(){
					$scope.c_admin.getqueue();
				},5000)
			}else{
				$scope.c_admin.polling0=true;
				$scope.c_admin.poller();
			}
		})
	}

	//tag done jobs as seen in the queue
	$scope.c_admin.seenqueue=function(){
		setter.seenqueue($scope.user.uid).then(function(data){
			$scope.c_admin.getqueue();
		})
	}
	//lightweight poller to check if there is content being processed
	$scope.c_admin.poller=function(){
		$timeout.cancel(Asp.poller);
		$timeout.cancel(Asp.poller2);
		if($scope.c_admin.polling0){
			getter.poller().then(function(data){
				$timeout.cancel(Asp.poller);
				$timeout.cancel(Asp.poller2);
				if(data > 0){
					$scope.c_admin.getqueue();
				}else{
					Asp.poller=$timeout(function(){
						$scope.c_admin.poller();
					},5000)
				}
			})
		}
	}

}])
//controller for detailed admin panel
.controller('admin2', ['$scope','getter',function($scope,getter) {
	getter.storylist().then(function(data){
		$scope.storynames=data;
	})
}])
//controller for user admin
.controller('adminusers', ['$scope','getter','setter',function($scope,getter,setter) {
	$scope.getusers=function(){
		getter.users().then(function(data){;
			$scope.allusers=data;
		})
	}
	$scope.getusers();

	//add a new user
	$scope.newuser=function(){
		setter.newuser($scope.newusername,$scope.newusermail,$scope.mailmessage).then(function(data){
			$scope.notice(null,data);
			$scope.getusers();
		});
		$scope.newusername=null;
		$scope.newusermail=null;

	}
}])
//controller for file uploads

Asp.page.story.controller('FileUp', ['$scope', '$http', '$timeout', 'Upload',function($scope, $http, $timeout, Upload) {



	$scope.c_admin.setvid();
	$scope.onFileSelect = function($files,input) {

		$scope.input=$('.admin_subsection input.'+input)[0];
		$scope.uploading=false;
		if($files.length > 0){

			//get the file mimetype
			var mime=$files[0].type;
			mime=mime.split('/');
			$scope.placeholder[$scope.c_admin.subcontext]={};

			var thisfile=$scope.placeholder[$scope.c_admin.subcontext].selectedFiles=$files;
			$scope.placeholder[$scope.c_admin.subcontext].progress = 0;

			var mes = [];
			$scope.kick=function(mes){
				$scope.placeholder[$scope.c_admin.subcontext]={};
				$scope.input.value=null;
				$files=null;
				if($scope.c_admin.subcontext === 'fvideo' || $scope.c_admin.subcontext == 'bvideo'){
					$scope.c_admin.setvid();
				}
				$scope.notice(null,mes);

			}

			//set the allowed upload file types
			$scope.placeholder[$scope.c_admin.subcontext].allowed = [];
			if($scope.c_admin.subcontext === 'fimage' || $scope.c_admin.subcontext === 'timage'){
				if(mime[0]!=='image'){
					mes[0]={
						message:thisfile[0].name+" is not an image file and has been removed.",
						class:'warning'
					};
					$scope.kick(mes);
					return;
				}
			}

			if($scope.c_admin.subcontext === 'foverlay'){
				if(mime[1]!=='png'){
					mes[0]={
						message:thisfile[0].name+" is not of the type '.png' and has been removed.",
						class:'warning'
					};
					$scope.kick(mes);
					return;
				}
			}

			if($scope.c_admin.subcontext === 'fvideo' || $scope.c_admin.subcontext == 'bvideo'){
				if(mime[0]!=='video'){
					mes[0]={
						message:thisfile[0].name+" is not a video file and has been removed.",
						class:'warning'
					};
					$scope.kick(mes);
					return;
				}else{
					$scope.c_admin.setvid();
				}
			}
			if($scope.c_admin.subcontext === 'oaudio'){
				if(mime[0]!=='audio' && mime[1]!=='ogg'){
					mes[0]={
						message:thisfile[0].name+" is not an audio file and has been removed.",
						class:'warning'
					};
					$scope.kick(mes);
					return;
				}else{
					$scope.c_admin.setvid();
				}
			}

			//check files for size and type

			if(thisfile[0].size > $scope.all.a.maxbyte){
				mes[0]={
					message:thisfile[0].name+" is too big to upload and has been removed. Max size = "+$scope.all.a.maxsize,
					class:'warning'
				};
				$scope.kick(mes);
				return;
			};





			// get thumbnail preview

			if (window.FileReader && thisfile[0].type.indexOf('image') > -1) {

				var fileReader = new FileReader();
				fileReader.readAsDataURL(thisfile[0]);
				var loadFile = function(fileReader) {
					fileReader.onload = function(e) {
						$scope.$apply(function() {
							$scope.placeholder[$scope.c_admin.subcontext].dataUrls = e.target.result;
						});
					}
				}(fileReader);
			}
		}

	};

	//start the file upload
	$scope.start = function(subcontext,scrape) {

		if(!scrape){
			var file=$scope.placeholder[$scope.c_admin.subcontext].selectedFiles[0];
		}else{
			var file=null;
		}
		$scope.uploading=true;
		$scope.placeholder[$scope.c_admin.subcontext].progress=1;
		var fields = {
			'method' : 'fileup',
			'corder' : $scope.c.context.c_order,
			'chid' : $scope.c.context.chid,
			'porder' : $scope.c.context.p_order,
			'pid'  : $scope.c.context.pid,
			'sid' : $scope.c.context.sid,
			'type': $scope.c_admin.subcontext,
			'vidi' : $scope.placeholder[$scope.c_admin.subcontext].vidi,
			'secret':'aesopsupersecret'
		}
		$scope.up=Upload.upload({
			url : '/handler.php',
			method: 'POST',
			fields : fields,
			file: file
		}).then(function(data){

			$scope.notification.message=data.data;
			if(data.data[0].class==='success'){
				$scope.placeholder[$scope.c_admin.subcontext]={};
				$scope.reload();
			}
		},function(err){
			console.error(err);
		},function(evt){
			$scope.placeholder[$scope.c_admin.subcontext].progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
		})

	};

	$scope.abort=function(){
		if($scope.up){
			$scope.up.abort();
		}
		delete $scope.placeholder[$scope.c_admin.subcontext].selectedFiles;
		$scope.input.value=null;
		$scope.uploading=false;
		$scope.placeholder[$scope.c_admin.subcontext].progress=0;
		$scope.up=null;
	}

}]);
