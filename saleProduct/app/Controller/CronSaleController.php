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
		$products = $this->Amazonaccount->getAccountProductsForLevelSale( $accountId , $level ) ;
		
		$account = $this->Amazonaccount->getAccount($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		$accountName = $account['NAME'] ;
		
		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
		
		$_products = array() ;
		for( $i = 0 ;$i < count($products) ;$i++  ){
			$product = $products[$i]['sc_amazon_account_product'] ;

			$sku = $product["SKU"] ;
			$oriPrice = $product["PRICE"] ;
			
			$shipPrice = $product["SHIPPING_PRICE"] ;
			$channel = $product["FULFILLMENT_CHANNEL"] ;
			if( (!empty($channel)) &&  strpos($channel,"AMAZON") === 0 ){
				$shipPrice = 0.00 ;
			}
			
			//////////////////根据策略生成价格///////////////////
			$price = $this->getPriceForStatery($product,$accountName) ;
			///////////////////////////////////////////////////
			if( empty($price) || $price == "" ) continue ;
			
			$price = $price - $shipPrice ;
			
			$_products[] = array("SKU"=>$sku,"FEED_PRICE"=>$price,'ORI_PRICE'=>$oriPrice) ;
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
	
	public function getPriceForStatery($product,$accountName){
		$asin = $product["ASIN"] ;
		
		//当前价格
		$price = $product['PRICE'] ;
		
		//最低限价
		$lowerPrice = $product['EXEC_PRICE'] ;
		
		//如果没有设置最低限价，则不更新
		$auto = $this->Config->getAmazonConfig("AUTO_LOWEST_PRICE_STRATERY") ;
		
		$shipPrice = $product["SHIPPING_PRICE"] ;
		$channel = $product["FULFILLMENT_CHANNEL"] ;
		if( (!empty($channel)) &&  strpos($channel,"AMAZON") === 0 ){
			$shipPrice = 0.00 ;
		}
		
		$price = $price+$shipPrice ;
		$oriPrice = $price ;
		
		//如果当前价格小于最低限价，则调整当前价格为最低限价
		if( $price < $lowerPrice ){
			$price = $lowerPrice ;
		}
		
		//产品是否为FBA产品
		
		if( (!empty($channel)) &&  strpos($channel,"AMAZON") === 0 ){
			//是fba产品，只与FBA产品比较价格
			$fbas  = $this->Product->getProductFbaDetails($asin) ;
			$allPrices = array() ;
			$lowestPrice = 0 ;
			foreach($fbas as $com){
				$com = $com['sc_sale_fba_details'] ;
				
				$_price = $com['SELLER_PRICE'] + $com['SELLER_SHIP_PRICE'] ;
				
				if( $com['SELLER_NAME'] == $accountName ){
					continue ;
				}
				
				if($lowestPrice === 0)
					$lowestPrice = $_price ;
				else
					$lowestPrice = min($lowestPrice,$_price) ;
				
				$allPrices[] = $_price ;
			}
			$price = $this->comparePrice($allPrices,$lowestPrice , $price ,$lowerPrice ) ;
			if($price > $oriPrice){
				return $price ;
			}else{
				if( !empty($auto) && $auto == "true" ){
					//
				}else{
					if(empty($lowerPrice) || $lowerPrice == "") return "" ;
				}
			}
			return $price ;
		}
		
		///////////////////////////////////////////
		$competitions  = $this->Product->getProductCompetitionDetails($asin) ;
		
		$allPrices = array() ;
		$fmPrices  = array() ;
		$lowestPrice = 0 ;
		foreach($competitions as $com){
			$com = $com['sc_sale_competition_details'] ;
			
			$type = $com['TYPE'] ;
			$_price = $com['SELLER_PRICE'] + $com['SELLER_SHIP_PRICE'] ;
			
			if(strpos($type,"U")===0 || $com['SELLER_NAME'] == $accountName ){
				continue ;
			}
			
			if($lowestPrice === 0)
				$lowestPrice = $_price ;
			else
				$lowestPrice = min($lowestPrice,$_price) ;
			
			$allPrices[] = $_price ;
			
			if(strpos($type,"F")===0){
				$fmPrices[] = $_price ;
			}
		}
		
		//获取当前产品是否为FM产品
		$isFM = $product['IS_FM'] ;
		if( $isFM == "FM" ){
			//是否FM产品，则只需要跟FM里面的产品进行价格比较
			$price = $this->comparePrice($fmPrices,$lowestPrice , $price ,$lowerPrice ) ;
			if($price > $oriPrice){
				return $price ;
			}else{
				if( !empty($auto) && $auto == "true" ){
					//
				}else{
					if(empty($lowerPrice) || $lowerPrice == "") return "" ;
				}
			}
			return $price ;
		}else if($product['ITEM_CONDITION'] == 1){
			//不执行价格更新
			return "" ;
		}else{
			//是NEW产品，需要跟所有产品进行价格比较
			$price = $this->comparePrice($allPrices,$lowestPrice , $price ,$lowerPrice ) ;
			if($price > $oriPrice){
				return $price ;
			}else{
				if( !empty($auto) && $auto == "true" ){
					//
				}else{
					if(empty($lowerPrice) || $lowerPrice == "") return "" ;
				}
			}
			return $price ;
		}
		
	}
	
	public function comparePrice($array,$lowestPrice,$price,$limitPrice){
		
		if( count($array) >= 1 ){
			//$lowestPrice = 	$array[0] ;
			if( $price < $lowestPrice ){
				$price = max($lowestPrice - 0.01,$limitPrice) ;
			}else if($limitPrice < $lowestPrice ){
				$price = max($lowestPrice - 0.01,$limitPrice) ;
			}else{
				$price = $limitPrice ;
			}
		}
		return $price ;
	}
}