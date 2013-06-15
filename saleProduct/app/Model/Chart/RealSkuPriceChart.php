<?php
/**
 *  货品库存小部件
 * 
 * @author Administrator
 *
 */
class RealSkuPriceChart extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function load( $params ){
		$sku = $params['sku'] ;
		$records = $this->exeSqlWithFormat("sql_saleproduct_listHistoryTradePrice", array('sku'=>$sku)) ;
		
		return array( "categories"=>'','records'=>$records ) ;
	}
}