<?php
/**
 * 产品开发小部件
 * 
 * @author Administrator
 *
 */
class ProductDevWidget extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function load(){
		$user = $this->getUser() ;
	
		//sql_widget_purchase_my
		//sql_widget_purchase_myexecutor
		//sql_widget_purchase_all
	
		$cpzyMy = $this->getObject("sql_widget_product_cpzy_my", array('loginId'=>$user['LOGIN_ID'])) ;
		$cpzyAll = $this->getObject("sql_widget_product_cpzy_all", array('loginId'=>$user['LOGIN_ID'])) ;
		$cpjl = $this->getObject("sql_widget_product_cpjl", array('loginId'=>$user['LOGIN_ID'])) ;
		$zjl = $this->getObject("sql_widget_product_zjl", array('loginId'=>$user['LOGIN_ID'])) ;
	
		return array(
				'cpzyMy'					=>array('value'=>$cpzyMy['c'],'url'=>'/saleProduct/index.php/sale/filter/1')
				,'cpzyAll'					=>array('value'=>$cpzyAll['c'],'url'=>'/saleProduct/index.php/sale/filter/1')
				,'cpjl'							=>array('value'=>$cpjl['c'],'url'=>'/saleProduct/index.php/sale/filter/2')
				,'zjl'							=>array('value'=>$zjl['c'],'url'=>'/saleProduct/index.php/sale/filter/3')
		) ;
	}
}