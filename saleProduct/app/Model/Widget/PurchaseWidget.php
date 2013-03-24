<?php
/**
 * 产品开发小部件
 * 
 * @author Administrator
 *
 */
class PurchaseWidget extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function load(){
			$user = $this->getUser() ;
			
			//sql_widget_purchase_my
			//sql_widget_purchase_myexecutor
			//sql_widget_purchase_all
			
			$my = $this->getObject("sql_widget_purchase_my", array('loginId'=>$user['LOGIN_ID'])) ;
			$myexecutor = $this->getObject("sql_widget_purchase_myexecutor", array('loginId'=>$user['LOGIN_ID'])) ;
			$all = $this->getObject("sql_widget_purchase_all", array('loginId'=>$user['LOGIN_ID'])) ;
			
			return array(
					'my'					=>array('value'=>$my['c'],'url'=>'/sale/purchaseList/1')
					,'myexecutor'	=>array('value'=>$myexecutor['c'],'url'=>'/sale/purchaseList/2')
					,'all'					=>array('value'=>$all['c'],'url'=>'/sale/purchaseList/1')
				) ;
	}
	
}