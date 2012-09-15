<?php

class ConfigController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
	
	 public function add($id = null){
	 	$user =  $this->getCookUser() ;
	 	$this->set("user",$user) ;
	 	$configItem = null ;
	 	if(empty($id)){
	 		//
	 	}else{
	 		$configItem = $this->Config->getConfigItem($id) ;
	 	}
	 	$this->set("configItem",$configItem) ;
	 }

	 public function saveConfigItem(){
	 		$user =  $this->getCookUser() ;
	 	
			$this->Config->saveConfigItem($this->request->data,$user) ;

			$this->response->type("json") ;
			$this->response->body( "success")   ;

		  return $this->response ;
	 }
	 
	 public function deleteConfigItem($id){
	 		$user =  $this->getCookUser() ;
	 	
			$this->Config->deleteConfigItem($id) ;

			$this->response->type("json") ;
			$this->response->body( "success")   ;

		  return $this->response ;
	 }
	 
	 
}