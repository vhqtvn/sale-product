<?php

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");


/**
 * 单个产品获取
 */
class GatherProductController extends AppController {
    public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Utils', 'Config','GatherData','GatherMarketing',"Amazonaccount","Log","Tasking","System");
	public $taskId = null ;
	
	public function execute($asin , $platformId ){
		$accountId = null ;
		$productId = null ;
		$status = $this->Tasking->status("gather_product",$asin,$accountId) ;
		if( $status ){//执行中
			$this->response->type("json");
			$this->response->body("");
			return $this->response;
		}else{
			$this->taskId = $this->Tasking->start("gather_product",$asin,$accountId) ;
		}
		try{
			try{
				$this->baseInfo( $asin ,$platformId  ) ;
				$this->competition( $asin ,$platformId  ) ;
				$this->fba( $asin ,$platformId  ) ;
			
				if(!empty($productId)){
					$this->price( $productId ,$accountId  ) ;
					$this->marketing($productId , $accountId ) ;
				}
				$this->Tasking->stop("gather_product",$asin,$accountId) ;
			}catch(Exception $e){
				$this->Log->saveException($this->taskId, $e );
				$this->Tasking->stop("gather_product",$asin,$accountId) ;
			}
		}catch(Exception $e){
			$this->response->type("json");
			$this->response->body("result->".$e->getMessage());
			return $this->response;
		}
		$this->response->type("json");
		$this->response->body("");
		return $this->response;
	}
	
	/**
	 * 基本信息
	 */
	public function baseInfo($asin , $platformId  ){
		$gatherParams = array(
				"asin"=>$asin,
				"platformId"=>$platformId,
				"id"=>"",
				"index"=>"",
				"taskId"=>""
		) ;
		$this->GatherData->asinInfoPlatform($gatherParams) ;
	}
	
	/**
	 * 竞争信息
	 */
	public function competition($asin , $platformId = null ){
		$gatherParams = array(
				"asin"=>$asin,
				"platformId"=>$platformId,
				"id"=>"",
				"index"=>"",
				"taskId"=>""
		) ;
		$this->GatherData->asinCompetitionPlatform($gatherParams) ;
	}
	
	/**
	 * fba竞争信息
	 */
	public function fba($asin , $platformId ){
		$gatherParams = array(
				"asin"=>$asin,
				"platformId"=>$platformId,
				"id"=>"",
				"index"=>"",
				"taskId"=>""
		) ;
		$this->GatherData->asinFbasPlatform($gatherParams ) ;
	}
	
	/**
	 * 价格信息
	 */
	public function price($productId , $accountId ){
		$config = $this->System->getAccountPlatformConfig($accountId) ;
		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		
		$product =  $this->Amazonaccount->getAccountProduct($productId) ;
		$asin = $product[0]['sc_amazon_account_product']['ASIN'] ;
		$condition = $product[0]['sc_amazon_account_product']['CONDITION'] ;
		
		$gatherParams = array(
				"asin"=>$asin,
				"platformId"=>$config['PLATFORM_ID'],
				"code"=>$account['CODE'],
				"condition"=>$condition,
				"id"=>$accountId,
				"index"=>"",
				"taskId"=>""
		) ;
		
		$this->GatherData->asinPrice( $gatherParams ) ;

		$this->GatherData->asinPrice($asin,$account['CODE'],$condition,$accountId ) ;

	}
	
	/**
	 * 营销
	 */
	public function marketing($productId , $accountId ){
		//获取分类产品信息
		
		//获取账号相关信息
		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		$accountName = $account['NAME'] ;
		
		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
		

		//获取单个产品
		$product =  $this->Amazonaccount->getAccountProduct($productId) ;
		$product = $product[0]['sc_amazon_account_product'] ;
		$asin = $product['ASIN'] ;
		$sku = $product['SKU'] ;
		//单个产品所属分类
		$productCategory = $this->Amazonaccount->getAmazonProductCategoryBySKU($accountId,$sku) ;
		
		//当前价格
		$price = $product['PRICE'] + $product['SHIPPING_PRICE'] ;
		//最低限价
		$execPrice = $product['EXEC_PRICE']  ;
		if( empty( $product['EXEC_PRICE'] ) ){
			$product['EXEC_PRICE'] = $price ;
			$execPrice = $price ;
		}
		
		if(!empty($productCategory)){
			$productCategory = $productCategory[0]['sc_amazon_product_category'] ;
		}
		
		//_processStratery
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
			$price = $processPrice - $product['SHIPPING_PRICE'] ;
			$_products[] = array("SKU"=>$sku,"FEED_PRICE"=>$price,'ORI_PRICE'=>$product['PRICE']) ;
		}

		
		if( count($_products) <=0 ){
			$this->response->type("html");
			$this->response->body("nothing to update");
			return $this->response;
		}
		
		$Feed = $this->Amazonaccount->getPriceFeed($MerchantIdentifier , $_products) ;
    	
		$url = $this->Utils->buildUrl($account,"taskAsynAmazon/price") ;
		$url = $url.'?1' ;
		
		$this->triggerRequest($url,array("feed"=>$Feed )) ;
	}
}