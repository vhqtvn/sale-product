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
	
	public function relationProduct( $params ){
		//删除该账号Listing关联货品
		$sql = "delete from sc_real_product_rel where ACCOUNT_ID='{@#ACCOUNT_ID#}' and SKU='{@#SKU#}'" ;
		$this->exeSql($sql, $params) ;
		
		$sql = " INSERT INTO sc_real_product_rel 
							(REAL_SKU, 
							SKU, 
							ACCOUNT_ID, 
							REAL_ID
							)
							VALUES
							('{@#REAL_SKU#}', 
							'{@#SKU#}', 
							'{@#ACCOUNT_ID#}', 
							'{@#REAL_ID#}'
							)" ;
		$this->exeSql($sql, $params) ;
	}
						
}