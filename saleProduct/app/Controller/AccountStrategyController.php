<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");
/**
 * 账号策略控制类
 * 
 * @author Administrator
 *
 */
class AccountStrategyController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Utils', 'Config','SqlUtils','Amazonaccount');
	
    public function adjustPrice(){
    	
    	//date_default_timezone_set("Asia/Shanghai") ;
    	
    	echo date('Y-m-d H:i:s',time());
    	$hour = date("H") +0  ;
    	$week = date("w") +0;
    	
    	if( $week == 7 ){
    		$week = 0 ;
    	}
    	
    	//获取系统对应账号
    	$accounts = $this->Amazonaccount->getAllAccounts() ;
    	
    	foreach( $accounts as $Record ){
    		$sfs = $Record['sc_amazon_account']  ;
    		$accountId   = $sfs['ID'] ;
    		$listings = $this->SqlUtils->exeSqlWithFormat("sql_saleStrategy_findExcetableListingConfig" , array( "hour"=>$hour , "week"=>$week,"accountId"=>$accountId )) ;
    		
    		
    		if( !empty( $listings ) ){
    			$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
    			$account = $account[0]['sc_amazon_account'] ;
    			$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
    			
    			$_products = array() ;
    			for( $i = 0 ;$i < count($listings) ;$i++  ){
    				$product = $listings[$i] ;
    			
    				$sku = $product["LISTING_SKU"] ;
    				$price = $product["PRICE"] ;
    			
    				$_products[] = array("SKU"=>$sku,"FEED_PRICE"=>$price) ;
    			}
    			
    			$Feed = $this->Amazonaccount->getPriceFeed($MerchantIdentifier , $_products) ;
    			
    			$url = $this->Utils->buildUrl($account,"taskAsynAmazon/price") ;
    			//echo $url."?feed=".urlencode($Feed) ;
    			//$url = $url."?feed=".urlencode($Feed) ;
    			
    			$this->triggerRequest($url,array("feed"=>urlencode($Feed) )) ;
    			//triggerRequest($url) ;
    			//file_get_contents($url."?feed=".urlencode($Feed));
    		}
    	} ;
    	
    	
    	//查找当前时间段需要调价的Listing
    	echo $hour."----".$week ;
    	
    }
}