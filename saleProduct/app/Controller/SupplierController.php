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
	 
	 public function add($id = null, $idValue = null ){
	 	if( $id == 'asin' ){
	 		$this->set("asin",$idValue) ;
	 		$this->set("id",'') ;
	 		$Supplier = null ;
	 		if( !empty($id) ){
	 			$Supplier =  $this->Supplier->getSupplier( $id  ) ;
	 		}
	 		$this->set("supplier",$Supplier) ;
	 		
	 		$categorys = $this->Product->getProductCategory();
	 		$this->set("categorys",$categorys) ;
	 	}else{
	 		$this->set("asin",'') ;
	 		$this->set("id",$id) ;
	 		$Supplier = null ;
	 		if( !empty($id) ){
	 			$Supplier =  $this->Supplier->getSupplier( $id  ) ;
	 		}
	 		$this->set("supplier",$Supplier) ;
	 		
	 		$categorys = $this->Product->getProductCategory();
	 		$this->set("categorys",$categorys) ;
	 	}
	 	
	 }
	 
	 
	 public function del($id){
	 	$this->Supplier->delSupplier( $id  ) ;
	 	$this->response->type("json") ;
		$this->response->body( "success")   ;

		  return $this->response ;
	 }
	 
	  public function view($id = null){
	 	$this->set("id",$id) ;
	 	$Supplier = null ;
	 	if( !empty($id) ){
			 $Supplier =  $this->Supplier->getSupplier( $id  ) ;
		}
		$this->set("supplier",$Supplier) ;
		
		$categorys = $this->Product->getProductCategory();  
    	$this->set("categorys",$categorys) ;
	 }

	 public function saveSupplier($asin=null){
 		$user =  $this->getCookUser() ;
 	
		$this->Supplier->saveSupplier($this->request->data,$user,$asin) ;
		
	

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		  return $this->response ;
	 }
	 
	public function grid(){
		 $records =  $this->Supplier->getGridRecords( $this->request->query ) ;
		 $count   =  $this->Supplier->getGridCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function saveProductSupplier(){
		$user =  $this->getCookUser() ;
		$this->Supplier->saveProductSupplier($this->request->data,$user) ;
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
	public function saveProductSupplierXJ(){
		$fileName = $_FILES['supplierProductImage']["name"] ;
		$myfile = $_FILES['supplierProductImage']['tmp_name'] ;
		
		$data = $this->request->data ;
		
		$localUrl = "" ;
		if( !empty($fileName) ){
			$path = dirname(dirname(dirname(__FILE__)))."/images/supplier/".$data["id"]."/" ;
			$localUrl = "images/supplier/".$data["id"]."/".$fileName ;
			$this->creatdir($path) ;

        	move_uploaded_file($myfile,$path.$fileName);
		}
		
		$user =  $this->getCookUser() ;
		$this->Supplier->saveProductSupplierXJ($this->request->data,$user,$localUrl) ;
		
		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.opener.location.reload();window.close();</script>");
		return $this->response;
	}
	
	public function updateProductSupplierPage($asin,$supplierId){
		$this->set("asin",$asin) ;
		$this->set("supplierId",$supplierId) ;
		
		
		
		$this->set("productSupplier", $this->Product->getProductSupplierById($asin,$supplierId) ) ;
	}
	
	public function creatdir($path)
	{
		if(!is_dir($path))
		{
			if($this->creatdir(dirname($path)))
			{
				mkdir($path,0777);
				return true;
			}
		}
		else
		{
			return true;
		}
	}
}