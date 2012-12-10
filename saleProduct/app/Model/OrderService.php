<?php
/**
 SELECT sc_amazon_order.* 
		  FROM  sc_amazon_order_status ,sc_amazon_order
		  LEFT JOIN sc_amazon_account_product 
		  ON sc_amazon_account_product.sku = sc_amazon_order.sku
		WHERE sc_amazon_order_status.order_id = sc_amazon_order.order_id AND
		   sc_amazon_order_status.order_item_id = sc_amazon_order.order_item_id 
		   AND sc_amazon_order_status.pick_status IN ('9','10')
		   AND ( sc_amazon_order.track_number IS NULL OR sc_amazon_order.track_number = '') 
		   AND ( sc_amazon_order.tn_status IS NULL OR sc_amazon_order.tn_status NOT IN ('1') )
 */
class OrderService extends AppModel {
	var $useTable = "sc_account_product_warning" ;
	
	var $auditStatus = array('2'=>'风险客户',
			'3'=>'待退单',
			'4'=>'外购订单',
			'5'=>'合格订单',
			'6'=>'加急单',
			'7'=>'特殊单') ;
			
	var $pickStatus = array('9'=>'拣货中','10'=>'发货完成','11'=>'异常订单','12'=>'拣货完成') ;
	var $tnStatus   = array('1'=>'同步AMAZON完成','2'=>'同步进行中') ;
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
		
	var $shipMethod = array(
			'FCL'=>'First-Class Letter',
			'FCLLE'=>'First-Class Letter Large Envelp（Flat）',
			'FCPS'=>'First-Class Package Service',
			'PM'=>'Priority Mail'
		) ;
	
	function updateProcessField($params){
		$type = $params['type'] ;
		$value = $params['value'] ;
		$orderId = $params['orderId'] ;
		$sql = "update sc_amazon_order set $type = '$value' where order_id = '$orderId'" ;
		return $this->query($sql) ;
	}
	
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
	function saveOrderItem($accountId , $items ,$id,$header){
		$this->setDataSource('gbk');
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		$items['account_id'] = $accountId ;
		$items['upload_id'] = $id ;
		
		if( count( $header ) > 60 ){//详细信息
			//sql_order_update
			$sql = $this->getDbSql("sql_order_update") ;
			$sql = $this->getSql($sql,$items) ;
			$this->query($sql) ;
		}else{
			try{
				$orderId = $items['order-id'] ;
				$sql = "select * from sc_amazon_order where order_id = '$orderId' and upload_id != '$id'" ;
				$record = $this->query($sql) ;
				if(!empty($record)){//判断是否已经存在，重复导入
					return ;
				}
				
				$sql = "select * from sc_amazon_order where order_id = '$orderId'" ;
				$record = $this->query($sql) ;
				$orderNumber = '' ;
				if(empty($record)){
					$orderNumber = $this->getMaxValue("order",$orderId,'1000000000') ;
				}else{
					$orderNumber = $record[0]['sc_amazon_order']['ORDER_NUMBER'] ;
				}
				
				$items['order-barcode'] = $orderNumber ;
				
				$sql = $this->getDbSql("sql_order_insert") ;
				$sql = $this->getSql($sql,$items) ;
				$this->query($sql) ;
				
				/*$sql = "select * from sc_order_result where order_id = '$orderId'" ;
				$result = $this->query($sql) ; 
				if(empty($result)){
					$sql = "insert into sc_order_result(order_id) values('$orderId')" ;
					$this->query($sql) ;
				}*/
				
			}catch(Exception $e){
				print_r($e->getMessage()) ;
			}
			
			try{
				$sql = $this->getDbSql("sql_order_user_insert") ;
				$sql = $this->getSql($sql,$items) ;
				$this->query($sql) ;
			}catch(Exception $e){}
		}
		
	}
	

