<?php
/**
 * 产品开发小部件
 * 
 * @author Administrator
 *
 */
class TagWidget extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function load(){
			$user = $this->getUser() ;
			
			//sql_widget_purchase_my
			//sql_widget_purchase_myexecutor
			//sql_widget_purchase_all
			
			$listingTag = $this->exeSqlWithFormat("sql_widget_tag_listing", array('loginId'=>$user['LOGIN_ID'])) ;
			$productTag = $this->exeSqlWithFormat("sql_widget_tag_product", array('loginId'=>$user['LOGIN_ID'])) ;
			$productDevTag = $this->exeSqlWithFormat("sql_widget_tag_productDev", array('loginId'=>$user['LOGIN_ID'])) ;
			
			return array(
					'listingTag'							=>	array("items"=>$listingTag,"url"=>"/amazonaccount/productLists/4")
					,'productTag'						=>	array("items"=>$productTag,"url"=>"/saleProduct/lists")
					,'productDevTag'					=>	array("items"=>$productDevTag,"url"=>"/sale/filter/1")
				) ;
	}
	
}