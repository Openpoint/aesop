<div ng-controller='frontlist' ng-show="c.iready && c_admin.context !== 'admin'" class='container fteaserwrap'>
	<div ng-if="list!=='false'" ng-repeat='story in list' class="fouter" ng-mouseEnter="down($event)" ng-mouseLeave="down($event)">
		<div class='w100 fteaser' ng-show="c.iready">
			<a ng-click='c.iready=false' href='/story?story={{story.title}}'>

				<img ng-if="story.location" ng-src='/static/resources/timage/{{story.location}}' />
				<img ng-if="!story.location" ng-src='/static/resources/timage/placeholder.jpg' />
				<span class='ftitle'>{{story.title}}</span>
			</a>
		</div>
		<div class="storydets">
			<div class="inner">
				{{story.text}}
			</div>
		</div>
	</div>
	<div ng-if="list==='false'">
		<h1>Welcome to Aesop</h1>
		<p>Nothing has been created yet.</p>
		<p ng-if="c.user" ng-click="add('add',null)" >Please create a new story</p>
	</div>
	<div class='clearfix'></div>

</div>
