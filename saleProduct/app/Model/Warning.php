<?php
class Warning extends AppModel {
	var $useTable = "sc_account_product_warning" ;
	
	function getById($id){
		$sql = "select * from sc_account_product_warning where code = '$id'" ;
		return $this->query($sql) ;
	}
	
	function save($data , $user){
		if( empty($data['id']) ){
			$sql = "insert into sc_account_product_warning(code,name,memo,account_id,value1,value2)
					values('".$data['code']."','".$data['name']."','".$data['memo']."','".$data['accountId']."','".$data['value1']."','".$data['value2']."')" ;
			$this->query($sql) ;
		}else{
			$sql = "update sc_union_seller
						set name = '".$data['name']."',
							memo = '".$data['memo']."',
							account_id = '".$data['accountId']."',
							value1 = '".$data['value1']."',
							value2 = '".$data['value2']."'
						where code = '".$data['code']."'" ;
			$this->query($sql) ;
		}
	}
	
	function getWarnings($accountId = null ){
		$sql = "select * from sc_account_product_warning where 1=1 {@ and account_id = #accountId#}" ;
		$sql = $this->getSql($sql , array('accountId'=>$accountId) ) ;
		return $this->query($sql) ;
	}
	
	function getByCategoryId($categoryId){
		$sql = "select * from sc_warning_category where 1=1 {@ and category_id = #categoryId#}" ;
		$sql = $this->getSql($sql , array('categoryId'=>$categoryId) ) ;
		return $this->query($sql) ;
	}
	
	/**
	 * 获取产品预警
	 */
	function getProductWarning($product,$accountId,$accountName){
		//获取预警分类
		$productId = $product['ID'] ;
		$sku 		= $product['SKU'] ;
		$itemCondition = $product['ITEM_CONDITION'] ;
		//获取产品分类
		$sql = "select * from sc_amazon_product_category_rel
				where sku = '$sku' and account_id = '$accountId'  " ;
		
		$category = $this->query($sql) ;
		if( !empty($category) && count($category) >0 ){
			$categoryId = $category[0]['sc_amazon_product_category_rel']['CATEGORY_ID'] ;
			//获取分类的预警信息
			$warnings = $this->getByCategoryId( $categoryId ) ;
			if( empty($warnings) || count($warnings) <= 0){
				$categorys = $this->getRecursionUp('sc_amazon_product_category','ID','PARENT_ID',$categoryId) ;
				foreach($categorys as $item){
					if( $item['ID'] == $categoryId ){
						continue ;
					}
					
					$categoryId = $item['ID'] ;
					$warnings = $this->getByCategoryId( $categoryId ) ;
					if( count($warnings) > 0){
						break ;
					}
				} ;
			}
			
			//处理预警信息
			if( empty($warnings) || count($warnings) <= 0 ){
				//do nothing
				return "" ;
			}else{
				//处理预警
				return $this->doWarning($warnings , $product,$accountId,$accountName) ;
			}
		}
	}
	
	/*****************************************
	 * ***************预警处理****************
	 * **************************************/
	function doWarning($warnings , $product,$accountId,$accountName){
		$rWarn = array() ;
		foreach( $warnings as $warn ){
			$warn = $warn['sc_warning_category'] ;
			$code = $warn['WARNING_ID'] ;
			$is = false ;
			if( $code == "rights_warning" ){//维权预警
				$is = 	$this->isDoRightWarning($product , $accountId,$accountName);
			}else if($code == 'ranking_warning'){//排名预警
				$is =   $this->isDoRandingWarning($product , $accountId,$accountName);
			}
			if( $is ){
				$rWarn[] = $code ;
			}
		}  	
		return json_encode($rWarn) ;
	}
	
	//维权预警
	function isDoRightWarning($product,$accountId,$accountName){
		$asin = $product['ASIN'] ;
		$sql = "SELECT count(*) as c FROM sc_sale_competition_details s1
				WHERE s1.asin = '$asin'
				AND s1.seller_name NOT IN (
					SELECT NAME FROM sc_union_seller WHERE account_id = '$accountId'
					UNION
					SELECT NAME FROM sc_amazon_account WHERE id = '$accountId'
				) " ;
		$result = $this->query($sql) ;
		$count = $result[0][0]['c'] ;
		
		return $count > 0 ;
	}
	
	//排名预警 - 全排名
	function isDoRandingWarning($product,$accountId,$accountName){
		$asin = $product['ASIN'] ;
		$itemCondition = $product['ITEM_CONDITION'] ;
		$isFM = $product['IS_FM'] ;
		
		$randwarn = $this->getById("ranking_warning") ;
		$randwarn = $randwarn[0]['sc_account_product_warning'] ;
		
		$count = 0 ;
		if( $itemCondition == 11 ){//new
			$sql = " SELECT *   FROM sc_sale_competition_details WHERE ASIN = '$asin' AND ( TYPE LIKE 'F%' OR TYPE LIKE 'N%') 
					ORDER BY SELLER_PRICE+SELLER_SHIP_PRICE " ;
					
			$records = $this->query($sql) ;
			
			foreach( $records as $record ){
				$count++ ;
				$record = $record['sc_sale_competition_details'] ;
				$sellerName = $record['SELLER_NAME'] ;
				if( $sellerName == $accountName ){
					break ;
				}
			}
					
		}else if($itemCondition == 1){//used
			$sql = " SELECT *   FROM sc_sale_competition_details WHERE ASIN = '$asin' AND TYPE LIKE 'U%'  
					ORDER BY SELLER_PRICE+SELLER_SHIP_PRICE " ;
					
			$records = $this->query($sql) ;
			
			foreach( $records as $record ){
				$count++ ;
				$record = $record['sc_sale_competition_details'] ;
				$sellerName = $record['SELLER_NAME'] ;
				if( $sellerName == $accountName ){
					break ;
				}
			}
		}
		$value1 = $randwarn['VALUE1'] || 3 ;
		
		return $count > $value1 ;
		
	}
}