<?php
class SaleProduct extends AppModel {
	var $useTable = "sc_product_flow" ;
	
	function saveProduct($data , $user){
		$data['loginId'] = $user['LOGIN_ID'] ;
		$sql = $this->getDbSql("sql_saleproduct_insert") ;
		$sql = $this->getSql($sql,$data) ;
		$this->query($sql) ;
	}
}