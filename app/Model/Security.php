<?php
class User extends AppModel {
	var $useTable = 'sc_user';
	
	function isAllow($url){
		return true ;
	}
}