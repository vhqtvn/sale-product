<?php
class NewIn extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	/**
	 * AMazon入库数量校验
	 */
	public function checkQuantityForAmazon($params){
		$inId = $params['inId'] ;
		$p = $this->getObject("sc_warehouse_in_new_checkQuantityForAmazon", $params) ;
		return empty($p)?true:false ;
	}
	
	/**
	 * 转仓出库
	 * @param unknown_type $params
	 */
	public function transOutInventory($params){
		
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		
		try{
			$inId = $params['inId'] ;
			$result = $this->getObject("sql_warehouse_in_getById",array('id'=>$inId)) ;
			/**
			 * 出库仓库ID
			 */
			$sourceWarehouseId = $result['SOURCE_WAREHOUSE_ID'] ;
		
			/**
			 * $goodsId = $item->goodsId ;
				$quantity = $item->quantity ;
				$badQuantity = $item->badQuantity ;
				$inventoryType = $item->inventoryType ;
			 */
			/**
			 * 获取出库货品
			 */
			$products = $this->exeSql("sql_warehouse_in_productsV20", array('inId'=>$inId)) ;
			
			$InventoryNew  = ClassRegistry::init("InventoryNew") ;
			
			foreach($products as $product  ){
				$product = $this->formatObject( $product ) ;
	
				//目标仓库出库
				$inventoryParams = array() ;
				$inventoryParams['guid'] = $this->create_guid() ;
				
				$inventoryParams['actionType'] = $InventoryNew->ACTION_TYPE_OUT ; //出库
				$inventoryParams['action'] = $InventoryNew->ACTOIN_OUT_TRANSFER_FBA ;
				$inventoryParams['realProductId'] = $product['REAL_ID']  ;
				$inventoryParams['warehouseId'] = $sourceWarehouseId ;
				$inventoryParams['loginId'] = $params['loginId'] ;
					
				$inventoryParams['quantity'] = $product['QUANTITY'] ;
				$inventoryParams['listingSku'] = $product['LISTING_SKU'] ;
				$inventoryParams['accountId'] = $product['ACCOUNT_ID'] ;
					
				//$inventoryParams['inventoryType']   =  $InventoryNew->INVENTORY_TYPE_FBA ;
				$inventoryParams['inventoryStatus'] =  $InventoryNew->INVENTORY_STATUS_LIBRARY ;
				$inventoryParams['inventoryTo']       =  $InventoryNew->INVENTORY_TO_SELF ;
				$inventoryParams['sourceId'] = "" ;
				//debug($inventoryParams) ;
				$InventoryNew->doSave( $inventoryParams ) ;
			}
			
			$this->exeSql("sql_warehouse_in_update_status",$params) ;
			$this->doSaveTrack($params) ;
			
			$dataSource->commit() ;
		}catch(Exception $e){
			$dataSource->rollback() ;
			print_r($e) ;
		}
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
			
			$dataSource->commit() ;
			
		}catch(Exception $e){
			$dataSource->rollback() ;
			print_r($e) ;
		}
	}
	
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

}