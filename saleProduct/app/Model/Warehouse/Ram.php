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
	
	/**
	 * 审批痛过
	 */
	public function doAuditPass($params){
		$params['status'] = 2 ;
		$this->exeSql("sql_ram_event_updateStatus",$params) ;
		
		$trackMemo = "[审批通过]".$params['trackMemo'] ;
		$params['trackMemo'] = $trackMemo ;
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
	}
	
	public function doSaveTrack($params){
		//保存跟踪意见
		$this->exeSql("sql_ram_track_insert",$params) ;
	}
	
	/**
	 * 确认收到退货
	 */
	public function doUpdateRecevie($params){
		$this->exeSql("sql_ram_event_updateRecieve",$params) ;
	}
	
	/**
	 * 确认收到退货
	 */
	public function doCompleteRecevie($params){

		//完成订单订单收货
		$this->exeSql("sql_ram_event_completeRecieve",$params) ;
		//订单重发
		//$policyCode = $result['POLICY_CODE'];
		//if(!empty($policyCode)){
		//	$policy = $SqlUtils->getObject("sql_ram_options_getByCode",array('code'=>$policyCode) ) ;
		//}
		//更新订单状态为待重发货 302
		$params['status'] = '302' ;
		$this->exeSql("sql_order_redostatusUpdate",$params) ;
	}
	
	
	
	/**
	 * Ram入库
	 */
	public function doSaveRam($params){
		try{
			
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
	
}