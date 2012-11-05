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
	 
	 public function deleteById($id){
	 	$sql = "delete from sc_seller where ID = '$id' and not exists
			( select ID from sc_gather_asin where sc_gather_asin.task_id = '$id' ) " ;
			
		$item = $this->Seller->query($sql) ;
		
		$this->response->type("json") ;
			$this->response->body( "success")   ;

		  return $this->response ;
	 }
}