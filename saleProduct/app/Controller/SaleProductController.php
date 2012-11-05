<?php

class SaleProductController extends AppController {
	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $uses = array('SaleProduct', 'Config');
    
    public function lists(){	
    }
    
    public function editProduct(){
    	
    }
    
    public function saveProduct(){
    	$user =  $this->getCookUser() ;
    	$this->SaleProduct->saveProduct($this->request->data , $user) ;

		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
}