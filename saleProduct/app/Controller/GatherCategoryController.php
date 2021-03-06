<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

/**
 * 按照获取类别进行获取
 */
class GatherCategoryController extends AppController {
    public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Utils', 'Config','GatherData',
						'GatherMarketing',"Amazonaccount","Log","Tasking");
	public $taskId = null ;
	
	public function doGather($accountId , $categoryId){
		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		
		$url = $this->Utils->buildUrl($account,"gatherCategory/execute") ;
		$url = $url.'/'.$categoryId ;
		$random = date("U") ;
		
		file_get_contents($url."?".$random);
	}
	
	public function execute( $accountId , $categoryId ){
		$status = $this->Tasking->status("gather_category",$categoryId,$accountId) ;
		if( $status ){//执行中
			return ;
		}else{
			$_id = $this->Tasking->start("gather_category",$categoryId,$accountId) ;
			$this->taskId = $_id ;
		}
		try{
			$this->Tasking->setStep("gather_category",$categoryId,$accountId,"开始获取基本信息..") ;
			$this->baseInfo( $accountId , $categoryId ) ;
			$this->Tasking->setStep("gather_category",$categoryId,$accountId,"开始获取竞争信息..") ;
			$this->competition( $accountId , $categoryId  ) ;
			$this->Tasking->setStep("gather_category",$categoryId,$accountId,"开始获取FBA信息..") ;
			$this->fba( $accountId , $categoryId ) ;
			$this->Tasking->setStep("gather_category",$categoryId,$accountId,"开始获取价格信息..") ;
			$this->price( $accountId , $categoryId  ) ;
			$this->Tasking->setStep("gather_category",$categoryId,$accountId,"开始执行竞价营销..") ;
			$this->marketing($accountId , $categoryId ) ;
			$this->Tasking->setStep("gather_category",$categoryId,$accountId,"执行竞价营销结束") ;
			$this->Tasking->stop("gather_category",$categoryId,$accountId) ;
		}catch(Exception $e){
			$this->Log->saveException($this->taskId, $e );
			$this->Tasking->stop("gather_category",$categoryId,$accountId) ;
		}
	}
	
	/**
	 * 基本信息
	 */
	public function baseInfo($accountId , $categoryId ){
		try{
			$config = $this->System->getAccountPlatformConfig($accountId) ;
			$asinArray =  $this->Amazonaccount->getAccountProducts($accountId,$categoryId) ;
			
			//开始获取产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				$asin = $_asin['sc_amazon_account_product']['ASIN'] ;
				
				if( $this->Tasking->isStop("gather_category",$categoryId,$accountId) ){
					$this->Tasking->stop("gather_category",$categoryId,$accountId) ;
					exit("exit gather product as baseinfo for ".$asin ) ;
				}
				
				$index = $index + 1 ;
				$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] details  " );
				
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
	public function competition($accountId , $categoryId ){
		try{
			//获取商家产品asin
			$config = $this->System->getAccountPlatformConfig($accountId) ;
			$array = $this->Amazonaccount->getAccountProducts($accountId,$categoryId) ;

			$index = 0 ;
			$this->Log->savelog($this->taskId, "start gather competition" );
			foreach( $array as $arr ){
				if( $this->Tasking->isStop("gather_category",$categoryId,$accountId) ){
					$this->Tasking->stop("gather_category",$categoryId,$accountId) ;
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
	public function fba($accountId , $categoryId ){
		//更新获取状态
		try{
			$config = $this->System->getAccountPlatformConfig($accountId) ;
			$array =  $this->Amazonaccount->getAccountProducts($accountId,$categoryId) ;
			$index = 0 ;
			$this->Log->savelog($this->taskId, "start gather fba" );
			foreach( $array as $arr ){
				
				if( $this->Tasking->isStop("gather_category",$categoryId,$accountId) ){
					$this->Tasking->stop("gather_category",$categoryId,$accountId) ;
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
	public function price($accountId , $categoryId ){
		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;

		try{
			$config = $this->System->getAccountPlatformConfig($accountId) ;
			$asinArray =  $this->Amazonaccount->getAccountProducts($accountId,$categoryId) ;
			
			//开始获取产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				
				if( $this->Tasking->isStop("gather_category",$categoryId,$accountId) ){
					$this->Tasking->stop("gather_category",$categoryId,$accountId) ;
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
	public function marketing($accountId , $categoryId ){
		//获取分类产品信息
		$products = $this->Amazonaccount->getAccountProductsForCategorySale( $accountId , $categoryId ) ;
	
		//获取账号相关信息
		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		$accountName = $account['NAME'] ;
		
		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
		
		$_products = array() ;
		for( $i = 0 ;$i < count($products) ;$i++  ){
			
			if( $this->Tasking->isStop("gather_category",$categoryId,$accountId) ){
					$this->Tasking->stop("gather_category",$categoryId,$accountId) ;
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
				$processPrice = $price;
			} ;
			
			if( $processPrice < $execPrice ){
				$processPrice = $execPrice ;
			}
			
			if($processPrice == $price){
				//do nothing
			}else{
				$price = $processPrice - $product['SHIPPING_PRICE'] ;
				if( round($price,2) != $product['PRICE'] )
					$_products[] = array("SKU"=>$sku,"FEED_PRICE"=>round($price,2),'ORI_PRICE'=>$product['PRICE']) ;
			}
		}
		
		$this->Log->savelog($this->taskId, "执行价格更新记录=>".json_encode($_products) );
		
		if( count($_products) <=0 ){
			//$this->response->type("html");
			//$this->response->body("nothing to update");
			return ;// $this->response;
		}
		
		$Feed = $this->Amazonaccount->getPriceFeed($MerchantIdentifier , $_products) ;
		
		$url = $this->Utils->buildUrl($account,"taskAsynAmazon/price") ;
		$url = $url.'?1' ;
		
		$this->triggerRequest($url,array("feed"=>$Feed )) ;
	}
    
}