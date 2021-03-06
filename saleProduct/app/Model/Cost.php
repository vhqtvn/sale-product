<?php
class Cost extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	/**
	 * 初始化开发产品成本信息
	 * 
	 */
	public function initDevCost($asin,$loginId){
		$sql = "SELECT sc_product_cost.* FROM sc_product_cost where asin = '$asin'";
		$productCost = $this->getObject($sql,array()) ;
		
		//判断是否有初始化成本数据，如果没有则初始化成本数据
		if( empty( $productCost ) ){
			$costId = $this->create_guid() ;
			$this->exeSql("sql_cost_insert_new", array("ASIN"=>$asin,"ID"=>$costId,"loginId"=>$loginId)) ;
		}else{
			$costId = $productCost['ID'] ;
		}
		
		//判断是否有成本明细
		$sql = "SELECT * FROM sc_product_cost_details where asin = '$asin' and type='FBA'";
		$productCostDetails = $this->getObject($sql,array()) ;
		if( empty( $productCostDetails ) ){
			$costId_ = $this->create_guid() ;
			$this->exeSql("sql_cost_details_insert_new", array("ASIN"=>$asin,'COST_ID'=>$costId,'TYPE'=>'FBA',"ID"=>$costId_,"loginId"=>$loginId)) ;
		}
		
		$sql = "SELECT * FROM sc_product_cost_details where asin = '$asin' and type='FBM'";
		$productCostDetails = $this->getObject($sql,array()) ;
		if( empty( $productCostDetails ) ){
			$costId_ = $this->create_guid() ;
			$this->exeSql("sql_cost_details_insert_new", array("ASIN"=>$asin,'COST_ID'=>$costId,'TYPE'=>'FBM',"ID"=>$costId_,"loginId"=>$loginId)) ;
		}
		
		$sql = "SELECT * FROM sc_product_cost_details where asin = '$asin' and type='FBC'";
		$productCostDetails = $this->getObject($sql,array()) ;
		if( empty( $productCostDetails ) ){
			$costId_ = $this->create_guid() ;
			$this->exeSql("sql_cost_details_insert_new", array("ASIN"=>$asin,'COST_ID'=>$costId,'TYPE'=>'FBC',"ID"=>$costId_,"loginId"=>$loginId)) ;
		}
		
		//更新转仓库成本
		$tranferUnit = 5.41 ;
		$sql = "select * from sc_purchase_supplier_inquiry where asin='$asin' and weight >0" ;
		$inquiry = $this->getObject($sql, array()) ;
		//debug($inquiry) ;
		if( !empty($inquiry)  ){
			$weight = $inquiry['WEIGHT'] ;
			$transferCost =  $tranferUnit*$weight  ;
			if(!empty($transferCost)){
				$sql = "update sc_product_cost_details set _TRANSFER_COST ='$transferCost'  where  asin ='$asin'";
				$this->exeSql($sql, array()) ;
			}
		}
	}

	
	public function saveDevCostByFee( $params ){
		//debug($params) ;
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
				try{
					$sql = "INSERT INTO  sc_product_cost_asin
									(ASIN,
									VARIABLE_CLOSING_FEE,
									COMMISSION_LOWLIMIT,
									COMMISSION_RATIO,
									FBA_COST
									)
									VALUES
									('{@#asin#}',
									'{@#variableClosingFee#}',
									'{@#commissionLowlimit#}',
									'{@#commissionRatio#}',
									'{@#fbaCost#}'
									)" ;
					$this->exeSql($sql,$params) ;
				}catch(Exception $e){
					if( $params['variableClosingFee'] == 0 ){
						$params['variableClosingFee'] = "" ;
					}
					if( $params['commissionLowlimit'] == 0 ){
						$params['commissionLowlimit'] = "" ;
					}
					if( $params['commissionRatio'] == 0 ){
						$params['commissionRatio'] = "" ;
					}
					if( $params['fbaCost'] == 0 ){
						$params['fbaCost'] = "" ;
					}
					
					$sql = "UPDATE  sc_product_cost_asin 
									SET
									ASIN = '{@#asin#}' 
									{@ ,VARIABLE_CLOSING_FEE = '#variableClosingFee#'}
									{@ ,COMMISSION_LOWLIMIT = '#commissionLowlimit#' } 
									{@ ,COMMISSION_RATIO = '#commissionRatio#' }
									{@ ,FBA_COST = '#fbaCost#'}
									WHERE
									ASIN = '{@#asin#}'" ;
					$this->exeSql($sql,$params) ;
		 }
	}
	
	public function saveCostAsin($data){
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
			$sql = "select * from sc_product_cost_details where asin=  '{@#ASIN#}' and type='{@#TYPE#}'" ;
			$costDetail = $this->getObject($sql, $listingCost) ;
			if(empty($costDetail)){
				$costDetailId = $this->create_guid() ;
				$listingCost['ID'] = $costDetailId ;
				$listingCost['COST_ID'] = $costId ;
				$listingCost['loginId'] =$data['loginId'] ;
				$listingCost['ASIN'] =$productCost['ASIN'] ;
				//插入
				$this->exeSql("sql_cost_details_insert_new", $listingCost) ;
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
			
			//更新售价
			/*
			if( $listingCost['FULFILLMENT_CHANNEL'] == 'Merchant' ){ //FBM
				$sql = "update sc_amazon_account_product set lowest_price = '{@#TOTAL_PRICE#}'  where ACCOUNT_ID = '{@#ACCOUNT_ID#}' and SKU =  '{@#LISTING_SKU#}'" ;
				$this->exeSql( $sql , $listingCost ) ;
			}else{ //FBA
				$sql = "update sc_amazon_account_product set lowest_fba_price = '{@#TOTAL_PRICE#}'  where ACCOUNT_ID = '{@#ACCOUNT_ID#}' and SKU =  '{@#LISTING_SKU#}'" ;
				$this->exeSql( $sql , $listingCost ) ;
			}*/
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
		$realId = $data['realId'] ;
		$realQuotePrice = $data['realQuotePrice'] ;//realQuotePrice
		$realShipFee = $data['realShipFee'] ;//realShipFee
		$qualifiedProductsNum = $data['qualifiedProductsNum'] ;
		if( !empty($qualifiedProductsNum) &&  $qualifiedProductsNum >0 ){
			//
		}else{
			return ;
		}
		$purchaseCost = $realQuotePrice ;

		if( empty($purchaseCost) || $purchaseCost<=0 ) return ;
		
		$purchaseShipCost = 0 ;
		if( empty($realShipFee)  &&$realShipFee>0 ){
			$purchaseShipCost = round( ($realShipFee/$qualifiedProductsNum),2 );
		}
		
		
		//如果未创建成本数据，则新创建FBA和FBM成本
		$Sql = "select * from sc_product_cost where REAL_ID='{@#realId#}'" ;
		
		$insertSql = "" ;
		$fbaCost = $this->getObject($Sql, $data) ;
		if( empty($fbaCost) ){
			$params  = array() ;
			$params['ID'] = $this->create_guid() ;
			$params['REAL_ID']  = $realId ;
			$params['PURCHASE_COST'] = $purchaseCost ;
			$params['LOGISTICS_COST'] = $purchaseShipCost ;
			$this->exeSql("sql_cost_product_insert_simple",$params) ;
		}else{
			$params  = array() ;
			$params['ID'] = $fbaCost['ID'] ;
			$params['REAL_ID']  = $realId ;
			$params['PURCHASE_COST'] = $purchaseCost ;
			$params['LOGISTICS_COST'] = $purchaseShipCost ;
			//$this->exeSql("sql_cost_product_update",$params) ;
			$this->exeSql("sql_cost_update_new", $params) ;
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
			/*$sql ="SELECT sptp.* FROM sc_purchase_task_products sptp
						        ,sc_purchase_plan_details sppd
						        WHERE sptp.PRODUCT_ID = sppd.ID
						        AND ( sppd.REAL_ID ='{@#realId#}'  OR sppd.SKU = '{@#sku#}' )
								AND sptp.WAREHOUSE_TIME is not null
								AND sptp.REAL_QUOTE_PRICE is not null
						     ORDER BY sptp.WAREHOUSE_TIME  desc
						     LIMIT 0,1" ;*/
			
			$sql = "select * from sc_purchase_product spp where spp.real_id = '{@#realId#}' and spp.REAL_QUOTE_PRICE is not null and spp.REAL_QUOTE_PRICE != ''
					    and spp.QUALIFIED_PRODUCTS_NUM >0
					    order by  spp.WAREHOUSE_TIME  desc
						limit 0,1 " ;
			
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