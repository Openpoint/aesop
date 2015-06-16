'use strict';

/* Filters */

Aesop.filter('storyname', function() {

    return function(sid,stories) {
		if(stories && sid!=null){
			for (var i=0; i<stories.length; i++) {
				if (stories[i].sid === sid) {
					return stories[i].title;
				}
			}
			return sid;
		}else{
			return sid;
		}
    }
})
.filter('jobtype', function(){
	return function(type){
		if (type==='bvideo'){
			return 'Background Video'
		}
		if (type==='fvideo'){
			return 'Featured Video'
		}
		if (type==='oaudio'){
			return 'Overlay Audio'
		}
	}
})

