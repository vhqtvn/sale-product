<?php
/**
 * 
 * 库存操作
 * 
 * @author Administrator
 *
 */
class Inventory extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	/**
	 * 入库
	 */
	public function in($params){
		$warehouseId = $params['warehouseId'] ;
		$details =  json_decode( $params['details'] ) ;
		foreach( $details as $item ){
			
			$goodsId = $item->goodsId ;
			$quantity = $item->quantity ;
			$badQuantity = $item->badQuantity ;
			$inventoryType = $item->inventoryType ;
			
			//获取货品信息
			$goods = $this->getObject("sql_saleproduct_getById", array('goodsId'=>$goodsId,'realProductId'=>$goodsId)) ;
			
			if(empty($quantity)){
				$quantity = 0 ;
			}
			
			if(empty($badQuantity)){
				$badQuantity = 0 ;
			}
			
			$query = array(
					'warehouseId'=>$warehouseId ,
					'goodsId'=>$goodsId,
					'quantity'=>$quantity,
					'badQuantity'=>$badQuantity,
					'inventoryType'=>$inventoryType,
					'type'=>'in'
			 ) ;
			
			foreach( $params as $key=>$val ){
				$query[ $key ] = $val ;
			}
			
			/**
			 出入库明细
			 */
			$result = $this->getObject("sql_warehouse_storage_in_find",$query) ;
			if(empty($result)){
				$this->exeSql("sql_warehouse_storage_in_insert",$query) ;
			}else{
				//已经入库，忽略
				continue ;
				//$this->exeSql("sql_warehouse_storage_in_update",$query) ;
			}
			
			
			//查找该产品对应库存
			$typeInventory = $this->getObject("sql_warehouse_inventory_type_get", $query)  ;
	
			//将库存插入到具体类型
			if( empty($typeInventory) ){
				$this->exeSql("sql_warehouse_inventory_type_insert", $query) ;
			}else{
				$query['quantity'] =  $quantity + $typeInventory['QUANTITY'] ;
				$query['badQuantity'] =  $badQuantity + $typeInventory['BAD_QUANTITY'] ;
				$query['id'] = $typeInventory['ID'] ;
				
				$this->exeSql("sql_warehouse_inventory_type_update", $query) ;
			}
			
			//更新库存到货品表
			$query['quantity'] = $quantity + $goods['QUANTITY'] ;
			$query['badQuantity'] = $badQuantity + $goods['BAD_QUANTITY'] ;
		//	debug( $query  );
			
			$this->exeSql("sql_warehouse_inventory_update", $query) ;
		} 
	}
	
	/**
	 * RMA入库
	 */
	public function ramIn($params,$type){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
	
		//入库库存
		$inQuantity = $params['quantity'] ;
		$inGoodQuantity = 0 ;
		$inBadQuantity  = 0 ;
		if( $params['quality'] == 'good' ){
			$inGoodQuantity = $inQuantity ;
		}else if( $params['quality'] == 'bad' ){
			$inBadQuantity = $inQuantity ;
		}
	
		//将产品库存计入分类库存
		$query = array(
				'warehouseId'=>$params['warehouseId'] ,
				'goodsId'=>$params['realProductId'] ,
				'quantity'=>$inGoodQuantity ,
				'badQuantity'=>$inBadQuantity ,
				'inventoryType'=>'1',
				'type'=>'out'
		) ;
		
		//查找该产品对应库存
		$typeInventory = $this->getObject("sql_warehouse_inventory_type_get", $query)  ;
		
		//将库存插入到具体类型
		if( empty($typeInventory) ){
			$this->exeSql("sql_warehouse_inventory_type_insert", $query) ;
		}else{
			$query['quantity'] =   $typeInventory['QUANTITY']  + $inGoodQuantity ;
			$query['badQuantity'] =  $typeInventory['BAD_QUANTITY'] + $inBadQuantity;
			$query['id'] = $typeInventory['ID'] ;
			
			
			$this->exeSql("sql_warehouse_inventory_type_update", $query) ;
		}
	
		//将产品信息计入总库存
		$p = $this->getObject("sql_saleproduct_getById",$params) ;
	
		//总库存
		$quantity = $p['QUANTITY'] ;
		$badQuantity = $p['BAD_QUANTITY'] ;
		
		$query = array() ;
		$query['goodsId'] = $params['realProductId'] ;
		$query['quantity'] = $quantity + $inGoodQuantity  ;
		$query['badQuantity'] =  $badQuantity +$inBadQuantity  ;
		//	debug( $query  );
		
		$this->exeSql("sql_warehouse_inventory_update", $query) ;
	}
	
	/**
	 * 出库
	 */
	public function out($params){
		$warehouseId = $params['warehouseId'] ;
		$details =  json_decode( $params['details'] ) ;
		foreach( $details as $item ){
	
			$goodsId = $item->goodsId ;
			$quantity = $item->quantity ;
			$badQuantity = $item->badQuantity ;
			$inventoryType = $item->inventoryType ;
	
			//获取货品信息
			$goods = $this->getObject("sql_saleproduct_getById", array('goodsId'=>$goodsId,'realProductId'=>$goodsId)) ;
	
			if(empty($quantity)){
				$quantity = 0 ;
			}
	
			if(empty($badQuantity)){
				$badQuantity = 0 ;
			}
	
			$query = array(
					'warehouseId'=>$warehouseId ,
					'goodsId'=>$goodsId,
					'quantity'=>$quantity,
					'badQuantity'=>$badQuantity,
					'inventoryType'=>$inventoryType,
					'type'=>'out'
			) ;
	
			foreach( $params as $key=>$val ){
				$query[ $key ] = $val ;
			}
	
			/**
			 出入库明细
			 */
			$result = $this->getObject("sql_warehouse_storage_in_find",$query) ;
		
			if(empty($result)){
				$this->exeSql("sql_warehouse_storage_in_insert",$query) ;
			}else{
				//已经入库，忽略
				continue ;
				//$this->exeSql("sql_warehouse_storage_in_update",$query) ;
			}
	
			//查找该产品对应库存
			$typeInventory = $this->getObject("sql_warehouse_inventory_type_get", $query)  ;
			
			//将库存插入到具体类型
			if( empty($typeInventory) ){
				//	$this->exeSql("sql_warehouse_inventory_type_insert", $query) ;
			}else{
				$query['quantity'] =   $typeInventory['QUANTITY']  - $quantity;
				$query['badQuantity'] =  $typeInventory['BAD_QUANTITY'] - $badQuantity;
				$query['id'] = $typeInventory['ID'] ;
				
				
				$this->exeSql("sql_warehouse_inventory_type_update", $query) ;
			}
	
			//更新库存到货品表
			$query['quantity'] = $goods['QUANTITY'] -  $quantity  ;
			$query['badQuantity'] =  $goods['BAD_QUANTITY'] - $badQuantity ;
			//	debug( $query  );
	
			$this->exeSql("sql_warehouse_inventory_update", $query) ;
		}
	}
	
	/**
	 * 执行订单出库操作
	 */
	function orderOut($orderId,$pickId=null){
		//sql_order_storage_getByOrderId
		//根据订单ID，通过拣货单获取仓库ID
		$warehouse = $this->getObject("sql_warehouse_getId_byOrderId",array('orderId'=>$orderId)) ;
		
		//查询订单库存
		$items = $this->exeSql("sql_order_storage_getByOrderId",array('orderId'=>$orderId)) ;
		foreach( $items as $item ){
			$item = $this->formatObject($item) ;
			$realId = $item['REAL_ID'] ;
			$quantity = $item['QUANTITY'] ;
			
			//获取货品信息
			$goods = $this->getObject("sql_saleproduct_getById", array('goodsId'=>$realId,'realProductId'=>$realId)) ;
			
			//更新类型库存出库
			$query = array(
					'warehouseId'=>$warehouse['WAREHOUSE_ID'] ,
					'goodsId'=>$realId ,
					'quantity'=>$quantity,
					'badQuantity'=>0,
					'inventoryType'=>'1',
					'type'=>'out'
			) ;
			//查找该产品对应库存
			$typeInventory = $this->getObject("sql_warehouse_inventory_type_get", $query)  ;
			
			//将库存插入到具体类型
			if( empty($typeInventory) ){
				//	$this->exeSql("sql_warehouse_inventory_type_insert", $query) ;
			}else{
				$query['quantity'] =   $typeInventory['QUANTITY']  - $quantity;
				$query['badQuantity'] =  $typeInventory['BAD_QUANTITY'] - 0;
				$query['id'] = $typeInventory['ID'] ;
			
			
				$this->exeSql("sql_warehouse_inventory_type_update", $query) ;
			}
			
			//更新库存到货品表
			$query['quantity'] = $goods['QUANTITY'] -  $quantity  ;
			$query['badQuantity'] =  $goods['BAD_QUANTITY']  ;
			//	debug( $query  );
			
			$this->exeSql("sql_warehouse_inventory_update", $query) ;
			
			//更新仓库库存减少
			//$this->exeSql("sql_saleproduct_quantity_out",array('realProductId'=>$realId,"quantity"=>$quantity)) ;
		}
		//sql_order_storage_shipped
		//更新为已经出库
		$this->exeSql("sql_order_storage_shipped",array('orderId'=>$orderId)) ;
	}
	
}