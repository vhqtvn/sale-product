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
		$type  = $params['type'] ;
		$records = array() ;
		if( $type == 1 ){//父ASIN
			$records = $this->exeSqlWithFormat("sql_flow_listParentAsinChartByRealId", array('realId'=>$realId)) ;
		}else if( $type ==2 ){//父ASIN/子ASIN
			$records = $this->exeSqlWithFormat("sql_flow_listParentAsinChildAsinChartByRealId", array('realId'=>$realId)) ;
		}else if( $type ==3){//父ASIN/子ASIN/SKU
			$records = $this->exeSqlWithFormat("sql_flow_listParentAsinChildAsinSkuChartByRealId", array('realId'=>$realId)) ;
		}
		
		return array( "categories"=>'','records'=>$records ) ;
	}
	
}