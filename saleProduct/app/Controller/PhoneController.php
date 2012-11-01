<?php

class PhoneController extends AppController {
	var $uses = array('Salegrid','Product','SqlUtils');
	
	public function test(){
		 $user_agent = $_SERVER['HTTP_USER_AGENT'];
		  //This can also be used to detect a mobile device
		 $accept = $_SERVER['HTTP_ACCEPT'];
		 
		 echo 'userAgent::::'.$user_agent.'<br><br>' ;
		 echo 'accept:::::::'.$accept.'<br><br>' ;
	}
	
    public function purchaseExList(){
    	$user =  $this->getCookUser() ;
    	$query = array('limit'=>100,'start'=>0,'curPage'=>0,'end'=>100,
			'sqlId'=>"sql_purchase_plan_list",'loginId'=>$user['LOGIN_ID']) ;
    	
    	$recordSql = $this->SqlUtils->getRecordSql( $query) ;
    	$records = $this->SqlUtils->query($recordSql) ;

		$this->set("purchaseList",$records);
    }
    
    public function purchasePlanDetails($planId){
    	$user =  $this->getCookUser() ;
    	$query = array('limit'=>100,'start'=>0,'curPage'=>0,'end'=>100,
			'sqlId'=>"sql_purchase_plan_details_list",'planId'=>"$planId") ;
    	
    	$recordSql = $this->SqlUtils->getRecordSql( $query) ;
    	$records = $this->SqlUtils->query($recordSql) ;
    	
    	
    	//$query = array('limit'=>100,'start'=>0,'curPage'=>0,'end'=>100,'planId'=>"$planId") ;
		//$records=  $this->Salegrid->getPurchasePlanDetailsRecords( $query ,$user,2 ) ;//2采购执行列表
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