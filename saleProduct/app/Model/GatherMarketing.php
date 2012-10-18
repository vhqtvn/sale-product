<?php

App::import('Model', 'Amazonaccount') ;
App::import('Model', 'Product') ;
App::import('Model', 'Config') ;

/**
 * 采集营销
 */
class GatherMarketing extends AppModel {
	var $useTable = "sc_product_cost" ;
	
		
	public function _processStratery($product ,$productCategory ,$accountName){
		$amazonAccount = new Amazonaccount() ;
		
		//获取产品个性化竞争策略
		$productStratery = $product["STRATEGY"] ;
		
		//获取分类竞价策略
		$categoryStratery = $amazonAccount->getAmazonProductCategoryStratery($productCategory) ;
		
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
		$productModel = new Product() ;
		$config = new Config() ;
		
		$asin = $product['ASIN'] ;
		$shipPrice = $product["SHIPPING_PRICE"] ;
		$channel = $product["FULFILLMENT_CHANNEL"] ;
		$itemCondition    = $product['ITEM_CONDITION'] ;
		
		if( (!empty($channel)) &&  strpos($channel,"AMAZON") === 0 ){//fba产品
			//$fbas  = $this->Product->getProductFbaDetails($asin) ;
			return null ;
		}else{
			$competitions  = $productModel->getProductCompetitionDetails($asin) ;
			
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
				if( $_*0.15 < 2 ){
					$doPrice = $_ + 2 ;
				}else{
					$doPrice = $_ * 1.15 ;
				}
			}else{
				$_ = $prices[3] ;
				if( $_*0.15 < 2 ){
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
		$productModel = new Product() ;
		$config = new Config() ;
		
		$asin = $product['ASIN'] ;
		$artPrice = $product['ART_PRICE'] ;//手工价格
		$execPrice =  $product['EXEC_PRICE'] ;//最低限价
		$itemCondition    = $product['ITEM_CONDITION'] ;
		
		//全区域排名1-4竞价排名
		$competitions  = $productModel->getProductCompetitionDetails($asin) ;
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
		$fbas  = $productModel->getProductFbaDetails($asin) ;
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
		$productModel = new Product() ;
		$config = new Config() ;
		
		$asin = $product['ASIN'] ;
		$artPrice = $product['ART_PRICE'] ;//手工价格
		$execPrice =  $product['EXEC_PRICE'] ;//最低限价
		$itemCondition    = $product['ITEM_CONDITION'] ;
		
		//全区域排名1-4竞价排名
		$competitions  = $productModel->getProductCompetitionDetails($asin) ;
		$prices = array() ;
		foreach($competitions as $com){
			$com = $com['sc_sale_competition_details'] ;
			
			$type = $com['TYPE'] ;
			$_price = $com['SELLER_PRICE'] + $com['SELLER_SHIP_PRICE'] ;
			$country = $com['COUNTRY'] ;
			
			$auto = $config->getAmazonConfig("EXCLUDE_OUTOF_AMERI") ;//排除中国卖家
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
		$productModel = new Product() ;
		$config = new Config() ;
		
		$asin = $product['ASIN'] ;
		$artPrice = $product['ART_PRICE'] ;//手工价格
		$execPrice =  $product['EXEC_PRICE'] ;//最低限价
		$itemCondition    = $product['ITEM_CONDITION'] ;
		
		//全区域排名1-4竞价排名
		$competitions  = $productModel->getProductCompetitionDetails($asin) ;
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