<?php
class AmazonFeedProcess extends AppModel {
	var $useTable = "sc_election_rule" ;
	
	function process( $reportType,$productItem ,$HeadArray,$accountId ){
		if( !empty( $reportType ) && $reportType == '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_' ){
			$this->_process_GET_FLAT_FILE_OPEN_LISTINGS_DATA_($reportType, $productItem, $HeadArray, $accountId) ;
		}else if( !empty( $reportType ) && $reportType == '_GET_MERCHANT_LISTINGS_DATA_' ){
			$this->_process_GET_MERCHANT_LISTINGS_DATA_($reportType, $productItem, $HeadArray, $accountId) ;
		}else if( !empty( $reportType ) && $reportType == '_GET_AFN_INVENTORY_DATA_' ){
			$this->_process_GET_AFN_INVENTORY_DATA_($reportType, $productItem, $HeadArray, $accountId) ;
		}else if( !empty( $reportType ) && $reportType == '_GET_FLAT_FILE_ORDERS_DATA_' ){
			$this->_process_GET_FLAT_FILE_ORDERS_DATA_($reportType, $productItem, $HeadArray, $accountId) ;
		}
	}
	
	/**
	 * 保存订单项  
	 * @param unknown_type $reportType
	 * @param unknown_type $productItem
	 * @param unknown_type $HeadArray
	 * @param unknown_type $accountId
	 */
	function _process_GET_FLAT_FILE_ORDERS_DATA_( $reportType,$productItem ,$HeadArray,$accountId ){
		
		debug( $productItem ) ;
		
		$NOrderService = ClassRegistry::init("NOrderService") ;
		$log  = ClassRegistry::init("Log") ;
	
		/*
		$asin 		= $productItem['asin'] ;
		$sku  		= $productItem['sku'] ;
		$quantity  	= $productItem['quantity'] ;
		$price  	= $productItem['price'] ;
	
		if(empty($asin)){
			$log->savelog("account_asyn_$accountId" ,json_encode($productItem) ) ;
		}*/
	
		$NOrderService->saveOrderItem( $productItem , true ) ;
	}
	
	function _process_GET_FLAT_FILE_OPEN_LISTINGS_DATA_( $reportType,$productItem ,$HeadArray,$accountId ){
		$amazonAccount  = ClassRegistry::init("Amazonaccount") ;
		$log  = ClassRegistry::init("Log") ;
		
		$asin 		= $productItem['asin'] ;
		$sku  		= $productItem['sku'] ;
		$quantity  	= $productItem['quantity'] ;
		$price  	= $productItem['price'] ;
		
		if(empty($asin)){
			$log->savelog("account_asyn_$accountId" ,json_encode($productItem) ) ;
		}
		
		$amazonAccount->saveAccountProductByAsyn(array(
				'ASIN'=>$asin,
				'SKU'=>$sku,
				'accountId'=>$accountId,
				'price'=>$price,
				'quantity'=>$quantity
		),1) ;
	}
	
	function _process_GET_MERCHANT_LISTINGS_DATA_( $reportType,$productItem ,$HeadArray,$accountId ){
		$amazonAccount  = ClassRegistry::init("Amazonaccount") ;
		$log  = ClassRegistry::init("Log") ;
		
		$asin 		= $productItem['product-id'] ;
		$sku  		= $productItem['seller-sku'] ;
		$listingId  = $productItem['listing-id'] ;
		$quantity  	= $productItem['quantity'] ;
		$price  	= $productItem['price'] ;
		$fulfillment  = $productItem['fulfillment-channel'] ;
		$pendingQuantity  = $productItem['pending-quantity'] ;
		$itemCondition = $productItem['item-condition'] ;
		
		if( trim($fulfillment) == 'DEFAULT' )
			$fulfillment = "Merchant" ;
		
		if(empty($asin)){
			$log->savelog("account_asyn_$accountId" ,json_encode($productItem) ) ;
		}
		
		$amazonAccount->saveAccountProductByAsyn(array(
				'ASIN'=>$asin,
				'SKU'=>$sku,
				'accountId'=>$accountId,
				'listingId'=>$listingId,
				'price'=>$price,
				'fulfillment'=>$fulfillment,
				'quantity'=>$quantity,
				'pendingQuantity'=>$pendingQuantity,
				'itemCondition'=>$itemCondition
		),2) ;
	}
	
	function _process_GET_AFN_INVENTORY_DATA_( $reportType,$productItem ,$HeadArray,$accountId ){
		$amazonAccount  = ClassRegistry::init("Amazonaccount") ;
		$log  = ClassRegistry::init("Log") ;
		
		$asin 		= $productItem['asin'] ;
		$sku  		= $productItem['seller-sku'] ;
		$quantity  	= $productItem['Quantity Available'] ;
		$sellable 	= $productItem['Warehouse-Condition-code'] ;
		
		$fulfillment = "AMAZON_NA" ;
		
		if( 'SELLABLE' == $sellable ){
			if(empty($asin)){
				$log->savelog("account_asyn_$accountId" ,json_encode($productItem) ) ;
			}
		
			$amazonAccount->saveAccountProductByAsyn(array(
					'ASIN'=>$asin,
					'SKU'=>$sku,
					'accountId'=>$accountId,
					'FBA_SELLABLE'=>$sellable,
					'fulfillment'=>$fulfillment,
					'quantity'=>$quantity
			),3) ;
		}
	}

}