<?php
class OrderService extends AppModel {
	var $useTable = "sc_account_product_warning" ;
	
	/**
	 * 保存上传记录
	 */
	function saveUpload($id, $fileName,$accountId,$user){
		$sql = "
			INSERT INTO  sc_amazon_order_upload 
				(ID, 
				NAME, 
				CREATE_TIME, 
				CREATOR, 
				ACCOUNT_ID
				)
				VALUES
				('$id', 
				'$fileName', 
				NOW(), 
				'".$user['LOGIN_ID']."', 
				'$accountId'
				)" ;
		return $this->query($sql) ;
	}
	
	/**
	 * 保存订单明细
	 */
	function saveOrderItem($accountId , $items ,$id){
		$items['account_id'] = $accountId ;
		$items['upload_id'] = $id ;
		try{
			$sql = $this->getDbSql("sql_order_insert") ;
			$sql = $this->getSql($sql,$items) ;
			$this->query($sql) ;
		}catch(Exception $e){}
	}
	

	function saveAudit($params,$user){
		$status = $params['status'] ;
		$orders = $params['orders'] ;
		$loginId = $user['LOGIN_ID'] ;
		$memo = $params['memo'] ;
		$orders = explode(",",$orders) ;
		
		foreach( $orders as $order ){
			$item = explode("|",$order) ;
			$orderId = $item[0] ;
			$orderItemId = $item[1] ;
			
			$sql = $this->getDbSql("sql_order_status_delete") ;
			$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'AUDIT_STATUS'=>$status,"AUDIT_MEMO"=>$memo)) ;
			$this->query($sql) ;
			
			$sql = $this->getDbSql("sql_order_status_insert") ;
			$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'AUDIT_STATUS'=>$status,"AUDIT_MEMO"=>$memo)) ;
			$this->query($sql) ;
			
			$sql = $this->getDbSql("sql_order_track_insert") ;
			$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'STATUS'=>$status,"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
			$this->query($sql) ;
			
		}
	}
}