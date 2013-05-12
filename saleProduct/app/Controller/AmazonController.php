<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App :: import('Vendor', 'Amazon');
App :: import('Vendor', 'AmazonOrder');

class AmazonController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Amazonaccount', 'Config','Utils');
    
    public function priceimportPage($accountId){
    	$this->set("accountId",$accountId) ;
    }
    
    public function priceimportLog($accountId){
    	$this->set("accountId",$accountId) ;
    }
    
    public function quantityimportPage($accountId){
    	$this->set("accountId",$accountId) ;
    }
    
     public function quantityimportLog($accountId){
    	$this->set("accountId",$accountId) ;
    }
    
    /**
     * 库存分配
     */
    public function quantity(){
    	$id = "UC_Quantity_".date('U') ;
    	
    	$params = $this->requestMap()  ;
    	//$accountId = $params["accountId"] ;
    	
    	$user =  $this->getCookUser() ;
    	$loginId = $user["LOGIN_ID"] ;
    	
    	foreach( $params as $key=>$value ){
    		$accountId = $key ;
    		$skuQuantity = $value ;
    	
    		$_products = array() ;
    		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId)  ;
    		$account = $account[0]['sc_amazon_account']  ;
    		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
    		foreach ( $skuQuantity as $kq ){
    			$_products[] = array("SKU"=>$kq['sku'],"FEED_QUANTITY"=>$kq['quantity']) ;
    		}
    		$Feed = $this->Amazonaccount->getQuantityFeed($MerchantIdentifier , $_products) ;
    	
    		$url = $this->Utils->buildUrl($account,"taskAsynAmazon/quantity") ;
    		file_get_contents($url."?feed=".urlencode($Feed));
    		//echo $Feed;
    	 }
    		
    	 $this->response->type("html");
    	 $this->response->body("");
    	 return $this->response;
    }
    
    /**
     * 价格调整
     */
    public function price($accountId){
    	$id = "UC_Price_".date('U') ;
    
    	$params = $this->requestMap()  ;
    	//$accountId = $params["accountId"] ;
    
    	$user =  $this->getCookUser() ;
    	$loginId = $user["LOGIN_ID"] ;
    
    	foreach( $params as $key=>$value ){
    		$accountId = $key ;
    		$skuQuantity = $value ;
    
    		$_products = array() ;
    		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId)  ;
    		$account = $account[0]['sc_amazon_account']  ;
    		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
    		foreach ( $skuQuantity as $kq ){
    			$_products[] = array("SKU"=>$kq['sku'],"FEED_QUANTITY"=>$kq['quantity']) ;
    		}
    		$Feed = $this->Amazonaccount->getQuantityFeed($MerchantIdentifier , $_products) ;
    
    		$url = $this->Utils->buildUrl($account,"taskAsynAmazon/quantity") ;
    		file_get_contents($url."?feed=".urlencode($Feed));
    		//echo $Feed;
    	}
    
    	$this->response->type("html");
    	$this->response->body("");
    	return $this->response;
    }
    
    public function listOrders($accountId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$params = $this->requestMap()  ;
    	
    	$queryString = "?1=1" ;
    	if( isset($params["LastUpdatedAfter"]) ){
    		$queryString .= '&LastUpdatedAfter='.$params["LastUpdatedAfter"] ;
    	}
    	if( isset($params["LastUpdatedBefore"]) ){
    		$queryString .= '&LastUpdatedBefore='.$params["LastUpdatedBefore"] ;
    	}
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/listOrders") ;
    	
    	file_get_contents($url.$queryString);
    	
    	$this->response->type("json") ;
    	$this->response->body( "success")   ;
    
    	return $this->response ;
    }
    
    public function listOrderItems($accountId,$orderId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/listOrderItems") ;
    	$random = date("U") ;

    	file_get_contents($url."/$orderId"."?".$random);
    	
    	$this->response->type("json") ;
    	$this->response->body( "success")   ;
    	
    	return $this->response ;
    }
    
    public function getFBAInventory1($accountId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/startAsynAmazonFba") ;
    	$random = date("U") ;
    	
    	file_get_contents($url."?".$random);
    	
    	$this->response->type("json") ;
    	$this->response->body( "success")   ;
    	
    	return $this->response ;
    }
    
    
    public function getFBAInventory2($accountId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/asynAmazonFba") ;
    	$random = date("U") ;
    	
    	file_get_contents($url."?".$random);
    	
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
    
    public function getFBAInventory3($accountId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/asynAmazonFba") ;
    	$random = date("U") ;
    	
    	file_get_contents($url."?".$random);
    	
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }

    public function getProductReport1($accountId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/startAsynAmazonProducts") ;
    	$random = date("U") ;
    	
    	file_get_contents($url."?".$random);
    	
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
    
    public function getProductReport2($accountId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/asynAmazonProducts") ;
    	$random = date("U") ;
    	
    	file_get_contents($url."?".$random);
    	
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
    
    public function getProductReport3($accountId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/asynAmazonProducts") ;
    	$random = date("U") ;
    	
    	file_get_contents($url."?".$random);
    	
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
    
    public function getProductActiveReport1($accountId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/startAsynAmazonActiveProducts") ;
    	$random = date("U") ;
    	
    	file_get_contents($url."?".$random);
    	
    	$this->response->type("json") ;
		$this->response->body( "success")   ;
		return $this->response ;
    }
    
    public function getProductActiveReport2($accountId){
    	
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/asynAmazonActiveProducts") ;
    	$random = date("U") ;
    	
    	file_get_contents($url."?".$random);
    	
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
    
    public function getProductActiveReport3($accountId){
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	
    	$url = $this->Utils->buildUrl($account,"taskAsynAmazon/asynAmazonActiveProducts") ;
    	$random = date("U") ;
    	
    	file_get_contents($url."?".$random);
    	
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
    
    public function getFeedSubmissionResult($accountId,$feedSubmissionId){
    	$account = $this->Amazonaccount->getAccount($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		
		$amazon->getFeedSubmissionResult($accountId,$feedSubmissionId) ;//'5628762486' 
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
    }
    
	 public function getProductAsyns($id){
	 	$user =  $this->getCookUser() ;
	 	
	 	$accountAsyn = $this->Amazonaccount->getAccountAsyn($id,"_GET_FLAT_FILE_OPEN_LISTINGS_DATA_") ;
	 	
		$this->response->type("text") ;
		$this->response->body( json_encode( $accountAsyn ) )   ;

		return $this->response ;
	 }
	 
	  public function getProductActiveAsyns($id){
	 	$user =  $this->getCookUser() ;
	 	
	 	$accountAsyn = $this->Amazonaccount->getAccountAsyn($id,"_GET_MERCHANT_LISTINGS_DATA_") ;
	 	
		$this->response->type("text") ;
		$this->response->body( json_encode( $accountAsyn ) )   ;

		return $this->response ;
	 }
	 
	 //////////////////////////////////////////////////////
	 public function getFeedReport1($accountId,$reportType){
	 	$account = $this->Amazonaccount->getAccount($accountId) ;
	 	$account = $account[0]['sc_amazon_account'] ;
	 	$amazon = new Amazon(
	 			$account['AWS_ACCESS_KEY_ID'] ,
	 			$account['AWS_SECRET_ACCESS_KEY'] ,
	 			$account['APPLICATION_NAME'] ,
	 			$account['APPLICATION_VERSION'] ,
	 			$account['MERCHANT_ID'] ,
	 			$account['MARKETPLACE_ID'] ,
	 			$account['MERCHANT_IDENTIFIER']
	 	) ;
	 	
	 	$params = $this->request->data  ;
	 	
	 	$request = $amazon->getFeedReport1($accountId,$reportType,$params) ;
	 	if( !empty($request) ){
	 		$user =  $this->getCookUser() ;
	 		$this->Amazonaccount->saveAccountAsyn($accountId ,$request , $user) ;
	 	}
	 
	 	$this->response->type("json") ;
	 	$this->response->body( "success")   ;
	 
	 	return $this->response ;
	 }
	 
	 public function getFeedReport2($accountId,$reportType){
	 	$account = $this->Amazonaccount->getAccount($accountId) ;
	 	$accountAsyn = $this->Amazonaccount->getAccountAsyn($accountId,$reportType) ;
	 
	 	$account = $account[0]['sc_amazon_account'] ;
	 	$amazon = new Amazon(
	 			$account['AWS_ACCESS_KEY_ID'] ,
	 			$account['AWS_SECRET_ACCESS_KEY'] ,
	 			$account['APPLICATION_NAME'] ,
	 			$account['APPLICATION_VERSION'] ,
	 			$account['MERCHANT_ID'] ,
	 			$account['MARKETPLACE_ID'] ,
	 			$account['MERCHANT_IDENTIFIER']
	 	) ;
	 
	 	$request = $amazon->getFeedReport2($accountId,$reportType,$accountAsyn[0]['sc_amazon_account_asyn']['REPORT_REQUEST_ID']) ;
	 	if( !empty($request) ){
	 
	 		$user =  $this->getCookUser() ;
	 		$this->Amazonaccount->updateAccountAsyn2($accountId ,$request , $user) ;
	 	}
	 
	 	$this->response->type("json") ;
	 	$this->response->body( "success")   ;
	 
	 	return $this->response ;
	 }
	 
	 public function getFeedReport3($accountId,$reportType){
	 	$account = $this->Amazonaccount->getAccount($accountId) ;
	 	$account = $account[0]['sc_amazon_account'] ;
	 	$amazon = new Amazon(
	 			$account['AWS_ACCESS_KEY_ID'] ,
	 			$account['AWS_SECRET_ACCESS_KEY'] ,
	 			$account['APPLICATION_NAME'] ,
	 			$account['APPLICATION_VERSION'] ,
	 			$account['MERCHANT_ID'] ,
	 			$account['MARKETPLACE_ID'] ,
	 			$account['MERCHANT_IDENTIFIER']
	 	) ;
	 	$accountAsyn = $this->Amazonaccount->getAccountAsyn($accountId,$reportType) ;
	 	$reportId = $accountAsyn[0]['sc_amazon_account_asyn']['REPORT_ID'] ;
	 
	 	$this->Amazonaccount->asynProductStatusStart($accountId , $reportType) ;
	 	$request = $amazon->getFeedReport3($accountId,$reportType , $reportId ) ;
	 	$this->Amazonaccount->asynProductStatusEnd($accountId , $reportType) ;
	 
	 	//if( !empty($request) ){
	 	$user =  $this->getCookUser() ;
	 	$this->Amazonaccount->updateAccountAsyn3($accountId ,array("reportId"=>$reportId,"reportType"=>$reportType) , $user) ;
	 	//}
	 
	 	$this->response->type("json") ;
	 	$this->response->body( "success")   ;
	 
	 	return $this->response ;
	 }
}