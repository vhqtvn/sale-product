<?php
class Ram extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function doOrderRam($params){
		$this->exeSql("sql_order_ram_status",$params ) ;
	}
	
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
		//初始化
		$params['rmaValue'] = 0 ;
		$this->doOrderRam($params) ;
	}
	
	/**
	 * 保存并审批事件
	 */
	public function doSaveAndAuditEvent($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_ram_event_insert",$params) ;
		}else{
			$this->exeSql("sql_ram_event_update",$params) ;
			$this->exeSql("sql_ram_event_updateStatus",$params) ;
		}
		$params['rmaValue'] = 1 ;
		$this->doOrderRam($params) ;
	}
	
	/**
	 * 审批痛过
	 */
	public function doAuditPass($params){
		$params['status'] = 2 ;
		$this->exeSql("sql_ram_event_updateStatus",$params) ;
		
		$trackMemo = "[审批通过]".$params['trackMemo'] ;
		$params['trackMemo'] = $trackMemo ;
		
		$params['rmaValue'] = 2 ;
		$this->doOrderRam($params) ;
	}
	
	/**
	 * 审批不痛过
	 */
	public function doAuditNotPass($params){
		$params['status'] = 1 ;
		$this->exeSql("sql_ram_event_updateStatus",$params) ;
		
		$trackMemo = "[审批不通过]".$params['trackMemo'] ;
		$params['trackMemo'] = $trackMemo ;
		
		$this->exeSql("sql_ram_track_insert",$params) ;
		
		$params['rmaValue'] = 3 ;
		$this->doOrderRam($params) ;
	}
	
	public function doSaveTrack($params){
		//保存跟踪意见
		$this->exeSql("sql_ram_track_insert",$params) ;
	}
	
	/**
	 * 确认已经退款完成
	 */
	public function doRefundConfrim($params){
		$this->exeSql("sql_ram_event_confirmRefund",$params) ;
	}
	
	/**
	 * 处理完成
	 */
	public function doFinish($params){
		$params['status'] = 3 ;
		$this->exeSql("sql_ram_event_updateStatus",$params) ;
	}
	
	/**
	 * 确认收到退货
	 */
	public function doUpdateRecevie($params){
		$this->exeSql("sql_ram_event_updateRecieve",$params) ;
	}
	
	/**
	 * 完成退货入库
	 */
	public function doCompleteRecevie($params){
		//完成订单订单收货
		$this->exeSql("sql_ram_event_completeRecieve",$params) ;
		
	}
	
	/**
	 * Ram入库
	 */
	public function doSaveRam($params){
		try{
			$fileName = $_FILES['image']["name"] ;
			if( !empty($fileName) ){
				$myfile   = $_FILES['image']['tmp_name'] ;
				$path = dirname(dirname(dirname(dirname(__FILE__))))."/images/bad_product/";
			
				if( !file_exists($path) ) {
					$this->creatdir($path) ;
				}
				$fileName = date('YmdHis').'_'.$fileName ;
				$fileUrl = $path.$fileName;
				move_uploaded_file($myfile,$fileUrl) ;
				
				$params['image'] = "/images/bad_product/".$fileName;
			}
			
			if( empty( $params['ramId'] ) ){
				$params['ramId'] = 0 ;
			}
	
			//保存到RMA入库表
			$this->exeSql("sql_warehouse_rma_insert",$params) ;
			
			//更新库存
			$this->doRMAIn($params , 'in') ;
		}catch(Exception $e){
			print_r( $e ) ;
		}
	}
	
	/**
	 * RMA入库
	 */
	public function doRMAIn($params,$type){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		$inQuantity = $params['quantity'] ;
		
		//将产品信息计入总库存
		$p = $this->getObject("sql_saleproduct_getById",$params) ;
		
		$quantity = $p['QUANTITY'] ;
		$badQuantity = $p['BAD_QUANTITY'] ;
		
		if( $params['quality'] == 'good' ){
			if(empty($quantity)){
				$quantity = 0 ;
			}
		
			$quantity = $quantity + $inQuantity ;
			$params['genQuantity'] = $quantity ;
			
			$this->exeSql("sql_saleproduct_quantity_in",$params) ;
		}else if( $params['quality'] == 'bad' ){
			//将残品信息计入库存
			if(empty($badQuantity)){
				$badQuantity = 0 ;
			}
			$quantity = $inQuantity + $badQuantity ;
			$params['badQuantity'] = $quantity ;
			$this->exeSql("sql_saleproduct_bad_quantity_in",$params) ;
		}
	}
	
	/**
	 * 用户加入风险客户
	 */
	public function doDangerUser($params){
		$params['status'] = 'danger' ;
		$this->exeSql("sql_saleuser_updateStatusByEmail",$params) ;
	}
	
	/**
	 * 保存从发货
	 */
	public function saveReship($params){
		$result = $params['result'] ;
		$json = json_decode($result) ;
		
		foreach($json as $item){
			$rmaReship = $item->rmaReship ;
			$orderId   = $item->orderId ;
			$orderItemId   = $item->orderItemId ;
			
			$this->exeSql("sql_saleuser_saveReship",array(
				"rmaReship"=>$rmaReship,
				"orderId"=>$orderId,
				"orderItemId"=>$orderItemId
			)) ;
		}
		
		if(  $params['resendStatus'] == 1 ){
			$this->exeSql("sql_ram_event_updateResend",$params ) ;
			
			//更新拣货单状态
			$this->exeSql("sql_order_ram_UpdatePickStatus",$params) ;
			
			//更新为可重发货状态
			$params['rmaValue'] = 10 ;
			$this->doOrderRam($params) ;
		}
	}
	
	/**
	 * 添加到RMA订单从订单管理
	 */
	public function addRamFromOrder($params){
		$status = $params['status'] ;
		$orders = $params['orders'] ;
		$loginId = $params['loginId'] ;
		$memo = $params['memo'] ;
		$orders = explode(",",$orders) ;
		$index = 0 ;
		foreach( $orders as $order ){
			$index++ ;
			//sql_ram_event_insert
			
			$item = explode("|",$order) ;
			$orderId = $item[0] ;
			$orderItemId = $item[1] ;
			if(empty($orderId)) continue ;
			
			$orders = $SqlUtils->exeSql("sql_order_list",array('orderId'=>$orderId) ) ;
			$order = $SqlUtils->formatObject($orders[0]) ;
			
			$random = date("His")+$index ;
			
			$defaultCode = "RMA-".date("Ymd")."-".$random ;
			
			$sqlParams = array("code"=>$defaultCode, 
					"orderId"=>$orderId, 
					"orderNo"=>$order['ORDER_NUMBER'], 
					"causeCode"=>'', 
					"policyCode"=>'', 
					"loginId"=>'', 
					"status"=>'', //状态为空
					"memo"=>$memo) ;
			
			//保存订单进RAM
			$this->exeSql("sql_ram_event_insert",$sqlParams) ;
			
			//更新订单状态为
			
			
			//更新订单状态为RMA状态
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
	
}