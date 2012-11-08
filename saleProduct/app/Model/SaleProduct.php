<?php
class SaleProduct extends AppModel {
	var $useTable = "sc_product_flow" ;
	
	function saveProduct($data , $user){
		$data['loginId'] = $user['LOGIN_ID'] ;
		$sql = $this->getDbSql("sql_saleproduct_insert") ;
		$sql = $this->getSql($sql,$data) ;
		$this->query($sql) ;
	}
	
	function getSaleProduct($sku){
		$sql = "select * from sc_real_product where real_sku = '$sku'" ;
		return $this->query($sql) ;
	}
	
	function saveSelectedProducts($params,$user){
		$items = $params['items'] ;
		$unitems = $params['unitems'] ;
		$realSku = $params['realSku'] ;
		
		$items = explode(",",$items) ;
		foreach( $items as $item ){
			$item = explode("|",$item) ;
			$accountId = $item[0] ;
			$sku = $item[1] ;
			
			$sql = " INSERT INTO sc_real_product_rel 
				(REAL_SKU, 
				SKU, 
				ACCOUNT_ID
				)
				VALUES
				('$realSku', 
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
}