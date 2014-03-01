<?php
class Cost extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	
	public function saveDevCostByFee( $params ){
		debug($params) ;
		//清楚数据库查询缓存
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
	
		$sql = "select * from sc_product_cost where asin = '{@#asin#}'" ;
		$cost = $this->getObject($sql, $params) ;
		$costId = null ;
		$params['ASIN'] = $params['asin'] ;
		$params['TYPE'] = $params['type'] ;
		//如果开发产品成本不存在，则添加
		$costId = $cost['ID'] ;
	
		$sql = "select * from sc_product_cost_details where asin = '{@#asin#}' and type =  '{@#type#}'" ;
		$costDetail = $this->getObject($sql, $params) ;
		$costDetailId = null ;
		if(empty($costDetail)){
			$costDetailId = $this->create_guid() ;
			$params['ID'] = $costDetailId ;
			$params['COST_ID'] = $costId ;
			//插入
			$this->exeSql("sql_cost_details_insert_new_forfee", $params) ;
		}else{
			//修改
			$costDetailId = $costDetail['ID'] ;
		}
			
		$params['ID'] = $costDetailId ;
		$params['COST_ID'] = $costId ;
	
		//debug($params) ;
		//插入
		$this->exeSql("sql_cost_details_update_new_forfee", $params) ;
	
	}
	
	
	//commissionRatio   fbaCost
	public function saveCostByFee( $params ){
		//清楚数据库查询缓存
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		$sql = "select * from sc_product_cost where real_id = '{@#realId#}'" ;
		$cost = $this->getObject($sql, $params) ;
		$costId = null ;
		$params['REAL_ID'] = $params['realId'] ;
		//如果货品成本不存在，则添加
		if(empty($cost)){
			$costId = $this->create_guid() ;
			$params['ID'] = $costId ;
			//插入
			$this->exeSql("sql_cost_insert_new", $params) ;
		}else{
			//修改
			$costId = $cost['ID'] ;
		}
		
		$sql = "select * from sc_product_cost_details where ACCOUNT_ID = '{@#accountId#}' and LISTING_SKU =  '{@#listingSku#}'" ;
		$costDetail = $this->getObject($sql, $params) ;
		$costDetailId = null ;
		if(empty($costDetail)){
			$costDetailId = $this->create_guid() ;
			$params['ID'] = $costDetailId ;
			$params['COST_ID'] = $costId ;
			//插入
			$this->exeSql("sql_cost_details_insert_new_forfee", $params) ;
		}else{
			//修改
			$costDetailId = $costDetail['ID'] ;
		}
			
		$params['ID'] = $costDetailId ;
		$params['COST_ID'] = $costId ;
		
		//debug($params) ;
		//插入
		$this->exeSql("sql_cost_details_update_new_forfee", $params) ;
		
	}
	
	public function saveCostAsin($data){
		//debug( $data ) ;
		//return ;
		$productCost = get_object_vars( json_decode($data['productCost']) )   ;
		$_listingCosts  = json_decode( $data['listingCosts'])   ;
		$listingCosts = array() ;
		foreach($_listingCosts as $listingCost){
			$listingCosts[] = get_object_vars($listingCost);
		}
		//1、保存productCost
		$sql = "select * from sc_product_cost where asin = '{@#ASIN#}'" ;
		$cost = $this->getObject($sql, $productCost) ;
		
		if(empty($cost)){
			$costId = $this->create_guid() ;
			$params['ID'] = $costId ;
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
		foreach($listingCosts as $listingCost){
			//sql_cost_details_insert_new
			$sql = "select * from sc_product_cost_details where id=  '{@#ID#}'" ;
			$costDetail = $this->getObject($sql, $listingCost) ;
			if(empty($costDetail)){
				$costDetailId = $this->create_guid() ;
				$listingCost['ID'] = $costDetailId ;
				$listingCost['COST_ID'] = $costId ;
				$listingCost['loginId'] =$data['loginId'] ;
				$listingCost['ASIN'] =$productCost['ASIN'] ;
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
	
	/**
	 * 格式化当前的货品价格
	 */
	public function formatCurrentPrice(){
		$sql= "select * from sc_real_product where status=1" ;
		$realproducts = $this->exeSqlWithFormat($sql, array()) ;
		foreach( $realproducts as $product ){
			//获取当前货品最近的采购记录
			$sql ="SELECT sptp.* FROM sc_purchase_task_products sptp
						        ,sc_purchase_plan_details sppd
						        WHERE sptp.PRODUCT_ID = sppd.ID
						        AND ( sppd.REAL_ID ='{@#realId#}'  OR sppd.SKU = '{@#sku#}' )
								AND sptp.WAREHOUSE_TIME is not null
								AND sptp.REAL_QUOTE_PRICE is not null
						     ORDER BY sptp.WAREHOUSE_TIME  desc
						     LIMIT 0,1" ;
			$item = $this->getObject($sql, array("realId"=>$product['ID'],"sku"=>$product['REAL_SKU'])) ;
			
			//debug($item) ;
			//REAL_QUOTE_PRICE
			//QUALIFIED_PRODUCTS_NUM
			//REAL_SHIP_FEE
			$realId = $product['ID'] ;
			$realQuotePrice = $this->findNum( $item['REAL_QUOTE_PRICE'] ) ;
			$qualfiedNum = $item['QUALIFIED_PRODUCTS_NUM'] ;
			
			if( empty($qualfiedNum) || $qualfiedNum == 0) continue ;
			
			$realShipFee = round( $item['REAL_SHIP_FEE']/$qualfiedNum , 2 ) ;
			
			$sql = "select * from sc_product_cost where real_id= '{@#realId#}'" ;
			$cost = $this->getObject($sql,array("realId"=>$realId)) ;
			if( empty($cost) ){
					$id = $this->create_guid() ;
					$sql = "
					INSERT INTO  sc_product_cost
					(ID,
					PURCHASE_COST,
					LOGISTICS_COST,
					REAL_ID,
					CREATE_TIME,
					LAST_UPDATE_TIME
					)
					VALUES
					('$id',
					'$realQuotePrice',
					'$realShipFee',
					'$realId',
					NOW(),
					NOW()
					);" ;
					$this->exeSql($sql,array()) ;
			}else{
				$sql = "update sc_product_cost set PURCHASE_COST = '{@#purchaseCost:0#}',LOGISTICS_COST='{@#realShipFee#}' where real_id = '{@#realId#}'" ;
				$this->exeSql($sql,array("realId"=>$realId,"purchaseCost"=>$realQuotePrice,"realShipFee"=>$realShipFee)) ;
			}
			
		}
	}
	
	function findNum($str=''){
		$str=trim($str);
		if(empty($str)){return '';}
		$result='';
		for($i=0;$i<strlen($str);$i++){
			if(is_numeric($str[$i])){
				$result.=$str[$i];
			}else if( $str[$i] == '.' ){
				$result.=$str[$i];
			}else{
				break ;
			}
		}
		return $result;
	}
}