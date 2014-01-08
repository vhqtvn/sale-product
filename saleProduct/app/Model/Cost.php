<?php
class Cost extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function saveCostFix($data){
		$productCost = get_object_vars( json_decode($data['productCost']) )   ;
		$_listingCosts  = json_decode( $data['listingCosts'])   ;
		$listingCosts = array() ;
		foreach($_listingCosts as $listingCost){
			$listingCosts[] = get_object_vars($listingCost);
		}
		//1、保存productCost
		$sql = "select * from sc_product_cost where real_id = '{@#REAL_ID#}'" ;
		$cost = $this->getObject($sql, $productCost) ;
		$costId = null ;
		if(empty($cost)){
			$costId = $this->create_guid() ;
			$productCost['ID'] = $costId ;
			$productCost['loginId'] =$data['loginId'] ; 
			//插入
			$this->exeSql("sql_cost_insert_new", $productCost) ;
		}else{
			//修改
			$costId = $cost['ID'] ;
		}
		$productCost['ID'] = $costId ;
		$productCost['loginId'] =$data['loginId'] ;
		//插入
		$this->exeSql("sql_cost_update_new", $productCost) ;
		
		//2、保存listingCost
		debug($listingCosts) ;
		foreach($listingCosts as $listingCost){
			//sql_cost_details_insert_new
			$sql = "select * from sc_product_cost_details where ACCOUNT_ID = '{@#ACCOUNT_ID#}' and LISTING_SKU =  '{@#LISTING_SKU#}'" ;
			$costDetail = $this->getObject($sql, $listingCost) ;
			if(empty($costDetail)){
				$costDetailId = $this->create_guid() ;
				$listingCost['ID'] = $costDetailId ;
				$listingCost['COST_ID'] = $costId ;
				$listingCost['loginId'] =$data['loginId'] ;
				//插入
				$this->exeSql("sql_cost_details_insert_new", $listingCost) ;
			}else{
				//修改
				$costDetailId = $costDetail['ID'] ;
			}
			
			$listingCost['ID'] = $costDetailId ;
			$listingCost['COST_ID'] = $costId ;
			$listingCost['loginId'] =$data['loginId'] ;
			//插入
			$this->exeSql("sql_cost_details_update_new", $listingCost) ;
		}
	}
	
	public function saveCost($data){
		$loginId = $data['loginId'] ;
		if( isset($data['ID']) && !empty($data["ID"]) ){
			$this->exeSql("sql_cost_product_update",$data) ;
		}else{
			$this->exeSql("sql_cost_product_insert",$data) ;
		}
	}
	
	/**
	 * 更新成本采购价
	 * @param unknown_type $data
	 */
	public function saveCostWithPurchase($data){
		$sku = $data['sku'] ;
		$realQuotePrice = $data['realQuotePrice'] ;
		$realShipFee = $data['realShipFee'] ;
		$qualifiedProductsNum = $data['qualifiedProductsNum'] ;
		
		$purchaseCost = $realQuotePrice+round( ($realShipFee/$qualifiedProductsNum),2 );
		
		//如果未创建成本数据，则新创建FBA和FBM成本
		$fbaSql = "select * from sc_product_cost where type='FBA' and sku='{@#sku#}'" ;
		$fbmSql= "select * from sc_product_cost where type='FBM' and sku='{@#sku#}'" ;
		$insertSql = "" ;
		$fbaCost = $this->getObject($fbaSql, $data) ;
		$fbmCost= $this->getObject($fbmSql, $data) ;
		if( empty($fbaCost) ){
			$params  = array() ;
			$params['SKU']  = $sku ;
			$params['TYPE'] = "FBA" ;
			$params['PURCHASE_COST'] = $purchaseCost ;
			$this->exeSql("sql_cost_product_insert_simple",$params) ;
		}else{
			$params  = array() ;
			$params['ID'] = $fbaCost['ID'] ;
			$params['SKU']  = $sku ;
			$params['TYPE'] = "FBA" ;
			$params['PURCHASE_COST'] = $purchaseCost ;
			$this->exeSql("sql_cost_product_update",$params) ;
		}
		
		if( empty($fbmCost) ){
			$params  = array() ;
			$params['SKU']  = $sku ;
			$params['TYPE'] = "FBM" ;
			$params['PURCHASE_COST'] = $purchaseCost ;
			$this->exeSql("sql_cost_product_insert_simple",$params) ;
		}else{
			$params  = array() ;
			$params['ID'] = $fbmCost['ID'] ;
			$params['SKU']  = $sku ;
			$params['TYPE'] = "FBM" ;
			$params['PURCHASE_COST'] = $purchaseCost ;
			$this->exeSql("sql_cost_product_update",$params) ;
		}
	}
	
	public function getProductCost($id){
		$sql = "SELECT sc_product_cost.* FROM sc_product_cost where id = '$id'";
		$array = $this->query($sql);
		return $array ;
	}
	
	public function getProductCostByAsinType($asin , $type){
		$sql = "" ;
		if( $type == 'FBA' ){
			$sql = "SELECT sc_product_cost.* FROM sc_product_cost where asin = '$asin' and type = 'FBA' ";
		}else if( $type == 'FBM' ){
			$sql = "SELECT sc_product_cost.* FROM sc_product_cost where asin = '$asin' and ( type = 'FBM' or type is null ) ";
		}
		
		$array = $this->query($sql);
		return $array ;
	}
}