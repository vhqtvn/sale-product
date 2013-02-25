<?php
/**
 *  货品库存小部件
 * 
 * @author Administrator
 *
 */
class GoodsWidget extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function load(){
		$user = $this->getUser() ;
	
		//sql_widget_purchase_my
		//sql_widget_purchase_myexecutor
		//sql_widget_purchase_all
	
		$goodsInventory = $this->exeSql("sql_widget_goods_inventory", array('loginId'=>$user['LOGIN_ID'])) ;
		
		$return = array() ;
		foreach( $goodsInventory as $item ){
			$item = $this->formatObject($item) ;
			$return[] = $item ;
		}
	
		return $return ;
	}
}