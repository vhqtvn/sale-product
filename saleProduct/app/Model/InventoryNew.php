<?php
/**
 * 
 * 库存操作
 * 
 * @author Administrator
 *
 */
class InventoryNew extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	/**
	 * 库存操作类型
	 */
	var  $ACTION_TYPE_IN	= 1 ; //入库
	var  $ACTION_TYPE_OUT = 2 ;//出库
	
	/**
	 * 库存变更操作
	 * @var unknown_type
	 */
	var $ACTOIN_IN_PURCHASE = 101 ;//采购入库
	var $ACTOIN_IN_TRANSFER = 102  ;//转仓入库
	var $ACTOIN_IN_RMA = 103 ; //RMA入库
	var $ACTOIN_IN_DEPOSIT = 104; //托管入库 
	var $ACTOIN_IN_TRANSFORM  = 105;//库存转换入库
	var $ACTOIN_IN_BORROW = 106  ;//借调入库
	var $ACTOIN_IN_UNPURCHASE = 107  ;//非采购入库，如免费赠送等
	var $ACTOIN_IN_FBM = 108 ; //FBM入库
	var $ACTOIN_IN_FIX = 109 ; //手工修正入库
	
	var $ACTOIN_OUT_TRANSFER = 201  ;//转仓出库
	var $ACTOIN_OUT_ORDER    = 202 ;//订单出库
	var $ACTOIN_OUT_BORROW_RETURN = 203 ;//借调归还出库
	var $ACTOIN_OUT_RETURN_SUPPLIER = 204 ;//退货出库
	var $ACTOIN_OUT_TRANSFER_FBA = 205  ;//转仓出库
	
	/**
	 * 库存类型
	 */
	var $INVENTORY_TYPE_FBM = 1 ;//FBM
	var $INVENTORY_TYPE_FBA  = 2 ;//FBA
	var $INVENTORY_TYPE_DAMAGED  = 3 ;//残品
	var $INVENTORY_TYPE_FREE = 4 ;//自由库存
	/**
	 * 库存状态
	 */
	var $INVENTORY_STATUS_LIBRARY = 1;//在库
	var $INVENTORY_STATUS_TRANSIT = 2 ;//在途
	/**
	 * 库存所属
	 */
	var $INVENTORY_TO_SELF = 1 ;//自有库存
	var $INVENTORY_TO_DELEGATION = 2 ;//托管库存
	
	/**
	 * 保存库存
	 * @param unknown_type $params
	 */
	public function saveInventoryFix($params){
		$realId = $params['realId'] ;
		$inventorys = $params['inventorys'] ;
		$existInventorys = $params['existInventorys'] ;
		
		$inventorys = json_decode($inventorys) ;
		foreach( $inventorys as $item  ){
			$item = get_object_vars($item) ;
			$inventoryId =  $this->create_guid() ;
			$item['guid'] = $inventoryId;
			$item['realId'] = $realId;
			$item['loginId'] = $params['loginId'] ;
			$item['inventoryTo'] = $this->INVENTORY_TO_SELF ;

			$this->exeSql("sc_warehouse_in_new_addFixed", $item) ;
			
			$trackParams = $item ;
			$trackParams['guid'] =  $this->create_guid() ;
			$trackParams['actionType'] = $this->ACTION_TYPE_IN ;
			$trackParams['action'] = $this->ACTOIN_IN_FIX ;
			$trackParams['inventoryId'] = $inventoryId ;
			
			$this->exeSql("sql_inventory_track_insert", $trackParams) ;
		}
		
		$existInventorys = json_decode($existInventorys) ;
		foreach( $existInventorys as $item  ){
			$item = get_object_vars($item) ;
			$item['loginId'] = $params['loginId'] ;
			$inventory = $this->getObject("select * from sc_warehouse_inventory where inventory_id='{@#inventoryId#}'", $item) ;
			$this->exeSql("sc_warehouse_in_new_updateFixed", $item) ;
			
			$trackParams = $item ;
			$trackParams['guid'] =  $this->create_guid() ;
			$trackParams['actionType'] = $this->ACTION_TYPE_IN ;
			$trackParams['action'] = $this->ACTOIN_IN_FIX ;
			$trackParams['inventoryId'] = $inventory['INVENTORY_ID'] ;
			$trackParams['realProductId'] = $inventory['REAL_PRODUCT_ID'] ;
			$trackParams['warehouseId'] = $inventory['WAREHOUSE_ID'] ;
			$trackParams['listingSku'] = $inventory['LISTING_SKU'] ;
			$trackParams['accountId'] = $inventory['ACCOUNT_ID'] ;
			$trackParams['inventoryType'] = $inventory['INVENTORY_TYPE'] ;
			$trackParams['inventoryStatus'] = $inventory['INVENTORY_STATUS'] ;
			$trackParams['inventoryTo'] = $inventory['INVENTORY_TO'] ;
			$trackParams['result'] = $inventory['QUANTITY'] .'  fix  to  '.$item['quantity'] ;
			$this->exeSql("sql_inventory_track_insert", $trackParams) ;
		}
	}
	
	public function transferIn($params){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		$warehouseId = $params['warehouseId'] ;
		$inId  = $params['inId'] ;
		$details =  json_decode( $params['details'] ) ;
		
		$sql = "select * from sc_warehouse_inventory where inventory_status=2 and source_id = 'INID_{@#inId#}'" ;
		$transferItems = $this->exeSqlWithFormat($sql, array('inId'=>$inId)) ;
		
		//分配有效库存到在途库存中，计算所有在途库存
		$inventoryArray = array() ;
		$freeArray = array() ;
		$badArray = array() ;
		foreach( $details as $item ){
			$goodsId = $item->goodsId ;
			$quantity = $item->quantity ;
			$badQuantity = $item->badQuantity ;
			
			
			foreach( $transferItems as $item ){
					$realId = $item['REAL_PRODUCT_ID'] ;
					$tQuantity = $item['QUANTITY'] ;
					if( $goodsId == $realId ){
						if( $quantity >= $tQuantity  ){
							$item['REAL_QUANTITY'] = $tQuantity ;
							$quantity = $quantity - $tQuantity ;
						}else{
							$item['REAL_QUANTITY'] = $quantity ;
							$quantity = 0 ;
						}
						$inventoryArray[] = $item ;
					}
			}
			
			if( $quantity >0  ){
				//入库到自由库存 $goodsId
				$freeArray[] = array("realId"=>$goodsId , "quantity"=>$quantity) ;// $quantity ;
			}
			
			if( $badQuantity >0 ){
				$badArray[] = array("realId"=>$goodsId , "quantity"=>$badQuantity) ;// $quantity ;
			}
		}
		
		foreach( $inventoryArray as $item ){
			$inventoryParams = array() ;
			$inventoryParams['guid'] = $this->create_guid() ;
			
			$inventoryParams['actionType'] = $this->ACTION_TYPE_IN ; //出库
			$inventoryParams['action'] = $this->ACTOIN_IN_TRANSFER ;
			$inventoryParams['realProductId'] = $item['REAL_PRODUCT_ID'] ;
			$inventoryParams['warehouseId'] = $item['WAREHOUSE_ID'] ;
			$inventoryParams['loginId'] = $params['loginId'] ;
				
			$inventoryParams['quantity'] = $item['REAL_QUANTITY'] ;
			$inventoryParams['listingSku'] = $item['LISTING_SKU'] ;
			$inventoryParams['accountId'] = $item['ACCOUNT_ID'] ;
				
			$inventoryParams['inventoryType']   =  $item['INVENTORY_TYPE'] ;
			$inventoryParams['inventoryStatus'] = $this->INVENTORY_STATUS_LIBRARY ;//在途库存
			$inventoryParams['inventoryTo']       = $item['INVENTORY_TO']  ;
			$inventoryParams['sourceId'] = "" ;
				
			$this->doSave( $inventoryParams ) ;
		} 
		
		foreach( $freeArray as $item ){
			$inventoryParams = array() ;
			$inventoryParams['guid'] = $this->create_guid() ;
		
			$inventoryParams['actionType'] = $this->ACTION_TYPE_IN ; //出库
			$inventoryParams['action'] = $this->ACTOIN_IN_TRANSFER ;
			$inventoryParams['realProductId'] = $item['realId'] ;
			$inventoryParams['warehouseId'] = $warehouseId ;
			$inventoryParams['loginId'] = $params['loginId'] ;
		
			$inventoryParams['quantity'] = $item['quantity'] ;
			$inventoryParams['listingSku'] = '' ;
			$inventoryParams['accountId'] = '' ;
		
			$inventoryParams['inventoryType']   =  $this->INVENTORY_TYPE_FREE ;
			$inventoryParams['inventoryStatus'] = $this->INVENTORY_STATUS_LIBRARY ;//在库库存
			$inventoryParams['inventoryTo']       = $this->INVENTORY_TO_SELF ;
			$inventoryParams['sourceId'] = "" ;
		
			$this->doSave( $inventoryParams ) ;
		}
		
		foreach( $badArray as $item ){
			$inventoryParams = array() ;
			$inventoryParams['guid'] = $this->create_guid() ;
		
			$inventoryParams['actionType'] = $this->ACTION_TYPE_IN ; //出库
			$inventoryParams['action'] = $this->ACTOIN_IN_TRANSFER ;
			$inventoryParams['realProductId'] = $item['realId'] ;
			$inventoryParams['warehouseId'] = $warehouseId ;
			$inventoryParams['loginId'] = $params['loginId'] ;
		
			$inventoryParams['quantity'] = $item['QUANTITY'] ;
			$inventoryParams['listingSku'] = '' ;
			$inventoryParams['accountId'] = '' ;
		
			$inventoryParams['inventoryType']   =  $this->INVENTORY_TYPE_DAMAGED ;
			$inventoryParams['inventoryStatus'] = $this->INVENTORY_STATUS_LIBRARY ;//在库库存
			$inventoryParams['inventoryTo']       = $this->INVENTORY_TO_SELF ;
			$inventoryParams['sourceId'] = "" ;
		
			$this->doSave( $inventoryParams ) ;
		}
		
		//清除在途库存
		$this->exeSql("sql_supplychain_inventory_clearTransferForIn", array('inId'=>$inId)) ;
		
		//更新入库计划为已完成入库
		$sql = "update sc_warehouse_in set UPDATE_TIME = NOW() where id = '{@#inId#}'" ;
		$this->exeSql($sql, array('inId'=>$inId)) ;
		
		//更新需求为已完成
		$this->exeSql("sql_supplychain_requirement_complete", array('inId'=>$inId)) ;
	}
	
	public function purchaseIn($params){
		//debug($params) ;
		//$this->_doSave($params) ;
		$inventoryItem = $params['inventoryItem'] ;
		$inventoryItem = json_decode($inventoryItem) ;
		//debug() ;
		
		//获取对应货品
		/*$taskProduct =  $this->getObject("select s1.*,s2.REQ_PLAN_ID from sc_purchase_task_products s1,
				sc_purchase_plan_details s2
				 where s1.product_id = s2.id and s1.task_id = '{@#taskId#}' and s1.product_id = '{@#productId#}'",
				array("taskId"=> $params['taskId'],"productId"=>$params['planProductId'])) ;*/
		$taskProduct =  $this->getObject("select  spp.* from sc_purchase_product spp
				 where spp.id = '{@#productId#}'",
				array("productId"=>$params['purchaseProductId'])) ;
		
		//70 入库确认
		if( $taskProduct['STATUS'] >= 70 ){//已经入库
			return "已经入库，不能再进行操作入库！" ;
		}
		
		//保存自由库存
		$freeQuantity = $params['freeQuantity'] ;
		if( $freeQuantity && $freeQuantity >0 ){
			$inventoryParams = array() ;
			$inventoryParams['guid'] = $this->create_guid() ;
				
			$inventoryParams['actionType'] = $this->ACTION_TYPE_IN ;
			$inventoryParams['action'] = $params['action'] ;
			$inventoryParams['realProductId'] = $params['realId'] ;
			$inventoryParams['warehouseId'] = $params['warehouseId'] ;
			$inventoryParams['loginId'] = $params['loginId'] ;
				
			$inventoryParams['quantity'] = $freeQuantity ;
			$inventoryParams['listingSku'] = '' ;
			$inventoryParams['accountId'] = '' ;
				
			$inventoryParams['inventoryType'] = $this->INVENTORY_TYPE_FREE ;
			$inventoryParams['inventoryStatus'] = $this->INVENTORY_STATUS_LIBRARY ;
			$inventoryParams['inventoryTo'] = $this->INVENTORY_TO_SELF ;
			$this->_doSave($inventoryParams) ;
		}

		//保存残品
		$badProductsNum = $params['badProductsNum'] ;
		if( $badProductsNum && $badProductsNum >0 ){
			$inventoryParams = array() ;
			$inventoryParams['guid'] = $this->create_guid() ;
		
			$inventoryParams['actionType'] = $this->ACTION_TYPE_IN ;
			$inventoryParams['action'] = $params['action'] ;
			$inventoryParams['realProductId'] = $params['realId'] ;
			$inventoryParams['warehouseId'] = $params['warehouseId'] ;
			$inventoryParams['loginId'] = $params['loginId'] ;
		
			$inventoryParams['quantity'] = $badProductsNum ;
			$inventoryParams['listingSku'] = '' ;
			$inventoryParams['accountId'] = '' ;
		
			$inventoryParams['inventoryType'] = $this->INVENTORY_TYPE_DAMAGED ;
			$inventoryParams['inventoryStatus'] = $this->INVENTORY_STATUS_LIBRARY ;
			$inventoryParams['inventoryTo'] = $this->INVENTORY_TO_SELF ;
			$this->_doSave($inventoryParams) ;
		}
		
		//保存listing库存
		foreach( $inventoryItem as $item ){
			$item = get_object_vars($item) ;
			
			if( $item['purchaseQuantity'] && $item['purchaseQuantity'] > 0 ) {
				$inventoryParams = array() ;
				$inventoryParams['guid'] = $this->create_guid() ;
					
				$inventoryParams['actionType'] = $this->ACTION_TYPE_IN ;
				$inventoryParams['action'] = $params['action'] ;
				$inventoryParams['realProductId'] = $params['realId'] ;
				$inventoryParams['warehouseId'] = $params['warehouseId'] ;
				$inventoryParams['loginId'] = $params['loginId'] ;
					
				$inventoryParams['quantity'] = $item['purchaseQuantity'] ;
				$inventoryParams['listingSku'] = $item['listingSku'] ;
				$inventoryParams['accountId'] = $item['accountId'] ;
					
				$inventoryParams['inventoryType'] = $this->getInventoryType(  $item['channel'] ) ;
				$inventoryParams['inventoryStatus'] = $this->INVENTORY_STATUS_LIBRARY ;
				$inventoryParams['inventoryTo'] = $this->INVENTORY_TO_SELF ;
					
				$this->_doSave($inventoryParams) ;
			}else{
				$item['purchaseQuantity'] = 0 ;
			}
			//更新需求实际采购数量
			$sql = "update sc_supplychain_requirement_item set real_purchase_quantity = '{@#quantity#}' 
								where account_id='{@#accountId#}' and listing_sku='{@#listingSku#}' and plan_id = '{@#planId#}'" ;
			$this->exeSql($sql, array("quantity"=> $item['purchaseQuantity'],
						'accountId'=>$item['accountId'],
						'listingSku'=>$item['listingSku'] ,
						"planId"=>$item['planId'])) ;
			
		}
		
		//更新状态为已入库,开启采购审计开关
		$this->exeSql("update sc_purchase_product set status=75,is_audit=1,warehouse_id='{@#warehouseId#}',warehouse_time = '{@#warehouseTime#}'
				 where id = '{@#productId#}'",
				array(
						'warehouseId'=>$params['warehouseId'],
						'warehouseTime'=>$params['warehouseTime'],
						"productId"=>$params['purchaseProductId'])) ;
		
		
		//更新需求为已采购
		if( !empty($taskProduct['REQ_PRODUCT_ID']) ){
			$sql = "update sc_supplychain_requirement_plan_product set status = 4 where REQ_PRODUCT_ID='{@#reqProductId#}'" ;
			$this->exeSql($sql, array('reqProductId'=>$taskProduct['REQ_PRODUCT_ID'])) ;
		}
	}
	
	private function getInventoryType( $channel ){
		if( strpos($channel, "AMAZON") >= 0){
			return $this->INVENTORY_TYPE_FBA ;
		}
		return $this->INVENTORY_TYPE_FBM;
	}
	
	/**
	 * 库存操作入口方法
	 * @param unknown_type $type
	 * @param unknown_type $params
	 */
	public function doInventory($actionType, $action , $params ){
		$params['action']  = $action ;
		$params['actionType'] = $this->ACTION_TYPE_IN ;
		$this->_doSave($params) ;
	}
	
	public function doSave($params){
		$this->_doSave($params) ;
	}
	
	/**
	 * 入库操作
	 * @param unknown_type $params
	 */
	private function _doSave($params){
		//1、库存主数据表操作
		
		//判断改状态库存是否存在
		$inventoryObj = $this->getObject("sql_inventory_exists", $params) ;
		
		if( empty($inventoryObj) ){//执行插入操作
			$this->exeSql("sql_inventory_insert", $params) ;
		}else{ //更新当前库存操作
			$quantity = $inventoryObj['QUANTITY'] ;//获取当前库存数量
			
			//判断是入库还是出库
			$actionType = $params['actionType'] ;
			$changeQuantity = $params['quantity'] ;
			if( $this->ACTION_TYPE_IN ==$actionType  ){//入库
				$quantity = $quantity + $changeQuantity ;
			}else if( $this->ACTION_TYPE_OUT ==$actionType  ){
				$quantity = $quantity - $changeQuantity ;
			}
			
			//如果小于0，则修正为0
			if( $quantity <0 ) $quantity = 0 ;
			
			//更新库存
			$this->exeSql("sql_inventory_update", array(
					'inventoryId'=>$inventoryObj['INVENTORY_ID'],
					'loginId'=>$params['loginId'],
					'quantity'=>$quantity
			)) ;
		}
		
		//2、库存轨迹表操作
		$params['guid'] =  $this->create_guid() ;
		$this->exeSql("sql_inventory_track_insert", $params) ;
	}
}