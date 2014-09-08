'use strict';

myApp.service('getter',function($q,$http,$rootScope) {
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
		}
	}		
});

myApp.service('setter',function($q,$http,$rootScope) {
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
		deletechap : function(chid,sid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'delchap','chid':chid,'sid':sid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		deletepage : function(pid,chid,sid){
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'delpage','pid':pid,'chid':chid,'sid':sid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;			
		},
		deleteres : function(type,name,sub,pid,chid){
			name=name.split("/");
			console.log(name[name.length-1]);
			name=name[name.length-1];
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'delres','name':name,'type':type,'sub':sub,'pid':pid,'chid':chid}).success(function(data) {
				defer.resolve(data);
			});
			return defer.promise;
		},
		edit : function(type, element, value, context){
			//console.log('type-'+type+' element -'+element+' value -'+value+' context-'+context);
			var defer = $q.defer();
			$http.post('../php/connect.php', {'method':'edit','value':value,'type':type,'element':element,'sid':context.sid,'chid':context.chid,'pid':context.pid,'porder':context.p_order,'corder':context.c_order}).success(function(data) {
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
		}
	}		
});


myApp.service('auth',function($q,$http,$rootScope) {
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
