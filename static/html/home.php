<div ng-controller='frontlist' ng-show="c.iready && c_admin.context !== 'admin'" class='container fteaserwrap'>
	<div ng-repeat='story in list' class="fouter" ng-mouseEnter="down($event)" ng-mouseLeave="down($event)">
		<div class='w100 fteaser' ng-show="c.iready">
			<a ng-click='c.iready=false;console.log("hjhjh")' href='/#/story?story={{story.title}}'>
				<span class='ftitle'>{{story.title}}</span>
				<img ng-src='/static/resources/timage/{{story.location}}' />
			</a>
		</div>
		<div class="storydets">
			<div class="inner">
				{{story.text}}
			</div>
		</div>
	</div>
	<div class='clearfix'></div>

</div>
