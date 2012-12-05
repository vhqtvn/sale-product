<?php
class Warehouse extends AppModel {
	var $useTable = 'sc_user';
	
	function findById($id){
		$sql = "select * from sc_warehouse where id = '$id'" ;
		return $this->query($sql) ;
	}
	
	function saveManage($params,$user){
		$val = $params['value'] ;
		$warehouseId = $params['warehouseId'] ;
		$array = explode(",",$val) ;
		foreach( $array as $id ){
			$sql = "insert into sc_warehouse_manage(warehouse_id,user_id) values('$warehouseId','$id')" ;
			$this->query($sql) ; 
		}
	}
	
	function findUnitById($id){
		$sql = "select * from sc_warehouse_unit where id = '$id'" ;
		return $this->query($sql) ;
	}
	
}