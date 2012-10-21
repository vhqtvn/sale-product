<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App :: import('Vendor', 'Amazon');


/**
 * 按照采集类别进行采集
 */
class GatherLevelController extends AppController {
    public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Utils', 'Config','GatherData',"Amazonaccount","Log","GatherMarketing","Tasking");
	public $taskId = null ;
	
	/**
	 * 执行采集分类采集
	 */
	public function execute( $accountId , $level ){
		$status = $this->Tasking->status("gather_level",$level,$accountId) ;
		if( $status ){//执行中
			return ;
		}else{
			$_id = $this->Tasking->start("gather_level",$level,$accountId) ;
			$this->taskId = $_id ;
		}
		
		try{
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始采集基本信息..") ;
			$this->baseInfo( $accountId , $level ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始采集竞争信息..") ;
			$this->competition( $accountId , $level  ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始采集FBA信息..") ;
			$this->fba( $accountId , $level ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始采集价格信息..") ;
			$this->price( $accountId , $level  ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始执行竞价营销..") ;
			$this->marketing($accountId , $level ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"执行竞价营销结束") ;
			$this->Tasking->stop("gather_level",$level,$accountId) ;
		}catch(Exception $e){
			$this->Log->savelog($this->taskId, "error::::".$e->getMessage() );
			$this->Tasking->stop("gather_level",$level,$accountId) ;
		}
	}
	
	/**
	 * 基本信息
	 */
	public function baseInfo($accountId , $level ){
		try{
		
			$asinArray =  $this->Amazonaccount->getAccountProductsForLevel($accountId,$level) ;
			
			//开始采集产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				
				if( $this->Tasking->isStop("gather_level",$level,$accountId) ){
					$this->Tasking->stop("gather_level",$level,$accountId) ;
					exit() ;
				}
				
				$asin = $_asin['sc_amazon_account_product']['ASIN'] ;
				$index = $index + 1 ;
				$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] details" );
				//echo $asin.'<br>' ;
				$this->GatherData->asinInfo($asin,$accountId,$index,$this->taskId) ;
			} 
			//采集产品信息结束
			$this->Log->savelog($this->taskId,"end!" );
		}catch(Exception $e){
			try{
				$this->Log->savelog($this->taskId, "error::::".$e->getMessage() );
			}catch(Exception $e){}
		}
	}
	
	/**
	 * 竞争信息
	 */
	public function competition($accountId , $level ){
		try{
			//获取商家产品asin
			$array = $this->Amazonaccount->getAccountProductsForLevel($accountId,$level) ;

			$index = 0 ;
			$this->Log->savelog($this->taskId, "start gather competition" );
			foreach( $array as $arr ){
				if( $this->Tasking->isStop("gather_level",$level,$accountId) ){
					$this->Tasking->stop("gather_level",$level,$accountId) ;
					exit() ;
				}
				
				$index = $index + 1 ;
				$asin = $arr['sc_amazon_account_product']['ASIN'] ;
				$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] competitions" );
				$this->GatherData->asinCompetition($asin,$accountId,$index,$this->taskId) ;
			}
			$this->Log->savelog($this->taskId, "end!" );
		
		}catch(Exception $e){
			$this->Log->savelog($this->taskId, "error::::".$e->getMessage() );
		}
	}
	
	/**
	 * fba竞争信息
	 */
	public function fba($accountId , $level ){
		//更新采集状态
		try{
			$array =  $this->Amazonaccount->getAccountProductsForLevel($accountId,$level) ;
			$index = 0 ;
			$this->Log->savelog($this->taskId, "start gather fba" );
			foreach( $array as $arr ){
				if( $this->Tasking->isStop("gather_level",$level,$accountId) ){
					$this->Tasking->stop("gather_level",$level,$accountId) ;
					exit() ;
				}
				
				$index = $index + 1 ;
				$asin = $arr['sc_amazon_account_product']['ASIN'] ;
				$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] fba" );
				$this->GatherData->asinFbas($asin,$accountId,$index,$this->taskId) ;
			}
			$this->Log->savelog($this->taskId, "end!" );
		}catch(Exception $e){
			$this->Log->savelog($this->taskId, "error::::".$e->getMessage() );
		}
	}
	
