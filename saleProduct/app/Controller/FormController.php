<?php

class FormController extends AppController {
   	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Config','Form');
    
    public function ajaxSave(){
    	$user =  $this->getCookUser() ;
    	$params = $this->request->data  ;
    	$params['loginId'] = $user['LOGIN_ID'] ;
    	
    	$this->Form->ajaxSave($params) ;
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
    
    public function dataService(){
    	$user =  $this->getCookUser() ;
    	$params = $this->request->data  ;
    	$params['loginId'] = $user['LOGIN_ID'] ;
    	
    	$command = $params['CommandName'] ;
    	
    	if(strpos($command,'model:')==0){
    		$command = str_replace("model:","",$command) ;
    		$as = explode(".",$command) ;
    		$clsName = $as[0] ;
    		$method = $as[1] ;
    		
    		$service = ClassRegistry::init($clsName)  ;
    		$r = new ReflectionClass($service); 
    		
    		$method = $r->getMethod($method) ;
    		$result = $method->invoke($service, $params); 
    	}
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
}