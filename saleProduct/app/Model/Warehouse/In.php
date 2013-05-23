<?php
class In extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function deleteBox($params){
		$boxId = $params['boxId'] ;
		
		$boxProduct = $this->getObject("sql_warehouse_box_product_getByBoxId",array('boxId'=>$boxId)) ;
		if( !empty( $boxProduct ) ){
			throw new Exception("存在包装箱产品，请将包装箱产品清空再删除包装箱！") ;
		}else{
			$this->exeSql("sql_warehouse_box_deleteById",array('boxId'=>$boxId)) ;
		}
	}
	
	public function deleteBoxProduct($params){
		$bpId = $params['bpId'] ;
	
		$this->exeSql("sql_warehouse_box_product_deleteById",array('bpId'=>$bpId)) ;
		return "" ;
	}
	
	public function edit($params,$user){
		$inId = $params['arg1'] ;
		$result = $this->getObject("sql_warehouse_in_getById",array('id'=>$inId)) ;
		return $result ;
	}
	
	public function editTab(){
		return null ;
	}
	
	public function editBox(){
	}
	
	public function editBoxPage($params,$user){
		$inId = $params['arg1'] ;
		$boxId = $params['arg2'] ;
		if(empty($boxId)){
			return null ;
		}else{
			$result = $this->getObject("sql_warehouse_box_getById",array('boxId'=>$boxId)) ;
			return $result ;
		}
	}
	
	/**
	 * 转仓出库
	 * @param unknown_type $params
	 */
	public function transOutInventory($params){
		$inId = $params['inId'] ;
		$result = $this->getObject("sql_warehouse_in_getById",array('id'=>$inId)) ;
		$sourceWarehouseId = $result['SOURCE_WAREHOUSE_ID'] ;
		$inventory  = ClassRegistry::init("Inventory") ;
		
		$outparams = array() ;
		$outparams['warehouseId'] = $sourceWarehouseId ;
		/**
		 * $goodsId = $item->goodsId ;
			$quantity = $item->quantity ;
			$badQuantity = $item->badQuantity ;
			$inventoryType = $item->inventoryType ;
		 */
		$products = $this->exeSql("sql_warehouse_in_products", array('inId'=>$inId)) ;
		
		$inventory  = ClassRegistry::init("Inventory") ;
		
		$inventoryParams = array() ;
		$inventoryParams['warehouseId'] = $sourceWarehouseId ;//warehouseId
		$inventoryParams['inId']  = $inId ;
		$details = array() ;
		
		foreach($products as $product  ){
			$product = $this->formatObject( $product ) ;
		
			$details[] = array(
					'goodsId'=>$product['REAL_PRODUCT_ID']  ,
					'quantity'=>$product['QUANTITY'] ,
					'badQuantity'=>0 ,
					'inventoryType'=>$product['INVENTORY_TYPE']
			) ;
			$inventoryParams['details'] =  json_encode( $details ) ;
			
		}
		
		//debug($inventoryParams) ;
		
		$inventory->out( $inventoryParams ) ;
		$this->doStatus($params) ;
	}
	
	public function doStatus($params){
		$inId = $params['inId'] ;
		$this->exeSql("sql_warehouse_in_update_status",$params) ;
		
		$this->doSaveTrack($params) ;
	}

	public function doSave4Quantity($params){
		$this->exeSql("sql_warehouse_boxp_update_status",$params) ;
	}

 
	public function editBoxProductPage($params,$user){
		$boxId = $params['arg1'] ;
		$boxProductId = $params['arg2'] ;
		if(empty($boxProductId)){
			return null ;
		}else{
			$result = $this->getObject("sql_warehouse_box_product_getById",array('boxProductId'=>$boxProductId)) ;
			return $result ;
		}
	}
	
	public function editTrack(){}
	
	/**
	 * 保存入库单
	 * @param unknown_type $params
	 */
	public function doSave($params){
	
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_warehouse_in_insert",$params) ;
		}else{
			$this->exeSql("sql_warehouse_in_update",$params) ;
		}
	}
	
	public function doSaveBox($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_warehouse_box_insert",$params) ;
		}else{
			$this->exeSql("sql_warehouse_box_update",$params) ;
		}
	}
	
	public function doSaveBoxProduct($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_warehouse_box_product_insert",$params) ;
		}else{
			$this->exeSql("sql_warehouse_box_product_update",$params) ;
		}
	}
	/**
	 * '{@#IN_ID#}', 
		'{@#STATUS#}', 
		'{@#MEMO#}', 
	 */
	public function doSaveTrack($params){
		if(!isset( $params['IN_ID'] )){
			$params['IN_ID'] = $params['inId'] ;
		}
		
		if(!isset( $params['STATUS'] )){
			$params['STATUS'] = $params['status'] ;
		}
		
		if(!isset( $params['MEMO'] )){
			if(isset($params['memo'])){
				$params['MEMO'] = $params['memo'] ;
			}
		}
		
		$this->exeSql("sql_warehouse_track_insert",$params) ;
	}
	
	public function saveDesign($params){
		$this->exeSql("sql_warehouse_saveDesgin",$params) ;
	}
	
	public function loadDesign($params){
		//getWarehouse
		$result = $this->getObject("sql_warehouse_getById",array('warehouseId'=>$params['arg1'])) ;
		//getWarehouse Unit（Item）
		$items = $this->exeSql("sql_warehouse_item_listByWarehouseId",array('warehouseId'=>$params['arg1'])) ;
		
		return array('warehouse'=>$result,'units'=>$items) ;
	}
	
	public function loadDesignView($params){
		//getWarehouse
		$result = $this->getObject("sql_warehouse_getById",array('warehouseId'=>$params['arg1'])) ;
		//getWarehouse Unit（Item）
		$items = $this->exeSql("sql_warehouse_item_listByWarehouseId",array('warehouseId'=>$params['arg1'])) ;
		
		return array('warehouse'=>$result,'units'=>$items) ;
	}
	
	/**
	 * 保存包装箱产品异常信息
	 */
	public function saveBoxProductException($params){
		  $this->exeSql("sql_warehouse_boxproduct_updateForException",$params) ;
	}
	
	/**
	 * 包装箱产品状态
	 */
	public function doBoxProductStatus($params){
		$this->exeSql("sql_warehouse_boxproduct_updateStatus",$params) ;
	}
	
	/**
	 * 加载入库单统计信息
	 */
	public function loadStatusCount(){
		return $this->exeSql("sql_warehouse_in_loadStatusCount",array());
	}
	
	public function loadStatusCount4Out(){
		return $this->exeSql("sql_warehouse_out_loadStatusCount",array());
	}
	
	/**
	 * 执行计划入库操作
	 * 
	 * sql_warehouse_in_getById
	 */
	public function doIn($params){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		$inId = $params['inId'] ;
		//检查是否已经入库
		
		$in = $this->getObject("sql_warehouse_in_getById",array('id'=>$inId)) ;
		if(!empty($in) && $in['STATUS'] == 70){ //已经入库
			return "has storaged!" ;
		}
		
		$inventory  = ClassRegistry::init("Inventory") ;
		$inventory->in( $params ) ;
		
		//更新计划单为已入库完成
		$this->doStatus( array('inId'=>$inId,'status'=>'70') ) ;
	}
	
	/**
	 * 出库操作
	 * @param unknown_type $params
	 */
	public function doOut($params){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		$inId = $params['inId'] ;
		
		$in = $this->getObject("sql_warehouse_in_getById",array('id'=>$inId)) ;
		if(!empty($in) && $in['STATUS'] == 400){ //已经出库
			return "has Out of the warehouse!" ;
		}
		
		//获取要出库的产品
		$products = $this->exeSql("sql_warehouse_in_products", array('inId'=>$inId)) ;
		
		$inventory  = ClassRegistry::init("Inventory") ;
		
		foreach($products as $product  ){
			$product = $this->formatObject( $product ) ;
		
			$inventoryParams = array() ;
			$inventoryParams['warehouseId'] =$params['warehouseId']  ;//warehouseId
			$inventoryParams['inId']  = $params['inId'] ;
			
			$details = array() ;
			$details[] = array(
					'goodsId'=>$product['REAL_PRODUCT_ID']  ,
					'quantity'=>$product['GEN_QUANTITY'] ,
					'badQuantity'=>0 ,
					'inventoryType'=>$product['INVENTORY_TYPE']
			) ;
			$inventoryParams['details'] =  json_encode( $details ) ;
		    $inventory->out( $inventoryParams ) ;
		}
		
		
		$this->doStatus( array('inId'=>$inId,'status'=>'300') ) ;
	}
}