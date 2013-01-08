<?php
class Disk extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
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
	public function doNoPass($params){
		$params['status'] = 3 ;//1 提交审批
		$this->exeSql("sql_warehouse_disk_updateStatus",$params) ;
	}
	
	
	/**
	 * 通过审批，结束盘点
	 */
	public function doPass($params){
		//判断是否已经执行了盘点
		
		$params['status'] = 2 ;
		$this->exeSql("sql_warehouse_disk_updateStatus",$params) ;
		
		$disk = $this->getObject("sql_warehouse_disk_lists",array('id'=>$params['diskId']) ) ;
		
		$deskDetails = $this->exeSql("sql_warehouse_disk_details",array('id'=>$params['diskId']) ) ;
		
		if( !empty($deskDetails) ){
			foreach($deskDetails as $detail){
				$product = $this->formatObject($detail) ;
				$lossNum = $product['LOSS_NUM'] ;
				$gainNum = $product['GAIN_NUM'] ;
				
				if( !empty($lossNum) ){//盘点出库
					$this->doDiskIn(array(
						'realProductId'=>$product['REAL_ID'],
						'QUANTITY'=>$lossNum,
						'diskId'=>$params['diskId'],
						'warehouseId'=>$disk['WAREHOUSE_ID']
					),'out') ;
				}
				
				if( !empty($gainNum) ){//盘点入库
					$this->doDiskIn(array(
						'realProductId'=>$product['REAL_ID'],
						'QUANTITY'=>$gainNum,
						'diskId'=>$params['diskId'],
						'warehouseId'=>$disk['WAREHOUSE_ID']
					),'in') ;
				}
			}
		}
	}
	
	/**
	 * 盘点入库操作
	 */
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
		
	}
	
}