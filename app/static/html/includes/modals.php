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

<div ng-if='modal.modals.story'>
	<div>
		<h1>Are you sure you want to delete the story?</h1>
		<div>This cannot be undone and will erase everything</div>
	</div>
	<div class='yesno'>
		<a ng-click='del("story")'>YES</a><a ng-click='modal.modal()'>NO</a>
	</div>
</div>
<div ng-if='modal.modals.chap'>
	<div>
		<h1>Are you sure you want to delete the chapter?</h1>
		<div>This cannot be undone and will erase everything in the chapter</div>
	</div>
	<div class='yesno'>
		<a ng-click='del("chapter")'>YES</a><a ng-click='modal.modal()'>NO</a>
	</div>
</div>
<div ng-if='modal.modals.page'>
	<div>
		<h1>Are you sure you want to delete this page?</h1>
		<div>This cannot be undone</div>
	</div>
	<div class='yesno'>
		<a ng-click='del("page")'>YES</a><a ng-click='modal.modal()'>NO</a>
	</div>
</div>
<div ng-if='modal.modals.login'>
	<h1>Login</h1>
	<form novalidate id="loginf" name="loginf" ng-submit="login(usern,password)">
		<div class="infield" ng-class="{required : loginf.usern.$error.required}">
			<input id="username" type="textinput" ng-model="usern" name="usern" required />
			<label ng-hide="loginf.usern.$viewValue">Username or Email:</label>
		</div>
		<div class="infield" ng-class="{required : loginf.password.$error.required}">
			<input id="userpass" type="password" ng-model="password" name="password" required />
			<label ng-hide="loginf.password.$viewValue">Password:</label>
		</div>

	</form>
	<input type="submit" value="Login" ng-disabled="loginf.$invalid" form="loginf">
	<button ng-click="newpass(usern)" ng-disabled="!usern">reset password</button>
</div>
<div ng-if='modal.modals.reset'>
	<div>
		<h1>Please set a password for your account</h1>
		<form novalidate ng-submit="setpass()">
			<div class='infield'>
				<input type='password' ng-model='c_admin.pass1' placeholder='Your password'>
			</div>
			<div class='infield'>
				<input type='password' ng-model='c_admin.pass2' placeholder='Confirm your password'>
			</div>
			<div>
				<div class="bubb warning" ng-if="c_admin.pass2 && c_admin.pass1!==c_admin.pass2 && c_admin.pass2.length >= c_admin.pass1.length">Passwords do not match</div>
				<input type='submit' ng-disabled="!c_admin.pass1 || c_admin.pass1!==c_admin.pass2" value='submit'/>
			</div>
		</form>
	</div>
</div>
<div id='notification' ng-if='size(notification.message) > 0 && modal.show_modal' ng-click='notice("clear")'>
	<div ng-repeat="mes in notification.message" ng-class="mes.class" class='bubb'>
		{{mes.message}}
	</div>
</div>