	/**
	 * 价格信息
	 */
	public function price($accountId , $level ){
		$account = $this->Amazonaccount->getAccount($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;

		try{
			$asinArray =  $this->Amazonaccount->getAccountProductsForLevel($accountId,$level) ;
			
			//开始采集产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				
				if( $this->Tasking->isStop("gather_level",$level,$accountId) ){
					$this->Tasking->stop("gather_level",$level,$accountId) ;
					exit() ;
				}
				
				$asin = $_asin['sc_amazon_account_product']['ASIN'] ;
				
				$condition = $_asin['sc_amazon_account_product']['ITEM_CONDITION'] ;
				$condition = $condition == 1?"used":"new" ;
				
				$index = $index + 1 ;
				$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] price" );
				$this->GatherData->asinPrice($asin,$account['CODE'],$condition,$accountId ,$index,$this->taskId) ;
				
			} 
			//采集产品信息结束
			$this->Log->savelog($this->taskId,"end!" );
		}catch(Exception $e){
			$this->Log->savelog($this->taskId, "error::::".$e->getMessage() );
		}
	}
	
	/**
	 * 营销
	 */
	public function marketing($accountId , $level ){
		$this->Log->savelog($this->taskId, "开始执行竞价营销策略" );
		
		//获取分类产品信息
		$products = $this->Amazonaccount->getAccountProductsForLevelSale( $accountId , $level ) ;
	
		//获取账号相关信息
		$account = $this->Amazonaccount->getAccount($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		$accountName = $account['NAME'] ;
		
		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
		
		$_products = array() ;
		for( $i = 0 ;$i < count($products) ;$i++  ){
			
			if( $this->Tasking->isStop("gather_level",$level,$accountId) ){
				$this->Tasking->stop("gather_level",$level,$accountId) ;
				exit() ;
			}
			
			//获取单个产品
			$product = $products[$i]['sc_amazon_account_product'] ;
			//单个产品所属分类
			$productCategory = $products[$i]['sc_amazon_product_category'] ;
			
			$sku = $product['SKU'] ;
			
			//当前价格
			$price = $product['PRICE'] + $product['SHIPPING_PRICE'] ;
			//最低限价
			$execPrice = $product['EXEC_PRICE']  ;
			if( empty( $product['EXEC_PRICE'] ) ){
				$product['EXEC_PRICE'] = $price ;
				$execPrice = $price ;
			}
			
			$processPrice = $this->GatherMarketing->_processStratery($product , $productCategory,$accountName) ;
			
			if(empty($processPrice)) {
				$processPrice = $execPrice;
			} ;
			
			if( $processPrice < $execPrice ){
				$processPrice = $execPrice ;
			}
			
			if($processPrice == $price){
				//do nothing
			}else{
				//$this->Log->savelog($this->taskId, "SKU:::$sku  ==>$price " );
				$price = $processPrice - $product['SHIPPING_PRICE'] ;
				$_products[] = array("SKU"=>$sku,"FEED_PRICE"=>round($price,2),'ORI_PRICE'=>$product['PRICE']) ;
			}
		}
		
		$this->Log->savelog($this->taskId, "执行价格更新记录=>".json_encode($_products) );
		if( count($_products) <=0 ){
			/*$this->response->type("html");
			$this->response->body("nothing to update");*/
			return ;//$this->response;
		}
		
		$Feed = $this->Amazonaccount->getPriceFeed($MerchantIdentifier , $_products) ;
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		
		
		$result = $amazon->updatePrice($accountId,$Feed,"cron") ;
		$this->Amazonaccount->saveAccountFeed($result) ;
	}
    
}