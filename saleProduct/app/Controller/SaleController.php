<?php

class SaleController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Sale', 'Product','Supplier');
	
	 public function productFilter($id = null , $type = null){
	 	$this->set('id', $id );
	 	$this->set('type', $type );
	 }

	 public function save(){
		$this->Sale->saveSeller($this->request->data) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	 /**
	  * 保存采购计划
	  */
	 public function savePurchasePlan($planId=null){
	 	$user =  $this->getCookUser() ;
	 	$this->Sale->savePurchasePlan($this->request->data,$user,$planId) ;

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
	 
	 public function savePurchasePlanProduct(){
	 	$user =  $this->getCookUser() ;
	 	$this->Sale->savePurchasePlanProduct($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	 public function updatePurchasePlanProductStatus(){
	 	$user =  $this->getCookUser() ;
	 	$this->Sale->updatePurchasePlanProductStatus($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	 
	public function filter($type){
		$this->set('type', $type );
	}
	
	public function details1($filterId,$asin,$type,$status){
		$details = $this->Product->getProductDetails($asin) ;
		$images   = $this->Product->getProductImages($asin) ;
		$competitions  = $this->Product->getProductCompetitionDetails($asin) ;
		$fbas  = $this->Product->getProductFbaDetails($asin) ;
		$rankings  = $this->Product->getProductRankingDetails($asin) ;
		$flows  = $this->Product->getProductFlowDetails($asin) ;
		$strategys  = $this->Product->getProductStrategy() ;
		$suppliers  = $this->Product->getProductSupplier($asin) ;
		
		$this->set('details', $details);
		$this->set('images', $images);
		$this->set('competitions', $competitions);
		$this->set('fbas', $fbas);
		$this->set('rankings', $rankings);
		$this->set('flows', $flows);
		$this->set('strategys', $strategys);
		$this->set('suppliers', $suppliers);
		
		$this->set('filterId', $filterId );
		$this->set('asin', $asin );
		$this->set('type', $type );
		$this->set('status', $status );
	}
	
	
	
	public function productKnowlege(){

		//更新状态
		$this->Sale->productKnowlege($this->request->data) ;
	
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
	public function productTestStatus(){

		//更新状态
		$this->Sale->updateProductTestStatus($this->request->data) ;
	
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
	/*description:5555555555555
		filterId:948
		asin:B00005NPOB
		status:2*/
	public function productFlowProcess(){
		$query = $this->request->data ;
		$status = $query["status"] ;
		$description = $query["description"] ;
		$filterId    = $query["filterId"] ;
		$asin = $query["asin"] ;
		$strategy = $query["strategy"] ;
		/*print_r( $this->request ) ; 
		print_r($this->request->data) ;
		echo ">>>>>>>>>>>>>>>>>".$description ;*/
		
		
		//更新状态
		$this->Sale->updateProductFilterStatus($this->request->data) ;
		
		//添加备注
		//if( trim($description) != "" ){
		$this->Product->updateProductComment($asin,$description,$strategy) ;
		//}
		
		//加入黑名单
		if( $status == 3 ){
			$user =  $this->getCookUser() ;
			$this->Sale->removeProduct($this->request->data,$user) ;
		}else{
			
		}
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
	public function removeProduct(){
		//$this->request->data
		$this->Sale->removeProduct($this->request->data) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
	public function purchaseTodoList(){
		
	}
	
	public function purchaseList($flag){
		$this->set('flag', $flag);
	}
	
	public function purchaseListPrint($planId){
		$plans = $this->Sale->getPurchasePlan($planId) ;
		
		$this->set('planId', $planId);
		$this->set('plan', $plans);
	}
	
	
	public function deletePurchaseList(){}
	
	public function createPurchasePlan($planId=null){
		$test = $this->Sale->getPurchasePlan($planId) ;
		
		if( !empty($planId) ){
			$this->set('plan', $test);
		}else{
			$this->set('plan', null);
		}
		$this->set('planId', $planId);
	}
	
	public function addPurchasePlanOuterProduct($planId){
		$this->set('planId', $planId);
	}
	
	public function selectPurchaseProduct($planId){
		$this->set('planId', $planId);
	}
	
	public function editPurchasePlanProduct($planProductId){
		
		$product = $this->Sale->getProductPlanProduct($planProductId) ;
		
		$sku = $product["SKU"]  ;
		$user =  $this->getCookUser() ;
		$suppliers = $this->Supplier->getProductSuppliersBySku( $sku  ) ;
		
		$this->set('supplier', $suppliers );
		$this->set('product', $product);
		$this->set('planProductId', $planProductId);
		$this->set('user', $user);
	}
	
	function editPurchaseProductStatus($id , $status){
		$this->set('id', $id );
		$this->set('status', $status);
	}
	
	
	public function deletePurchasePlanProduct(){
		$user =  $this->getCookUser() ;
		
		$this->Sale->deletePurchasePlanProduct($this->request->data,$user) ;
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
	public function exportForPurchasePlanDetails($planId){
		$test = $this->Sale->getPurchasePlan($planId) ;
		$name = $test[0]['sc_purchase_plan']["NAME"] ;
		
		$filename =  "$name.csv";
	    header("Content-type:text/csv");
	    header("Content-Disposition:attachment;filename=".$filename);
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
	    header('Expires:0');
	    header('Pragma:public');
	   
	    $details = $this->Sale->getPurchasePlanDetails($planId) ;
	    
	    echo iconv('utf-8','gb2312','SKU,ASIN,货品名称,采购数量,产品报价,供应商,样品,样品编码')."\n" ;
	    
	    foreach($details as $detail){
	    	$detail  = $this->Sale->formatObject($detail) ;
	    	$sku = $detail['SKU'] ;
	    	$title = $detail['TITLE'] ;
	    	$plan_num = $detail['PLAN_NUM'] ;
	    	$quote_price = $detail['QUOTE_PRICE'] ;
	    	$providor = $detail['PROVIDOR_NAME'] ;
	    	$sample = $detail['SAMPLE'] ;
	    	$sample_code = $detail['SAMPLE_CODE'] ;
	    	$asin = isset($detail['ASIN'])?$detail['ASIN']:"" ;
	    	
	    	echo iconv('utf-8','gb2312', $sku.','.$asin.','.$title.','.$plan_num.','.$quote_price.','.$providor.','.$sample.','.$sample_code)."\n" ;
	    }
	    
	}
}