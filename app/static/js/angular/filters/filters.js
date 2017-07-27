/*
Copyright 2017 Michael Jonker (http://openpoint.ie)

This file is part of Aesop.

Aesop is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Aesop is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Aesop.  If not, see <http://www.gnu.org/licenses/>.
*/

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
