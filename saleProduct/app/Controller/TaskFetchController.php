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
	 	$index=0;
	 	foreach( $listings as $listing ){
	 		$index=$index+1;
	 		echo $index.'<br>' ;
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
	 /**
	  * 获取开发产品费用信息
	  * 
	  * @param unknown_type $realId
	  */
	 public function formatDevProductFee($asin){
	 	//通过asin获取成本信息
	 	$listings = $this->Amazonaccount->exeSqlWithFormat("select * from sc_product_cost_details where asin = '{@#asin#}'",array('asin'=>$asin)) ;
	 	//ob_start() ;
	 	//debug($listings) ;
	 	foreach( $listings as $listing ){
	 			$this->_processDevProductFee($listing) ;
	 	}
	 }
	 
	 public function formatListingFee($account,$listingSku){
	 	$listing_ = $this->Amazonaccount->getAmazonProductCostBySku($account,$listingSku) ;
	 	if( !empty($listing_) ){
	 		$this->_processListingFee($listing_) ;
	 	}
	 	
	 }
	 
	 private function _processDevProductFee($listing){
	 	$user =  $this->getCookUser() ;
	 	$loginId = $user["LOGIN_ID"] ;
	 	 
	 	$url = "https://sellercentral.amazon.com/gp/fba/revenue-calculator/data/product-matches.html" ;
	 	$afnUrl		= "https://sellercentral.amazon.com/gp/fba/revenue-calculator/data/afn-fees.html" ;
	 	$mfnUrl   ="https://sellercentral.amazon.com/gp/fba/revenue-calculator/data/mfn-fees.html" ;
	 
	 	$asin = $listing['ASIN'] ;
	 	$channel = $listing['TYPE'] ;
	 	//echo '<br>111<br>';
	 	$result = $this->send_post($url,array('method'=>'GET',
	 			'model'=>'{"searchString":"'.$asin.'","lang":"en_US","marketPlace":"ATVPDKIKX0DER"}'
	 	)) ;
	 	//debug(trim($result)) ;
	 	//$totalPrice = $listing['SELLER_COST']  ;
	 	 
	 	$result = json_decode(trim($result)) ;
	 	$result = get_object_vars($result) ;
	 	 
	 	$isFba = true ;
	 	if($channel == 'FBM' || $channel =='FBC' ){
	 		$isFba = false ;
	 	}
	 	 
	 	if( $isFba  ){
	 		foreach($result['data'] as  $item){
	 			$item = get_object_vars($item) ;
	 			//debug( $item ) ;
	 			$item["selected"] = true ;
	 			$item["language"] = "en_US" ;
	 			$item["price"] = 2 ;
	 			$item["revenueTotal"] = 2 ;
	 			$item["undefined"] = 0 ;
	 			$item["inbound-delivery"] = 0 ;
	 			$item["prep-service"] = 0 ;
	 			$item["fulfillmentTotal"] = 0 ;
	 			try{
	 				echo '<br>FBA<br>';
	 				$feeResult = $this->send_post($afnUrl,array('method'=>'GET',
	 						'model'=> json_encode($item)
	 				)) ;
	 
	 				$feeResult = json_decode(trim($feeResult)) ;
	 				$feeResult = get_object_vars($feeResult) ;
	 				$feeResult = $feeResult['data'] ;
	 
	 				if( empty($feeResult) ){
	 					echo 'FBA Error, Try It<br>';
	 					$feeResult = $this->send_post($afnUrl,array('method'=>'GET',
	 							'model'=> json_encode($item)
	 					)) ;
	 						
	 					$feeResult = json_decode(trim($feeResult)) ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					$feeResult = $feeResult['data'] ;
	 					if( empty($feeResult) ){
	 						echo 'FBA Error, End<br>';
	 						continue ;
	 					}
	 				}
	 
	 				$feeResult = get_object_vars($feeResult) ;
	 	
	 				
	 				if( isset($feeResult['commissionFee']) ){//success
	 					$feeResult['asin'] = $listing['ASIN'] ;
	 					$feeResult['type'] = $listing['TYPE'] ;
	 					$feeResult['loginId'] = $loginId ;
	 						
	 					$feeResult['fbaCost'] = 
	 								(empty($feeResult['weightHandlingFee'])?0:$feeResult['weightHandlingFee'])
	 							+ (empty($feeResult['orderHandlingFee'])?0:$feeResult['orderHandlingFee'])
	 							+ (empty($feeResult['fbaDeliveryServicesFee'])?0:$feeResult['fbaDeliveryServicesFee'])
	 					        + (empty($feeResult['pickAndPackFee'])?0:$feeResult['pickAndPackFee'])
	 					        + (empty($feeResult['storageFee'])?0:$feeResult['storageFee']) ;
	 						
	 					if( $feeResult['commissionFee'] == 1 ){ //存在最低价限制
	 						$feeResult['commissionLowlimit'] = 1 ;
	 						//重新计算一次
	 						$item["price"] = 100 ;
	 						$item["revenueTotal"] = 100 ;
	 						$feeResult_ = $this->send_post($afnUrl,array('method'=>'GET',
	 								'model'=> json_encode($item)
	 						)) ;
	 						$feeResult_ = json_decode(trim($feeResult_)) ;
	 						$feeResult_ = get_object_vars($feeResult_) ;
	 						$feeResult_ = $feeResult_['data'] ;
	 						$feeResult_ = get_object_vars($feeResult_) ;
	 
	 						if( isset($feeResult_['commissionFee']) ){//success
	 							$feeResult['commissionRatio'] = round($feeResult_['commissionFee']/100,4) ;
	 						}
	 					}else{
	 						$feeResult['commissionRatio'] = round($feeResult['commissionFee']/2,4) ;
	 					}
	 					$this->Cost->saveDevCostByFee($feeResult) ;
	 				}else{//执行失败
	 					echo 'ERROR:'. json_encode($item).'<br>';
	 					echo 'Try ReGet :::<br>' ;
	 					$feeResult = $this->send_post($mfnUrl,array('method'=>'GET',
	 							'model'=> json_encode($item)
	 					)) ;
	 
	 					$feeResult = json_decode(trim($feeResult)) ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					$feeResult = $feeResult['data'] ;
	 					$feeResult = get_object_vars($feeResult) ;
	 						//weightHandlingFee
	 					$feeResult['fbaCost'] =
		 					(empty($feeResult['weightHandlingFee'])?0:$feeResult['weightHandlingFee'])
		 					+ (empty($feeResult['orderHandlingFee'])?0:$feeResult['orderHandlingFee'])
		 					+ (empty($feeResult['fbaDeliveryServicesFee'])?0:$feeResult['fbaDeliveryServicesFee'])
		 					+ (empty($feeResult['pickAndPackFee'])?0:$feeResult['pickAndPackFee'])
		 					+ (empty($feeResult['storageFee'])?0:$feeResult['storageFee']) ;
	 
	 					if( isset($feeResult['commissionFee']) ){//success
	 						$feeResult['asin'] = $listing['ASIN'] ;
	 						$feeResult['type'] = $listing['TYPE'] ;
	 						$feeResult['loginId'] = $loginId ;
	 						$feeResult['fbaCost'] = 0 ;
	 							
	 						if( $feeResult['commissionFee'] == 1 ){ //存在最低价限制
	 							$feeResult['commissionLowlimit'] = 1 ;
	 							//重新计算一次
	 							$item["price"] = 100 ;
	 							$item["revenueTotal"] = 100 ;
	 							$feeResult_ = $this->send_post($afnUrl,array('method'=>'GET',
	 									'model'=> json_encode($item)
	 							)) ;
	 							$feeResult_ = json_decode(trim($feeResult_)) ;
	 							$feeResult_ = get_object_vars($feeResult_) ;
	 							$feeResult_ = $feeResult_['data'] ;
	 							$feeResult_ = get_object_vars($feeResult_) ;
	 								
	 							if( isset($feeResult_['commissionFee']) ){//success
	 								$feeResult['commissionRatio'] = round($feeResult_['commissionFee']/100,4) ;
	 							}
	 						}else{
	 							$feeResult['commissionRatio'] = round($feeResult['commissionFee']/100,4) ;
	 						}
	 							
	 						$this->Cost->saveDevCostByFee($feeResult) ;
	 						echo 'Try Success<br>';
	 					}else{
	 						echo 'Try ERROR......<br>';
	 					}
	 				}
	 			}catch(Exception $e){
	 			}
	 		}
	 	}else{
	 		foreach($result['data'] as  $item){
	 			$item = get_object_vars($item) ;
	 			//debug( $item ) ;
	 			$item["selected"] = true ;
	 			$item["language"] = "en_US" ;
	 			$item["price"] = 2 ;
	 			$item["shipping"] = 0 ;
	 			$item["revenueTotal"] = 2 ;
	 			$item["order-handling"] = 0 ;
	 			$item["pick-pack"] = 0 ;
	 			$item["outbound-delivery"] = 0 ;
	 			$item["storage"] = 0 ;
	 			$item["inbound-delivery"] = 0 ;
	 			$item["customer-service"] = 0 ;
	 			$item["prep-service"] = 0 ;
	 			$item["fulfillmentTotal"] = 0 ;
	 			 
	 			try{
	 				echo "<br>$channel<br>";
	 				$feeResult = $this->send_post($mfnUrl,array('method'=>'GET',
	 						'model'=> json_encode($item)
	 				)) ;
	 				 
	 				$feeResult = json_decode(trim($feeResult)) ;
	 				$feeResult = get_object_vars($feeResult) ;
	 				$feeResult = $feeResult['data'] ;
	 
	 				if( empty($feeResult) ){
	 					echo 'FBMC Error, Try It<br>';
	 					$feeResult = $this->send_post($afnUrl,array('method'=>'GET',
	 							'model'=> json_encode($item)
	 					)) ;
	 
	 					$feeResult = json_decode(trim($feeResult)) ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					$feeResult = $feeResult['data'] ;
	 					if( empty($feeResult) ){
	 						echo 'FBA Error, End<br>';
	 						continue ;
	 					}
	 				}
	 
	 				$feeResult = get_object_vars($feeResult) ;
	 
	 				if( isset($feeResult['commissionFee']) ){//success
	 					$feeResult['asin'] = $listing['ASIN'] ;
	 					$feeResult['type'] = $listing['TYPE'] ;
	 					$feeResult['loginId'] = $loginId ;
	 					$feeResult['fbaCost'] = 0 ;
	 						
	 					if( $feeResult['commissionFee'] == 1 ){ //存在最低价限制
	 						$feeResult['commissionLowlimit'] = 1 ;
	 						//重新计算一次
	 						$item["price"] = 100 ;
	 						$item["revenueTotal"] = 100 ;
	 						$feeResult_ = $this->send_post($afnUrl,array('method'=>'GET',
	 								'model'=> json_encode($item)
	 						)) ;
	 						$feeResult_ = json_decode(trim($feeResult_)) ;
	 						$feeResult_ = get_object_vars($feeResult_) ;
	 						$feeResult_ = $feeResult_['data'] ;
	 						if( !empty( $feeResult_ ) ){
	 							$feeResult_ = get_object_vars($feeResult_) ;
	 
	 							if( isset($feeResult_['commissionFee']) ){//success
	 								$feeResult['commissionRatio'] = round($feeResult_['commissionFee']/100,4) ;
	 							}
	 							echo 'ReCalc Success <br>';
	 						}else{
	 							echo 'ReCalc ERROR <br>';
	 						}
	 					}else{
	 						$feeResult['commissionRatio'] = round($feeResult['commissionFee']/2,4) ;
	 					}
	 						
	 					$this->Cost->saveDevCostByFee($feeResult) ;
	 				}else{//执行失败
	 					echo 'ERROR:'. json_encode($item).'<br>';
	 					echo 'Try ReGet :::<br>' ;
	 					$feeResult = $this->send_post($mfnUrl,array('method'=>'GET',
	 							'model'=> json_encode($item)
	 					)) ;
	 						
	 					$feeResult = json_decode(trim($feeResult)) ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					$feeResult = $feeResult['data'] ;
	 					$feeResult = get_object_vars($feeResult) ;
	 						
	 					if( isset($feeResult['commissionFee']) ){//success
	 						$feeResult['asin'] = $listing['ASIN'] ;
	 						$feeResult['type'] = $listing['TYPE'] ;
	 						$feeResult['loginId'] = $loginId ;
	 						$feeResult['fbaCost'] = 0 ;
	 							
	 						if( $feeResult['commissionFee'] == 1 ){ //存在最低价限制
	 							$feeResult['commissionLowlimit'] = 1 ;
	 							//重新计算一次
	 							$item["price"] = 100 ;
	 							$item["revenueTotal"] = 100 ;
	 							$feeResult_ = $this->send_post($afnUrl,array('method'=>'GET',
	 									'model'=> json_encode($item)
	 							)) ;
	 							$feeResult_ = json_decode(trim($feeResult_)) ;
	 							$feeResult_ = get_object_vars($feeResult_) ;
	 							$feeResult_ = $feeResult_['data'] ;
	 								
	 							if( !empty( $feeResult_ ) ){
	 								$feeResult_ = get_object_vars($feeResult_) ;
	 									
	 								if( isset($feeResult_['commissionFee']) ){//success
	 									$feeResult['commissionRatio'] = round($feeResult_['commissionFee']/100,4) ;
	 								}
	 								echo 'ReCalc Success <br>';
	 							}else{
	 								echo 'ReCalc ERROR <br>';
	 							}
	 						}else{
	 							$feeResult['commissionRatio'] = round($feeResult['commissionFee']/100,4) ;
	 						}
	 
	 						$this->Cost->saveDevCostByFee($feeResult) ;
	 					}else{
	 						echo 'Try ERROR......<br>';
	 					}
	 				}
	 			}catch(Exception $e){
	 			}
	 		}
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
	 			$item["price"] = 2 ;
	 			$item["revenueTotal"] = 2 ;
	 			$item["undefined"] = 0 ;
	 			$item["inbound-delivery"] = 0 ;
	 			$item["prep-service"] = 0 ;
	 			$item["fulfillmentTotal"] = 0 ;
	 			try{
	 				echo '<br>FBA<br>';
	 				$feeResult = $this->send_post($afnUrl,array('method'=>'GET',
	 						'model'=> json_encode($item)
	 				)) ;
	 				
	 				$feeResult = json_decode(trim($feeResult)) ;
	 				$feeResult = get_object_vars($feeResult) ;
	 				$feeResult = $feeResult['data'] ;
	 				
	 				if( empty($feeResult) ){
	 					echo 'FBA Error, Try It<br>';
	 					$feeResult = $this->send_post($afnUrl,array('method'=>'GET',
	 							'model'=> json_encode($item)
	 					)) ;
	 					
	 					$feeResult = json_decode(trim($feeResult)) ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					$feeResult = $feeResult['data'] ;
	 					if( empty($feeResult) ){
	 						echo 'FBA Error, End<br>';
	 						continue ;
	 					}
	 				}
	 				
	 				$feeResult = get_object_vars($feeResult) ;
	 				
	 				if( isset($feeResult['commissionFee']) ){//success
	 					$feeResult['accountId'] = $listing['ACCOUNT_ID'] ;
	 					$feeResult['listingSku'] = $listing['LISTING_SKU'] ;
	 					$feeResult['realId'] = $listing['ID'] ;
	 					$feeResult['loginId'] = $loginId ;
	 					
	 					
	 					$feeResult['fbaCost'] =
		 					(empty($feeResult['weightHandlingFee'])?0:$feeResult['weightHandlingFee'])
		 					+ (empty($feeResult['orderHandlingFee'])?0:$feeResult['orderHandlingFee'])
		 					+ (empty($feeResult['fbaDeliveryServicesFee'])?0:$feeResult['fbaDeliveryServicesFee'])
		 					+ (empty($feeResult['pickAndPackFee'])?0:$feeResult['pickAndPackFee'])
		 					+ (empty($feeResult['storageFee'])?0:$feeResult['storageFee']) ;
	 					
	 					if( $feeResult['commissionFee'] == 1 ){ //存在最低价限制
	 						$feeResult['commissionLowlimit'] = 1 ;
	 						//重新计算一次
	 						$item["price"] = 100 ;
	 						$item["revenueTotal"] = 100 ;
	 						$feeResult_ = $this->send_post($afnUrl,array('method'=>'GET',
	 								'model'=> json_encode($item)
	 						)) ;
	 						$feeResult_ = json_decode(trim($feeResult_)) ;
	 						$feeResult_ = get_object_vars($feeResult_) ;
	 						$feeResult_ = $feeResult_['data'] ;
	 						$feeResult_ = get_object_vars($feeResult_) ;
	 						
	 						if( isset($feeResult_['commissionFee']) ){//success
	 							$feeResult['commissionRatio'] = round($feeResult_['commissionFee']/100,4) ;
	 						}
	 					}else{
	 						$feeResult['commissionRatio'] = round($feeResult['commissionFee']/2,4) ;
	 					}
	 					
	 					$this->Cost->saveCostByFee($feeResult) ;
	 				}else{//执行失败
	 					echo 'ERROR:'. json_encode($item).'<br>';
	 					echo 'Try ReGet :::<br>' ;
	 					$feeResult = $this->send_post($mfnUrl,array('method'=>'GET',
	 							'model'=> json_encode($item)
	 					)) ;
	 						
	 					$feeResult = json_decode(trim($feeResult)) ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					$feeResult = $feeResult['data'] ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					
	 					$feeResult['fbaCost'] =
		 					(empty($feeResult['weightHandlingFee'])?0:$feeResult['weightHandlingFee'])
		 					+ (empty($feeResult['orderHandlingFee'])?0:$feeResult['orderHandlingFee'])
		 					+ (empty($feeResult['fbaDeliveryServicesFee'])?0:$feeResult['fbaDeliveryServicesFee'])
		 					+ (empty($feeResult['pickAndPackFee'])?0:$feeResult['pickAndPackFee'])
		 					+ (empty($feeResult['storageFee'])?0:$feeResult['storageFee']) ;
	 						
	 					if( isset($feeResult['commissionFee']) ){//success
	 						$feeResult['accountId'] = $listing['ACCOUNT_ID'] ;
	 						$feeResult['listingSku'] = $listing['LISTING_SKU'] ;
	 						$feeResult['realId'] = $listing['ID'] ;
	 						$feeResult['loginId'] = $loginId ;
	 						$feeResult['fbaCost'] = 0 ;
	 							
	 						if( $feeResult['commissionFee'] == 1 ){ //存在最低价限制
	 							$feeResult['commissionLowlimit'] = 1 ;
	 							//重新计算一次
	 							$item["price"] = 100 ;
	 							$item["revenueTotal"] = 100 ;
	 							$feeResult_ = $this->send_post($afnUrl,array('method'=>'GET',
	 									'model'=> json_encode($item)
	 							)) ;
	 							$feeResult_ = json_decode(trim($feeResult_)) ;
	 							$feeResult_ = get_object_vars($feeResult_) ;
	 							$feeResult_ = $feeResult_['data'] ;
	 							$feeResult_ = get_object_vars($feeResult_) ;
	 					
	 							if( isset($feeResult_['commissionFee']) ){//success
	 								$feeResult['commissionRatio'] = round($feeResult_['commissionFee']/100,4) ;
	 							}
	 						}else{
	 							$feeResult['commissionRatio'] = round($feeResult['commissionFee']/100,4) ;
	 						}
	 							
	 						$this->Cost->saveCostByFee($feeResult) ;
	 						echo 'Try Success<br>';
	 					}else{
	 						echo 'Try ERROR......<br>';
	 					}
	 				}
	 			}catch(Exception $e){
	 			}
	 		}
	 	}else{
	 		foreach($result['data'] as  $item){
	 			$item = get_object_vars($item) ;
	 			//debug( $item ) ;
	 			$item["selected"] = true ;
	 			$item["language"] = "en_US" ;
	 			$item["price"] = 2 ;
	 			$item["shipping"] = 0 ;
	 			$item["revenueTotal"] = 2 ;
	 			$item["order-handling"] = 0 ;
	 			$item["pick-pack"] = 0 ;
	 			$item["outbound-delivery"] = 0 ;
	 			$item["storage"] = 0 ;
	 			$item["inbound-delivery"] = 0 ;
	 			$item["customer-service"] = 0 ;
	 			$item["prep-service"] = 0 ;
	 			$item["fulfillmentTotal"] = 0 ;
	 	
	 			try{
	 				echo '<br>FBM<br>';
	 				$feeResult = $this->send_post($mfnUrl,array('method'=>'GET',
	 						'model'=> json_encode($item)
	 				)) ;
	 	
	 				$feeResult = json_decode(trim($feeResult)) ;
	 				$feeResult = get_object_vars($feeResult) ;
	 				$feeResult = $feeResult['data'] ;
	 				
	 				if( empty($feeResult) ){
	 					echo 'FBA Error, Try It<br>';
	 					$feeResult = $this->send_post($afnUrl,array('method'=>'GET',
	 							'model'=> json_encode($item)
	 					)) ;
	 						
	 					$feeResult = json_decode(trim($feeResult)) ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					$feeResult = $feeResult['data'] ;
	 					if( empty($feeResult) ){
	 						echo 'FBA Error, End<br>';
	 						continue ;
	 					}
	 				}
	 				
	 				$feeResult = get_object_vars($feeResult) ;
	 				
	 				if( isset($feeResult['commissionFee']) ){//success
	 					$feeResult['accountId'] = $listing['ACCOUNT_ID'] ;
	 					$feeResult['listingSku'] = $listing['LISTING_SKU'] ;
	 					$feeResult['realId'] = $listing['ID'] ;
	 					$feeResult['loginId'] = $loginId ;
	 					$feeResult['fbaCost'] = 0 ;
	 					
	 					if( $feeResult['commissionFee'] == 1 ){ //存在最低价限制
	 						$feeResult['commissionLowlimit'] = 1 ;
	 						//重新计算一次
	 						$item["price"] = 100 ;
	 						$item["revenueTotal"] = 100 ;
	 						$feeResult_ = $this->send_post($afnUrl,array('method'=>'GET',
	 								'model'=> json_encode($item)
	 						)) ;
	 						$feeResult_ = json_decode(trim($feeResult_)) ;
	 						$feeResult_ = get_object_vars($feeResult_) ;
	 						$feeResult_ = $feeResult_['data'] ;
	 						if( !empty( $feeResult_ ) ){
	 							$feeResult_ = get_object_vars($feeResult_) ;
	 								
	 							if( isset($feeResult_['commissionFee']) ){//success
	 								$feeResult['commissionRatio'] = round($feeResult_['commissionFee']/100,4) ;
	 							}
	 							echo 'ReCalc Success <br>';
	 						}else{
	 							echo 'ReCalc ERROR <br>';
	 						}
	 					}else{
	 						$feeResult['commissionRatio'] = round($feeResult['commissionFee']/2,4) ;
	 					}
	 					
	 					$this->Cost->saveCostByFee($feeResult) ;
	 				}else{//执行失败
	 					echo 'ERROR:'. json_encode($item).'<br>';
	 					echo 'Try ReGet :::<br>' ;
	 					$feeResult = $this->send_post($mfnUrl,array('method'=>'GET',
	 							'model'=> json_encode($item)
	 					 )) ;
	 					
	 					$feeResult = json_decode(trim($feeResult)) ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					$feeResult = $feeResult['data'] ;
	 					$feeResult = get_object_vars($feeResult) ;
	 					
	 					if( isset($feeResult['commissionFee']) ){//success
	 						$feeResult['accountId'] = $listing['ACCOUNT_ID'] ;
	 						$feeResult['listingSku'] = $listing['LISTING_SKU'] ;
	 						$feeResult['realId'] = $listing['ID'] ;
	 						$feeResult['loginId'] = $loginId ;
	 						$feeResult['fbaCost'] = 0 ;
	 							
	 						if( $feeResult['commissionFee'] == 1 ){ //存在最低价限制
	 							$feeResult['commissionLowlimit'] = 1 ;
	 							//重新计算一次
	 							$item["price"] = 100 ;
	 							$item["revenueTotal"] = 100 ;
	 							$feeResult_ = $this->send_post($afnUrl,array('method'=>'GET',
	 									'model'=> json_encode($item)
	 							)) ;
	 							$feeResult_ = json_decode(trim($feeResult_)) ;
	 							$feeResult_ = get_object_vars($feeResult_) ;
	 							$feeResult_ = $feeResult_['data'] ;
	 							
	 							if( !empty( $feeResult_ ) ){
		 							$feeResult_ = get_object_vars($feeResult_) ;
		 								
		 							if( isset($feeResult_['commissionFee']) ){//success
		 								$feeResult['commissionRatio'] = round($feeResult_['commissionFee']/100,4) ;
		 							}
	 								echo 'ReCalc Success <br>';
	 							}else{
	 								echo 'ReCalc ERROR <br>';
	 							}
	 						}else{
	 							$feeResult['commissionRatio'] = round($feeResult['commissionFee']/100,4) ;
	 						}
	 						
	 						$this->Cost->saveCostByFee($feeResult) ;
	 					}else{
	 						echo 'Try ERROR......<br>';
	 					}
	 				}
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