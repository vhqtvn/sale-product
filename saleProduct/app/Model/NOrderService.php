<?php
class NOrderService extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	function saveOrder($order){
		
		$orderId = $order['OrderId'] ;
		$record = $this->getObject("sql_sc_order_findById", array("orderId"=>$orderId)) ;
		
		$orderNumber = '' ;
		if(empty($record)){
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			$db->_queryCache = array() ;
			
			//保存
			$orderNumber = $this->getMaxValue("order",$orderId,'1000000000') ;
			$order['OrderNumber'] = $orderNumber ;
			$this->exeSql("sql_sc_order_insert", $order) ;
			
			try{
				$this->exeSql("sql_sc_order_user_insert", $record) ;
			}catch(Exception $e){
			}
		}else{
			//更新
			$orderNumber = $record['ORDER_NUMBER'] ;
			$this->exeSql("sql_sc_order_update", $order)  ;
		}
	}
	
	function saveOrderItem($orderItem){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		$orderId = $orderItem['OrderId'] ;
		$orderItemId = $orderItem['OrderItemId'] ;
		$record = $this->getObject("sql_sc_order_item_findById", array("orderId"=>$orderId,'orderItemId'=>$orderItemId)) ;
	
		if(empty($record)){//item未添加
			$this->exeSql("sql_sc_order_item_insert", $orderItem) ;
		}else{
			$this->exeSql("sql_sc_order_item_update", $orderItem)  ;
		}
	}
}