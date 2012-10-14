<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App :: import('Vendor', 'Amazon');

class CronSaleController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Task', 'Config','Amazonaccount',"Product");
	
	/**
	 * 价格营销
	 */
	public function priceSale($accountId , $level){
		
		//获取分类产品信息
		$products = $this->Amazonaccount->getAccountProductsForLevelSale( $accountId , $level ) ;
	
		//获取账号相关信息
		$account = $this->Amazonaccount->getAccount($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		$accountName = $account['NAME'] ;
		
		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
		
		$_products = array() ;
		for( $i = 0 ;$i < count($products) ;$i++  ){
			
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
			
			$processPrice = $this->_processStratery($product , $productCategory,$accountName) ;
			
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
		}
		
		if( count($_products) <=0 ){
			$this->response->type("html");
			$this->response->body("nothing to update");
			return $this->response;
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

		$this->response->type("html");
		$this->response->body("update price for sale cron complete");
		return $this->response;

	}
	
	public function _processStratery($product ,$productCategory ,$accountName){
		
		//获取产品个性化竞争策略
		$productStratery = $product["STRATEGY"] ;
		
		//获取分类竞价策略
		//$categoryStratery = $productCategory['PRICE_STRATERY'] ;
		$categoryStratery = $this->Amazonaccount->getAmazonProductCategoryStratery($productCategory) ;
		
		//jjfxs  fjjxs jjxs VIP 
		if( empty($categoryStratery) ) {//无策略，执行默认策略
			return $this->_processStrateryForJJFXS( $product ,$productCategory,$accountName ) ;
		} ;
		
		if($categoryStratery == "jjfxs"){
			return $this->_processStrateryForJJFXS( $product ,$productCategory,$accountName ) ;
		}
		
		if($categoryStratery == "fjjxs"){
			return $this->_processStrateryForFJJXS( $product ,$productCategory,$accountName ) ;
		}
		
		if($categoryStratery == "jjxs"){
			return $this->_processStrateryForJJXS( $product ,$productCategory ,$accountName) ;
		}
		
		if($categoryStratery == "VIP"){
			return $this->_processStrateryForVIP( $product ,$productCategory,$accountName ) ;
		}
		
	}
	
	public function _processStrateryForDEFAULT($product ,$productCategory,$accountName){
		//do nothing
		return null;
	}
	
	/**
	 * 竞价非销售
	 */
	public function _processStrateryForJJFXS($product ,$productCategory,$accountName){
		$asin = $product['ASIN'] ;
		$shipPrice = $product["SHIPPING_PRICE"] ;
		$channel = $product["FULFILLMENT_CHANNEL"] ;
		$itemCondition    = $product['ITEM_CONDITION'] ;
		
		if( (!empty($channel)) &&  strpos($channel,"AMAZON") === 0 ){//fba产品
			//$fbas  = $this->Product->getProductFbaDetails($asin) ;
			return null ;
		}else{
			$competitions  = $this->Product->getProductCompetitionDetails($asin) ;
			
			$prices = array() ;
			$count = 0 ;
			foreach($competitions as $com){
				$com = $com['sc_sale_competition_details'] ;
				
				$type = $com['TYPE'] ;
				$_price = $com['SELLER_PRICE'] + $com['SELLER_SHIP_PRICE'] ;
				
				if( $com['SELLER_NAME'] == $accountName ){//owner
					continue ;
				}
				
				if( $itemCondition == 11 ){//new
					$count++ ;
					$prices[] = $_price ;
				}else  if($itemCondition == 1){//used
					$count++ ;
					$prices[] = $_price ;
				}
			}
			//****************************************************************
			//应用定价策略，暂时硬编码
			//****************************************************************
			$doPrice = $prices[$count-1] ;
			if( $count<=4 ){//如果竞争对手小于或等于4个
				$_ = $prices[$count - 1]  ;
				if( $_ < 2 ){
					$doPrice = $_ + 2 ;
				}else{
					$doPrice = $_ * 1.15 ;
				}
			}else{
				$_ = $prices[3] ;
				if( $_ < 2 ){
					$doPrice = $_ + 2 ;
				}else{
					$doPrice = $_ * 1.15 ;
				}
			}
			
			return $doPrice ;
		}
	}
	
	/**
	 * 非竞价销售
	 */
	public function _processStrateryForFJJXS($product ,$productCategory,$accountName){
		return null ;
	}
	
	public function _processStrateryForVIP($product ,$productCategory ,$accountName){
		$artPrice = $product['ART_PRICE'] ;//手工价格
		$execPrice =  $product['EXEC_PRICE'] ;//最低限价
		if( empty($artPrice) ){
			return null ;
		}else{
			if( $artPrice < $execPrice  ){
				$artPrice = $execPrice ;
			}
		}
		
		return $artPrice ;
	}
	
	/**
	 * 竞价销售
	 */
	public function _processStrateryForJJXS($product ,$productCategory,$accountName){
		$channel = $product["FULFILLMENT_CHANNEL"] ;
		$itemCondition    = $product['ITEM_CONDITION'] ;
		$isFM = $product['IS_FM'] ;

		if( (!empty($channel)) &&  strpos($channel,"AMAZON") === 0 ){//fba产品
			return $this->_processStrateryForJJXS_FBA($product ,$productCategory ,$accountName) ;
		}else if($isFM == "FM"){//FM产品
			return $this->_processStrateryForJJXS_FM($product ,$productCategory ,$accountName) ;
		}else if($isFM == "NEW"){//New产品
			return $this->_processStrateryForJJXS_NEW($product ,$productCategory ,$accountName) ;
		}else if($itemCondition == 1){//Used产品
			return $this->_processStrateryForJJXS_USED($product ,$productCategory ,$accountName) ;
		}
		
		return null ;
	}
	
	public function _processStrateryForJJXS_FBA($product ,$productCategory ,$accountName){
		$asin = $product['ASIN'] ;
		$artPrice = $product['ART_PRICE'] ;//手工价格
		$execPrice =  $product['EXEC_PRICE'] ;//最低限价
		$itemCondition    = $product['ITEM_CONDITION'] ;
		
		//全区域排名1-4竞价排名
		$competitions  = $this->Product->getProductCompetitionDetails($asin) ;
		$prices = array() ;
		foreach($competitions as $com){
			$com = $com['sc_sale_competition_details'] ;
			
			$type = $com['TYPE'] ;
			$_price = $com['SELLER_PRICE'] + $com['SELLER_SHIP_PRICE'] ;
			
			if( $com['SELLER_NAME'] == $accountName ){//owner
				continue ;
			}
			
			if( $itemCondition == 11 ){//new
				$prices[] = $_price ;
			}else  if($itemCondition == 1){//used
				$prices[] = $_price ;
			}
		}
		
		$index = 0 ;
		foreach( $prices as $p ){
			$index++ ;
			if( $p > $execPrice  ){//找到离最低限价最近的那个产品，设置价格
				return $p - 0.01 ;
			}
			if($index>=4) break ;//只处理排名1-4的情况
		}
		
		//执行FBA1-3区域竞价排名
		$fbas  = $this->Product->getProductFbaDetails($asin) ;
		$prices = array() ;
		foreach($competitions as $com){
			$com = $com['sc_sale_fba_details'] ;
			
			$_price = $com['SELLER_PRICE'] + $com['SELLER_SHIP_PRICE'] ;
			
			if( $com['SELLER_NAME'] == $accountName ){//owner
				continue ;
			}
			
			if( $itemCondition == 11 ){//new
				$prices[] = $_price ;
			}else  if($itemCondition == 1){//used
				$prices[] = $_price ;
			}
		}
		
		$index = 0 ;
		foreach( $prices as $p ){
			$index++ ;
			if( $p > $execPrice  ){//找到离最低限价最近的那个产品，设置价格
				return $p - 0.01 ;
			}
			if($index>=3) break ;//只处理排名1-3的情况
		}
		
		//执行最低限价策略
		return $execPrice ;
	}
	
	public function _processStrateryForJJXS_FM($product ,$productCategory ,$accountName){
		$asin = $product['ASIN'] ;
		$artPrice = $product['ART_PRICE'] ;//手工价格
		$execPrice =  $product['EXEC_PRICE'] ;//最低限价
		$itemCondition    = $product['ITEM_CONDITION'] ;
		
		//全区域排名1-4竞价排名
		$competitions  = $this->Product->getProductCompetitionDetails($asin) ;
		$prices = array() ;
		foreach($competitions as $com){
			$com = $com['sc_sale_competition_details'] ;
			
			$type = $com['TYPE'] ;
			$_price = $com['SELLER_PRICE'] + $com['SELLER_SHIP_PRICE'] ;
			$country = $com['COUNTRY'] ;
			
			$auto = $this->Config->getAmazonConfig("EXCLUDE_OUTOF_AMERI") ;//排除中国卖家
			if( $com['SELLER_NAME'] == $accountName || ( !empty($country) && $country=='china'  )){//owner
				continue ;
			}
			
			if( $itemCondition == 11 ){//new
				$prices[] = $_price ;
			}else  if($itemCondition == 1){//used
				$prices[] = $_price ;
			}
		}
		
		$count = count($prices) ;
		if( $count <=1 ){//竞价有效人数小于或等于1个
			return null ;//当前价格，不处理 
		}
		
		$index = 0 ;
		foreach( $prices as $p ){
			$index++ ;
			if( $p > $execPrice  ){//找到离最低限价最近的那个产品，设置价格
				return $p - 0.01 ;
			}
			if($index>=4) break ;//只处理排名1-4的情况
		}
		
		//执行最低限价策略
		return $execPrice ;
	}
	
	public function _processStrateryForJJXS_NEW($product ,$productCategory ,$accountName){
		$asin = $product['ASIN'] ;
		$artPrice = $product['ART_PRICE'] ;//手工价格
		$execPrice =  $product['EXEC_PRICE'] ;//最低限价
		$itemCondition    = $product['ITEM_CONDITION'] ;
		
		//全区域排名1-4竞价排名
		$competitions  = $this->Product->getProductCompetitionDetails($asin) ;
		$prices = array() ;
		foreach($competitions as $com){
			$com = $com['sc_sale_competition_details'] ;
			
			$type = $com['TYPE'] ;
			$_price = $com['SELLER_PRICE'] + $com['SELLER_SHIP_PRICE'] ;
			
			if( $com['SELLER_NAME'] == $accountName ){//owner
				continue ;
			}
			
			if( $itemCondition == 11 ){//new
				$prices[] = $_price ;
			}else  if($itemCondition == 1){//used
				$prices[] = $_price ;
			}
		}
		
		$count = count($prices) ;
		if( $count <=1 ){//竞价有效人数小于或等于1个
			return null ;//当前价格，不处理 
		}
		
		$index = 0 ;
		foreach( $prices as $p ){
			$index++ ;
			if( $p > $execPrice  ){//找到离最低限价最近的那个产品，设置价格
				return $p - 0.01 ;
			}
			if($index>=4) break ;//只处理排名1-4的情况
		}
		
		//执行最低限价策略
		return $execPrice ;
	}
	
	public function _processStrateryForJJXS_USED($product ,$productCategory ,$accountName){
		$artPrice = $product['ART_PRICE'] ;//手工价格
		$execPrice =  $product['EXEC_PRICE'] ;//最低限价
		if( empty($artPrice) ){
			return null ;
		}else{
			if( $artPrice < $execPrice  ){
				$artPrice = $execPrice ;
			}
		}
		
		return $artPrice ;
	}
}