<?php
require_once("amazon/MarketplaceWebServiceOrders/Client.php");
require_once("amazon/MarketplaceWebServiceOrders/Model/ListOrdersRequest.php");
require_once("amazon/MarketplaceWebServiceOrders/Model/ListOrderItemsRequest.php");
require_once("amazon/MarketplaceWebServiceOrders/Model/ListOrdersByNextTokenRequest.php");
require_once("amazon/MarketplaceWebServiceOrders/Model/MarketplaceIdList.php");
require_once("amazon/MarketplaceWebServiceOrders/Exception.php");

class AmazonOrder {
	var $AWS_ACCESS_KEY_ID ; 
	var $AWS_SECRET_ACCESS_KEY ;
	var $APPLICATION_NAME;
	var $APPLICATION_VERSION;
	var $MERCHANT_ID ;
	var $MARKETPLACE_ID ;
	var $MerchantIdentifier ;
	var $APPLICATION_ID ;
	
	public function AmazonOrder( 
		$AWS_ACCESS_KEY_ID, 
		$AWS_SECRET_ACCESS_KEY, 
		$APPLICATION_NAME, 
		$APPLICATION_VERSION, 
		$MERCHANT_ID, 
		$MARKETPLACE_ID,
		$MerchantIdentifier , 
		$APPLICATION_ID = '' 
	){
		$this->AWS_ACCESS_KEY_ID 	= $AWS_ACCESS_KEY_ID ;
		$this->AWS_SECRET_ACCESS_KEY= $AWS_SECRET_ACCESS_KEY ;
		$this->APPLICATION_NAME 	= $APPLICATION_NAME ;
		$this->APPLICATION_VERSION 	= $APPLICATION_VERSION ;
		$this->MERCHANT_ID 			= $MERCHANT_ID ;
		$this->MARKETPLACE_ID 		= $MARKETPLACE_ID ;
		$this->MerchantIdentifier 	= $MerchantIdentifier ;
		$this->APPLICATION_ID       = $APPLICATION_ID;
	}
	
	public function getAccountPlatform($accountId){
		$System = ClassRegistry::init("System") ;
		return $System->getAccountPlatformConfig($accountId ) ;
	}
	
