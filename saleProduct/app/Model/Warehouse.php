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
	
	/**
	 * 执行订单出库操作
	 */
	function doOrderOut($orderId){
		//sql_order_storage_getByOrderId
		//查询订单库存
		$items = $this->exeSql("sql_order_storage_getByOrderId",array('orderId'=>$orderId)) ;
		foreach( $items as $item ){
			$item = $this->formatObject($item) ;
			$realId = $item['REAL_ID'] ;
			$quantity = $item['QUANTITY'] ;
			//更新仓库库存减少
			$this->exeSql("sql_saleproduct_quantity_out",array('realProductId'=>$realId,"quantity"=>$quantity)) ;
		}
		//sql_order_storage_shipped
		//更新为已经出库
		$this->exeSql("sql_order_storage_shipped",array('orderId'=>$orderId)) ;
	}
}