	function saveAudit($params,$user){
		//$this->setDataSource('gbk');
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
	
	function repickedException($params , $user,$pickedId = null){
		//$this->setDataSource('gbk');
		$orderId = null ;
		$orderNumber = null ;
		if(isset($params['orderId'])){
			$orderId = $params['orderId'] ;
		}
		
		if(isset($params['orderNumber'])){
			$orderNumber =  $params['orderNumber'] ;
		}
		
		$loginId = $user['LOGIN_ID'] ;
		$memo    = $params['memo'] ;
		$type    = $params['type'] ;
		$status  = $params['status'] ;
		$memo = "[$type]$memo" ;
		
		if( $orderId  == null ){
			$sql = "select * from sc_amazon_order where order_number = '$orderNumber' limit 0,1 " ;
			$result = $this->query($sql) ;
			if( empty($result) ) return false ;
			$orderId = $result[0]['sc_amazon_order']['ORDER_ID'] ;
		}
		
		$clause = "" ;
		if( $status == '10'){//出仓
			$sql = "select * from sc_amazon_order_status where order_id = '$orderId' and pick_status='12' " ;
			$result = $this->query($sql) ;
			if( count($result) <= 0 ){
				return false ;
			}
		}
		
		$sql = "update sc_amazon_order_status set pick_status = '$status' where order_id = '$orderId'" ;
		$result = $this->query($sql) ;
		$sql = $this->getDbSql("sql_order_track_insert") ;
		$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'STATUS'=>$this->pickStatus[$status],"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
		$this->query($sql) ;
		return true ;
	}
	
	function savePickedOrder($params , $user,$pickedId = null){
		//$this->setDataSource('gbk');
		
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
		
		if( $action == 4 ){//售后管理
			$isEndService = '' ;
			$isDangerUser = '' ;
			if( isset($params['isEndService']) ){
				$isEndService = $params['isEndService'] ;
			}
			
			if( isset($params['isDangerUser']) ){
				$isDangerUser = $params['isDangerUser'] ;
			}
			
			if( $isEndService == 1 ){//结束售后服务
				$sql = "update sc_amazon_order set service_status = 'done' where order_id = '$orderId' and order_item_id = '$orderItemId'" ;
				$this->query($sql) ;
			}
			
			if( $isDangerUser == 1 ){//风险用户
				$sql = "UPDATE sc_amazon_order_user SET STATUS = 'danger'
						WHERE email IN (SELECT buyer_email FROM sc_amazon_order WHERE
						order_id = '$orderId' AND order_item_id = '$orderItemId')" ;
				$this->query($sql) ;
			}else{
				$sql = "UPDATE sc_amazon_order_user SET STATUS = ''
						WHERE email IN (SELECT buyer_email FROM sc_amazon_order WHERE
						order_id = '$orderId' AND order_item_id = '$orderItemId')" ;
				$this->query($sql) ;
			}
		
			$sql = "INSERT INTO sc_amazon_order_aftermarket 
						(
						ORDER_ID, 
						ORDER_ITEM_ID, 
						ACTION_TYPE, 
						DETAIL_TYPE, 
						MEMO, 
						RESOLVER,
						ACT_TIME,
						CREATOR
						)
						VALUES
						(
						'$orderId', 
						'$orderItemId', 
						'$action', 
						'$type', 
						'$memo', 
						'$relover',
						NOW(), 
						'$loginId'
						)" ;
			$this->query($sql) ;
		}else{
			$sql = "update sc_amazon_order set redo_status = '$action'
					, redo_type = '$type'
					, redo_memo = '$memo'
					, redo_resolver = '$relover'
					, SERVICE_STATUS = 'doing'
					where order_id = '$orderId' and order_item_id = '$orderItemId'" ;
			
			$this->query($sql) ;
			
			$sql = $this->getDbSql("sql_order_track_insert") ;
			$status = $this->redoStatus[$action] ;
			$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'STATUS'=>$status,"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
			$this->query($sql) ;
		}

			
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
	
	//判断是否为风险用户
	function getOrderUser($orderId ,$orderItemId ){
		$sql = "SELECT * FROM sc_amazon_order_user 
			WHERE  email IN 
			(SELECT buyer_email FROM sc_amazon_order WHERE order_id = '$orderId' AND order_item_id = '$orderItemId')" ;
		$item = $this->query($sql) ;
		return $item ;
	}
	
	function getPickOrders($pickId){
		$sql = $this->getDbSql("sql_order_list_picked_export") ;
		$sql = $this->getSql($sql,array('pickId'=>$pickId)) ;
		$items = $this->query($sql) ;
		return $items ;
	}
	
	function getDownloadById($id){
		$sql = "select * from sc_amazon_order_download where id = '$id'" ;
		return $this->query($sql) ;
	}
	
	function downloadOrderFeed($accountId,$MerchantIdentifier,$user,$name){
		$loginId = $user['LOGIN_ID'] ;
		
		//更改状态
		$sql = $this->getDbSql("sql_order_set_asying") ;
		$sql = $this->getSql($sql,array('accountId'=>$accountId)) ;
		$this->query($sql) ;
		
		//获取列表
		$sql = $this->getDbSql("sql_order_asying_list") ;
		$sql = $this->getSql($sql,array('accountId'=>$accountId)) ;
		$items = $this->query($sql) ;
		
		$feed = $this->_getTrackNumberTxt($MerchantIdentifier , $items) ;
		
		//插入到下载表
		$sql = $this->getDbSql("sql_order_download_insert") ;
		$sql = $this->getSql($sql,array('accountId'=>$accountId,'feed'=>$feed,'loginId'=>$loginId,'name'=>$name)) ;
		$this->query($sql) ;
		
		//将插入到下载明细
		$sql = $this->getDbSql("sql_order_set_download_details") ;
		$sql = $this->getSql($sql,array('accountId'=>$accountId,'downloadId'=>$name)) ;
		$this->query($sql) ;
		
		//更新订单状态为完成
		$sql = $this->getDbSql("sql_order_set_asyed") ;
		$sql = $this->getSql($sql,array('accountId'=>$accountId)) ;
		$this->query($sql) ;
		
		return $feed ;
		
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
		//$this->setDataSource('gbk');
		
		$loginId = $user['LOGIN_ID'] ;
		$sql = $this->getDbSql("sql_order_can_do_ship") ;
		$sql = $this->getSql($sql,array('accountId'=>$accountId)) ;
		$items = $this->query($sql) ;
		foreach($items as $order1){
			$order = $order1['sc_amazon_order'] ;
			$orderId = $order['ORDER_ID'] ;
			$orderItemId = $order['ORDER_ITEM_ID'] ;
			$shippingMethod = $order['SHIP_SERVICE_LEVEL'] ;
			$trackNumber = $order[0]['TN'] ;	
			
			$sql = "update sc_amazon_order set TN_STATUS = '1' where ORDER_ID='$orderId'" ;
			$this->query($sql) ;
			
			$status = $this->tnStatus['1'] ;
			$sql = $this->getDbSql("sql_order_track_insert") ;
			$memo = "" ;
			$sql = $this->getSql($sql,array('ORDER_ID'=>$orderId,'ORDER_ITEM_ID'=>$orderItemId,'STATUS'=>$status,"MESSAGE"=>$memo,'ACTOR'=>$loginId)) ;
			$this->query($sql) ;
		}
	}
	
	function getPicked($picked){
		$sql = "select * from sc_amazon_picked where id = '$picked'" ;
		$result = $this->query($sql);
		return $result[0]['sc_amazon_picked'] ;
	}
	
	function updatePickedStatus($picked){
		$sql = "update sc_amazon_picked set status = '1' where id = '$picked'" ;
		$this->query($sql);
	}
	
	////////////////////////////////////////////////
	public function _getTrackNumberTxt($MerchantIdentifier , $orders){
		$return = "order-id	order-item-id	quantity	ship-date	carrier-code	carrier-name	tracking-number	ship-method\n" ;
		
		$time = new DateTime('now', new DateTimeZone('UTC')) ;
		$time->modify( '+6 hour +40 minute +31 second' );
    
    	$FulfillmentDate = $this->getFormattedTimestamp($time);
		
		foreach($orders as $order1){
			$order = $order1['sc_amazon_order'] ;
			$orderId = $order['ORDER_ID'] ;
			$orderItemId = $order['ORDER_ITEM_ID'] ;
			$quantity = $order['QUANTITY_TO_SHIP'] ;
			$shippingMethod = $order['MAIL_CLASS'] ;
			$trackNumber = $order1[0]['TN'] ;
			$carrierCode = $order['CARRIER_CODE'] ;
			if(empty($carrierCode)){
				$carrierCode = "USPS" ;
			}
			//$shipMethod
			if(empty($shippingMethod))	{
				$shippingMethod = "First Class Mail" ;
			}else{
				if(isset($shipMethod[$shippingMethod])){
					$shippingMethod = $shipMethod[$shippingMethod]  ;
				}else{
					$shippingMethod = "First Class Mail" ;
				}
			}
	
			$return .= "$orderId	$orderItemId	$quantity	$FulfillmentDate	$carrierCode		$trackNumber	$shippingMethod\n" ;
		}
		
		return $return ;
	}	
	
	///////getOrderFeed
	/*
	 <CarrierCode>USPS</CarrierCode>
<ShippingMethod>Standard</ShippingMethod>
<ShipperTrackingNumber>42031909940011020088254XXXXXXX</ShipperTrackingNumber>*/
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
		foreach($orders as $order1){
			$order = $order1['sc_amazon_order'] ;
			$index++ ;
			$orderId = $order['ORDER_ID'] ;
			$orderItemId = $order['ORDER_ITEM_ID'] ;
			$shippingMethod = $order['SHIP_SERVICE_LEVEL'] ;
			$trackNumber = $order1[0]['TN'] ;
			$carrierCode = $order['CARRIER_CODE'] ;
			if(empty($carrierCode)){
				$carrierCode = "USPS" ;
			}
			
			//$shipMethod
			if(empty($shippingMethod))	{
				$shippingMethod = "First Class Mail" ;
			}else{
				if(isset($shipMethod[$shippingMethod])){
					$shippingMethod = $shipMethod[$shippingMethod]  ;
				}else{
					$shippingMethod = "First Class Mail" ;
				}
			}
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