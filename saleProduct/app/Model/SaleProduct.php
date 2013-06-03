<?php
class SaleProduct extends AppModel {
	var $useTable = "sc_product_flow" ;
	
	function saveLimitPrice($params){
		$this->exeSql("sql_saleproduct_saveLimitPrice", $params) ;
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
	
	function saveProduct($data , $user){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		$data['loginId'] = $user['LOGIN_ID'] ;
		
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

		}else{
			$sql = $this->getDbSql("sql_saleproduct_insert") ;
			$sql = $this->getSql($sql,$data) ;
			$this->query($sql) ;
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
		foreach( $skus as $sku ){
			$sku = trim($sku) ;
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
		}
	}
	
	function saveSelectedProducts($params,$user){
		$items = $params['items'] ;
		$unitems = $params['unitems'] ;
		$realId = $params['id'] ;
		
		$product = $this->getSaleProductById($realId) ;
		$realSku = $product[0]['sc_real_product']['REAL_SKU'] ;
		
		$items = explode(",",$items) ;
		foreach( $items as $item ){
			$item = explode("|",$item) ;
			$accountId = $item[0] ;
			$sku = $item[1] ;
			
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
			
		}
		
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
	
	
}