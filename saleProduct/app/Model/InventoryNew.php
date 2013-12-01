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
	
	var $ACTOIN_OUT_TRANSFER = 201  ;//转仓出库
	var $ACTOIN_OUT_ORDER    = 202 ;//订单出库
	var $ACTOIN_OUT_BORROW_RETURN = 203 ;//借调归还出库
	var $ACTOIN_OUT_RETURN_SUPPLIER = 204 ;//退货出库
	
	/**
	 * 库存类型
	 */
	var $INVENTORY_TYPE_FBM = 1 ;//FBM
	var $INVENTORY_TYPE_FBA  = 2 ;//FBA
	var $INVENTORY_TYPE_DAMAGED  = 3 ;//残品
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
	 * 库存操作入口方法
	 * @param unknown_type $type
	 * @param unknown_type $params
	 */
	public function doInventory($actionType, $action , $params ){
		$params['action']  = $action ;
		$params['actionType'] = $this->ACTION_TYPE_IN ;
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
			
			//更新库存
			$this->exeSql("sql_inventory_update", array(
					'inventoryId'=>$inventoryObj['INVENTORY_ID'],
					'loginId'=>$params['loginId'],
					'quantity'=>$quantity
			)) ;
		}
		
		//2、库存轨迹表操作
		$this->exeSql("sql_inventory_track_insert", $params) ;
	}
}