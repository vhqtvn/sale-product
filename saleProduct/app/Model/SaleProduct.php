<?php
class SaleProduct extends AppModel {
	var $useTable = "sc_product_flow" ;
	
	function saveLimitPrices($params){
		$limitPrices = json_decode( $params['limitPrices'] ) ;
		foreach($limitPrices as $limit){
			$limit = get_object_vars($limit) ;
			$sql = "update sc_amazon_account_product set limit_price = '{@#limitPrice#}' 
								where account_id = '{@#accountId#}' and sku='{@#sku#}'" ;
			$this->exeSql($sql, $limit) ;
		}
	}
	
	function saveLimitPrice($params){
		$this->exeSql("sql_saleproduct_saveLimitPrice", $params) ;
	}
	
	function isAnalysis($params){
		$sql= "update  sc_amazon_account_product set IS_ANALYSIS = {@#isAnalysis#} where id='{@#id#}'";
		$this->exeSql($sql, $params) ;
		
		$sql = "select * from sc_amazon_account_product where id='{@#id#}'";
		$accountProduct = $this->getObject($sql, $params) ;
		
		if( $params['isAnalysis'] == 0  ){ //将改货品计算的需求删除
			$sql = "delete from sc_supplychain_requirement_item where status is null and account_id= '{@#ACCOUNT_ID#}' and LISTING_SKU = '{@#SKU#}'" ;
			$this->exeSql($sql, $accountProduct) ;
			$sql = "delete from sc_supplychain_requirement_log where  account_id= '{@#ACCOUNT_ID#}' and  SKU = '{@#SKU#}'" ;
			$this->exeSql($sql, $accountProduct) ;
			//判断对应采购货品是否存在
		}
	}
	
	/**
	 * 保存账户产品分类
	 * 
	 * @param unknown_type $params
	 */
	function saveAccountProductCateogory($params){
			//删除已经关联的关联
			$sql = "delete from sc_amazon_product_category_rel where account_id='{@#accountId#}' and sku='{@#sku#}'" ;
			$this->exeSql($sql, $params) ;
	
			$categoryId = $params['categoryId'] ;
			$categoryId = explode(",",$categoryId) ;
			foreach( $categoryId as $cId  ){
				if( empty($cId) ) continue ;
				$params['categoryId'] = $cId ;
				//添加
				$sql = "INSERT INTO  sc_amazon_product_category_rel
					(
					CATEGORY_ID,
					SKU,
					ACCOUNT_ID
					)
					VALUES
					(
					'{@#categoryId#}',
					'{@#sku#}',
					'{@#accountId#}'
					)" ;
				$this->exeSql($sql, $params) ;
			}
			
			
	
	}
	
	function getChartForSku($params){
		$sku = $params['sku'] ;
		$records = $this->exeSqlWithFormat("sql_chart_realSku_quantity", array('sku'=>$sku)) ;
		
		return $records ;
	}
	
	function onSale($params){
		$sql = "update sc_real_product set is_onsale ='{@#isOnsale#}' where id = '{@#id#}'" ;
		$this->exeSql($sql, $params) ;
	}
	
	function giveup($id,$type){
		if( $type == 1 ){//作废
			$sql = "update sc_real_product set status='0' where id = '$id'" ;
			$this->query($sql) ;
		}else if( $type == 2 ){
			$sql = "update sc_real_product set status='1' where id = '$id'" ;
			$this->query($sql) ;
		}else if($type == 3){ //删除该产品
			$sql = "delete from sc_real_product_composition where COMPOSITION_ID = '$id'" ;
			$this->query($sql) ;
			
			$sql = "delete from sc_real_product_composition where REF_ID = '$id'" ;
			$this->query($sql) ;
			
			$sql = "delete from sc_real_product_rel where real_id = '$id'" ;
			$this->query($sql) ;
			
			$sql = "delete from sc_real_product where id = '$id'" ;
			$this->query($sql) ;
		}
	}
	
