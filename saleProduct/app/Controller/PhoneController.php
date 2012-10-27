<?php

class PhoneController extends AppController {
	var $uses = array('Salegrid','Product');
	
    public function purchaseExList(){
    	$user =  $this->getCookUser() ;
    	$query = array('limit'=>100,'start'=>0,'curPage'=>0,'end'=>100) ;
		$records=  $this->Salegrid->getPurchasePlanRecords( $query ,$user,2 ) ;//2采购执行列表
		$this->set("purchaseList",$records);
    }
    
    public function purchasePlanDetails($planId){
    	$user =  $this->getCookUser() ;
    	$query = array('limit'=>100,'start'=>0,'curPage'=>0,'end'=>100,'planId'=>"$planId") ;
		$records=  $this->Salegrid->getPurchasePlanDetailsRecords( $query ,$user,2 ) ;//2采购执行列表
		$this->set("purchaseList",$records);
    }
    
    public function productDetails($asin){
    	$details = $this->Product->getProductDetails($asin) ;
		$images   = $this->Product->getProductImages($asin) ;
		$competitions  = $this->Product->getProductCompetitionDetails($asin) ;
		$fbas  = $this->Product->getProductFbaDetails($asin) ;
		$rankings  = $this->Product->getProductRankingDetails($asin) ;
		$flows  = $this->Product->getProductFlowDetails($asin) ;
		$strategys  = $this->Product->getProductStrategy() ;
		$suppliers  = $this->Product->getProductSupplier($asin) ;
		
		$this->set('asin', $asin);
		$this->set('sku', null);
		$this->set('accountId', null);
		$this->set('details', $details);
		$this->set('images', $images);
		$this->set('competitions', $competitions);
		$this->set('fbas', $fbas);
		$this->set('rankings', $rankings);
		$this->set('flows', $flows);
		$this->set('strategys', $strategys);
		$this->set('suppliers', $suppliers);
    }
    
    
}