<?php
class CostNew extends AppModel {
	var $useTable = "sc_product_cost" ;
	
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
				//插入
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
				$realQuotePrice = $this->findNum( $item['REAL_QUOTE_PRICE'] ) ;
				$qualfiedNum = $item['QUALIFIED_PRODUCTS_NUM']||0 ;
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
			if( empty($costDetail['COMMISSION_RATIO']) ||  $costDetail['COMMISSION_RATIO'] <=0 ){
				//FBA fee is incorrect
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
				"salesNum"=>$salesNum
			) ;
	}
	
	
}