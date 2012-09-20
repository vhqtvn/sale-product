<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	var $components = array('Session','Mysecurity') ;//'Acl','Auth',
	var $users = array('Grid',"User") ;
	
	function beforeFilter(){
		$url = $this->request->url ;
		
		/*if(!isset($_SESSION)){
		    session_start();
		}*/
		
		//setcookie("name", "value")
		
		$userId = null ;
		//print_r($_COOKIE) ;
		if( isset($_COOKIE) && isset($_COOKIE["userId"]) ){
			$userId  = $_COOKIE["userId"] ; 
		}

		if( $url == "users/login" || $this->startsWith($url,"error") || $url == 'users/logout' || $this->startsWith($url,"cron") ){
			//ignore
		}else{
			if( !empty($userId) ){
				
				App::import('Model', 'User') ;
				$u = new User() ;
				$user1 = $u->queryUserByUserName($userId) ;
				$user = $user1[0]['sc_user'] ;

				$this->set("User" ,$user ) ;
				
				//判断用户是否有权限访问该URL
				if( $this->Mysecurity->isAllow( $url , $user ) ){
					//do nothing
				}else{
					//$this->layout = "error/error403";
					$this->redirect("/error/error403", 403);
				}			
			//do nothing
			}else{
				$this->redirect("/users/login");
			}
		}
		
	}
	
	function startsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    return (substr($haystack, 0, $length) === $needle);
	}
	
	function endsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    if ($length == 0) {
	        return true;
	    }
	
	    return (substr($haystack, -$length) === $needle);
	}
	
	function getCookUser(){
		$userId  = $_COOKIE["userId"] ; 
		App::import('Model', 'User') ;
		$u = new User() ;
		$user1 = $u->queryUserByUserName($userId) ;
		$user = $user1[0]['sc_user'] ;
		return $user ;
	}
	
	
	function array2json($arr) { 
			if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality.
			$parts = array(); 
			$is_list = false; 

			//Find out if the given array is a numerical array 
			$keys = array_keys($arr); 
			$max_length = count($arr)-1; 
			if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
				$is_list = true; 
				for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position 
					if($i != $keys[$i]) { //A key fails at position check. 
						$is_list = false; //It is an associative array. 
						break; 
					} 
				} 
			} 

			foreach($arr as $key=>$value) { 
				if(is_array($value)) { //Custom handling for arrays 
					if($is_list) $parts[] = array2json($value); /* :RECURSION: */ 
					else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */ 
				} else { 
					$str = ''; 
					if(!$is_list) $str = '"' . $key . '":'; 

					//Custom handling for multiple data types 
					if(is_numeric($value)) $str .= $value; //Numbers 
					elseif($value === false) $str .= 'false'; //The booleans 
					elseif($value === true) $str .= 'true'; 
					else $str .= '"' . addslashes($value) . '"'; //All other things 
					// :TODO: Is there any more datatype we should be in the lookout for? (Object?) 

					$parts[] = $str; 
				} 
			} 
			$json = implode(',',$parts); 
			 
			if($is_list) return '[' . $json . ']';//Return numerical JSON 
			return '{' . $json . '}';//Return associative JSON 
	} 

}
