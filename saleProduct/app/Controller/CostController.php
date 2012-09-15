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
	 
	public function product(){
		$records =  $this->Cost->getProductRecords( $this->request->query ) ;
		$count   =  $this->Cost->getProductCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function productCost(){
		$records =  $this->Cost->getProductCostRecords( $this->request->query ) ;
		$count   =  $this->Cost->getProductCostCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
}