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
    	$accounts = $this->Amazonaccount->getAllAccountsFormat();
    	
    	//先计算成本
    	$this->asynCost() ;
    	
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
    
    public function getFbaLowestPrice(){
    	/*获取系统所有FBA产品的ASIN  */
    	$accounts = $this->Amazonaccount->getAllAccountsFormat();
    	foreach( $accounts as $account ){
    		$accountId = $account['ID'] ;
    		$config = $this->System->getAccountPlatformConfig($accountId) ;
    		$sql = "select distinct ASIN from sc_amazon_account_product saap ,
    				                    sc_amazon_account saa
    				where
    				 saap.FULFILLMENT_CHANNEL like 'AMAZON%' 
    				and saap.status = 'Y'
    				and saap.account_id = saa.id
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
     * 执行营销
     */
	public function  execMarketing(){
		$accounts = $this->Amazonaccount->getAllAccountsFormat();
		foreach( $accounts as $account ){
			$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
			try{
				$sql = "select * from sc_amazon_account_product 
						where 
						FULFILLMENT_CHANNEL like 'AMAZON%' 
						and account_id = '{@#accountId#}' 
						and limit_price > 0
						and status = 'Y'
						and lowest_fba_price >0 " ;
				$items = $this->Utils->exeSqlWithFormat($sql,array("accountId"=>$account['ID'])) ;
				$_products = array() ;
				
				foreach( $items as $item ){
					
					$listPrice = $item['PRICE'] ;
					$lowestFbaPrice = $item['LOWEST_FBA_PRICE'] ;
					$fbaPriceArray   = $item['FBA_PRICE_ARRAY'] ;
					$execPrice =  $item['LIMIT_PRICE'] ;//限价
					
					if( empty($execPrice) || $execPrice==0 ){
						$execPrice = $listPrice ;
					}
					//debug($item) ;
					
					$fbaPriceArray = explode(",", $fbaPriceArray) ;
					$fbaPriceCount = count( $fbaPriceArray ) ;
					
					if( $fbaPriceCount <= 1 ){
						//如果只有一个FBA卖家，就不处理价格
						continue ;
					}
					
					//当前价格比FBA最低价格高
					if( $listPrice > $lowestFbaPrice ){
						if( $lowestFbaPrice >  $execPrice  ){//如果最低价格大于限价
							$_products[] = array("SKU"=>$item['SKU'],"FEED_PRICE"=>$lowestFbaPrice ) ;
						}else{ //设置价格为限价
							$_products[] = array("SKU"=>$item['SKU'],"FEED_PRICE"=>$execPrice ) ;
						}
						continue ;
					}
					
					//如果价格就是FBA最低价格，判断是否存在多个卖家都是最低价格
					if($listPrice ==  $lowestFbaPrice ){
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
						if( $lowestCount == 1 ){//如果最低价格只有一个，则调整价格为第二
							if( $secondPrice >0 ){
								$_products[] = array("SKU"=>$item['SKU'],"FEED_PRICE"=>$secondPrice ) ;
							}
						}
					}
				} 
				
				$Feed = $this->Amazonaccount->getPriceFeed( $MerchantIdentifier , $_products ) ;
				debug($Feed) ;
				$url = $this->Utils->buildUrl( $account, "taskAsynAmazon/price" ) ;
				$this->triggerRequest($url,array("feed"=>$Feed )) ;
			}catch(Exception $e){ }
		}
	}

}