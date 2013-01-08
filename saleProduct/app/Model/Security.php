<?php
class Security extends AppModel {
	var $useTable = 'sc_user';
	
	function isAllow($url){
		return true ;
	}
	
	/**
	 * 是否有权限
	 */
	function hasPermission( $loginId , $code ){
		$count = $this->getObject("sql_security_haspermissionByCode",array('loginId'=>$loginId,'code'=>$code) ) ;
	
		if( $count['C'] > 0 ) $result = true ;
		else $result = false ;
		
		return $result ;
	}
}