<?php
class Rma extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function doSave($params){
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
}