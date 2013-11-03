<?php
/**
 *  货品库存小部件
 * 
 * @author Administrator
 *
 */
class RealSkuChart extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function load( $params ){
		$sku = $params['sku'] ;
		$records = $this->exeSqlWithFormat("sql_chart_realSku_quantity", array('sku'=>$sku)) ;
		
		return array( "categories"=>'','records'=>$records ) ;
	}
	
	public function loadDay( $params ){
		$sku = $params['sku'] ;
		$records = $this->exeSqlWithFormat("sql_chart_realSku_quantity_byDay",$params) ;
	
		return array( "categories"=>'','records'=>$records ) ;
	}
}