	function getMaxSku(){
		$result = $this->getMaxValue("sku",null,20000000) ;
		return $result ;
	}
	
	function saveProductCategoryByObj($params){
		$productId = $params['productId'] ;
		$categoryId = $params['categoryId'] ;
		$this->saveProductCategory($productId, $categoryId) ;
	}
	
	function saveProductCategory($productId , $categoryId){
		if(empty($categoryId)) {
			$categoryId = "" ;
		}
		//删除现有分类
		$this->exeSql("delete from sc_real_product_category where product_id = '{@#productId#}'", array('productId'=>$productId)) ;
		//保存新分类
		$categoryIds = explode(",", $categoryId) ;
		foreach($categoryIds as $cId  ){
			$this->exeSql("			
			INSERT INTO  sc_real_product_category 
				(PRODUCT_ID, 
				CATEGORY_ID
				)
				VALUES
				( '{@#productId#}',  '{@#categoryId#}'
				) ", array('productId'=>$productId,"categoryId"=>$cId)) ;
		} ;
	}
	
	function saveProduct($data , $user=null){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		//$data['loginId'] = $user['LOGIN_ID'] ;
		
		$sku = $data['sku'] ;
		$id =  $data['id'] ;
		$item = $this->getSaleProductById($data['id']) ;
		if( count($item) > 0){
			//update
			$sql = $this->getDbSql("sql_saleproduct_update") ;
			$sql = $this->getSql($sql,$data) ;
			$this->query($sql) ;
			
			//更新引用表
			$sql = "update sc_real_product_rel set real_sku = '$sku' where real_id = '$id'" ;
			$this->query($sql) ;
			
			$sql = "update sc_real_product_composition set COMPOSITION_SKU = '$sku' where COMPOSITION_ID = '$id'" ;
			$this->query($sql) ;
			
			$sql = "update sc_real_product_composition set REF_SKU = '$sku' where REF_ID = '$id'" ;
			$this->query($sql) ;
		
			$this->saveProductCategory($id, $data['categoryId']) ;
			return "" ;
		}else{
			$guid = $this->create_guid() ;
			$data['guid'] = $guid ;
			$sql = $this->getDbSql("sql_saleproduct_insert") ;
			$sql = $this->getSql($sql,$data) ;
			$this->query($sql) ;
			//通过guid获取ID
			$realProduct = $this->getObject("select * from sc_real_product where guid = '{@#guid#}'", $data) ;
			$id = $realProduct["ID"] ;
			
			$this->saveProductCategory($id, $data['categoryId']) ;

			return $id ;
		}
	}
	
	function getSaleProductById($id){
		//$sql = "select * from sc_real_product where id = '$id'" ;
		return $this->exeSql("sql_saleproduct_getById",array('realProductId'=>$id) )  ;
	}
	
	function getSaleProduct($sku){
		$sql = "select * from sc_real_product where real_sku = '$sku'" ;
		return $this->query($sql) ;
	}
	
	function saveSkuToRealProduct($params,$user){
		$accountId = $params['accountId'] ;
		$skus = $params['skus'] ;
		$realId = $params['id'] ;
		
		$product = $this->getSaleProductById($realId) ;
		$realSku = $product[0]['sc_real_product']['REAL_SKU'] ;
		
		$skus = explode(",",$skus) ;
		
		$exists = array() ;
		foreach( $skus as $sku ){
			$sku = trim($sku) ;
			
			//判断SKU是否已经存在
			$rel = $this->getObject("select * from sc_real_product_rel 
					where sku='{@#sku#}' and account_id ='{@#accountId#}' ", array("sku"=>$sku,"accountId"=>$accountId)) ;
			
			if( empty($rel) ){
				$sql = " INSERT INTO sc_real_product_rel
				(REAL_ID,REAL_SKU,
				SKU,
				ACCOUNT_ID
				)
				VALUES
				('$realId','$realSku',
				'$sku',
				'$accountId'
				)" ;
				try{
				$this->query($sql) ;
				}catch(Exception $e){}
			}else{
				if($realSku != $rel['REAL_SKU'])$exists[] = $rel ;
			}
		}
		return $exists ;
	}
	
	function saveSelectedProducts($params,$user){
		$items = $params['items'] ;
		$unitems = $params['unitems'] ;
		$realId = $params['id'] ;
		
		$product = $this->getSaleProductById($realId) ;
		$realSku = $product[0]['sc_real_product']['REAL_SKU'] ;
		
		$items = explode(",",$items) ;
		$exists = array() ;
		foreach( $items as $item ){
			$item = explode("|",$item) ;
			$accountId = $item[0] ;
			$sku = $item[1] ;
			
			//判断SKU是否已经存在
			$rel = $this->getObject("select * from sc_real_product_rel
					where sku='{@#sku#}' and account_id ='{@#accountId#}' ", array("sku"=>$sku,"accountId"=>$accountId)) ;
				
			if( empty($rel) ){
				$sql = " INSERT INTO sc_real_product_rel 
					(REAL_ID,REAL_SKU, 
					SKU, 
					ACCOUNT_ID
					)
					VALUES
					('$realId',
					'$realSku', 
					'$sku', 
					'$accountId'
					)" ;
				try{
					$this->query($sql) ;
				}catch(Exception $e){}
			}else{
				if($realSku != $rel['REAL_SKU'])$exists[] = $rel ;
			}
		}
		if(!empty($unitems)){
			$unitems = explode(",",$unitems) ;
			foreach( $unitems as $item ){
				$item = explode("|",$item) ;
				$accountId = $item[0] ;
				$sku = $item[1] ;
				$sql = "DELETE FROM sc_real_product_rel
				WHERE
				REAL_SKU = '$realSku' AND SKU = '$sku' AND ACCOUNT_ID = '$accountId' " ;
				try{
				$this->query($sql) ;
				}catch(Exception $e){}
			}
		}
		
		return $exists;
		
	}
	
	public function deleteRelProduct($params ,$user){
		$accountId = $params['accountId'] ;
		$sku = $params['sku'] ;
		$realSku = $params['realSku'] ;
		$sql = "delete from sc_real_product_rel where account_id='$accountId' and sku='$sku' and real_sku='$realSku'" ;
		$this->query($sql) ;
	}
	
	/**
	 * [COMPOSITION_ID] => 2
    [COMPOSITION_SKU] => 1001
    [REF_ID] => 1
    [REF_SKU] => 10006
	 */
	public function deleteComposition($params ,$user){
		$accountId = $params['accountId'] ;
		$sku = $params['sku'] ;
		$realSku = $params['realSku'] ;
		$sql = "DELETE FROM  sc_real_product_composition 
				WHERE
					COMPOSITION_ID = '".$params['COMPOSITION_ID']."' AND COMPOSITION_SKU = '".$params['COMPOSITION_SKU']."'
					AND REF_ID = '".$params['REF_ID']."' AND REF_SKU = '".$params['REF_SKU']."'" ;
		$this->query($sql) ;
	}
	
	public function getListingCategory($params){
		$sql = "SELECT sapc.* FROM sc_amazon_product_category_rel sapcr , sc_amazon_product_category sapc
						WHERE sapcr.CATEGORY_ID = sapc.ID
						AND sapcr.SKU = '{@#sku#}'
						AND sapcr.ACCOUNT_ID = '{@#accountId#}'" ;
		return $this->exeSqlWithFormat($sql, $params) ;
	}
	
	/**
	 * 获取产品流转状态
	 */
	public function getProductStatusBy($query){
		$warehourceIn = $this->exeSqlWithFormat("sql_getProductWarehouseInStatus", $query) ;
		$purchase       = $this->exeSqlWithFormat("sql_getProductPurchaseStatus", $query) ;
		
		return array("in"=>$warehourceIn ,"purchase"=>$purchase ) ;
	}
}