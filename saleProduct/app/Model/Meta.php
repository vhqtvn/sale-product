<?php
class Meta extends AppModel {
	var $useTable = "sc_seller" ;
	
	function listAddress(){
		$sql = "SELECT * from sc_meta_address ";
		$array = $this->exeSqlWithFormat($sql, array()) ;
		return $array ;
	}
	
	function saveAddress($params){
		if( empty($params['metaId']) ){
			$params['metaId'] = $this->create_guid() ;
			$this->exeSql("sql_meta_address_insert", $params) ;
		}else{
			$this->exeSql("sql_meta_address_update", $params) ;
		}
		return $params['metaId'] ;
	}
	
	function getAddressById($params){
		$metaId = $params['metaId'] ;
		$sql = "SELECT * from sc_meta_address ";
		$array = $this->getObject($sql, array("metaId"=>$metaId)) ;
		return $array ;
	}
	
}