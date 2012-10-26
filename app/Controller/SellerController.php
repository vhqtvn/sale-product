<?php

class SellerController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
	
	 public function add(){
	 }

	 public function save(){
	 		$user =  $this->getCookUser() ;
	 	
			$this->Seller->saveSeller($this->request->data,$user) ;

			$this->response->type("json") ;
			$this->response->body( "success")   ;

		  return $this->response ;
	 }
}