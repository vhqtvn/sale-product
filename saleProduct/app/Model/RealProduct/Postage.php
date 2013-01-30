<?php
class Postage extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function doSaveVendor($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_postage_vendor_insert",$params) ;
		}else{
			$this->exeSql("sql_postage_vendor_update",$params) ;
		}
	}
	
	public function delPostageVendor($params){
		
	}
	
	public function doSaveServices($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_postage_services_insert",$params) ;
		}else{
			$this->exeSql("sql_postage_services_update",$params) ;
		}
	}
}