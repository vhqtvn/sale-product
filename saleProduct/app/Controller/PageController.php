<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
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

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PageController extends AppController {
	public function forward($routes,$arg1 = null ,$arg2 = null , $arg3 = null , $arg4 = null){
		
		$rs = str_replace(".","/",$routes) ;
		
		$params = $this->request->data ;
		
		$params['arg1'] = $arg1 ;
		$params['arg2'] = $arg2 ;
		$params['arg3'] = $arg3 ;
		$params['arg4'] = $arg4 ;
		
		$user =  $this->getCookUser() ;
		
		//App::import('controller','A/Test');
		
		//$test = new TestController() ;
		//debug($test) ;
		
		$this->set("params",$params) ;
		$this->set("user",$user) ;
		
		$this->layout = "../$rs" ;

	}
	
	public function controller($command,$arg1 = null ,$arg2 = null , $arg3 = null , $arg4 = null){
		
		$rs = str_replace(".","/",$command) ;
		
		$user =  $this->getCookUser() ;
    	$params = $this->request->data  ;
    	$params['loginId'] = $user['LOGIN_ID'] ;
    	$params['arg1'] = $arg1 ;
		$params['arg2'] = $arg2 ;
		$params['arg3'] = $arg3 ;
		$params['arg4'] = $arg4 ;
    	
		$as = explode(".",$command) ;
		$clsName = "" ;
		$method  = "" ;
		$simpleClassName = "" ;
 		if( count($as)<=2 ){
			$simpleClassName = $as[0] ;
			$clsName = $as[0] ;
			$method = $as[1] ;
		}else{
			$clsName = $as[0]."/".$as[1] ;
			$simpleClassName = $as[1] ;
			$method = $as[2] ;
		}
		
			
		App::import("controller",$clsName) ;
		$r = new ReflectionClass($simpleClassName."Controller");
		
		$instance = $r->newInstance(); 
		$method = $r->getMethod($method) ;
		
		$instance->request = $this->request ;
		$instance->response = $this->response ;

		$method->invoke($instance, $params); 
	
		$this->set("params",$params) ;
		$this->set("user",$user) ;
		
		$this->layout = "../$rs" ;

	}
	
	public function model($command,$arg1 = null ,$arg2 = null , $arg3 = null , $arg4 = null){
		
		$rs = str_replace(".","/",$command) ;
		
		$user =  $this->getCookUser() ;
    	$params = $this->request->data  ;
    	$params['loginId'] = $user['LOGIN_ID'] ;
    	$params['arg1'] = $arg1 ;
		$params['arg2'] = $arg2 ;
		$params['arg3'] = $arg3 ;
		$params['arg4'] = $arg4 ;
	
		$as = explode(".",$command) ;
		$clsName = "" ;
		$method  = "" ;
		$simpleClassName = "" ;
 		if( count($as)<=2 ){
			$simpleClassName = $as[0] ;
			$clsName = $as[0] ;
			$method = $as[1] ;
		}else{
			$clsName = $as[0]."/".$as[1] ;
			$simpleClassName = $as[1] ;
			$method = $as[2] ;
		}
		
		App::import("Model",$clsName) ;
	
		$r = new ReflectionClass($simpleClassName);
		
		$instance = $r->newInstance(); 
		$method = $r->getMethod($method) ;
		
		$instance->request = $this->request ;
		$instance->response = $this->response ;

		$result = $method->invoke($instance, $params,$user); 
		
		
	    $this->set("result",$result) ;
		$this->set("params",$params) ;
		$this->set("user",$user) ;
		
		$this->layout = "../$rs" ;

	}
}
