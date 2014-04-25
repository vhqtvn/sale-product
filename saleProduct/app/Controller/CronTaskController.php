<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

class CronTaskController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Utils','Amazonaccount','ScRequirement','Cost','NewPurchaseService','GatherData',"System");
    
    public function clearLimitPrice(){
    	$this->Utils->exeSql("delete from sc_sale_schedule",array()) ;
    }
    
    //LOWEST_PROFIT
    /**
     * 定时根据成本计算限价
     * @TODO
     */
    public function calcLimitPrice( ) {
    	$sql = "select * from sc_listing_cost where fulfillment_channel='AMAZON_NA' " ;
    }
    
    /**
     * 格式化计算货品重量
     */
    public  function calcRealProductWeight(){
    	$sql = "select ID,WEIGHT from sc_real_product where is_onsale=1 and ( weight is null or weight='' )" ;
    	$products= $this->Utils->exeSqlWithFormat($sql,array()) ;
    	foreach( $products as $product ){
    		$realId = $product['ID'] ;
    		$weight = $product['WEIGHT'] ;
    		if( empty($weight) || $weight == 0 ){
    			$sql = "SELECT MAX(spsi.WEIGHT) AS WEIGHT FROM sc_purchase_supplier_inquiry spsi
								WHERE ( (
								spsi.SKU IN (
								  SELECT srp.REAL_SKU FROM sc_real_product srp WHERE srp.ID = '{@#realId#}'
								)
								AND spsi.SKU IS NOT NULL 
								AND spsi.SKU !=''
								)
								OR (
								  spsi.ASIN IS NOT NULL
								  AND spsi.ASIN !=''
								  AND spsi.ASIN IN (
								    SELECT saap.ASIN FROM sc_amazon_account_product saap,
								    sc_real_product_rel srpr
								    WHERE saap.ACCOUNT_ID = srpr.ACCOUNT_ID
								    AND saap.SKU = srpr.SKU
								    AND srpr.REAL_ID = '{@#realId#}'  
								  )
								)
								)
								AND spsi.WEIGHT IS NOT NULL
								AND spsi.WEIGHT !=''" ;//获取询价重量
    			
    			$weight= $this->Utils->getObject($sql,array("realId"=>$realId)) ;
    			if(!empty($weight) && !empty($weight['WEIGHT']) ){
    				$sql = "update sc_real_product set weight='{@#weight#}' where id='{@#realId#}'" ;
    				$this->Utils->exeSql( $sql , array("realId"=>$realId,"weight"=>$weight['WEIGHT']) ) ;
    			}
    		}
    	}
    }
    
    /**
     * 同步成本
     */
    public function asynCost(){
    	$accounts = $this->Amazonaccount->getAllAccountsFormat();
    	foreach( $accounts as $account ){
    		try{
    			$this->Cost->formatAllListingCost( $account['ID'] ) ;
    		}catch(Exception $e){ 
    			debug($e);
    		}
    	}
    }
    
    /**
     * 创建需求
     * 1、同步amazon推荐数据
     */
    public function createAmazonRequirement(){
    	//$accounts = $this->Amazonaccount->getAllAccountsFormat();
    	
    	//先计算成本,需求计算与成本无关
    	//$this->asynCost() ;
    	
    	//1、同步需求数据
    	/*foreach( $accounts as $account ){
    		try{
	    		$url = $this->Utils->buildUrl($account,"taskAsynAmazon/listRecommendations") ;
	    		$result = file_get_contents($url  );
    		}catch(Exception $e){ }
    	}*/
    	//2、检测是否需要创建需求；新增加的需求产品是否都包括在未完成的需求产品里面
    	//3、创建需求
    	$this->ScRequirement->createRequirement() ;
    }
    
    /**
     * 获取FBA最低价
     */
    public function getFbaLowestPrice(){
    	/*获取系统所有FBA产品的ASIN  */
    	$accounts = $this->Amazonaccount->getAllAccountsFormat();
    	foreach( $accounts as $account ){
    		$accountId = $account['ID'] ;
    		$config = $this->System->getAccountPlatformConfig($accountId) ;
    		$sql = "select distinct saap.ASIN from sc_amazon_account_product saap ,
    				                    sc_amazon_account saa,
    									sc_fba_supply_inventory sfsi
    				where
    				 saap.FULFILLMENT_CHANNEL like 'AMAZON%' 
    				and saap.status = 'Y'
    				and saap.account_id = saa.id
    				and saap.account_id = sfsi.account_id
					and saap.sku =sfsi.seller_sku
					and sfsi.IN_STOCK_SUPPLY_QUANTITY >0 
    				and saa.status = 1
    				and saap.account_id = '{@#accountId#}'" ;
    		$items = $this->Utils->exeSqlWithFormat($sql,array("accountId"=>$account['ID'])) ;
    		echo "----------".$account['NAME']."--------------".count($items)."======================" ;
    		$index = 0 ;
    		foreach( $items as $item ){
    			sleep(1) ;
    			$index++ ;
    			echo "<br>index:$index<br>";
    			$asin = $item['ASIN'] ;
    			$gatherParams = array(
    					"asin"=>$asin ,
    					"platformId"=>$config['PLATFORM_ID'] ,
    					"id"=>$accountId,
    					"index"=>0,
    					"taskId"=>"getFbaLowestPrice"
    			) ;
    			$this->GatherData->fbaPricePlatform($gatherParams) ;
    		}
    	}
    }
    
    /**
     * 清除亚马逊需求
     */
    public function  clearAmazonInventoryReq(){
    	/*
    	1、列出当前已经到达Amazon入库的流程对应的采购单
    	2、判断采购单是否对应存在的需求，如果存在对应的需求，判断对应需求listing的库存是否已经到达Amazon，
    	      如果已经达到，结束该需求；未到达，不处理
    	3、采购单不存在对应的需求，检查对应该货品的未完成需求，是否满足，如果满足，结束改需求
    	*/
    	$this->ScRequirement->clearAmazonInventoryReq() ;
    	
    }
   
    /**
     * 
     * 执行营销,前提价格数据准确
     * 1、采集价格数据
     */
	public function  execMarketing(){
		ini_set('date.timezone','Asia/Shanghai');
		$hour = (int)date('H');
		debug($hour) ;
		$accounts = $this->Amazonaccount->getAllAccountsFormat();
		
		//获取全局配置
		$limitTimePriceStart = $this->Amazonaccount->getAmazonConfig("LIMIT_TIME_PRICE_START") ;//限时营销开始时间
		$limitTimePriceEnd = $this->Amazonaccount->getAmazonConfig("LIMIT_TIME_PRICE_END") ;//限时营销开始时间
		debug($limitTimePriceStart) ;
		
		//判断是不是限时调价时间
		$isLimitStrategy =  false ;
		if( (!empty($limitTimePriceStart)) && $limitTimePriceStart!='-'  ){ 
			$isLimitStrategy= true ;
		}
		$isLimitStart = false ;
		$isLimitEnd   = false ;
		$isLimiting    = false ;
		if( $isLimitStrategy ){
			if( $limitTimePriceStart == $hour ){
				$isLimitStart = true ;
			}else  if( $limitTimePriceEnd == $hour ){
				$isLimitEnd = true ;
			}else if( $hour >$limitTimePriceStart && $hour < $limitTimePriceEnd  ){
				$isLimiting = true ;
			}else{
				$isLimitStrategy = false ;
			}
		}
		echo $isLimitStrategy;
		foreach( $accounts as $account ){
			debug($account['NAME']) ;
			$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
			//		$url = "http://".$domain."/".$context."/index.php/taskAsynAmazon/getMyPriceForSKU/".$accountId."?".$random ;
			
			try{
				//同步实时价格
				$url = $this->Utils->buildUrl( $account, "taskAsynAmazon/getMyPriceForSKU" ) ;
				$this->triggerRequest($url,null) ;
			}catch(Exception $e){
				debug($e) ;
			}
			
			//开始执行营销，设置标志位为1
			//$sql = "update  sc_amazon_account_product set marketing_flag = 1 where account_id = '{@#accountId#}'" ;
			//$this->Utils->exeSql($sql,array("accountId"=>$account['ID'])) ;
			
			try{
				
				$start = 0 ;
				$limit = 200 ;
				$items = null ;
				$index=0 ;
				while(true){
					$_products = array() ;
					//库存大于0的才进行自动营销
					$sql = "select saap.* from sc_amazon_account_product  saap,
					          sc_fba_supply_inventory sfsi
					where
					saap.FULFILLMENT_CHANNEL like 'AMAZON%'
					and saap.account_id = sfsi.account_id
					and saap.sku =sfsi.seller_sku
					and sfsi.IN_STOCK_SUPPLY_QUANTITY >0 
					and saap.account_id = '{@#accountId#}'
					and limit_price > 0
					and status = 'Y'
					and lowest_fba_price >0
					order by id
					limit $start,$limit " ;
					$items = $this->Utils->exeSqlWithFormat($sql,array("accountId"=>$account['ID'])) ;
					$start = $start+$limit ;
					if( empty( $items ) || count($items)<=0 ) break ;
					
					foreach( $items as $item ){
						$index++ ;
						echo $index.'<br>' ;
						$mItem = $this->_marketingItem($item, $isLimitStrategy, $isLimitStart, $isLimitEnd, $isLimiting) ;
						if(!empty($mItem)){
							$_products[] = $mItem;
						}
					}
					try{
						$Feed = $this->Amazonaccount->getPriceFeed( $MerchantIdentifier , $_products ) ;
						debug($Feed) ;
						$url = $this->Utils->buildUrl( $account, "taskAsynAmazon/price" ) ;
						$this->triggerRequest($url,array("feed"=>$Feed )) ;
					}catch(Exception $e){
						debug( $e ) ;
					}
				}
				
			}catch(Exception $e){
				debug( $e ) ;
			}
		}
	}
	
	public function _marketingItem($item,$isLimitStrategy,$isLimitStart,$isLimitEnd,$isLimiting){

		//debug( $item ) ;
		$listPrice = $item['LIST_PRICE'] ;
		$lowestFbaPrice = $item['LOWEST_FBA_PRICE'] ;
		$fbaPriceArray   = $item['FBA_PRICE_ARRAY'] ;
		$execPrice =  $item['LIMIT_PRICE'] ;//限价
		//debug($item) ;
		//if($isLimitStrategy)echo 'true&&' ;else echo 'false&&' ;
		if( empty($execPrice) || $execPrice==0 ){
			$execPrice = empty($listPrice)?$item['PRICE']:$listPrice ;
		}
			
		if( empty($listPrice) ){
			$listPrice = $lowestFbaPrice*1.05 ;
		}
		//debug($item) ;
			
		$fbaPriceArray = explode(",", $fbaPriceArray) ;
		$fbaPriceCount = count( $fbaPriceArray ) ;
			
		if(  $isLimitStrategy  ){
			if( $isLimitStart ){//限时调价开始，记录当前价格到数据库
				$sql= "update sc_amazon_account_product set lt_before_price='{@#ltBeforePrice#}' where id='{@#id#}'" ;
				$this->Utils->exeSql($sql,array("ltBeforePrice"=>$listPrice,"id"=>$item['ID'] )) ;
			}else if($isLimitEnd){//限时调价结束
				//还原价格
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$item['LT_BEFORE_PRICE'] ) ;
			}else if($isLimiting){//限时调价中
				//调价中，下面做处理
			}
		}
			
			
		if( $fbaPriceCount <= 1 ){
			//如果只有一个FBA卖家，就不处理价格
			return null ;
		}
		
		$lowestCount = 0 ;
		$secondPrice = 0 ;
		foreach( $fbaPriceArray as $fpa ){
			if( $fpa ==$lowestFbaPrice  ){
				$lowestCount++ ;
			}else{
				$secondPrice = $fpa ;
				break ;
			}
		}
		
		$myPriceCount= 0 ;
		$limitPriceLargerPrice= 0 ;
		foreach( $fbaPriceArray as $fpa ){
			if( $fpa == $execPrice ){
				$myPriceCount++ ;
			}
			if( $fpa > $execPrice  ){
				$limitPriceLargerPrice = $fpa ;
				break ;
			}
		}
			
		//当前价格比FBA最低价格高
		if( $listPrice > $lowestFbaPrice ){
			if( $lowestFbaPrice >  $execPrice  ){//如果最低价格大于限价
				if(  $isLimitStrategy &&( $isLimiting || $isLimitStart ) ){//如果限时调价开始或进行中...
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$lowestFbaPrice-0.01 ) ;
				}else{
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$lowestFbaPrice ) ;
				}
			}else if( $lowestFbaPrice ==  $execPrice  ){ //设置价格为限价
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$execPrice ) ;
			}else if( $lowestFbaPrice <  $execPrice  ){ //最低价格小于限价
				if( $myPriceCount > 1 || ( $myPriceCount==1 && $execPrice != $listPrice ) ){
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$execPrice ) ;
				}
				
				if(  $isLimitStrategy &&( $isLimiting || $isLimitStart ) ){//如果限时调价开始或进行中...
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$limitPriceLargerPrice - 0.01 ) ;
				}else{
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$limitPriceLargerPrice ) ;
				}
			}
			return null ;
		}
			
		//如果价格就是FBA最低价格，判断是否存在多个卖家都是最低价格
		if($listPrice ==  $lowestFbaPrice ){
			
			if( $lowestFbaPrice < $execPrice ){//如果最低价格小于限价，则向上调整价格
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$limitPriceLargerPrice - 0.01 ) ;
			}

			if( $lowestCount == 1 ){//如果最低价格只有一个，则调整价格为第二
				if(  $isLimitStrategy &&( $isLimiting || $isLimitStart ) ){//如果限时调价开始或进行中...
					//在限时调价进行中，最低价不做处理......
				}else{
					if( $secondPrice >0 ){
						return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice ) ;
					}
				}
			}else{
				//如果最低价不止一个
				if(  $isLimitStrategy &&( $isLimiting || $isLimitStart ) ){//如果限时调价开始或进行中...
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$listPrice-0.01 ) ;
				}
			}
		}
		return null;
	}
}