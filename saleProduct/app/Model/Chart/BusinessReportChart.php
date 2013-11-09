<?php
/**
 *  流量报告报表
 * 
 * @author Administrator
 *
 */
class BusinessReportChart extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function load( $params ){
		$realId = $params['realId'] ;
		$records = $this->exeSqlWithFormat("sql_flow_listAsinChartByRealId", array('realId'=>$realId)) ;
		
		return array( "categories"=>'','records'=>$records ) ;
	}
	
	public function loadSku( $params ){
		$records = $this->exeSqlWithFormat("sql_flow_listSkuChartByRealId",$params) ;
	
		return array( "categories"=>'','records'=>$records ) ;
	}
}