<?php

class SaleUserController extends AppController {
	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $uses = array('Config','SaleUser');
    
    public function lists(){	
    }
    
    
    public function setDanger(){
    	$user =  $this->getCookUser() ;
    	
    	
    	$this->SaleUser->setDanger($this->request->data , $user) ;

		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
}