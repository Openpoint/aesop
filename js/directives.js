'use strict';

/* Directives */



//Upload a resource
var FileUp = [ '$scope', '$http', '$timeout', '$upload', function($scope, $http, $timeout, $upload) {
	//console.log("Fileup");
	var context={};
	
	$scope.$watch('c.context',function(){
		context=$scope.c.context;		
		context.method='fileup';				
	})
	$scope.placeholder[$scope.c_admin.subcontext].fileReaderSupported = window.FileReader != null;
	$scope.placeholder[$scope.c_admin.subcontext].uploadRightAway = false;

	$scope.hasUploader = function(index) {
		return $scope.placeholder[$scope.c_admin.subcontext].upload[index] != null;
	};
	$scope.abort = function(index) {
		$scope.placeholder[$scope.c_admin.subcontext].upload[index].abort();
		$scope.placeholder[$scope.c_admin.subcontext].upload[index] = null;
	};
	$scope.onFileSelect = function($files, acontext) {
		$scope.acontext=acontext;
		$scope.placeholder[$scope.c_admin.subcontext]={};
		$scope.placeholder[$scope.c_admin.subcontext].selectedFiles = [];
		$scope.placeholder[$scope.c_admin.subcontext].progress = [];
		if ($scope.placeholder[$scope.c_admin.subcontext].upload && $scope.placeholder[$scope.c_admin.subcontext].upload.length > 0) {
			for (var i = 0; i < $scope.placeholder[$scope.c_admin.subcontext].upload.length; i++) {
				if ($scope.placeholder[$scope.c_admin.subcontext].upload[i] != null) {
					$scope.placeholder[$scope.c_admin.subcontext].upload[i].abort();
				}
			}
		}
		$scope.placeholder[$scope.c_admin.subcontext].upload = [];
		$scope.placeholder[$scope.c_admin.subcontext].uploadResult = [];
		var i = 0;
		var mes = new Array();
		//set the allowed upload file types
		$scope.placeholder[$scope.c_admin.subcontext].allowed = new Array();
		if(acontext == 'fimage'){
			$scope.placeholder[$scope.c_admin.subcontext].allowed=['image/png','image/jpeg','image/gif','image/jpg'];
		}
		if(acontext == 'poster'){
			$scope.placeholder[$scope.c_admin.subcontext].allowed=['image/png','image/jpeg','image/jpg'];
		}
		if(acontext == 'foverlay'){
			$scope.placeholder[$scope.c_admin.subcontext].allowed=['image/png'];
		}
		if(acontext == 'timage'){
			$scope.placeholder[$scope.c_admin.subcontext].allowed=['image/jpeg','image/jpg'];
		}
		if(acontext == 'fvideo' || acontext == 'bvideo'){
			$scope.placeholder[$scope.c_admin.subcontext].allowed=['video/mp4','video/ogv','video/webm','video/ogg'];
		}
		if(acontext == 'oaudio'){
			$scope.placeholder[$scope.c_admin.subcontext].allowed=['audio/ogg','audio/mpeg','video/ogg'];
		}
		angular.forEach($files,function(value, key){
			if($files[key].size > $scope.all.maxbyte){								
				mes[i]=new Array();	
				mes[i]['message']=$files[key].name+" is too big to upload and has been removed. Max size = "+$scope.all.maxsize;
				mes[i]['class']='warning';
				delete $files[key];
				i++			
			};
			if($files[key] && $scope.placeholder[$scope.c_admin.subcontext].allowed.indexOf($files[key].type) < 0){
				mes[i]=new Array();	
				mes[i]['message']=$files[key].name+" is not of an allowed file type and has been removed.";
				mes[i]['class']='warning';
				delete $files[key];
				i++	
			}
			$scope.notice(null,mes);
		});
		var temp = new Array();
		i = 0;
		angular.forEach($files,function(value,key){
			temp[i]=$files[key];
			i++;
		})
		$files=temp;
		//$files = array_values($files);
		$scope.placeholder[$scope.c_admin.subcontext].selectedFiles = $files;
		$scope.placeholder[$scope.c_admin.subcontext].dataUrls = [];
		for ( var i = 0; i < $files.length; i++) {
			var $file = $files[i];
			if (window.FileReader && $file.type.indexOf('image') > -1) {
				var fileReader = new FileReader();
				fileReader.readAsDataURL($files[i]);
				var loadFile = function(fileReader, index) {
					fileReader.onload = function(e) {
						$timeout(function() {
							$scope.placeholder[$scope.c_admin.subcontext].dataUrls[index] = e.target.result;
						});
					}
				}(fileReader, i);
			}
			$scope.placeholder[$scope.c_admin.subcontext].progress[i] = -1;
		}
	};
	$scope.start = function(index,acontext) {
		$scope.placeholder[$scope.c_admin.subcontext].progress[index] = 0;
		$scope.placeholder[$scope.c_admin.subcontext].errorMsg = null;
		console.log(context);
		$scope.placeholder[$scope.c_admin.subcontext].upload[index] = $upload.upload({
			url : '/php/connect.php',
			method: 'POST',
			//headers: {'my-header': 'my-header-value'},
			data : {
					'method' : context.method,
					'c_order' : context.c_order,
					'chid' : context.chid,
					'p_order' : context.porder,
					'pid'  : context.pid,
					'sid' : context.sid,
					'type': $scope.c_admin.subcontext,
					'subtype' : acontext
				},
			file: $scope.placeholder[$scope.c_admin.subcontext].selectedFiles[index],
			fileFormDataName: 'myFile'
		}).then(function(response) {
			function vplay(){
				if(response.data.type == 'bvideo'){
					$scope.c.bvmute=true;					
					setTimeout(function(){						
						$('#mediafocus').css({opacity:1,zIndex:1});
						$('#media').css({zIndex:0});
						$('#media .pimage').clone().insertBefore($('#mediafocus .bvideo'));
						$scope.isize();
						bvideo=$('#mediafocus .bvideo')[0];											
						bvideo.pause();
						bvideo.load();
						bvideo.muted=true;
						bvideo.play();
					},10);
				}else if(response.data.type == 'fvideo'){
					
					setTimeout(function(){	
						fvideo=$('#media .video')[0];
						fvideo.load();					
						fvideo.play();
					},10);
				}						
			}
			if(response.data.message=='saved'){
				console.log(response.data);
				/*
				if(!$scope.all.resource){
					$scope.all.resource=new Array()
				}
				if(!$scope.all.resource[response.data.chid]){
					$scope.all.resource[response.data.chid]=new Array()
				}
				if(!$scope.all.resource[response.data.chid][response.data.pid]){
					$scope.all.resource[response.data.chid][response.data.pid]=new Array()
				}
				*/
				if(response.data.subtype == 'poster'){
					$scope.all.resource[response.data.chid][response.data.pid].bcarry=false;
					$scope.all.resource[response.data.chid][response.data.pid].fimage=false;
					$scope.all.resource[response.data.chid][response.data.pid].bvideo={};
					delete $scope.all.resource[response.data.chid][response.data.pid].fimage;
				}
				
				if(response.data.type == 'fimage'){
					$scope.all.resource[response.data.chid][response.data.pid].fcarry=false;
					setTimeout(function(){
						$scope.isize();
					})
					
				}
				if(!$scope.all.resource[response.data.chid][response.data.pid][response.data.subtype]){
					$scope.all.resource[response.data.chid][response.data.pid][response.data.subtype]=new Array()
				}
				if(response.data.name){
					$scope.all.resource[response.data.chid][response.data.pid][response.data.subtype].location=response.data.name
					$scope.isize();
				}
				if(response.data.type == 'fvideo'){
					$('#media').css({'top':0});
					$('#mediafocus img').remove();
				}				
				if(response.data.v_mp4){
					$scope.all.resource[response.data.chid][response.data.pid][response.data.subtype].v_mp4='/resources/'+response.data.subtype+'/'+response.data.v_mp4;
					vplay();
				}
				if(response.data.v_ogv){
					$scope.all.resource[response.data.chid][response.data.pid][response.data.subtype].v_ogv='/resources/'+response.data.subtype+'/'+response.data.v_ogv;
					vplay();
				}
				if(response.data.v_webm){
					$scope.all.resource[response.data.chid][response.data.pid][response.data.subtype].v_webm='/resources/'+response.data.subtype+'/'+response.data.v_webm;
					vplay();
				}
				if(response.data.a_ogg){
					$scope.all.resource[response.data.chid][response.data.pid][response.data.subtype].a_ogg='/resources/'+response.data.subtype+'/'+response.data.a_ogg;

				}				
				if(response.data.a_mp3){
					$scope.all.resource[response.data.chid][response.data.pid][response.data.subtype].a_mp3='/resources/'+response.data.subtype+'/'+response.data.a_mp3;

				}				
				delete $scope.placeholder[response.data.type].selectedFiles[index];
				
			}
			$scope.placeholder[$scope.c_admin.subcontext].uploadResult.push(response.data);
		}, function(response) {
			if (response.status > 0) $scope.placeholder[$scope.c_admin.subcontext].errorMsg = response.status + ': ' + response.data;
		}, function(evt) {
			// Math.min is to fix IE which reports 200% sometimes
			if(typeof $scope.placeholder[$scope.c_admin.subcontext].progress == 'undefined'){
				$scope.placeholder[$scope.c_admin.subcontext].progress = new Array();
			}
			$scope.placeholder[$scope.c_admin.subcontext].progress[index] = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
		}).xhr(function(xhr){
			xhr.upload.addEventListener('abort', function() {console.log('abort complete')}, false);
		});

	};
	$scope.resetInputFile = function() {
		var elems = document.getElementsByTagName('input');
		for (var i = 0; i < elems.length; i++) {
			if (elems[i].type == 'file') {
			elems[i].value = null;
			}
		}
	};
}]; 


