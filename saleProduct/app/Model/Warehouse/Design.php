<?php
class Design extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function saveHw($params){
		
		$item = $this->getObject("sql_warehouse_itemGetById",$params) ;
		if(empty($item)){
			$this->exeSql("sql_warehouse_item_insert",$params) ;
		}else{
			$this->exeSql("sql_warehouse_item_update",$params) ;
		}
		//debug($params);
		//if( empty( $params['id'] ) ){
		
		//}else{
		//	$this->exeSql("sql_warehouse_in_update",$params) ;
		//}
	}
}