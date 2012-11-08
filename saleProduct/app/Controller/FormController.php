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
}