<?php
class Disk extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function doSavePlan($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_warehouse_disk_plan_insert",$params) ;
		}else{
			$this->exeSql("sql_warehouse_disk_plan_update",$params) ;
		}
	}
	
	/**
	 * 保存盘点计划信息
	 */
	public function doSave($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_warehouse_disk_insert",$params) ;
		}else{
			$this->exeSql("sql_warehouse_disk_update",$params) ;
		}
	}
	
	/**
	 * 选择产品保存
	 */
	public function doSelectProduct($params){
		$value  = $params['value'] ;
		$diskId = $params['diskId'] ;
		$array = explode(",",$value) ;
		foreach( $array as $realId ){
			$product = $this->getObject("sql_saleproduct_getById",array('realProductId'=>$realId)) ;
			//debug($product);
			$this->exeSql("sql_warehouse_disk_product_insert",
				array('diskId'=>$diskId,'realId'=>$realId,'paperNum'=>$product['QUANTITY'])) ;
		}
	}
	
	/**
	 * 编辑明细信息
	 */
	public function doEditDetails($params){
		$this->exeSql("sql_warehouse_disk_product_update",$params) ;
	}
	
	/**
	 * 提交审批
	 */
	public function doCommit($params){
		$params['status'] = 1 ;//1 提交审批
		$this->exeSql("sql_warehouse_disk_updateStatus",$params) ;
	}
	
	/**
	 * 未通过审批，重新盘点
	 */
	/*public function doNoPass($params){
		$params['status'] = 3 ;//1 提交审批
		$this->exeSql("sql_warehouse_disk_updateStatus",$params) ;
	}*/
	
	
	/**
	 * 通过审批，结束盘点
	 */
	public function doAudit($params){
		//判断是否已经执行了盘点
		//$params['status'] = 1 ;
		$inventory  = ClassRegistry::init("Inventory") ;
		
		$passProductIds = $params['passProductIds'] ;
		
		//更新状态为已经审批
		$this->exeSql("sql_warehouse_disk_updateStatus",$params) ;
		
		$disk = $this->getObject("sql_warehouse_getWarehouseIdByDiskId",array('id'=>$params['diskId']) ) ;
		
		$passProductIds = explode(",",$passProductIds) ;
		
		foreach($passProductIds as $ddId){//ID
			
			$detail = $this->getObject("sql_warehouse_disk_details_getById",array('id'=>$ddId) ) ;
			
			$lossNum = $detail['LOSS_NUM'] ;
			$gainNum = $detail['GAIN_NUM'] ;
			
			if( !empty($lossNum) ){//盘点出库
					$inventoryParams = array() ;
					$inventoryParams['warehouseId'] =$disk['WAREHOUSE_ID']  ;//warehouseId
					$inventoryParams['diskId']  = $params['diskId'] ;
					
					$details = array() ;
					$details[] = array(
							'goodsId'=>$detail['REAL_ID']  ,
							'quantity'=>$lossNum ,
							'badQuantity'=>0 ,
							'inventoryType'=>'1' //普通库存
					) ;
					$inventoryParams['details'] =  json_encode( $details ) ;
				    $inventory->out( $inventoryParams ) ;
				
				/*$this->doDiskIn(array(
					'realProductId'=>$detail['REAL_ID'],
					'QUANTITY'=>$lossNum,
					'diskId'=>$params['diskId'],
					'warehouseId'=>$disk['WAREHOUSE_ID'],
					'status'=>'1'
				),'out') ;*/
			}
			
			if( !empty($gainNum) ){//盘点入库
				$inventoryParams = array() ;
				$inventoryParams['warehouseId'] =$disk['WAREHOUSE_ID']  ;
				$inventoryParams['diskId']  = $params['diskId'] ;
				
				$details = array() ;
				$details[] = array(
						'goodsId'=>$detail['REAL_ID']  ,
						'quantity'=>$gainNum ,
						'badQuantity'=>0 ,
						'inventoryType'=>'1' //普通库存
				) ;
				
				$inventoryParams['details'] =  json_encode( $details ) ;
				$inventory->in( $inventoryParams ) ;
				/*$this->doDiskIn(array(
					'realProductId'=>$detail['REAL_ID'],
					'QUANTITY'=>$gainNum,
					'diskId'=>$params['diskId'],
					'warehouseId'=>$disk['WAREHOUSE_ID'],
					'status'=>'1'
				),'in') ;*/
			}
			
			$this->exeSql("sql_warehouse_disk_details_updateStatus",array('status'=>'1','id'=>$ddId)) ;
			
		}
	}
	
	/**
	 * 盘点入库操作
	 */
	/*
	public function doDiskIn($params,$type){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		//将产品信息计入总库存
		$p = $this->getObject("sql_saleproduct_getById",$params) ;
		
		$quantity = $p['QUANTITY'] ;
		if(empty($quantity)){
			$quantity = 0 ;
		}
		
		$genQuantity = $params['QUANTITY'] ;
		
		if($type == 'in'){//入库
			$quantity = $quantity + $genQuantity ;
			$params['genQuantity'] = $quantity ;
			$params['type'] = 'in' ;
			$params['memo'] = '盘点入库' ;
			
			$this->exeSql("sql_saleproduct_quantity_in",$params) ;
		}else if($type == 'out'){//出库
			$quantity = $quantity - $genQuantity ;
			$params['genQuantity'] = $quantity ;
			$params['type'] = 'out' ;
			$params['memo'] = '盘点出库' ;
			
			$this->exeSql("sql_saleproduct_quantity_in",$params) ;
		}
		$params['genQuantity'] = $genQuantity ;
		//更新明细
		$this->exeSql("sql_warehouse_disk_in_insert",$params) ;
		
	}*/
	
}