<?php
class Ram extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function doSaveOption($params){
		$option = $this->getObject("sql_ram_options_getByCode",$params) ;
		
		if( empty( $option ) ){
			$this->exeSql("sql_ram_option_insert",$params) ;
		}else{
			$this->exeSql("sql_ram_option_update",$params) ;
		}
	}
	
	public function doSaveEvent($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_ram_event_insert",$params) ;
		}else{
			$this->exeSql("sql_ram_event_update",$params) ;
		}
	}
	
	public function doSaveAndAuditEvent($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_ram_event_insert",$params) ;
		}else{
			$this->exeSql("sql_ram_event_update",$params) ;
			
			$this->exeSql("sql_ram_event_updateStatus",$params) ;
		}
	}
	
	
	
}