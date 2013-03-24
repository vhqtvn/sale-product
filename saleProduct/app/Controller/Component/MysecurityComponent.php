<?php
/**
 * Security Component
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Controller.Component
 * @since         CakePHP(tm) v 0.10.8.2156
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Component', 'Controller');
App::uses('String', 'Utility');
App::uses('Hash', 'Utility');
App::uses('Security', 'Utility');


/**
 * The Security Component creates an easy way to integrate tighter security in 
 * your application. It provides methods for various tasks like:
 *
 * - Restricting which HTTP methods your application accepts.
 * - CSRF protection.
 * - Form tampering protection
 * - Requiring that SSL be used.
 * - Limiting cross controller communication.
 *
 * @package       Cake.Controller.Component
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/security-component.html
 */
class MysecurityComponent extends Component {
	public function ignore( $url ){
		
		if( $url == "users/login" 
			|| $url == "users/loginPhone" 
			|| $this->startsWith($url,"error") 
			|| $url == 'users/logout' 
			|| $this->startsWith($url,"gatherLevel") 
			|| $this->startsWith($url,"taskAsynAmazon") 
			|| $this->startsWith($url,"gatherCategory")
		){
			return true ;
		}
		
		return false ;
	}
	
	public function isAllow( $url , $user ) {
		$groupCode = $user['GROUP_CODE'] ;
		
		//未分配组
		if( empty($groupCode) ) return false ;
		
		$this->User = ClassRegistry::init('User')  ;
		
		$Functions = $this->User->getFunctionRelGroups($groupCode) ;
		
		foreach( $Functions as $Record ){
			$sfs = $Record['sc_security_function']  ;
			$selected =  $Record[0]['selected'] ;
			$url1   = $sfs['URL'] ;
			
			if( !empty($selected) && (trim($url1) == trim($url))){//分配了权限的
				return true ;
			}else if(trim($url1) == trim($url)){
				//路径相同，但未分配权限
				return false ;
			}
			
		} ;
		
		//未设置的默认有权限
		return true;
	}
	
	
	function startsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    return (substr($haystack, 0, $length) === $needle);
	}
}
