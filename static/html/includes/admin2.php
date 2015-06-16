<div id='admin2' class='admin' ng-controller='admin2' ng-style="{width:width}" ng-if="c_admin.context === 'admin'">

	<div id='a2con'>
		<div>
			<div class='sidemen first' ng-click="add('admin','jobs')" ng-class="{'active' : c_admin.subcontext=='jobs'}">Jobs</div>
			<div class='sidemen' ng-click="add('admin','users')" ng-class="{'active' : c_admin.subcontext=='users'}">Users</div>
			<div class='sidemen' ng-click="add('admin','settings')" ng-class="{'active' : c_admin.subcontext=='settings'}">Settings</div>
		</div>
	</div>
	<div id='a2frame' ng-style="{width:width-200,height:height}">
		<div id='notification' ng-if='size(notification.message) > 0 && !modal.show_modal' ng-click='notice("clear")'>
			<div ng-repeat="mes in notification.message" ng-class="mes.class" class='bubb'>
				{{mes.message}}
			</div>					
		</div>
		<div id='adminusers' ng-if='c_admin.subcontext==="users"' ng-controller='adminusers'>
			
			<form name='adduser' ng-submit="newuser()" novalidate>
				<h1>Create a new user</h1>
				<div  class='infield'>
					<input id='newuser' type='text' ng-model="newusername" size='38' required />
					<label for="newuser" ng-if="!newusername" >New user name</label>
				</div>
				<div  class='infield'>
					<input id='usermail' type='email' ng-model="newusermail" placeholder="NEW USER EMAIL" size='38' required />					
				</div>
				<textarea ng-model="mailmessage" placeholder="Personalised message (optional)"></textarea>
				<div>
					<input type='submit' value='create' ng-disabled="adduser.$invalid" />
				</div>
			</form>
			<h1>Existing Users</h1>
			<div ng-repeat='user in allusers'>
				{{user.username}} {{user.role}}<span ng-if='user.verified==="f"'> Unverified</span>
			</div>
		</div>
		<div id='adminjobs' ng-if='c_admin.subcontext==="jobs"'>
			<h1 ng-if='
			c_admin.queue.running.length===0 && 
			c_admin.queue.queued.length===0 && 
			c_admin.queue.error.length===0 && 
			c_admin.queue.complete.length===0
			'>There are no active jobs.</h1>
			<div ng-if='c_admin.queue.running.length > 0'>
				<h1>Jobs that are runnng:</h1>
				<div class='bubb message' ng-repeat="item in c_admin.queue.running">
					<div class='cntr'>
						<div ng-click="del_r(item.type,item.sid,item.pid,item.chid)">Cancel</div>
					</div>
					<h2>{{item.sid | storyname:storynames}}</h2>

					<div>{{item.type | jobtype}} : Chapter {{item.corder*1+1}}, Page {{item.porder*1+1}}</div>
					<div>{{item.title}}</div><br>						
					<div>Started: <span am-time-ago="item.time"></span></div>
					<div ng-repeat="mess in item.message">- {{mess}}</div>
					
				</div>
			</div>
			<div ng-if='c_admin.queue.queued.length > 0'>
				<h1>Jobs that are queued:</h1>
				<div class='bubb warning' ng-repeat="item in c_admin.queue.queued">
					<div class='cntr'>
						<div ng-click="del_r(item.type,item.sid,item.pid,item.chid)">Cancel</div>
					</div>
					<h2>{{item.sid | storyname:storynames}}</h2>

					<div>{{item.type | jobtype}} : Chapter {{item.corder*1+1}}, Page {{item.porder*1+1}}</div>
					<div>{{item.title}}</div>
				</div>
			</div>
			<div ng-if='c_admin.queue.error.length > 0'>
				<h1>Jobs with errors:</h1>
				<div class='bubb error' ng-repeat="item in c_admin.queue.error">
					<div class='cntr'>
						<div ng-click="del_r(item.type,item.sid,item.pid,item.chid)">Cancel</div>
					</div>
					<h2>{{item.sid | storyname:storynames}}</h2>

					<div>{{item.type | jobtype}} : Chapter {{item.corder*1+1}}, Page {{item.porder*1+1}}</div>
					<div>{{item.title}}</div><br>
					<div ng-repeat="mess in item.message">- {{mess}}</div>
				</div>
			</div>
			<div ng-if='c_admin.queue.complete.length > 0'>
				<h1>Completed jobs:</h1>
				<div class='bubb success'>
					<div class='cntr'>
						<div ng-click="reload()">Refresh</div>
					</div>
					<div class='bubbrow' ng-repeat="item in c_admin.queue.complete">
						<h2>{{item.sid | storyname:storynames}}</h2>

						<div>{{item.type | jobtype}} : Chapter {{item.corder*1+1}}, Page {{item.porder*1+1}}</div>
						<div>{{item.title}}</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
