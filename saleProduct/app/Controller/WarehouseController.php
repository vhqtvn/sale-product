<?php

class WarehouseController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $users = array('Warehouse',"User") ;
    
    public function lists(){
    }
    
    public function addPage($id = null){
    	$item = null ;
    	if(!empty($id)){
    		$item = $this->Warehouse->findById($id) ;
    		$item = $item[0]['sc_warehouse'] ;
    	}
    	$this->set("id",$id) ;
    	$this->set("warehouse",$item) ;
    }
    
    public function editPage($id = null){
    	$item = null ;
    	if(!empty($id)){
    		$item = $this->Warehouse->findById($id) ;
    		$item = $item[0]['sc_warehouse'] ;
    	}
    	$this->set("id",$id) ;
    	$this->set("warehouse",$item) ;
    }
    
    public function addUnitPage($warehouseId,$id = null ){
    	$this->set("warehouseId",$warehouseId) ;
    	$this->set("id",$id) ;
    	$item = null ;
    	if(!empty($id)){
    		$item = $this->Warehouse->findUnitById($id) ;
    		$item = $item[0]['sc_warehouse_unit'] ;
    	}
    	$this->set("item",$item) ;
    }
    
    //管理员配置页面
    public function managePage($id){
    	$this->set("id",$id) ;
    }
    
    //仓库单元
    public function unitPage($id){
    	$this->set("id",$id) ;
    }
    
    public function saveManage(){
    	$user =  $this->getCookUser() ;
	 	
		$this->Warehouse->saveManage($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
}