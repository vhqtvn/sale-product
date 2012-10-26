<?php

class MarketingController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
     var $uses = array('Marketing', 'Product','Supplier');
	
	 /**
	  * 保存采购计划
	  */
	 public function saveMarketingTestPlan($planId=null){
	 	$user =  $this->getCookUser() ;
	 	$this->Marketing->saveMarketingTestPlan($this->request->data,$user,$planId) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	 public function savePurchasePlanProducts(){
	 	$user =  $this->getCookUser() ;
	 	$this->Sale->savePurchasePlanProducts($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	 public function saveMarketingTestProducts(){
	 	$user =  $this->getCookUser() ;
	 	$this->Marketing->saveMarketingTestProducts($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	  public function saveMarketingTestProduct(){
	 	$user =  $this->getCookUser() ;
	 	$this->Marketing->saveMarketingTestProduct($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 

	public function lists(){}
	
	public function createMarketingTestPlan(){}
	
	public function marketingTestGrid(){
		$user =  $this->getCookUser() ;
		$records=  $this->Marketing->getMarketingTestGridRecords( $this->request->query ,$user ) ;
		$count=  $this->Marketing->getMarketingTestGridCount( $this->request->query ,$user ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function marketingTestDetailsGrid(){
		$user =  $this->getCookUser() ;
		$records=  $this->Marketing->getMarketingTestDetailsGridRecords( $this->request->query ,$user ) ;
		$count=  $this->Marketing->getMarketingTestDetailsGridCount( $this->request->query ,$user ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function exportForMarketingTestDetails($planId){
		$test = $this->Marketing->getMarketingTest($planId) ;
		$name = $test[0]['sc_marketing_test']["NAME"] ;
		
		$filename =  "$name.csv";
	    header("Content-type:text/csv");
	    header("Content-Disposition:attachment;filename=".$filename);
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
	    header('Expires:0');
	    header('Pragma:public');
	   
	   	echo iconv('utf-8','gb2312','ASIN,试销指导价格')."\n" ;
	    $details = $this->Marketing->getMarketingTestDetails($planId) ;
	    foreach($details as $detail){
	    	$asin = $detail['sc_marketing_test_details']['ASIN'] ;
	    	$guidePrice = $detail['sc_marketing_test_details']['GUIDE_PRICE'] ;
	    	
	    	echo $asin.','.$guidePrice."\n" ;
	    }
	    
	}
	
	
	public function createPurchasePlan($planId=null){
		$this->set('planId', $planId);
	}
	
	public function addMarketingTestOuterProduct($planId){
		$this->set('planId', $planId);
	}
	
	public function selectMarketingTestProducts($planId){
		$this->set('planId', $planId);
	}
	
	public function editMarketingTestProduct($planProductId){
		
		$product = $this->Marketing->getMarketingTestProduct($planProductId) ;
		$asin = $product[0]['sc_marketing_test_details']["ASIN"]  ;
		
		$suppliers = $this->Supplier->getProductSuppliers( $asin  ) ;
		
		$this->set('supplier', $suppliers );
		$this->set('product', $product);
		$this->set('planProductId', $planProductId);
	}
	
	public function deleteMarketingTestProduct(){
		$this->Marketing->deleteMarketingTestProduct($this->request->data) ;
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
}