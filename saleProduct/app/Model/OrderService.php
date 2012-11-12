<?php
class OrderService extends AppModel {
	var $useTable = "sc_account_product_warning" ;
	
	var $auditStatus = array('2'=>'风险客户',
			'3'=>'待退单',
			'4'=>'外购订单',
			'5'=>'合格订单',
			'6'=>'加急单',
			'7'=>'特殊单') ;
			
	var $pickStatus = array('9'=>'拣货中','10'=>'拣货完成') ;
	var $tnStatus   = array('1'=>'发货完成') ;
	var $redoStatus = array(
			'1'=>'退货',
			'2'=>'退款',
			'3'=>'重发货',
			'4'=>'FEEDBACK',
			'201'=>'退款审批',
			'301'=>'重发货审批',
			'202'=>'待退款',
			'302'=>'待重发货',
			'20001'=>'完成退款',
			'30001'=>'完成重发货'
		) ;
	
	/**
	 * 保存上传记录
	 */
	function saveUpload($id, $fileName,$accountId,$user,$startTime = null ,$endTime = null){
		$sql = "
			INSERT INTO  sc_amazon_order_upload 
				(ID, 
				NAME, 
				CREATE_TIME, 
				CREATOR, 
				ACCOUNT_ID,
				START_TIME, 
				END_TIME
				)
				VALUES
				('$id', 
				'$fileName', 
				NOW(), 
				'".$user['LOGIN_ID']."', 
				'$accountId',
				'$startTime',
				'$endTime'
				)" ;
		return $this->query($sql) ;
	}
	
	/**
	 * 保存订单明细
	 */
	function saveOrderItem($accountId , $items ,$id){
		$this->setDataSource('gbk');
		
		$items['account_id'] = $accountId ;
		$items['upload_id'] = $id ;
		try{
			$sql = $this->getDbSql("sql_order_insert") ;
			$sql = $this->getSql($sql,$items) ;
			$this->query($sql) ;
		}catch(Exception $e){
			print_r($e->getMessage()) ;
		}
		
		try{
			$sql = $this->getDbSql("sql_order_user_insert") ;
			$sql = $this->getSql($sql,$items) ;
			$this->query($sql) ;
		}catch(Exception $e){
			
		}
	}
	

