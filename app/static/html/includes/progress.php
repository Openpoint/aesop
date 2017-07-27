<?php
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
?>

<div ng-show="placeholder[c_admin.subcontext].selectedFiles != null || placeholder[c_admin.subcontext].vidi.vurl.length > 0">

		<div class='teaser'>
			<img class='teaser' ng-show="placeholder[c_admin.subcontext].dataUrls.length > 0" ng-src="{{placeholder[c_admin.subcontext].dataUrls}}" />
		</div>
		<div>
			<div class='infield' ng-if='c_admin.subcontext==="fvideo" || c_admin.subcontext==="bvideo" || c_admin.subcontext==="oaudio"'>
				<h1>Slice the time (optional)</h1>
				<div class='underline'>Start Time:</div>
				<div class='slicerow'>
					<span> Hours: </span>
					<input type='number' ng-model='placeholder[c_admin.subcontext].vidi.start.h' min='0' required />
					<span> Minutes: </span>
					<input type='number' ng-model='placeholder[c_admin.subcontext].vidi.start.m'  min='0' max='59' required />
					<span> Seconds: </span>
					<input type='number' ng-model='placeholder[c_admin.subcontext].vidi.start.s'  min='0'  max='59' required />
				</div>
				<div class='underline'>End Time:</div>
				<div class='slicerow'>
					<span> Hours: </span>
					<input type='number' ng-model='placeholder[c_admin.subcontext].vidi.end.h' min='0' required  />
					<span> Minutes: </span>
					<input type='number' ng-model='placeholder[c_admin.subcontext].vidi.end.m'  min='0' max='59' required />
					<span> Seconds: </span>
					<input type='number' ng-model='placeholder[c_admin.subcontext].vidi.end.s'  min='0'  max='59' required />
				</div>
			</div>
			<button class="button" ng-click="start(c_admin.subcontext,true)" ng-show="placeholder[c_admin.subcontext].vidi.vurl">Start</button>
			<button class="button" ng-click="start(c_admin.subcontext)" ng-show="placeholder[c_admin.subcontext].progress === 0 && !placeholder[c_admin.subcontext].vidi.vurl">Start</button>
			<button class="button" ng-click="abort()" ng-show="placeholder[c_admin.subcontext].progress === 0 && !placeholder[c_admin.subcontext].vidi.vurl">Cancel</button>
			<button class="button" ng-click="abort()" ng-show="uploading && placeholder[c_admin.subcontext].progress < 100">Abort</button>
			<span>{{f.name}}</span>
		</div>
		<div class="progress" ng-show="placeholder[c_admin.subcontext].progress >= 0 && !placeholder[c_admin.subcontext].vidi.vurl">
			<div style="width:{{placeholder[c_admin.subcontext].progress}}%">{{placeholder[c_admin.subcontext].progress}}%</div>
		</div>

</div>