	/**
	 * 保存订单
	 * @param unknown_type $order
	 */
	function getOrderItems($orderId,$accountId){
		
		$platform = $this->getAccountPlatform($accountId) ;
		
		$config = array (
				'ServiceURL' =>  $platform['AMAZON_ORDER_SERVICE_URL'],// "https://mws.amazonservices.com",
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
		
		$service = new MarketplaceWebServiceOrders_Client(
				$this->AWS_ACCESS_KEY_ID,
				$this->AWS_SECRET_ACCESS_KEY,
				$this->APPLICATION_NAME,
				$this->APPLICATION_VERSION,
				$config ) ;
		
		$request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
		$request->setSellerId( $this->MERCHANT_ID );
		$request->setAmazonOrderId( $orderId );
		
		$response = $service->listOrderItems( $request );
		//debug($response) ;
		if ($response->isSetListOrderItemsResult()) {
			$listOrderItemsResult = $response->getListOrderItemsResult();
			
			if ($listOrderItemsResult->isSetOrderItems()) {
				
				$items = array() ;
				
				$orderItems = $listOrderItemsResult->getOrderItems();
				$memberList = $orderItems->getOrderItem();
				foreach ($memberList as $member) {
					
					$item = array() ;
					if ($member->isSetOrderItemId())
					{
						$item['OrderItemId'] = $member->getOrderItemId() ;
					}else{
						continue ;
					}
					if ($member->isSetASIN())
					{
						$item['Asin'] = $member->getASIN() ;
					}
					if ($member->isSetSellerSKU())
					{
						$item['SellerSku'] = $member->getSellerSKU() ;
					}
					if ($member->isSetTitle())
					{
						$item['Title'] = $member->getTitle() ;
					}
					if ($member->isSetQuantityOrdered())
					{
						$item['QuantityOrdered'] = $member->getQuantityOrdered() ;
					}
					if ($member->isSetQuantityShipped())
					{
						$item['QuantityShipped'] = $member->getQuantityShipped() ;
					}
					if ($member->isSetGiftMessageText())
					{
						$item['GiftMessageText'] = $member->getGiftMessageText() ;
					}
					if ($member->isSetItemPrice()) {
						$itemPrice = $member->getItemPrice();
						if ($itemPrice->isSetCurrencyCode())
						{
							$item['ItemPriceCurrencyCode'] = $itemPrice->getCurrencyCode() ;
						}
						if ($itemPrice->isSetAmount())
						{
							$item['ItemPriceAmount'] = $itemPrice->getAmount() ;
						}
					}
					if ($member->isSetShippingPrice()) {
						$shippingPrice = $member->getShippingPrice();
						if ($shippingPrice->isSetCurrencyCode())
						{
							$item['ShippingPriceCurrencyCode'] = $shippingPrice->getCurrencyCode() ;
						}
						if ($shippingPrice->isSetAmount())
						{
							$item['ShippingPriceAmount'] = $shippingPrice->getAmount() ;
						}
					}
					if ($member->isSetGiftWrapPrice()) {
						$giftWrapPrice = $member->getGiftWrapPrice();
						if ($giftWrapPrice->isSetCurrencyCode())
						{
							$item['GiftWrapPriceCurrencyCode'] = $giftWrapPrice->getCurrencyCode() ;
						}
						if ($giftWrapPrice->isSetAmount())
						{
							$item['GiftWrapPriceAmount'] = $giftWrapPrice->getAmount() ;
						}
					}
					if ($member->isSetItemTax()) {
						$itemTax = $member->getItemTax();
						if ($itemTax->isSetCurrencyCode())
						{
							$item['ItemTaxCurrencyCode'] = $itemTax->getCurrencyCode() ;
						}
						if ($itemTax->isSetAmount())
						{
							$item['ItemTaxAmount'] = $itemTax->getAmount() ;
						}
					}
					if ($member->isSetShippingTax()) {
						$shippingTax = $member->getShippingTax();
						if ($shippingTax->isSetCurrencyCode())
						{
							$item['ShippingTaxCurrencyCode'] = $shippingTax->getCurrencyCode() ;
						}
						if ($shippingTax->isSetAmount())
						{
							$item['ShippingTaxAmount'] = $shippingTax->getAmount() ;
						}
					}
					if ($member->isSetGiftWrapTax()) {
						$giftWrapTax = $member->getGiftWrapTax();
						if ($giftWrapTax->isSetCurrencyCode())
						{
							$item['GiftWrapTaxCurrencyCode'] = $giftWrapTax->getCurrencyCode() ;
						}
						if ($giftWrapTax->isSetAmount())
						{
							$item['GiftWrapTaxAmount'] = $giftWrapTax->getAmount() ;
						}
					}
					if ($member->isSetShippingDiscount()) {
						$shippingDiscount = $member->getShippingDiscount();
						if ($shippingDiscount->isSetCurrencyCode())
						{
							$item['ShippingDiscountCurrencyCode'] = $shippingDiscount->getCurrencyCode() ;
						}
						if ($shippingDiscount->isSetAmount())
						{
							$item['ShippingDiscountAmount'] = $shippingDiscount->getAmount() ;
						}
					}
					if ($member->isSetPromotionDiscount()) {
						$promotionDiscount = $member->getPromotionDiscount();
						if ($promotionDiscount->isSetCurrencyCode())
						{
							$item['PromotionDiscountCurrencyCode'] = $promotionDiscount->getCurrencyCode() ;
						}
						if ($promotionDiscount->isSetAmount())
						{
							$item['PromotionDiscountAmount'] = $promotionDiscount->getAmount() ;
						}
					}
					
					$item['OrderId'] = $orderId ;
					
					$NOrderService  = ClassRegistry::init("NOrderService") ;
					
					$NOrderService->saveOrderItem($item) ;
				}
			}
		}
		
	}
	
	/**
	 * $createAfter=null,
		$createBefore=null,
		$LastUpdatedAfter=null,
		$LastUpdatedBefore=null,
		$OrderStatus = null,
		$FulfillmentChannel=null,
		$BuyerEmail = null,
		$MaxResultsPerPage = null
	 */
	public function getOrders($querys=array(),$accountId){
		$platform = $this->getAccountPlatform($accountId) ;
		
		$config = array (
				'ServiceURL' =>  $platform['AMAZON_ORDER_SERVICE_URL'],// "https://mws.amazonservices.com",
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
		
		$service = new MarketplaceWebServiceOrders_Client(
				$this->AWS_ACCESS_KEY_ID,
				$this->AWS_SECRET_ACCESS_KEY,
				$this->APPLICATION_NAME,
				$this->APPLICATION_VERSION,
				$config ) ;
		
		$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
		$request->setSellerId($this->MERCHANT_ID);
		
		//$LastUpdatedAfter = "2012-08-11 01:00:00" ;
		//$LastUpdatedAfter = "2012-08-11 23:00:00" ;
		//$MaxResultsPerPage = 50 ;
		
		if( ! isset( $querys['LastUpdatedAfter']  ) ){
			$querys['LastUpdatedAfter'] = date( "Y-m-d H:i:s", time()-16*60*60 ) ;
		}
		
		//debug( $LastUpdatedAfter ) ;
		
		if(!empty($querys['createAfter'])){
			$request->setCreatedAfter(new DateTime( $querys['createAfter'] , new DateTimeZone('UTC')));
		}
		
		if(!empty( $querys['createBefore'] )){
			$request->setCreatedBefore(new DateTime( $querys['createBefore'] , new DateTimeZone('UTC')));
		}
		
		if(!empty( $querys['LastUpdatedAfter']  )){
			$request->setLastUpdatedAfter(new DateTime(  $querys['LastUpdatedAfter']  , new DateTimeZone('UTC')));
		}
		
		if(!empty(  $querys['LastUpdatedBefore']  )){
			$request->setLastUpdatedBefore(new DateTime(  $querys['LastUpdatedBefore']  , new DateTimeZone('UTC')));
		}
		
		if(!empty(  $querys['OrderStatus']  )){
			$request->setOrderStatus(  $querys['OrderStatus']  );
		}
		
		if(!empty(  $querys['FulfillmentChannel']  )){
			$request->setFulfillmentChannel( $querys['FulfillmentChannel'] );
		}
		
		if(!empty( $querys['BuyerEmail'] )){
			$request->setBuyerEmail(  $querys['BuyerEmail'] );
		}
		
		if(!empty( $querys['MaxResultsPerPage'] )){
			$request->setMaxResultsPerPage( $querys['MaxResultsPerPage'] );
		}
		
		// Set the marketplaces queried in this ListOrdersRequest
		$marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
		$marketplaceIdList->setId(array($this->MARKETPLACE_ID));
		$request->setMarketplaceId($marketplaceIdList);
		
		try {
			$response = $service->listOrders($request);

			if ($response->isSetListOrdersResult()) {
				$listOrdersResult = $response->getListOrdersResult();

				$nextToken = "" ;
				
				if ($listOrdersResult->isSetNextToken())
				{
					$nextToken = $listOrdersResult->getNextToken()  ;
				}
				

				if ($listOrdersResult->isSetOrders()) {
					$orders = $listOrdersResult->getOrders();
					$memberList = $orders->getOrder();
					$index = 0 ;
					foreach ($memberList as $member) {
						$index++ ;
						$record = array() ;
						
						if ($member->isSetAmazonOrderId())
						{
							$record['OrderId'] = $member->getAmazonOrderId() ;
						}
						if ($member->isSetShipmentServiceLevelCategory())
						{
							$record['ShipmentServiceLevelCategory'] = $member->getShipmentServiceLevelCategory() ;
						}
						if ($member->isSetBuyerName())
						{
							$record['BuyerName'] = $member->getBuyerName() ;
						}
						if ($member->isSetBuyerEmail())
						{
							$record['BuyerEmail'] = $member->getBuyerEmail() ;
						}
		
						if ($member->isSetPurchaseDate())
						{
							$record['PurchaseDate'] = $member->getPurchaseDate() ;
						}
						if ($member->isSetLastUpdateDate())
						{
							$record['LastUpdateDate'] = $member->getLastUpdateDate() ;
						}
						if ($member->isSetOrderStatus())
						{
							$record['OrderStatus'] = $member->getOrderStatus() ;
						}
						if ($member->isSetFulfillmentChannel())
						{
							$record['FulfillmentChannel'] = $member->getFulfillmentChannel() ;
						}
						if ($member->isSetSalesChannel())
						{
							$record['SalesChannel'] = $member->getSalesChannel() ;
						}
						if ($member->isSetOrderChannel())
						{
							$record['OrderChannel'] = $member->getOrderChannel() ;
						}
						if ($member->isSetShipServiceLevel())
						{
							$record['ShipServiceLevel'] = $member->getShipServiceLevel() ;
						}
						if ($member->isSetShippingAddress()) {
							$shippingAddress = $member->getShippingAddress();
							if ($shippingAddress->isSetName())
							{
								$record['ShipperName'] = $shippingAddress->getName() ;
							}
							if ($shippingAddress->isSetAddressLine1())
							{
								$record['AddressLine1'] = $shippingAddress->getAddressLine1() ;
							}
							if ($shippingAddress->isSetAddressLine2())
							{
								$record['AddressLine2'] = $shippingAddress->getAddressLine2() ;
							}
							if ($shippingAddress->isSetAddressLine3())
							{
								$record['AddressLine3'] = $shippingAddress->getAddressLine3() ;
							}
							if ($shippingAddress->isSetCity())
							{
								$record['City'] = $shippingAddress->getCity() ;
							}
							if ($shippingAddress->isSetCounty())
							{
								$record['County'] = $shippingAddress->getCounty() ;
							}
							if ($shippingAddress->isSetDistrict())
							{
								$record['District'] = $shippingAddress->getDistrict() ;
							}
							if ($shippingAddress->isSetStateOrRegion())
							{
								$record['StateOrRegion'] = $shippingAddress->getStateOrRegion() ;
							}
							if ($shippingAddress->isSetPostalCode())
							{
								$record['PostalCode'] = $shippingAddress->getPostalCode() ;
							}
							if ($shippingAddress->isSetCountryCode())
							{
								$record['CountryCode'] = $shippingAddress->getCountryCode() ;
							}
							if ($shippingAddress->isSetPhone())
							{
								$record['Phone'] = $shippingAddress->getPhone() ;
							}
						}
						if ($member->isSetOrderTotal()) {
							$orderTotal = $member->getOrderTotal();
							if ($orderTotal->isSetCurrencyCode())
							{
								$record['CurrencyCode'] = $orderTotal->getCurrencyCode() ;
							}
							if ($orderTotal->isSetAmount())
							{
								$record['Amount'] = $orderTotal->getAmount() ;
							}
						}
						if ($member->isSetNumberOfItemsShipped())
						{
							$record['ShippedNum'] = $member->getNumberOfItemsShipped() ;
						}
						if ($member->isSetNumberOfItemsUnshipped())
						{
							$record['UnshippedNum'] = $member->getNumberOfItemsUnshipped() ;
						}
						
						$NOrderService  = ClassRegistry::init("NOrderService") ;
						
						$NOrderService->saveOrder($record,$accountId) ;
						
						try{
						 //$this->getOrderItems($record['OrderId']) ;
						}catch(MarketplaceWebServiceOrders_Exception $e){
							//
						}
					}
				}
				
				if( !empty($nextToken) ){
					$this->getOrdersByNextToken($nextToken,$accountId) ;
				}
			}
			
		} catch (MarketplaceWebServiceOrders_Exception $ex) {
			echo("Caught Exception: " . $ex->getMessage() . "\n");
			echo("Response Status Code: " . $ex->getStatusCode() . "\n");
			echo("Error Code: " . $ex->getErrorCode() . "\n");
			echo("Error Type: " . $ex->getErrorType() . "\n");
			echo("Request ID: " . $ex->getRequestId() . "\n");
			echo("XML: " . $ex->getXML() . "\n");
		}
	}
	
	function getOrdersByNextToken($nextToken,$accountId){
		$platform = $this->getAccountPlatform($accountId) ;
		
		$config = array (
				'ServiceURL' => $platform['AMAZON_ORDER_SERVICE_URL'],// "https://mws.amazonservices.com",
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
		
		$service = new MarketplaceWebServiceOrders_Client(
				$this->AWS_ACCESS_KEY_ID,
				$this->AWS_SECRET_ACCESS_KEY,
				$this->APPLICATION_NAME,
				$this->APPLICATION_VERSION,
				$config ) ;
		
		$request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
		$request->setSellerId($this->MERCHANT_ID);
		$request->setNextToken( $nextToken );
		
		$response = $service->listOrdersByNextToken($request);
		
		if ($response->isSetListOrdersByNextTokenResult()) {
			$listOrdersByNextTokenResult = $response->getListOrdersByNextTokenResult();
			
			$nextToken = "" ;
			
			if ($listOrdersByNextTokenResult->isSetNextToken())
			{
				$nextToken = $listOrdersByNextTokenResult->getNextToken()  ;
			}

			if ($listOrdersByNextTokenResult->isSetOrders()) {
				$orders = $listOrdersByNextTokenResult->getOrders();
				$memberList = $orders->getOrder();
				
					$index = 0 ;
					foreach ($memberList as $member) {
						$index++ ;
						$record = array() ;
				
						if ($member->isSetAmazonOrderId())
						{
							$record['OrderId'] = $member->getAmazonOrderId() ;
						}
						if ($member->isSetShipmentServiceLevelCategory())
						{
							$record['ShipmentServiceLevelCategory'] = $member->getShipmentServiceLevelCategory() ;
						}
						if ($member->isSetBuyerName())
						{
							$record['BuyerName'] = $member->getBuyerName() ;
						}
						if ($member->isSetBuyerEmail())
						{
							$record['BuyerEmail'] = $member->getBuyerEmail() ;
						}
				
						if ($member->isSetPurchaseDate())
						{
							$record['PurchaseDate'] = $member->getPurchaseDate() ;
						}
						if ($member->isSetLastUpdateDate())
						{
							$record['LastUpdateDate'] = $member->getLastUpdateDate() ;
						}
						if ($member->isSetOrderStatus())
						{
							$record['OrderStatus'] = $member->getOrderStatus() ;
						}
						if ($member->isSetFulfillmentChannel())
						{
							$record['FulfillmentChannel'] = $member->getFulfillmentChannel() ;
						}
						if ($member->isSetSalesChannel())
						{
							$record['SalesChannel'] = $member->getSalesChannel() ;
						}
						if ($member->isSetOrderChannel())
						{
							$record['OrderChannel'] = $member->getOrderChannel() ;
						}
						if ($member->isSetShipServiceLevel())
						{
							$record['ShipServiceLevel'] = $member->getShipServiceLevel() ;
						}
						if ($member->isSetShippingAddress()) {
							$shippingAddress = $member->getShippingAddress();
							if ($shippingAddress->isSetName())
							{
								$record['ShipperName'] = $shippingAddress->getName() ;
							}
							if ($shippingAddress->isSetAddressLine1())
							{
								$record['AddressLine1'] = $shippingAddress->getAddressLine1() ;
							}
							if ($shippingAddress->isSetAddressLine2())
							{
								$record['AddressLine2'] = $shippingAddress->getAddressLine2() ;
							}
							if ($shippingAddress->isSetAddressLine3())
							{
								$record['AddressLine3'] = $shippingAddress->getAddressLine3() ;
							}
							if ($shippingAddress->isSetCity())
							{
								$record['City'] = $shippingAddress->getCity() ;
							}
							if ($shippingAddress->isSetCounty())
							{
								$record['County'] = $shippingAddress->getCounty() ;
							}
							if ($shippingAddress->isSetDistrict())
							{
								$record['District'] = $shippingAddress->getDistrict() ;
							}
							if ($shippingAddress->isSetStateOrRegion())
							{
								$record['StateOrRegion'] = $shippingAddress->getStateOrRegion() ;
							}
							if ($shippingAddress->isSetPostalCode())
							{
								$record['PostalCode'] = $shippingAddress->getPostalCode() ;
							}
							if ($shippingAddress->isSetCountryCode())
							{
								$record['CountryCode'] = $shippingAddress->getCountryCode() ;
							}
							if ($shippingAddress->isSetPhone())
							{
								$record['Phone'] = $shippingAddress->getPhone() ;
							}
						}
						if ($member->isSetOrderTotal()) {
							$orderTotal = $member->getOrderTotal();
							if ($orderTotal->isSetCurrencyCode())
							{
								$record['CurrencyCode'] = $orderTotal->getCurrencyCode() ;
							}
							if ($orderTotal->isSetAmount())
							{
								$record['Amount'] = $orderTotal->getAmount() ;
							}
						}
						if ($member->isSetNumberOfItemsShipped())
						{
							$record['ShippedNum'] = $member->getNumberOfItemsShipped() ;
						}
						if ($member->isSetNumberOfItemsUnshipped())
						{
							$record['UnshippedNum'] = $member->getNumberOfItemsUnshipped() ;
						}
				
						$NOrderService  = ClassRegistry::init("NOrderService") ;
				
						$NOrderService->saveOrder($record,$accountId) ;
				
						try{
							//$this->getOrderItems($record['OrderId']) ;
						}catch(MarketplaceWebServiceOrders_Exception $e){
							//
						}
				}
			}
			
			if( !empty($nextToken) ){
				$this->getOrdersByNextToken($nextToken,$accountId) ;
			}
		}
		
	}
	
}
?>