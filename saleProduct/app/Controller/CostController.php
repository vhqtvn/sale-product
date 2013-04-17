<?php

class CostController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
	
	 public function lists(){
	 	$user =  $this->getCookUser() ;
	 	$this->set("user",$user) ;
	 }
	 
	 public function listAsin($asin){
	 	$user =  $this->getCookUser() ;
	 	$this->set("user",$user) ;
	 	$this->set("asin",$asin) ;
	 }
	 
	 public function add( $asin , $id = null ){
	 	$this->set("id",$id) ;
	 	$this->set("asin",$asin) ;
	 	$Supplier = null ;
	 	if( !empty($id) ){
			 $Supplier =  $this->Cost->getProductCost( $id  ) ;
		}
		$this->set("productCost",$Supplier) ;
		$user =  $this->getCookUser() ;
	 	$this->set("user",$user) ;
	 }
	 
	 public function addBySku( $sku , $id = null ){
	 	$this->set("id",$id) ;
	 	$this->set("sku",$sku) ;
	 	$Supplier = null ;
	 	if( !empty($id) ){
	 		$Supplier =  $this->Cost->getProductCost( $id  ) ;
	 	}
	 	$this->set("productCost",$Supplier) ;
	 	$user =  $this->getCookUser() ;
	 	$this->set("user",$user) ;
	 }
	 
	 public function view( $asin , $type ){
	 	$this->set("id",$type) ;
	 	$this->set("asin",$asin) ;
	    $Supplier =  $this->Cost->getProductCostByAsinType( $asin , $type   ) ;

		$this->set("productCost",$Supplier) ;
		$user =  $this->getCookUser() ;
	 	$this->set("user",$user) ;
	 }

	 public function saveCost(){
	 		$user =  $this->getCookUser() ;
	 	
			$this->Cost->saveCost($this->request->data,$user) ;

			$this->response->type("json") ;
			$this->response->body( "success")   ;

		  return $this->response ;
	 }
}