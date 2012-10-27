<?php

/**
 * 预警控制类
 * 
 * @author lixh@126.com
 * @date 
 */
class WarningController extends AppController {
	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $uses = array('SqlUtils',"Warning");
    
    public function lists($accountId = null){
    	$this->set("accountId",$accountId) ;
    }
    
    public function add($accountId = null,$id = null){
    	$item = null ;
    	if(!empty($id)){
    		$item = $this->Warning->getById($id) ;
    	}
    	$this->set("item",$item) ;
    	$this->set("accountId",$accountId) ;
    }
    
    public function save(){
    	$user =  $this->getCookUser() ;
	 	$this->Warning->save($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
    
    public function getByCategoryId($categoryId){
    	$result = $this->Warning->getByCategoryId($categoryId) ;
    	$this->response->type("json") ;
		$this->response->body( json_encode($result) )   ;

		return $this->response ;
    }
}