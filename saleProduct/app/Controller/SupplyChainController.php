<?php

class SupplierController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
	 var $uses = array('Sale', 'Product','Supplier');
	 
	 public function lists(){
	 }
	 
	 public function listsSelect($asin){
	 	$this->set("asin",$asin) ;
	 	
	 	//查询已经选择的供应商
	 	$suppliers = $this->Supplier->getProductSuppliers( $asin  ) ;
	 	
	 	$this->set("suppliers",$suppliers) ;
	 }
	 
}