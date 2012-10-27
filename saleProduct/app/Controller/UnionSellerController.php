<?php

class UnionSellerController extends AppController {
	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $uses = array('SqlUtils',"UnionSeller");
	
    public function lists($accountId = null){
    	$this->set("accountId",$accountId);
    }
    
    public function add($accountId,$id = null){
    	$item = null ;
    	if(!empty($id)){
    		$item = $this->UnionSeller->getById($id) ;
    	}
    	$this->set("item",$item) ;
    	$this->set("accountId",$accountId) ;
    }
    
    public function save(){
    	$user =  $this->getCookUser() ;
	 	$this->UnionSeller->save($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
}