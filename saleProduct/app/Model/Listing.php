<?php
class Listing extends AppModel {
	var $useTable = "sc_product_flow" ;
	
	public function saveListing($params){
		$sql = " UPDATE sc_amazon_account_product 
						SET
						SUPPLY_CYCLE = '{@#SUPPLY_CYCLE:0#}' , 
						REQ_ADJUST = '{@#REQ_ADJUST:0#}'
						
						WHERE
						ID = '{@#ID#}'" ;
		$this->exeSql($sql, $params) ;
	}
						
}