<?php
class SaleProduct extends AppModel {
	var $useTable = "sc_product_flow" ;
	
	function giveup($sku,$type){
		if( $type == 1 ){//作废
			$sql = "update sc_real_product set status='0' where real_sku = '$sku'" ;
			$this->query($sql) ;
		}else{
			$sql = "update sc_real_product set status='1' where real_sku = '$sku'" ;
			$this->query($sql) ;
		}
		
	}
	
	function getMaxSku(){
		$sql = $this->getDbSql("sql_saleproduct_getMaxSKU") ;
		$sql = $this->getSql($sql,array()) ;
		$count = $this->query($sql) ;
		$C = $count[0][0]['C'] ;
		return $C ;
	}
	
	function saveProduct($data , $user){
		$data['loginId'] = $user['LOGIN_ID'] ;
		
		$sku = $data['sku'] ;
		
		$item = $this->getSaleProduct($sku) ;
		if( count($item) > 0){
			$realId = $item[0]['sc_real_product']['ID'] ;
			//update
			$sql = $this->getDbSql("sql_saleproduct_update") ;
			$sql = $this->getSql($sql,$data) ;
			$this->query($sql) ;
			
			//更新引用表
			$sql = "update sc_real_product_rel set real_sku = '$sku' where real_id = '$realId'" ;
			$this->query($sql) ;
			
		}else{
			$sql = $this->getDbSql("sql_saleproduct_insert") ;
			$sql = $this->getSql($sql,$data) ;
			$this->query($sql) ;
		}
	}
	
	function getSaleProduct($sku){
		$sql = "select * from sc_real_product where real_sku = '$sku'" ;
		return $this->query($sql) ;
	}
	
	function saveSkuToRealProduct($params,$user){
		$accountId = $params['accountId'] ;
		$skus = $params['skus'] ;
		$realSku = $params['realSku'] ;
		
		$product = $this->getSaleProduct($realSku) ;
		$realId = $product[0]['sc_real_product']['ID'] ;
		
		$skus = explode(",",$skus) ;
		foreach( $skus as $sku ){
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
		$realSku = $params['realSku'] ;
		
		$product = $this->getSaleProduct($realSku) ;
		$realId = $product[0]['sc_real_product']['ID'] ;
		
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
}