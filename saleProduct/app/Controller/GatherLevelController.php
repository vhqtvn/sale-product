<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

/**
 * 按照获取类别进行获取
 */
class GatherLevelController extends AppController {
    public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Utils', 'Config','GatherData',"Amazonaccount","Log","GatherMarketing","Tasking","System");
	public $taskId = null ;
	
	/**
	 * 执行获取分类获取
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
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始获取基本信息..") ;
			$this->baseInfo( $accountId , $level ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始获取竞争信息..") ;
			$this->competition( $accountId , $level  ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始获取FBA信息..") ;
			$this->fba( $accountId , $level ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始获取价格信息..") ;
			$this->price( $accountId , $level  ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"开始执行竞价营销..") ;
			$this->marketing($accountId , $level ) ;
			$this->Tasking->setStep("gather_level",$level,$accountId,"执行竞价营销结束") ;
			$this->Tasking->stop("gather_level",$level,$accountId) ;
		}catch(Exception $e){
			$this->Log->saveException($this->taskId, $e );
			$this->Tasking->stop("gather_level",$level,$accountId) ;
		}
	}
	
	/**
	 * 基本信息
	 */
	public function baseInfo($accountId , $level ){
		try{
			
			$config = $this->System->getAccountPlatformConfig($accountId) ;
		
			$asinArray =  $this->Amazonaccount->getAccountProductsForLevel($accountId,$level) ;
			
			//开始获取产品信息
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
				$gatherParams = array(
						"asin"=>$asin,
						"platformId"=>$config['PLATFORM_ID'],
						"id"=>$accountId,
						"index"=>$index,
						"taskId"=>$this->taskId
				) ;
				
				$this->GatherData->asinInfoPlatform($gatherParams) ;
			} 
			//获取产品信息结束
			$this->Log->savelog($this->taskId,"end!" );
		}catch(Exception $e){
			try{
				$this->Log->saveException($this->taskId, $e );
			}catch(Exception $e){}
		}
	}
	
	/**
	 * 竞争信息
	 */
	public function competition($accountId , $level ){
		try{
			$config = $this->System->getAccountPlatformConfig($accountId) ;
			
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
				
				$gatherParams = array(
						"asin"=>$asin,
						"platformId"=>$config['PLATFORM_ID'],
						"id"=>$accountId,
						"index"=>$index,
						"taskId"=>$this->taskId
				) ;
				
				$this->GatherData->asinCompetitionPlatform($gatherParams) ;
			}
			$this->Log->savelog($this->taskId, "end!" );
		
		}catch(Exception $e){
			$this->Log->saveException($this->taskId, $e );
		}
	}
	
	/**
	 * fba竞争信息
	 */
	public function fba($accountId , $level ){
		//更新获取状态
		try{
			$config = $this->System->getAccountPlatformConfig($accountId) ;
			
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
				
				$gatherParams = array(
						"asin"=>$asin,
						"platformId"=>$config['PLATFORM_ID'],
						"id"=>$accountId,
						"index"=>$index,
						"taskId"=>$this->taskId
				) ;
				
				$this->GatherData->asinFbasPlatform($gatherParams) ;
			}
			$this->Log->savelog($this->taskId, "end!" );
		}catch(Exception $e){
			$this->Log->saveException($this->taskId, $e );
		}
	}
	
	/**
	 * 价格信息
	 */
	public function price($accountId , $level ){
		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;

		try{
			$config = $this->System->getAccountPlatformConfig($accountId) ;
			$asinArray =  $this->Amazonaccount->getAccountProductsForLevel($accountId,$level) ;
			
			//开始获取产品信息
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
				
				$gatherParams = array(
						"asin"=>$asin,
						"platformId"=>$config['PLATFORM_ID'],
						"code"=>$account['CODE'],
						"condition"=>$condition,
						"id"=>$accountId,
						"index"=>$index,
						"taskId"=>$this->taskId
				) ;
				
				$this->GatherData->asinPrice( $gatherParams ) ;
				
			} 
			//获取产品信息结束
			$this->Log->savelog($this->taskId,"end!" );
		}catch(Exception $e){
			$this->Log->saveException($this->taskId, $e );
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
		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
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
			
			if(empty($processPrice)) {//为空，等于当前价格
				$processPrice = $price ;
			} ;
			
			if( $processPrice < $execPrice ){
				$processPrice = $execPrice ;
			}
			
			if($processPrice == $price){
				//do nothing
			}else{
				//$this->Log->savelog($this->taskId, "SKU:::$sku  ==>$price " );
				$price = $processPrice - $product['SHIPPING_PRICE'] ;
				if( round($price,2) != $product['PRICE'] )
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
    	
		$Feed = $this->Amazonaccount->getPriceFeed($MerchantIdentifier , $_products) ;
		
		$url = $this->Utils->buildUrl($account,"taskAsynAmazon/price") ;
		$url = $url.'?1' ;
		
		$this->triggerRequest($url,array("feed"=>$Feed )) ;
	}
    
}