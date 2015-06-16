'use strict';

Aesop.service('getter',function($q,$http,$rootScope) {
	//console.log('getter');
	return{		
		all : function(sid){			
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'getall','sid':sid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;
		},
		sid : function(title){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'getsid','title':title}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		storylist : function(){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'getstories'}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		getqueue: function(uid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'getqueue','uid':uid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		poller: function(uid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'poller'}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		users: function(){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'users'}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		}
	}		
});

Aesop.service('setter',function($q,$http,$rootScope) {
	//console.log('setter');
	return{		
		add : function(method,title,sid,chid,pid,corder,porder){		
			var defer = $q.defer();
			$http.post('../php/connect.php', {'title':title,'method':'new_'+method,'sid':sid,'chid':chid,'pid':pid,'corder':corder,'porder':porder}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;
		},
		deletestory : function(sid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'delstory','sid':sid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		deletechapter : function(corder,chid,sid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'delchap','corder':corder,'chid':chid,'sid':sid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		deletepage : function(porder,pid,chid,sid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'delpage','porder':porder,'pid':pid,'chid':chid,'sid':sid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		deleteres : function(type,sid,pid,chid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'delres','type':type,'sid':sid,'pid':pid,'chid':chid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;
		},
		edit : function(type,story_name,story_text, chapter_title, chapter_subtitle, chapter_mentitle, page_title, page_text, page_menshow, context){
			if(type==='story'){
				var post={'method':'edit','type':type,'story_name':story_name,'story_text':story_text,'sid':context.sid,'chid':context.chid,'pid':context.pid,'porder':context.p_order,'corder':context.c_order};
			}
			if(type==='chapter'){
				var post={'method':'edit','type':type,'chapter_title':chapter_title,'chapter_subtitle':chapter_subtitle,'chapter_mentitle':chapter_mentitle,'sid':context.sid,'chid':context.chid,'pid':context.pid,'porder':context.p_order,'corder':context.c_order};				
			}
			if(type==='page'){
				var post={'method':'edit','type':type,'page_title':page_title,'page_text':page_text,'page_menshow':page_menshow,'sid':context.sid,'chid':context.chid,'pid':context.pid,'porder':context.p_order,'corder':context.c_order};				
			}
			var defer = $q.defer();
			$http.post('../php/connect.php',post).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;
		},
		redit : function(type, element, value, context){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'redit','value':value,'type':type,'element':element,'sid':context.sid,'chid':context.chid,'pid':context.pid,'porder':context.p_order,'corder':context.c_order}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;
		},
		order : function(direction, position, context, sid, chid, pid,swap){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'order','direction':direction,'position':position,'context':context,'sid':sid,'chid':chid,'pid':pid,'swap':swap}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		seenqueue: function(uid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'seenqueue','uid':uid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		retry: function(rid){
			$http.post('../php/connect.php', {'method':'seenqueue','rid':rid});
		},
		switcher: function(type,sid,chid,pid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'switcher','type':type,'sid':sid,'chid':chid,'pid':pid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		newuser: function(name,email,mess){
			var defer = $q.defer();
			$http.post('../php/auth.php', {'method':'newuser','username':name,'usermail':email,'mess':mess}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		setpass: function(uid,token,pass){
			var defer = $q.defer();
			$http.post('../php/auth.php', {'method':'setpass','uid':uid,'token':token,'pass':pass}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		newpass: function(user){
			var defer = $q.defer();
			$http.post('../php/auth.php', {'method':'newpass','user':user}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		}
	}		
});


Aesop.service('auth',function($q,$http,$rootScope) {
	//console.log('auth');
	return{
		login : function(user,password){
			
			var defer = $q.defer();
			$http.post('../php/auth.php', {'method':'login','username':user,'password':password}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;
			
		},
		verify : function(uid){
			var defer = $q.defer();
			$http.post('../php/auth.php', {'method':'verify','uid':uid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		}
	}
});



