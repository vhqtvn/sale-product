<?php
	ignore_user_abort(1);
	set_time_limit(0);

	ini_set("memory_limit", "62M");
	ini_set("post_max_size", "24M");

class CronTaskController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Utils','Amazonaccount','ScRequirement','Cost','NewPurchaseService','GatherData',"System","ScCache");
    
    public function initPurchaseLimitPrice(){
    	$sql = "sql_purchase_new_listForRepaire" ;
    	$products = $this->Utils->exeSqlWithFormat("sql_purchase_new_listForRepaire",array()) ;
    	foreach($products as $product  ){
    		$id = $product['ID'] ;
    		$realId =  $product['REAL_ID'] ;
    		
    		if( empty($product['LIMIT_PRICE']  ) || $product['LIMIT_PRICE'] <=0 ){
    			$limitPrice = $this->NewPurchaseService->getDefaultLimitPrice($realId) ;
    			echo $realId.'       '.$limitPrice.'<br>' ;
    			ob_flush() ;
    			if( empty($limitPrice) )continue ;
    			$sql = "update sc_purchase_product set limit_price = '{@#limitPrice#}' where id='{@#id#}'" ;
    			$this->Utils->exeSql($sql,array("limitPrice"=>$limitPrice,"id"=>$id)) ;
    		}
    	}
    }
    
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
    	$gatherTaskId = $this->Utils->create_guid() ;
    	//判断是否正在采集数据
    	$cache = $this->ScCache->getCache("__gatherFbaLowestPrice__") ;
    	
    	if( empty($cache)  ){
    		//如果不存在，则创建一个采集任务，继续往下面进行采集
    		$this->ScCache->createCache( "__gatherFbaLowestPrice__",$gatherTaskId ) ;
    	}else{
    		//判断是否过期
    		$lastUpdatedTime = $cache['LAST_UPDATED_TIME'] ;
    		ini_set('date.timezone','Asia/Shanghai');
    		$now = date('Y-m-d H:i:s');
    		$diffH = $this->Utils->DateDiff( $now , $lastUpdatedTime , 'h'  ) ;//小时差异

    		if( $diffH >=1  ){
    			//如果超过一个小时没有更新表示过期
    			$this->ScCache->removeCache("__gatherFbaLowestPrice__") ;
    			$this->ScCache->createCache( "__gatherFbaLowestPrice__",$gatherTaskId ) ;
    		}else{
    			//未过期，任务还正在进行中...
    			echo "存在采集任务在进行中......" ;
    			return ;
    		}
    	}
    	
    	$this->___getFbaLowestPrice( $gatherTaskId ) ;
    }
    
    
    /**
     * 获取FBA最低价
     */
    public function ___getFbaLowestPrice( $gatherTaskId ){
    	$cache = $this->ScCache->getCache("__gatherFbaLowestPrice__") ;
    	$cacheValue = $cache['CACHE_VALUE'] ;
    	if( $cacheValue != $gatherTaskId ){
    		echo "task is invalid ,break ;" ;
    		return ;
    	}
    	
    	$isTerminal = false ;
    	
    	/*获取系统所有FBA产品的ASIN  */
    	$accounts = $this->Amazonaccount->getAllAccountsFormat();
    	foreach( $accounts as $account ){
    		if($isTerminal) break ;
    		
    		$accountId = $account['ID'] ;
    		$config = $this->System->getAccountPlatformConfig($accountId) ;
    
    		$start = 0 ;
    		$limit = 200 ;
    
    		while(true){
    			if( $isTerminal ) break ;
    			
    			$sql = "select distinct saap.ASIN from sc_amazon_account_product saap ,sc_amazon_account saa,sc_fba_supply_inventory sfsi
    			where
    			saap.FULFILLMENT_CHANNEL like 'AMAZON%'
    			and saap.status = 'Y'
    			and saap.account_id = saa.id
    			and saap.account_id = sfsi.account_id
    			and saap.sku =sfsi.seller_sku
    			and sfsi.IN_STOCK_SUPPLY_QUANTITY >0
    			and saa.status = 1
    			and saap.account_id = '{@#accountId#}'
    			limit $start,$limit " ;
    			/*$sql = "select distinct saap.ASIN from sc_amazon_account_product saap
    			 where
    			saap.account_id = '{@#accountId#}'
    			limit $start,$limit " ;*/
    			 
    			$items = $this->Utils->exeSqlWithFormat($sql,array("accountId"=>$account['ID'])) ;
    			$start = $start+$limit ;
    			if( empty( $items ) || count($items)<=0 ) break ;
    			echo "----------".$account['NAME']."--------------".count($items)."======================" ;
    					$index = 0 ;
    			foreach( $items as $item ){
    				
    				$cache = $this->ScCache->getCache("__gatherFbaLowestPrice__") ;
    				$cacheValue = $cache['CACHE_VALUE'] ;
    				if( $cacheValue != $gatherTaskId ){
    					$isTerminal = true ;
    					break ;
    				}
    				
    				//刷新缓存最新更新时间
    				$this->ScCache->refreshCache("__gatherFbaLowestPrice__") ;
    				
        			sleep(5) ;//间隔5秒
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
        //递归循环采集
        sleep(180) ;
        $this->___getFbaLowestPrice($gatherTaskId) ;
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
     * /taskAsynAmazon/getMyPriceForSKU
     */
	public function  execMarketing(){
		ini_set('date.timezone','Asia/Shanghai');
		$hour = (int)date('H');
		debug($hour) ;
		$accounts = $this->Amazonaccount->getAllAccountsFormat();
		
		//获取全局配置
		$limitTimePriceStart = $this->Amazonaccount->getAmazonConfig("LIMIT_TIME_PRICE_START") ;//限时营销开始时间
		$limitTimePriceEnd = $this->Amazonaccount->getAmazonConfig("LIMIT_TIME_PRICE_END") ;//限时营销开始时间
		
		$noSaleTimeStart  = $this->Amazonaccount->getAmazonConfig("NO_SALE_TIME_START") ;//非营销时段开始时间
		$noSaleTimeEnd   = $this->Amazonaccount->getAmazonConfig("NO_SALE_TIME_END") ;//非营销时间结束时间
		
		//debug($limitTimePriceStart) ;
		$isNoSaleTime = false ;
		if(  $hour >= $noSaleTimeStart  && $hour < $noSaleTimeEnd  ){
			$isNoSaleTime = true ;
		}
		
		if( $isNoSaleTime  ){ //如果非营销时段，不执行价格调整
			return ;
		}
		
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
			$accountName = $account['NAME'] ;
			debug($account['NAME']) ;
			$sql = "select * from sc_amazon_account_union where status=1 order by piror desc" ;
			$unions = $this->Utils->exeSqlWithFormat( $sql , array() ) ;
			
			$unionAccount = null ;
			foreach($unions as $union){
				$unionAccountNames = $union['UNION_ACCOUNT_NAMES'] ;
				$unionAccountNames = json_decode($unionAccountNames) ;
				foreach ($unionAccountNames as $unionAccountName){
					$unionAccountName = get_object_vars($unionAccountName) ;
					$_aName = $unionAccountName['accountName'] ;
					if( $_aName == $accountName ){
						//当前账号在该联盟
						$unionAccount = $unionAccountNames ;
						break ;
					}
				}
				if( !empty($unionAccount) )break ;
			}
			$uams = array() ;
			if( !empty($unionAccount) ){
				foreach ($unionAccount as $unionAccountName){
					$unionAccountName = get_object_vars($unionAccountName) ;
					$uams[] = $unionAccountName ;
				}
			}
			
			if( count($uams) <=0 ){
				$uams[]  = array("accountName"=>$accountName) ;
			}
			
			$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
			
			/**
			 * 同步自己的实时价格
			 */
			try{
				//同步实时价格
				$url = $this->Utils->buildUrl( $account, "taskAsynAmazon/getMyPriceForSKU" ) ;
				$this->triggerRequest($url,null) ;
			}catch(Exception $e){
				debug($e) ;
			}
			/**
			 * 同步最低FBA价格
			 */
			/*try{
				//同步实时FBA最低价格
				$url = $this->Utils->buildUrl( $account, "taskAsynAmazon/GetLowestOfferListingsForASIN" ) ;
				$this->triggerRequest($url,null) ;
			}catch(Exception $e){
				debug($e);
			}*/
			
			try{
				$start = 0 ;
				$limit = 200 ;
				$items = null ;
				$index=0 ;
				while(true){
					$_products = array() ;
					//库存大于0的才进行自动营销
					$sql = "select saap.* ,
					                   saa.name as ACCOUNT_NAME
								from sc_amazon_account_product  saap,
					          sc_fba_supply_inventory sfsi,
					          sc_amazon_account saa
					where
					saap.account_id = saa.id
					and saap.FULFILLMENT_CHANNEL like 'AMAZON%'
					and saap.account_id = sfsi.account_id
					and saap.sku =sfsi.seller_sku
					and sfsi.IN_STOCK_SUPPLY_QUANTITY >0 
					and saap.account_id = '{@#accountId#}'
					and saap.limit_price > 0
					and saap.status = 'Y'
					order by saap.id
					limit $start,$limit " ;
					$items = $this->Utils->exeSqlWithFormat($sql,array("accountId"=>$account['ID'])) ;
					$start = $start+$limit ;
					if( empty( $items ) || count($items)<=0 ) break ;
					
					foreach( $items as $item ){
						$index++ ;
						echo $index.'---'.$item['SKU'].'<br>' ;
						$mItem = $this->_marketingItemV20($item, $isLimitStrategy, $isLimitStart, $isLimitEnd, $isLimiting,$uams ,$isNoSaleTime ) ;
						if(!empty($mItem)){
							$feedPrice = $mItem['FEED_PRICE'] ;
							$listPrice 			= $item['LIST_PRICE'] ;
							//if( $feedPrice == $listPrice ) continue ;
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
	
	public function _marketingItemV20($item,$isLimitStrategy,$isLimitStart,$isLimitEnd,$isLimiting , $unoinAccountNames , $isNoSaleTime ){
		//if(  $item['SKU'] != '10001826-F' ) return null ;
		//debug($item) ;
		/**
		 * 初始化计算需求所要数据
		 * @$listPrice   Listing价格
		 * @$execPrice  设置最低限价
		 * @$secondPrice  最低价格，除联盟卖家之外的
		 * @$limitPriceLargerPrice  比限价大的价格
		 */
		$listPrice 			= $item['LIST_PRICE'] ;
		$execPrice 		=  $item['LIMIT_PRICE'] ;//限价
		if( empty($listPrice) || $listPrice == 0  ){
			$listPrice = $execPrice ;
		}

		//if( $item['SKU'] != '10000207-1' ) return null ;
	
		$fbaPriceArray   = $item['FBA_PRICE_ARRAY'] ;
		//如果采集到的FBA价格为空，则不进行改listing调价
		if( empty( $fbaPriceArray ) ){
			if( $execPrice > $listPrice ){
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$execPrice) ;
			}else{
				return null ;
			}
		}
	
		//从价格中剔除联盟账号
		$fbaPriceArray = json_decode($fbaPriceArray) ;
		$fixFbaPriceArray = array() ;
		foreach( $fbaPriceArray as $fbaPrice ){
			$fbaPrice = get_object_vars($fbaPrice) ;
			$accoutName = $fbaPrice['seller'] ;
			$isUnionAccount = false ;
			foreach($unoinAccountNames as  $unionAccount){
				$unitAccountName = $unionAccount['accountName'] ;
				if( $accoutName == $unitAccountName  ){
					$isUnionAccount = true ;
					break ;
				}
			}
			if( !$isUnionAccount ){
				$fixFbaPriceArray[] = $fbaPrice ;
			}
		}
		
		//得到剔除联盟账号的价格列表$fixFbaPriceArray
		//获取剔除后的最低价
		$secondPrice = 0 ;
		$limitPriceLargerPrice = 0 ;
		foreach( $fixFbaPriceArray as $fixFbaPrice ){
			$_price = $fixFbaPrice['price'] ;
			if( $secondPrice== 0 ){
				$secondPrice = $_price ;//初始化出自己联盟卖家外价格最低的
			}
			
			if( $_price > $execPrice && $limitPriceLargerPrice == 0  ){
				$limitPriceLargerPrice = $_price ;
			}
		}
		
		//计算最低价卖家数为多少，如果小于2，则设置价格为最低价
		$secondPriceCount = 0 ;
		foreach( $fixFbaPriceArray as $fixFbaPrice ){
			$_price = $fixFbaPrice['price'] ;
			if( $secondPrice == $_price  ){
				$secondPriceCount++ ;
			}
		}
		
		echo '<br/>'.$secondPrice.'           '.$listPrice.'<br/>' ;

		/**
		 * @如果是限时执行，则区分限时开始、结束、限时进行中......
		 */
		if(  $isLimitStrategy  ){
			if( $isLimitStart ){//限时调价开始，记录当前价格到数据库
				$sql= "update sc_amazon_account_product set lt_before_price='{@#ltBeforePrice#}' where id='{@#id#}'" ;
				$this->Utils->exeSql($sql,array("ltBeforePrice"=>$listPrice,"id"=>$item['ID'] )) ;
			}else if($isLimitEnd){//限时调价结束
				//还原价格
				$isLimitStrategy = false ;
				//return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$item['LT_BEFORE_PRICE'] ) ;
			}else if($isLimiting){//限时调价中
				//调价中，下面做处理
			}
		}
	
		/**
         *@如果除了同盟卖家之外，不存在其他卖家，则价格只与限价进行比较，
         *    如果价格大于或等于限价，则不进行调价；如果小于限价，则价格向上调整至限价
		 */
		$fbaPriceCount = count( $fixFbaPriceArray ) ;
		if( $fbaPriceCount <= 0 ){
			//如果除了同盟卖家之外，没有其他卖家，则不进行调价
			$fixPrice = $execPrice * 0.2 ;
			if( $fixPrice <1 ){
				$fixPrice = 1 ;
			}
			
			if( $listPrice >= ($fixPrice+$execPrice)  ){
				return null ;
			}else{
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>($fixPrice+$execPrice) ) ;
			}
			/*
			if( $listPrice >= $execPrice ){
				return null ;
			}else{
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$execPrice ) ;
			}*/
		}

		/**
		 * @处理同盟卖家，还吃了再其他卖家
		 */
		
		//当前价格比其他FBA卖家最低价格高
		/**
		 * 1、如果自己价格大于其他卖家最低价格
		 *   C1：如果其他卖家价格最低价格（$secondPrice）大于限价，则在限时营销时，调整为$secondPrice-0.01，否则调整为其他卖家价格最低价格
		 *   C2：如果其他卖家最低价格等于限价，则自己价格调整为限价
		 *   C3：如果其他卖家价格小于限价，则分几种情况
		 *           如果不存在比限价大的价格，这调整价格为限价（$limitPriceLargerPrice==0）。
		 *           在限时营销时段，取比限价大的价格$limitPriceLargerPrice，然后$limitPriceLargerPrice-0.01
		 *           如果在其他时段，调整价格为比限价大的价格$limitPriceLargerPrice
		 */
		if( $listPrice > $secondPrice ){
			if( $secondPrice >  $execPrice  ){//如果最低价格大于限价
				if( $secondPriceCount <=2 ){
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice  ) ;
				}
				
				if(  $isLimitStrategy &&( $isLimiting || $isLimitStart ) ){//如果限时调价开始或进行中...
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice - 0.01 ) ;
				}else{
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice ) ;
				}
			}else if( $secondPrice ==  $execPrice  ){ //设置价格为限价
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$execPrice ) ;
			}else if( $secondPrice <  $execPrice  ){ //最低价格小于限价
				if( $limitPriceLargerPrice == 0  ){
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$execPrice ) ;
				}
				if(  $isLimitStrategy &&( $isLimiting || $isLimitStart ) ){//如果限时调价开始或进行中...
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$limitPriceLargerPrice - 0.01 ) ;//限价后面价格-0.01
				}else{
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$limitPriceLargerPrice - 0.01 ) ;
				}
			}
			return null ;
		}
			
		//如果价格就是FBA最低价格，判断是否存在多个卖家都是最低价格
		/**
		 * 2、如果自己价格与其他卖家最低价格一致的话
		 * 		C1：如果当前价格低于限价
		 *                如果不存在比限价大的其他卖家价格，则调整为限价
		 *                如果存在，则调整为其他卖家最低价格-0.01
		 *      C2：如果当前价格等于限价，则不进行处理
		 *      C3：如果当前价格大于限价
		 *      		   在限时营销时段设置为当前价格-0.01，保持最低价
		 *                如果其他时段，保持当前价格
		 */
		if($listPrice ==  $secondPrice ){
			if( $listPrice < $execPrice ){ //如果其他卖家最低价格小于限价
				if( $limitPriceLargerPrice == 0  ){
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$execPrice ) ;
				}
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$limitPriceLargerPrice - 0.01 ) ;
			}
			
			if( $listPrice == $execPrice ){
				return null ;//array("SKU"=>$item['SKU'],"FEED_PRICE"=>$execPrice ) ;
			}
			
			if( $listPrice > $execPrice ){
				if( $secondPriceCount <=2 ){
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice  ) ;
				}
				
				if(  $isLimitStrategy &&( $isLimiting || $isLimitStart ) ){//如果限时调价开始或进行中...
					return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$listPrice - 0.01 ) ;//最低价格-0.01
				}else{
					return null ;//array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice ) ;
				}
			}
		}
		
		if( $listPrice < $secondPrice ){
			if( $secondPriceCount <=2 ){
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice  ) ;
			}
			
			if(  $isLimitStrategy &&( $isLimiting || $isLimitStart ) ){//如果限时调价开始或进行中...
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice - 0.01 ) ;//最低价格-0.01
			}else{
				return array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice ) ;
			}
		}
		
		return null;
	}
	
	/**
	 * 
	 * @param unknown_type $item
	 * @param unknown_type $isLimitStrategy
	 * @param unknown_type $isLimitStart
	 * @param unknown_type $isLimitEnd
	 * @param unknown_type $isLimiting
	 * @param unknown_type $uams   联盟账号
	 * @return multitype:unknown |NULL|multitype:number unknown |multitype:unknown Ambigous <number, unknown>
	 * @deprecated
	 */
	public function _marketingItem($item,$isLimitStrategy,$isLimitStart,$isLimitEnd,$isLimiting , $unoinAccountNames ){

		//debug( $item ) ;
		$listPrice = $item['LIST_PRICE'] ;
		$lowestFbaPrice = $item['LOWEST_FBA_PRICE'] ;
		
		$fbaPriceArray   = $item['FBA_PRICE_ARRAY'] ;
		//从价格中剔除联盟账号
		//$fbaPriceArray = json_decode($fbaPriceArray) ;
		
		
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