<?php
class CostNew extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function readyListingCost($params){
		$listings = $params['listings'] ;
		$listings = json_decode($listings) ;
		
		$costTag = $this->getAmazonConfig("COST_TAG",0) ;
		$costLabor = $this->getAmazonConfig("COST_LABOR",0) ;
		$costTaxRate = $this->getAmazonConfig("COST_TAX_RATE",0.0) ;
		
		$costRecords = array() ;
		
		foreach( $listings as $listing ){
			$item = get_object_vars($listing) ;
			$sql = "select * from sc_real_product_rel where account_id = '{@#accountId#}' and sku='{@#listingSku#}'" ;
			$real= $this->getObject($sql, $item) ;
			$realId = $real['REAL_ID'] ;
			$productCost = $this->getLatestPurchase(array("realId"=>$realId)) ;//产品采购成本
			$costId = $productCost['ID'] ;

			$sql = "select * from sc_product_cost_details where ACCOUNT_ID = '{@#accountId#}' and LISTING_SKU =  '{@#listingSku#}'" ;
			$costDetail = $this->getObject($sql, $item) ;
			if(empty($costDetail)){
				$costDetailId = $this->create_guid() ;
				$item['ID'] = $costDetailId ;
				$item['COST_ID'] = $costId ;
				$item['loginId'] =$params['loginId'] ;
				$item['ACCOUNT_ID'] =$item['accountId'] ;
				$item['LISTING_SKU'] =$item['listingSku'] ;
				//插入
				$this->exeSql("sql_cost_details_insert_new", $item) ;
				$costDetail = $this->getObject($sql, $item) ;
			}else{
				//修改
				$costDetailId = $costDetail['ID'] ;
			}
			
			$listingCost 	= $this->getObject("sql_cost_new_ListingCostEvlate", $item) ;
			
			//查找最近销量
			$salesNum = $this->exeSql("sql_cost_new_getListingSalesNum", $item) ;
			
			$costRecords[] = array(
					"productCost"=>$productCost,
					"costTag"=>$costTag,
					"costLabor"=>$costLabor,
					"costTaxRate"=>$costTaxRate,
					"listingCost"=>$listingCost,
					"salesNum"=>$salesNum
			) ;
		}
		return $costRecords ;
	}
	
	/**
	 * 获取最近一次采购成本
	 */
	public function getLatestPurchase($params){
		$realId = $params['realId']  ;
		
		$sql_= "SELECT sc_product_cost.* FROM sc_product_cost where real_id = '$realId'";
		$productCost = $this->getObject($sql_,array()) ;
		
		$costId = null ;
		//
		if( empty( $productCost ) ){ //save costd
				$costId = $this->create_guid() ;
				$params['ID'] = $costId ;
				$params['REAL_ID'] = $realId ;
				$this->exeSql("sql_cost_insert_new", $params) ;
		}
		//获取最近一次采购记录
			$sql = "select * from sc_purchase_product spp where spp.real_id = '{@#realId#}' and spp.REAL_QUOTE_PRICE > 0
					    and spp.QUALIFIED_PRODUCTS_NUM >0
					    order by  spp.WAREHOUSE_TIME  desc
						limit 0,1 " ;
				
			$item = $this->getObject($sql, $params ) ;
			$providor = null ;
			if( !empty($item) ){
				$providor = $item['REAL_PROVIDOR'] ;
				$realQuotePrice =   $item['REAL_QUOTE_PRICE']  ;
				$qualfiedNum = $item['QUALIFIED_PRODUCTS_NUM']  ;
				$realShipFee = round( $item['REAL_SHIP_FEE']/$qualfiedNum , 2 ) ;
				
				$sql = "update sc_product_cost set PURCHASE_COST = '{@#purchaseCost:0#}',LOGISTICS_COST='{@#realShipFee#}' where real_id = '{@#realId#}'" ;
				$this->exeSql($sql,array("realId"=>$realId,"purchaseCost"=>$realQuotePrice,"realShipFee"=>$realShipFee)) ;
			}
			//产品成本
			$productCost = $this->getObject($sql_,array()) ;
			$productCost['providorId'] = $providor ;
			return $productCost ;
	}
	
	/**
	 * 货品成本
	 * @param unknown_type $params
	 * @return multitype:Ambigous <NULL, unknown> unknown NULL mixed Ambigous <NULL, multitype:unknown > multitype:multitype:unknown
	 */
	public function readyCost($params){
		$realId = $params['realId'] ;
		$error = "" ;
		$sql_= "SELECT sc_product_cost.* FROM sc_product_cost where real_id = '$realId'";
		$productCost = $this->getObject($sql_,array()) ;
		
		$costId = null ;
		//
		if( empty( $productCost ) ){ //save costd
				$costId = $this->create_guid() ;
				$params['ID'] = $costId ;
				$params['REAL_ID'] = $realId ;
				//插入
				/*'{@#ID#}',
				'{@#REAL_ID#}',
				'{@#ASIN#}',
				'{@#loginId#}',
				NOW()*/
				$this->exeSql("sql_cost_insert_new", $params) ;
		}
		//获取最近一次采购记录
		//if( empty($productCost['PURCHASE_COAT']) ){
			//insert purchaseCost and logic
			$sql = "select * from sc_purchase_product spp where spp.real_id = '{@#realId#}' and spp.REAL_QUOTE_PRICE > 0
					    and spp.QUALIFIED_PRODUCTS_NUM >0
					    order by  spp.WAREHOUSE_TIME  desc
						limit 0,1 " ;
				
			$item = $this->getObject($sql, $params ) ;
			$providor = null ;
			if( !empty($item) ){
				$providor = $item['REAL_PROVIDOR'] ;
				$realQuotePrice =   $item['REAL_QUOTE_PRICE']  ;
				$qualfiedNum = $item['QUALIFIED_PRODUCTS_NUM']  ;
				$realShipFee = round( $item['REAL_SHIP_FEE']/$qualfiedNum , 2 ) ;
				
				$sql = "update sc_product_cost set PURCHASE_COST = '{@#purchaseCost:0#}',LOGISTICS_COST='{@#realShipFee#}' where real_id = '{@#realId#}'" ;
				$this->exeSql($sql,array("realId"=>$realId,"purchaseCost"=>$realQuotePrice,"realShipFee"=>$realShipFee)) ;
			}
			//产品成本
			$productCost = $this->getObject($sql_,array()) ;
			if( !empty( $providor ) ){//最近采购供应商
				$sql = "select * from sc_supplier where id='{@#supplierId#}'" ;
				$providor = $this->getObject($sql,array("supplierId"=>$providor)) ;
			}
		//}
		
		$accoutListings 	= $this->exeSqlWithFormat("sql_cost_new_getAccountListing", $params) ;
		foreach( $accoutListings as $listing  ){
			$sql = "select * from sc_product_cost_details where ACCOUNT_ID = '{@#ACCOUNT_ID#}' and LISTING_SKU =  '{@#LISTING_SKU#}'" ;
			$costDetail = $this->getObject($sql, $listing) ;
			if(empty($costDetail)){
				$costDetailId = $this->create_guid() ;
				$listing['ID'] = $costDetailId ;
				$listing['COST_ID'] = $costId ;
				$listing['loginId'] =$params['loginId'] ;
				//插入
				$this->exeSql("sql_cost_details_insert_new", $listing) ;
				$costDetail = $this->getObject($sql, $listing) ;
			}else{
				//修改
				$costDetailId = $costDetail['ID'] ;
			}
			$sql = "select * from sc_product_cost_asin where ASIN = '{@#ASIN#}' " ;
			$asinCost = $this->getObject($sql, $listing) ;
			if( !empty($asinCost) ){
				$costDetail['COMMISSION_RATIO'] = $asinCost['COMMISSION_RATIO'] ;
				$costDetail['_FBA_COST'] = $asinCost['FBA_COST'] ;
				$costDetail['COMMISSION_LOWLIMIT'] = $asinCost['COMMISSION_LOWLIMIT'] ;
				$costDetail['VARIABLE_CLOSING_FEE'] = $asinCost['VARIABLE_CLOSING_FEE'] ;
			}
		}
		
		$listingCosts 		= $this->exeSqlWithFormat("sql_cost_new_realProductCostEvlate", $params) ;
		$costTag = $this->getAmazonConfig("COST_TAG",0) ;
		$costLabor = $this->getAmazonConfig("COST_LABOR",0) ;
		//VARIABLE_CLOSING_FEE  COMMISSION_RATIO  FBA_COST  COMMISSION_LOWLIMIT
		$costTaxRate = $this->getAmazonConfig("COST_TAX_RATE",0.0) ;
		$productCost = $this->getObject($sql_,array()) ;
		
		//查找最近销量
		$salesNum = $this->exeSql("sql_cost_new_getLatestSalesNum", $params) ;
		
		return array(
				"productCost"=>$productCost,
				"providor"=>$providor,
				"costTag"=>$costTag,
				"costLabor"=>$costLabor,
				"costTaxRate"=>$costTaxRate,
				"listingCosts"=>$listingCosts,
				"salesNum"=>$salesNum,
				"lastPurchase"=>$item
			) ;
	}
	
	
}