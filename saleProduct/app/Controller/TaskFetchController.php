<?php
ignore_user_abort(1);
set_time_limit(0);

#清除并关闭缓冲，输出到浏览器之前使用这个函数。
ob_end_clean();

#控制隐式缓冲泻出，默认off，打开时，对每个 print/echo 或者输出命令的结果都发送到浏览器。
ob_implicit_flush(1);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");


class TaskFetchController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Sale', 'Product','Cost',"ProductDev","Amazonaccount");
	
	 public function  formatAllListingFee(){
	 	$listings = $this->Amazonaccount->getAmazonAllAvalidProduct() ;
	 	//ob_start() ;
	 	foreach( $listings as $listing ){
	 		//debug($listing);
	 		//ob_flush() ;
	 		$this->_processListingFee($listing) ;
	 	}
	 	//ob_clean();
	 }
	 
	 public function formatRealFee($realId){
	 	//通过realId获取货品
	 	$listings = $this->Amazonaccount->getAccountProductByRealId($realId) ;
	 	//ob_start() ;
	 	foreach( $listings as $listing ){
	 		//debug($listing) ;
	 		$listing_ = $this->Amazonaccount->getAmazonProductCostBySku($listing['ACCOUNT_ID'],$listing['SKU']) ;
	 		if( !empty($listing_) ){
	 			$this->_processListingFee($listing_) ;
	 		}
	 	}
	 }
	 
	 public function formatListingFee($account,$listingSku){
	 	$listing_ = $this->Amazonaccount->getAmazonProductCostBySku($account,$listingSku) ;
	 	if( !empty($listing_) ){
	 		$this->_processListingFee($listing_) ;
	 	}
	 	
	 }
	 
	 private function _processListingFee($listing){
	 	$user =  $this->getCookUser() ;
	 	$loginId = $user["LOGIN_ID"] ;
	 	
	 	$url = "https://sellercentral.amazon.com/gp/fba/revenue-calculator/data/product-matches.html" ;
	 	$afnUrl		= "https://sellercentral.amazon.com/gp/fba/revenue-calculator/data/afn-fees.html" ;
	 	$mfnUrl   ="https://sellercentral.amazon.com/gp/fba/revenue-calculator/data/mfn-fees.html" ;
	 	 
	 	$asin = $listing['ASIN'] ;
	 	$channel = $listing['FULFILLMENT_CHANNEL'] ;
	 	//echo '<br>111<br>';
	 	$result = $this->send_post($url,array('method'=>'GET',
	 			'model'=>'{"searchString":"'.$asin.'","lang":"en_US","marketPlace":"ATVPDKIKX0DER"}'
	 	)) ;
	 	//debug(trim($result)) ;
	 	$totalPrice = $listing['TOTAL_PRICE']  ;
	 	
	 	$result = json_decode(trim($result)) ;
	 	$result = get_object_vars($result) ;
	 	
	 	$isFba = true ;
	 	if($channel == 'Merchant' ){
	 		$isFba = false ;
	 	}
	 	
	 	if( $isFba  ){
	 		foreach($result['data'] as  $item){
	 			$item = get_object_vars($item) ;
	 			//debug( $item ) ;
	 			$item["selected"] = true ;
	 			$item["language"] = "en_US" ;
	 			$item["price"] = $listing['TOTAL_PRICE'] ;
	 			$item["revenueTotal"] = $listing['TOTAL_PRICE'] ;
	 			$item["undefined"] = 0 ;
	 			$item["inbound-delivery"] = 0 ;
	 			$item["prep-service"] = 0 ;
	 			$item["fulfillmentTotal"] = 0 ;
	 			try{
	 				echo '<br>111<br>';
	 				$feeResult = $this->send_post($afnUrl,array('method'=>'GET',
	 						'model'=> json_encode($item)
	 				)) ;
	 				// debug($feeResult) ;
	 				// echo '<br>111<br>';
	 				// {"weightHandlingFee":"0.46","orderHandlingFee":0,"fbaDeliveryServicesFee":0,"commissionFee":"2.25","pickAndPackFee":"1","storageFee":"0.02","variableClosingFee":"1.35"}
	 	
	 				$feeResult = json_decode(trim($feeResult)) ;
	 				$feeResult = get_object_vars($feeResult) ;
	 				$feeResult = $feeResult['data'] ;
	 				$feeResult = get_object_vars($feeResult) ;
	 				$feeResult['accountId'] = $listing['ACCOUNT_ID'] ;
	 				$feeResult['listingSku'] = $listing['LISTING_SKU'] ;
	 				$feeResult['realId'] = $listing['ID'] ;
	 				$feeResult['loginId'] = $loginId ;
	 				$feeResult['commissionRatio'] = round($feeResult['commissionFee']/$totalPrice,4) ;
	 				$feeResult['fbaCost'] = $feeResult['weightHandlingFee']+$feeResult['orderHandlingFee']
	 				+$feeResult['fbaDeliveryServicesFee']+$feeResult['pickAndPackFee']+$feeResult['storageFee'] ;
	 				$this->Cost->saveCostByFee($feeResult) ;
	 			}catch(Exception $e){
	 			}
	 		}
	 	}else{
	 		foreach($result['data'] as  $item){
	 			$item = get_object_vars($item) ;
	 			//debug( $item ) ;
	 			$item["selected"] = true ;
	 			$item["language"] = "en_US" ;
	 			$item["price"] = $listing['PRICE'] ;
	 			$item["shipping"] = $listing['SHIPING_PRICE']  ;
	 			$item["revenueTotal"] = $listing['TOTAL_PRICE'] ;
	 			$item["order-handling"] = 0 ;
	 			$item["pick-pack"] = 0 ;
	 			$item["outbound-delivery"] = 0 ;
	 			$item["storage"] = 0 ;
	 			$item["inbound-delivery"] = 0 ;
	 			$item["customer-service"] = 0 ;
	 			$item["prep-service"] = 0 ;
	 			$item["fulfillmentTotal"] = 0 ;
	 	
	 			try{
	 				$feeResult = $this->send_post($afnUrl,array('method'=>'GET',
	 						'model'=> json_encode($item)
	 				)) ;
	 	
	 				$feeResult = json_decode(trim($feeResult)) ;
	 				$feeResult = get_object_vars($feeResult) ;
	 				$feeResult = $feeResult['data'] ;
	 				$feeResult = get_object_vars($feeResult) ;
	 				$feeResult['accountId'] = $listing['ACCOUNT_ID'] ;
	 				$feeResult['listingSku'] = $listing['LISTING_SKU'] ;
	 				$feeResult['realId'] = $listing['ID'] ;
	 				$feeResult['loginId'] = $loginId ;
	 				$feeResult['commissionRatio'] = round($feeResult['commissionFee']/$totalPrice,4) ;
	 				$feeResult['fbaCost'] = 0 ;
	 				$this->Cost->saveCostByFee($feeResult) ;
	 			}catch(Exception $e){
	 			}
	 		}
	 	}
	 }
	 
	 /**
	  * 发送post请求
	  * @param string $url 请求地址
	  * @param array $post_data post键值对数据
	  * @return string
	  */
	 function send_post($url, $post_data) {
	 
	 	$postdata = http_build_query($post_data);
	 	$options = array(
	 			'http' => array(
	 					'method' => 'POST',
	 					'header' => 'Content-type:application/x-www-form-urlencoded',
	 					'content' => $postdata,
	 					'timeout' => 10 // 超时时间（单位:s）
	 			)
	 	);
	 	$context = stream_context_create($options);
	 	$result = file_get_contents($url, false, $context);
	 
	 	return $result;
	 }
}