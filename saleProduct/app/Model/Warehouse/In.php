<?php
class In extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
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
			$result = $this->getObject("sql_warehouse_box_getById",array('boxProductId'=>$boxProductId)) ;
			return $result ;
		}
	}
	
	public function editTrack(){}
	
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
		if(!empty($in) && $in['STATUS'] == 1){
			return "has storaged!" ;
		}
		
		$inProducts = $this->exeSql("sql_warehouse_in_products",$params) ;
		
		foreach($inProducts as $product){
			$db->_queryCache = array() ;
			$product = $this->formatObject($product) ;
			
			$warehouseId = $product['WAREHOUSE_ID'] ;
			$realProductId = $product['REAL_PRODUCT_ID'] ;
			$genQuantity = $product['GEN_QUANTITY'] ;
			$badQuantity = $product['WASTE_QUANTITY'] ;
			
			$params1 = array('inId'=>$inId,
						'warehouseId'=>$warehouseId,
						'realProductId'=>$realProductId
						,'genQuantity'=>$genQuantity
						,'loginId'=>$params['loginId']
						,'badInQuantity'=>$badQuantity
						,'deliveryTime'=>$product['DELIVERY_TIME']
						,'type'=>'in'
					) ;
					
			$result = $this->getObject("sql_warehouse_storage_in_find",$params1) ;
			if(empty($result)){
				$this->exeSql("sql_warehouse_storage_in_insert",$params1) ;
			}else{
				$this->exeSql("sql_warehouse_storage_in_update",$params1) ;
			}

			//将产品信息计入总库存
			$p = $this->getObject("sql_saleproduct_getById",$params1) ;
			
			$quantity = $p['QUANTITY'] ;
			if(empty($quantity)){
				$quantity = 0 ;
			}
			
			$quantity = $quantity + $genQuantity ;
			$params1['genQuantity'] = $quantity ;
			
			$this->exeSql("sql_saleproduct_quantity_in",$params1) ;
			
			//将残品信息计入库存
			$quantity = $p['BAD_QUANTITY'] ;
			if(empty($quantity)){
				$quantity = 0 ;
			}
			$quantity = $quantity + $badQuantity ;
			$params1['badQuantity'] = $quantity ;
			$this->exeSql("sql_saleproduct_bad_quantity_in",$params1) ;
			
			//将产品信息计入具体仓库库存
			
			//将残品信息计入具体仓库库存
					
			//$this->exeSql("sql_warehouse_storage_in_insert",$params1) ;
		}
		
		//更新计划单为已入库完成
		$this->doStatus( array('inId'=>$inId,'status'=>'70') ) ;
	}
}