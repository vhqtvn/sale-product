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
		
		//删除与需求发货关联
		$sql = "delete from sc_supplychain_reqitem_in where box_product_id = '{@#bpId#}'" ;
		$this->exeSql( $sql ,array('bpId'=>$bpId)) ;
		
		//删除锁定库存
		$sql = "delete from sc_warehouse_inventory_lock where entity_type='boxProduct' and entity_id = '{@#bpId#}'" ;
		$this->exeSql( $sql ,array('bpId'=>$bpId)) ;
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
		
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		
		try{
			$inId = $params['inId'] ;
			$status = $params['status'] ;
			$inSourceType = $params['inSourceType'] ;
			$this->exeSql("sql_warehouse_in_update_status",$params) ;
			
			$this->doSaveTrack($params) ;
			
			$InventoryNew  = ClassRegistry::init("InventoryNew") ;
			
			$warehouseIn = $this->getObject("sql_warehouse_in_getById",array('id'=>$inId)) ;
			
			if( $status == 16 && $inSourceType == 'fba' ){//审批完成，并创建FBA本地入库计划
				//sql_supplychain_inbound_local_plan_insert
				$p = array() ;
				$p['planId'] = $this->create_guid() ;
				$p['accountId'] = $warehouseIn['ACCOUNT_ID'] ;
				$p['inId'] = $inId ;
				$this->exeSql("sql_supplychain_inbound_local_plan_insert", $p) ;
			}
			
			if( $status == 30 ){ //库存状态转换为在途库存
				//获取该次入库所有锁定的库存
				$locks = $this->exeSqlWithFormat("sql_supplychain_inventory_getLockForIn", array( "inId"=>$inId )) ;
					
				foreach( $locks as $lock  ){
					
					debug( $lock ) ;
			
					//将锁定库存添加到目标仓库的库存列表（状态为在途库存）
					$inventoryParams = array() ;
					$inventoryParams['guid'] = $this->create_guid() ;
						
					$inventoryParams['actionType'] = $InventoryNew->ACTION_TYPE_IN ; //出库
					$inventoryParams['action'] = $InventoryNew->ACTOIN_IN_TRANSFER ;
					$inventoryParams['realProductId'] = $lock['REAL_PRODUCT_ID'] ;
					$inventoryParams['warehouseId'] = $warehouseIn['WAREHOUSE_ID'] ;
					$inventoryParams['loginId'] = $params['loginId'] ;
			
					$inventoryParams['quantity'] = $lock['LOCK_QUANTITY'] ;
					$inventoryParams['listingSku'] = $lock['LISTING_SKU'] ;
					$inventoryParams['accountId'] = $lock['ACCOUNT_ID'] ;
			
					$inventoryParams['inventoryType']   =  $lock['INVENTORY_TYPE'] ;
					$inventoryParams['inventoryStatus'] = $InventoryNew->INVENTORY_STATUS_TRANSIT ;//在途库存
					$inventoryParams['inventoryTo']       = $lock['INVENTORY_TO']  ;
					$inventoryParams['sourceId'] = "INID_".$inId ;
			
					$InventoryNew->doSave( $inventoryParams ) ;
			
					//目标仓库出库
					$inventoryParams = array() ;
					$inventoryParams['guid'] = $this->create_guid() ;
						
					$inventoryParams['actionType'] = $InventoryNew->ACTION_TYPE_OUT ; //出库
					$inventoryParams['action'] = $InventoryNew->ACTOIN_IN_TRANSFER ;
					$inventoryParams['realProductId'] = $lock['REAL_PRODUCT_ID'] ;
					$inventoryParams['warehouseId'] = $lock['WAREHOUSE_ID'] ;
					$inventoryParams['loginId'] = $params['loginId'] ;
			
					$inventoryParams['quantity'] = $lock['LOCK_QUANTITY'] ;
					$inventoryParams['listingSku'] = $lock['LISTING_SKU'] ;
					$inventoryParams['accountId'] = $lock['ACCOUNT_ID'] ;
			
					$inventoryParams['inventoryType']   =  $lock['INVENTORY_TYPE'] ;
					$inventoryParams['inventoryStatus'] =  $lock['INVENTORY_STATUS'] ;
					$inventoryParams['inventoryTo']       =  $lock['INVENTORY_TO'] ;
					$inventoryParams['sourceId'] = "" ;
					$InventoryNew->doSave( $inventoryParams ) ;
				}
					
				//清除锁定
				//$this->exeSql("sql_supplychain_inventory_clearLockForIn", array( "inId"=>$inId )) ;
					
			}
			
			$dataSource->commit() ;
		}catch(Exception $e){
			$dataSource->rollback() ;
			print_r($e) ;
		}
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
	
	public function lockInventory( $inventoryId,$entityType,$entityId,$quantity ){
		$sql = "
					INSERT INTO sc_warehouse_inventory_lock 
						(INVENTORY_ID, 
						ENTITY_TYPE, 
						ENTITY_ID, 
						LOCK_QUANTITY
						)
						VALUES
						('{@#inventoryId#}', 
						'{@#entityType#}', 
						'{@#entityId#}', 
						'{@#lockQuantity#}'
						)" ;
		$this->exeSql($sql, array('inventoryId'=>$inventoryId,'entityType'=>$entityType,
										'entityId'=>$entityId,'lockQuantity'=>$quantity
				));
	}
	
	/**
	 * '{@#BOX_ID#}', 
				'{@#REAL_PRODUCT_ID#}', 
				'{@#QUANTITY#}', 
				'{@#DELIVERY_TIME#}', 
				'{@#PRODUCT_TRACKCODE#}', 
				'{@#inventoryType#}', 
				'{@#MEMO#}'
	 * @param unknown_type $params
	 */
	public function doSaveBoxProductForReq($params){
		$items  = $params['items'] ;
		$boxId  = $params['boxId'] ;
		$inId  = $params['inId'] ;
		$items = json_decode($items) ;
		foreach($items as $item){
			$item = get_object_vars($item) ;
			
			$array = array() ;
			$array['BOX_ID'] = $boxId ;
			$array['REAL_PRODUCT_ID'] = $item['realId'] ;
			$array['QUANTITY'] = $item['quantity'] ;
			$array['DELIVERY_TIME'] = $item['DELIVERY_TIME'] ;
			$array['PRODUCT_TRACKCODE'] = $item['PRODUCT_TRACKCODE'] ;
			$array['inventoryType'] = 1 ;//普通库存
			$array['guid'] = $this->create_guid() ;
			
			$boxProductId= $this->doSaveBoxProduct($array) ;
			
			//保存需求记录
			$reqItemIds = $item['reqItemIds'] ;
			if( !empty($reqItemIds) ){
				foreach( explode(",", $reqItemIds)  as $reqItemId ){
					$sql = "INSERT INTO  sc_supplychain_reqitem_in 
										(REQ_ITEM_ID, 
										BOX_PRODUCT_ID
										)
										VALUES
										('{@#REQ_ITEM_ID#}', 
										'{@#BOX_PRODUCT_ID#}'
										)" ;
					$this->exeSql($sql, array('REQ_ITEM_ID'=>$reqItemId,'BOX_PRODUCT_ID'=>$array['guid'])) ;
				}
			}
			
			//锁定实际库存数量
			$locks = $item['locks'] ; 
			foreach($locks as $lock){
				$lock = get_object_vars($lock) ;
				$this->lockInventory($lock['inventoryId'], "boxProduct", $boxProductId , $lock['lockQuantity']) ;
			} 
		}
	}
	
	public function doSaveBoxProductForFBAReq($params){
		$items  = $params['items'] ;
		$boxId  = $params['boxId'] ;
		$inId  = $params['inId'] ;
		$items = json_decode($items) ;
		foreach($items as $item){
			$item = get_object_vars($item) ;
				
			$array = array() ;
			$array['BOX_ID'] = $boxId ;
			$array['REAL_PRODUCT_ID'] = $item['realId'] ;
			$array['QUANTITY'] = $item['quantity'] ;
			$array['ACCOUNT_ID'] = $item['accountId'] ;
			$array['LISTING_SKU'] = $item['listingSku'] ;
			$array['DELIVERY_TIME'] = $item['DELIVERY_TIME'] ;
			$array['PRODUCT_TRACKCODE'] = $item['PRODUCT_TRACKCODE'] ;
			$array['inventoryType'] = 2 ;//FBA库存
			$array['guid'] = $this->create_guid() ;
				
			$boxProductId= $this->doSaveBoxProduct($array) ;
				
			//保存需求记录
			$reqItemIds = $item['reqItemIds'] ;
			if( !empty($reqItemIds) ){
				foreach( explode(",", $reqItemIds)  as $reqItemId ){
					$sql = "INSERT INTO  sc_supplychain_reqitem_in
										(REQ_ITEM_ID,
										BOX_PRODUCT_ID
										)
										VALUES
										('{@#REQ_ITEM_ID#}',
										'{@#BOX_PRODUCT_ID#}'
										)" ;
					$this->exeSql($sql, array('REQ_ITEM_ID'=>$reqItemId,'BOX_PRODUCT_ID'=>$array['guid'])) ;
				}
			}
				
			//锁定实际库存数量
			$locks = $item['locks'] ;
			foreach($locks as $lock){
				$lock = get_object_vars($lock) ;
				$this->lockInventory($lock['inventoryId'], "boxProduct", $boxProductId , $lock['lockQuantity']) ;
			}
		}
	}
	
	public function doSaveBoxProduct($params){
		if( empty( $params['id'] ) ){
			if(!isset($params['guid']) ){
				$params['guid'] =$this->create_guid() ;
			}
			$this->exeSql("sql_warehouse_box_product_insert",$params) ;
			return $params['guid'] ;
		}else{
			$this->exeSql("sql_warehouse_box_product_update",$params) ;
			return $params['id'] ;
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
		
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		
		try{
			$inId = $params['inId'] ;
			//检查是否已经入库
			
			$in = $this->getObject("sql_warehouse_in_getById",array('id'=>$inId)) ;
			if(!empty($in) && $in['STATUS'] == 70){ //已经入库
				return "has storaged!" ;
			}
			
			$inventoryNew  = ClassRegistry::init("InventoryNew") ;
			$inventoryNew->transferIn( $params ) ;
			
			//更新计划单为已入库完成
			$this->doStatus( array('inId'=>$inId,'status'=>'70') ) ;
			
			$dataSource->commit() ;
		}catch(Exception $e){
			debug( $e ) ;
			$dataSource->rollback() ;
			print_r( $e->getMessage() ) ;
		}
		
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