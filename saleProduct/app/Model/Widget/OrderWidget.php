<?php
/**
 * 产品开发小部件
 * 
 * @author Administrator
 *
 */
class OrderWidget extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function load(){
		   $user = $this->getUser() ;
			
			//sql_widget_purchase_my
			//sql_widget_purchase_myexecutor
			//sql_widget_purchase_all
			
			$orderOne = $this->getObject("sql_widget_order_nostatus_one", array('loginId'=>$user['LOGIN_ID'])) ;
			$orderMany = $this->getObject("sql_widget_order_nostatus_many", array('loginId'=>$user['LOGIN_ID'])) ;
			$pickedMy = $this->getObject("sql_widget_order_picked_my", array('loginId'=>$user['LOGIN_ID'])) ;
			$pickedAll = $this->getObject("sql_widget_order_picked_all", array('loginId'=>$user['LOGIN_ID'])) ;
			
			return array(
					'orderOne'					=>array('value'=>$orderOne['c'],'url'=>'/saleProduct/index.php/order/lists/')
					,'orderMany'					=>array('value'=>$orderMany['c'],'url'=>'/saleProduct/index.php/order/lists/')
					,'pickedMy'					=>array('value'=>$pickedMy['c'],'url'=>'/saleProduct/index.php/order/doingLists')
					,'pickedAll'					=>array('value'=>$pickedAll['c'],'url'=>'/saleProduct/index.php/order/doingLists')
				) ;
	}
}