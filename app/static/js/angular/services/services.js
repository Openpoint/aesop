'use strict';
var secret = "aesopsupersecret";

Aesop.service('getter',function($q,$http,$rootScope) {
	//console.log('getter');
	return{
		all : function(sid){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'getall','sid':sid}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		sid : function(title){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'getsid','title':title}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		storylist : function(){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'getstories'}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		getqueue: function(uid){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'getqueue','uid':uid}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		poller: function(uid){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'poller'}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		users: function(){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'users'}).then(function(data) {
				defer.resolve(data.data);
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
			$http.post('handler.php', {'title':title,'secret':secret,'method':'new_'+method,'sid':sid,'chid':chid,'pid':pid,'corder':corder,'porder':porder}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		deletestory : function(sid){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'delstory','sid':sid}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		deletechapter : function(corder,chid,sid){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'delchap','corder':corder,'chid':chid,'sid':sid}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		deletepage : function(porder,pid,chid,sid){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'delpage','porder':porder,'pid':pid,'chid':chid,'sid':sid}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		killprocess : function(prid){
			console.error(prid);
			$http.post('handler.php', {'secret':secret,'method':'killprocess','prid':prid})
		},
		deleteres : function(type,sid,pid,chid){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'delres','type':type,'sid':sid,'pid':pid,'chid':chid}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		edit : function(type,story_name,story_text, chapter_title, chapter_subtitle, chapter_mentitle, page_title, page_text, page_menshow, context){
			if(type==='story'){
				var post={'secret':secret,'method':'edit','type':type,'story_name':story_name,'story_text':story_text,'sid':context.sid,'chid':context.chid,'pid':context.pid,'porder':context.p_order,'corder':context.c_order};
			}
			if(type==='chapter'){
				var post={'secret':secret,'method':'edit','type':type,'chapter_title':chapter_title,'chapter_subtitle':chapter_subtitle,'chapter_mentitle':chapter_mentitle,'sid':context.sid,'chid':context.chid,'pid':context.pid,'porder':context.p_order,'corder':context.c_order};
			}
			if(type==='page'){
				var post={'secret':secret,'method':'edit','type':type,'page_title':page_title,'page_text':page_text,'page_menshow':page_menshow,'sid':context.sid,'chid':context.chid,'pid':context.pid,'porder':context.p_order,'corder':context.c_order};
			}
			var defer = $q.defer();
			$http.post('handler.php',post).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		redit : function(type, element, value, context){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'redit','value':value,'type':type,'element':element,'sid':context.sid,'chid':context.chid,'pid':context.pid,'porder':context.p_order,'corder':context.c_order}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		order : function(direction, position, context, sid, chid, pid,swap){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'order','direction':direction,'position':position,'context':context,'sid':sid,'chid':chid,'pid':pid,'swap':swap}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		seenqueue: function(uid){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'seenqueue','uid':uid}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		retry: function(rid){
			$http.post('handler.php', {'secret':secret,'method':'seenqueue','rid':rid});
		},
		switcher: function(type,sid,chid,pid){
			var defer = $q.defer();
			$http.post('handler.php', {'secret':secret,'method':'switcher','type':type,'sid':sid,'chid':chid,'pid':pid}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		newuser: function(name,email,mess){
			var defer = $q.defer();
			$http.post('ahandler.php', {'secret':secret,'method':'newuser','username':name,'usermail':email,'mess':mess}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		setpass: function(uid,token,pass){
			var defer = $q.defer();
			$http.post('ahandler.php', {'secret':secret,'method':'setpass','uid':uid,'token':token,'pass':pass}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		},
		newpass: function(user){
			var defer = $q.defer();
			$http.post('ahandler.php', {'secret':secret,'method':'newpass','user':user}).then(function(data) {
				defer.resolve(data.data);
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
			$http.post('ahandler.php', {'secret':secret,'method':'login','username':user,'password':password}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;

		},
		verify : function(uid){
			var defer = $q.defer();
			$http.post('ahandler.php', {'secret':secret,'method':'verify','uid':uid}).then(function(data) {
				defer.resolve(data.data);
			});
			return defer.promise;
		}
	}
});
