<?php
class Package extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function doSaveGroup($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_package_group_insert",$params) ;
		}else{
			$this->exeSql("sql_package_group_update",$params) ;
		}
	}
	
	public function delPostageVendor($params){
		
	}
	
	public function doSaveGroupItem($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_package_group_item_insert",$params) ;
		}else{
			$this->exeSql("sql_package_group_item_update",$params) ;
		}
	}
}