	function saveAudit($params,$user){
		$this->setDataSource('gbk');
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
			$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'STATUS'=> $this->auditStatus[$status],"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
		
			$this->query($sql) ;
			
		}
	}
	
	function updateTrackNumber($params,$user){
		$key = $params['key'] ;
		$value = $params[$key] ;
		
		//更新trackingNumber
		//$trackNumber = $params['trackNumber'] ;
		$sql = $this->getDbSql("sql_order_update_tracknumber") ;
		$loginId = $user['LOGIN_ID'] ;
		$sql = $this->getSql($sql,$params) ;
		$this->query($sql) ;
		
		//修改订单状态为拣货中 9
		$memo = "update $key => $value" ;
		$sql = $this->getDbSql("sql_order_track_insert") ;
		$sql = $this->getSql($sql,array('ORDER_ID'=>$params['orderId'],'ORDER_ITEM_ID'=>$params['orderItemId'],'STATUS'=>'',"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
		$this->query($sql) ;	
	}
	
	function savePickedOrder($params , $user,$pickedId){
		$this->setDataSource('gbk');
		
		$orders = $params['orders'] ;
		$loginId = $user['LOGIN_ID'] ;
		$action  = $params['action'] ;
		$memo = "拣货中..." ;
		$orders = explode(",",$orders) ;
		
		foreach( $orders as $order ){
			$item = explode("|",$order) ;
			$orderId = $item[0] ;
			$orderItemId = $item[1] ;
			
			if( $action == 1 ){//添加到拣货单
				$sql = "
				INSERT INTO sc_amazon_picked_order 
					(ORDER_ID, 
					ORDER_ITEM_ID, 
					PICKED_ID
					)
					VALUES
					('$orderId', 
					'$orderItemId', 
					'$pickedId'
					)" ;
			
				$this->query($sql) ;
				
				//修改订单状态为拣货中 9
				$status = 9 ;
				$sql = "update sc_amazon_order_status set pick_status = '9' where order_id = '$orderId' and order_item_id = '$orderItemId'" ;
				$this->query($sql) ;
				
				$sql = $this->getDbSql("sql_order_track_insert") ;
				$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'STATUS'=> $this->pickStatus[$status],"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
				$this->query($sql) ;		
			}else if( $action == 2 ){//从拣货单删除
				$sql = "delete from sc_amazon_picked_order where order_id = '$orderId' and ORDER_ITEM_ID='$orderItemId' and picked_id = '$pickedId'" ;
				$this->query($sql) ;
				
				//修改订单状态为拣货中 9
				$status = '' ;
				$sql = "update sc_amazon_order_status set pick_status = '' where order_id = '$orderId' and order_item_id = '$orderItemId'" ;
				$this->query($sql) ;
				$memo = "从拣货单中移除" ;
				$sql = $this->getDbSql("sql_order_track_insert") ;
				$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'STATUS'=>'',"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
				$this->query($sql) ;	
			}else if( $action == 3){//完成拣货
				//修改订单状态为拣货中 9
				$status = 10 ;
				$sql = "update sc_amazon_order_status set pick_status = '$status' where order_id = '$orderId' and order_item_id = '$orderItemId'" ;
				$this->query($sql) ;
				
				$sql = $this->getDbSql("sql_order_track_insert") ;
				$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'STATUS'=>$this->pickStatus[$status],"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
				$this->query($sql) ;	
			}
		}
	}

	public function saveRedoOrder($params,$user){
		$orderId = $params['orderId'] ;
		$orderItemId = $params['orderItemId'] ;
		$memo = $params['memo'] ;
		$type = $params['type'] ;
		$action = $params['action'] ;
		$loginId = $user['LOGIN_ID'] ;
		$relover = '' ;
		if(isset($params['resolver'])){
			$relover = $params['resolver'];
		}

		$sql = "update sc_amazon_order set redo_status = '$action'
				, redo_type = '$type'
				, redo_memo = '$memo'
				, redo_resolver = '$relover'
				where order_id = '$orderId' and order_item_id = '$orderItemId'" ;
		
		$this->query($sql) ;
		
		$sql = $this->getDbSql("sql_order_track_insert") ;
		
		$status = $this->redoStatus[$action] ;
		$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'STATUS'=>$status,"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
		$this->query($sql) ;	
	}
	
	public function savePicked($params,$user){
		$id = $params['id'] ;
		$name = $params['name'] ;
		$loginId = $user['LOGIN_ID'] ;
		$memo = $params['memo'] ;
		$sql = "INSERT INTO sc_amazon_picked 
				(
				NAME, 
				MEMO, 
				CREATOR, 
				CREATE_TIME
				)
				VALUES
				(
				'$name', 
				'$memo', 
				'$loginId', 
				NOW()
				)" ;
		$this->query($sql) ;
	}
	
	function getTrackNumberFeed($params,$user ,$accountId,$MerchantIdentifier){
		//$sql = "select * from sc_amazon_order where account_id = '$accountId'
		//	and (tn_status is null or tn_status = '') AND track_number IS NOT NULL AND track_number !='' " ;
		$sql = $this->getDbSql("sql_order_can_do_ship") ;
		$sql = $this->getSql($sql,array('accountId'=>$accountId)) ;
		$items = $this->query($sql) ;
		//sql_order_can_do_ship
		$feed = $this->_getTrackNumberFeed($MerchantIdentifier , $items) ;
		
		return $feed ;
		
	}
	
	function updateTrackNumberStatus($params,$user ,$accountId){
		$loginId = $user['LOGIN_ID'] ;
		$sql = $this->getDbSql("sql_order_can_do_ship") ;
		$sql = $this->getSql($sql,array('accountId'=>$accountId)) ;
		$items = $this->query($sql) ;
		foreach($items as $order){
			$order = $order['sc_amazon_order'] ;
			$orderId = $order['ORDER_ID'] ;
			$orderItemId = $order['ORDER_ITEM_ID'] ;
			$shippingMethod = $order['SHIP_SERVICE_LEVEL'] ;
			$trackNumber = $order['TRACK_NUMBER'] ;	
			
			
			$sql = "update sc_amazon_order set TN_STATUS = '1' where
					TRACK_NUMBER='$trackNumber' and ORDER_ID='$orderId' and ORDER_ITEM_ID='$orderItemId'" ;
			$this->query($sql) ;
			
			$status = $this->tnStatus['1'] ;
			$sql = $this->getDbSql("sql_order_track_insert") ;
			$memo = "" ;
			$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'STATUS'=>$status,"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
			$this->query($sql) ;
		}
	}
	
	////////////////////////////////////////////////
	///////getOrderFeed
	public function _getTrackNumberFeed($MerchantIdentifier , $orders){
				////////////////////////////////////////////////////////////////////////////		
$Feed = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
	<Header>
		<DocumentVersion>1.01</DocumentVersion>
		<MerchantIdentifier>$MerchantIdentifier</MerchantIdentifier>
	</Header>
	<MessageType>OrderFulfillment</MessageType>
EOD;
////////////////////////////////////////////////////////////////////////////
		
		$time = new DateTime('now', new DateTimeZone('UTC')) ;
		$time->modify( '+6 hour +40 minute +31 second' );
    
    	$FulfillmentDate = $this->getFormattedTimestamp($time);
		
		$index = 0 ;
		foreach($orders as $order){
			$order = $order['sc_amazon_order'] ;
			$index++ ;
			$orderId = $order['ORDER_ID'] ;
			$orderItemId = $order['ORDER_ITEM_ID'] ;
			$shippingMethod = $order['SHIP_SERVICE_LEVEL'] ;
			$trackNumber = $order['TRACK_NUMBER'] ;	
			$carrierCode = $order['CARRIER_CODE'] ;	
////////////////////////////////////////////////////////////////////////////
$Feed .= <<<EOD
	<Message>
		<MessageID>$index</MessageID>
		<OrderFulfillment>
			<AmazonOrderID>$orderId</AmazonOrderID> 
			<FulfillmentDate>$FulfillmentDate</FulfillmentDate> 
			<FulfillmentData>
				<CarrierCode>$carrierCode</CarrierCode> 
				<ShippingMethod>$shippingMethod</ShippingMethod> 
				<ShipperTrackingNumber>$trackNumber</ShipperTrackingNumber> 
			</FulfillmentData>
			<Item>
				<AmazonOrderItemCode>$orderItemId</AmazonOrderItemCode>
			</Item>
		</OrderFulfillment>
	</Message>
EOD;
///////////////////////////////////////////////////
	
		}
$Feed .= <<<EOD
</AmazonEnvelope>
EOD;
		return $Feed ;
	}	
	
	private function getFormattedTimestamp($dateTime) {
	    return $dateTime->format(DATE_ISO8601);
	  }
}