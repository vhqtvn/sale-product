<?php
class Amazongrid extends AppModel {
	var $useTable = "sc_election_rule" ;

	//seller
	function getAccountRecords($query=null){
		$domain =  $_SERVER['SERVER_NAME'] ;
		
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$sql = "SELECT sc_amazon_account.*,( select count(1) from sc_amazon_account_product where
				sc_amazon_account_product.account_id = sc_amazon_account.id ) as TOTAL
		,( select sc_user.name from sc_user where sc_user.login_id = sc_amazon_account.creator ) as USERNAME
		FROM sc_amazon_account where domain = '$domain'
		limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getAccountCount($query=null){
		$domain =  $_SERVER['SERVER_NAME'] ;
		$sql = "SELECT count(*) FROM sc_amazon_account where domain = '$domain'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getProductAsynsHistoryRecords($query=null,$accountId){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$sql = "SELECT *,
				( select sc_user.name from sc_user where sc_user.login_id = sc_amazon_account_asyn.creator ) as USERNAME
				FROM sc_amazon_account_asyn where account_id = '$accountId'
				and report_type = '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_'
				and status = 'complete' order by create_time desc  limit ".$start.",".$limit;
		
		$array = $this->query($sql);
		return $array ;
	}

	function getProductAsynsHistoryCount($query=null,$accountId){
		$sql = "SELECT count(*) FROM sc_amazon_account_asyn where account_id = '$accountId'
				and report_type = '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_'
				and status = 'complete'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getProductActiveAsynsHistoryRecords($query=null,$accountId){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$sql = "SELECT *,
				( select sc_user.name from sc_user where sc_user.login_id = sc_amazon_account_asyn.creator ) as USERNAME
				FROM sc_amazon_account_asyn where account_id = '$accountId'
				and report_type = '_GET_MERCHANT_LISTINGS_DATA_'
				and status = 'complete' order by create_time desc  limit ".$start.",".$limit;
		
		$array = $this->query($sql);
		return $array ;
	}

	function getProductActiveAsynsHistoryCount($query=null,$accountId){
		$sql = "SELECT count(*) FROM sc_amazon_account_asyn where account_id = '$accountId'
				and report_type = '_GET_MERCHANT_LISTINGS_DATA_'
				and status = 'complete'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getProductFeedHistoryRecords($query=null,$accountId){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$sql = "SELECT *,
				( select sc_user.name from sc_user where sc_user.login_id = sc_amazon_account_feed.creator ) as USERNAME
				FROM sc_amazon_account_feed where account_id = '$accountId'
				and type = '_POST_PRODUCT_PRICING_DATA_'
				 order by create_time desc  limit ".$start.",".$limit;
		
		$array = $this->query($sql);
		return $array ;
	}

	function getProductFeedHistoryCount($query=null,$accountId){
		$sql = "SELECT count(*) FROM sc_amazon_account_feed where account_id = '$accountId'
				and type = '_POST_PRODUCT_PRICING_DATA_'
				";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getProductFeedQuantityHistoryRecords($query=null,$accountId){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$sql = "SELECT *,
				( select sc_user.name from sc_user where sc_user.login_id = sc_amazon_account_feed.creator ) as USERNAME
				FROM sc_amazon_account_feed where account_id = '$accountId'
				and type = '_POST_INVENTORY_AVAILABILITY_DATA_'
				 order by create_time desc  limit ".$start.",".$limit;
		
		$array = $this->query($sql);
		return $array ;
	}

	function getProductFeedQuantityHistoryCounts($query=null,$accountId){
		$sql = "SELECT count(*) FROM sc_amazon_account_feed where account_id = '$accountId'
				and type = '_POST_INVENTORY_AVAILABILITY_DATA_'
				";
		$array = $this->query($sql);
		return $array ;
	}
	
	
	//product
	function getProductRecords($query=null , $id = null ){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$accountId = "" ;
		
		$where = " where sc_amazon_account_product.status = 'Y'  " ;
		
		if( isset( $query["title"] )  && !empty( $query["title"]  )){
			$title = $query["title"] ;
			$where .= " and sc_amazon_account_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] )  && !empty( $query["asin"]  )){
			$asin = $query["asin"] ;
			$where .= " and sc_amazon_account_product.asin = '".$asin."' " ;
		}
		
		if( isset( $query["quantity1"] )  && !empty( $query["quantity1"]  )){
			$quantity1 = $query["quantity1"] ;
			$where .= " and sc_amazon_account_product.quantity >= ".$quantity1." " ;
		}
		
		if( isset( $query["quantity2"] )  && !empty( $query["quantity2"]  )){
			$quantity2 = $query["quantity2"] ;
			$where .= " and sc_amazon_account_product.quantity <= ".$quantity2." " ;
		}
		
		if( isset( $query["price1"] )  && !empty( $query["price1"]  )){
			$price1 = $query["price1"] ;
			$where .= " and sc_amazon_account_product.price >= ".price1." " ;
		}
		
		if( isset( $query["price2"] )  && !empty( $query["price2"]  )){
			$price2 = $query["price2"] ;
			$where .= " and sc_amazon_account_product.price <= ".$price2." " ;
		}
		
		if( isset( $query["itemCondition"] )  && !empty( $query["itemCondition"]  )){
			$itemCondition = $query["itemCondition"] ;
			if($itemCondition == '-'){
				$where .= " and ( sc_amazon_account_product.item_condition is null or sc_amazon_account_product.item_condition = '' )" ;
			}else{
				$where .= " and sc_amazon_account_product.item_condition = '".$itemCondition."' " ;
			}
		}
		
		if( isset( $query["accountId"] ) && !empty( $query["accountId"]  ) ){
			$accountId = $query["accountId"] ;
			$where .= " and  sc_amazon_account_product.account_id = '$accountId' " ;
			
			if( isset( $query["categoryId"] )  && !empty( $query["categoryId"]  )){
				$categoryId =  $query["categoryId"] ;
				if($categoryId == '-'){
					$where .= " and sc_amazon_account_product.asin not in (
								select asin from sc_amazon_product_category_rel
					) " ;
				}else{
					$where .= " and sc_amazon_account_product.asin in (
								select asin from sc_amazon_product_category_rel where category_id = '$categoryId'
					) " ;
				}
			}
		}
		
		
		if( isset( $query["fulfillmentChannel"] )  && !empty( $query["fulfillmentChannel"]  )){
			$fulfillmentChannel = $query["fulfillmentChannel"] ;
			if($fulfillmentChannel == '-'){
				$where .= " and ( sc_amazon_account_product.fulfillment_channel is null or sc_amazon_account_product.fulfillment_channel = '' )" ;
			}else{
				$where .= " and sc_amazon_account_product.fulfillment_channel  like '%".$fulfillmentChannel."%' " ;
			}
		}
		
		if( isset( $query["isFM"] )  && !empty( $query["isFM"]  )){
			$isFM = $query["isFM"] ;
			$where .= " and sc_amazon_account_product.IS_FM = '".$isFM."' " ;
		}
		
		if( isset( $query["type"] )  && !empty( $query["type"]  )){
			$type = $query["type"] ;
			if($type == "price" ){
				$where .= " and ( sc_amazon_account_product.feed_price <> '' and sc_amazon_account_product.feed_price is not null ) " ;
			}else if($type == "quantity" ){
				$where .= " and ( sc_amazon_account_product.feed_quantity <> '' and sc_amazon_account_product.feed_quantity is not null ) " ;
			}
		}
		
		
		if( isset( $query["pm"] )  && !empty( $query["pm"]  )){
			$pm = $query["pm"] ;
			if($pm == 'other'){
				$where .= " and ( sc_amazon_account_product.F_PM = '0' and sc_amazon_account_product.N_PM = '0'
					 and sc_amazon_account_product.U_PM = '0'  and sc_amazon_account_product.FBA_PM = '0' ) " ;
			}else{
				$where .= " and ( sc_amazon_account_product.F_PM = '".$pm."' or sc_amazon_account_product.N_PM = '".$pm."'
					 or sc_amazon_account_product.U_PM = '".$pm."'  or sc_amazon_account_product.FBA_PM = '".$pm."' ) " ;
			}
		}
		
		//询价状态  最低价 FBM TARGET_PRICE ， FBA最低价
		$sql = "
		      select t1.* from ( 
		              SELECT  sc_amazon_account_product.*
						FROM sc_amazon_account_product
					$where 
		            order by cast(sc_amazon_account_product.DAY_PAGEVIEWS as signed) desc
			  ) t1
			limit ".$start.",".$limit;
		//print_r( $sql ) ;
		$array = $this->query($sql);
		return $array ;
	}

	function getProductCount($query=null , $id = null){
		$where = " where sc_amazon_account_product.status = 'Y'  " ;
		$accountId = "" ;
		
		$where = " where sc_amazon_account_product.status = 'Y'  " ;
		
		if( isset( $query["title"] )  && !empty( $query["title"]  )){
			$title = $query["title"] ;
			$where .= " and sc_amazon_account_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] )  && !empty( $query["asin"]  )){
			$asin = $query["asin"] ;
			$where .= " and sc_amazon_account_product.asin = '".$asin."' " ;
		}
		
		if( isset( $query["quantity1"] )  && !empty( $query["quantity1"]  )){
			$quantity1 = $query["quantity1"] ;
			$where .= " and sc_amazon_account_product.quantity >= ".$quantity1." " ;
		}
		
		if( isset( $query["quantity2"] )  && !empty( $query["quantity2"]  )){
			$quantity2 = $query["quantity2"] ;
			$where .= " and sc_amazon_account_product.quantity <= ".$quantity2." " ;
		}
		
		if( isset( $query["price1"] )  && !empty( $query["price1"]  )){
			$price1 = $query["price1"] ;
			$where .= " and sc_amazon_account_product.price >= ".price1." " ;
		}
		
		if( isset( $query["price2"] )  && !empty( $query["price2"]  )){
			$price2 = $query["price2"] ;
			$where .= " and sc_amazon_account_product.price <= ".$price2." " ;
		}
		
		if( isset( $query["itemCondition"] )  && !empty( $query["itemCondition"]  )){
			$itemCondition = $query["itemCondition"] ;
			if($itemCondition == '-'){
				$where .= " and ( sc_amazon_account_product.item_condition is null or sc_amazon_account_product.item_condition = '' )" ;
			}else{
				$where .= " and sc_amazon_account_product.item_condition = '".$itemCondition."' " ;
			}
		}
		
		if( isset( $query["accountId"] ) && !empty( $query["accountId"]  ) ){
			$accountId = $query["accountId"] ;
			$where .= " and  sc_amazon_account_product.account_id = '$accountId' " ;
			
			if( isset( $query["categoryId"] )  && !empty( $query["categoryId"]  )){
				$categoryId =  $query["categoryId"] ;
				if($categoryId == '-'){
					$where .= " and sc_amazon_account_product.asin not in (
								select asin from sc_amazon_product_category_rel
					) " ;
				}else{
					$where .= " and sc_amazon_account_product.asin in (
								select asin from sc_amazon_product_category_rel where category_id = '$categoryId'
					) " ;
				}
			}
		}
		
		
		if( isset( $query["fulfillmentChannel"] )  && !empty( $query["fulfillmentChannel"]  )){
			$fulfillmentChannel = $query["fulfillmentChannel"] ;
			if($fulfillmentChannel == '-'){
				$where .= " and ( sc_amazon_account_product.fulfillment_channel is null or sc_amazon_account_product.fulfillment_channel = '' )" ;
			}else{
				$where .= " and sc_amazon_account_product.fulfillment_channel  like '%".$fulfillmentChannel."%' " ;
			}
		}
		
		if( isset( $query["isFM"] )  && !empty( $query["isFM"]  )){
			$isFM = $query["isFM"] ;
			$where .= " and sc_amazon_account_product.IS_FM = '".$isFM."' " ;
		}
		
		if( isset( $query["type"] )  && !empty( $query["type"]  )){
			$type = $query["type"] ;
			if($type == "price" ){
				$where .= " and ( sc_amazon_account_product.feed_price <> '' and sc_amazon_account_product.feed_price is not null ) " ;
			}else if($type == "quantity" ){
				$where .= " and ( sc_amazon_account_product.feed_quantity <> '' and sc_amazon_account_product.feed_quantity is not null ) " ;
			}
		}
		
		
		if( isset( $query["pm"] )  && !empty( $query["pm"]  )){
			$pm = $query["pm"] ;
			if($pm == 'other'){
				$where .= " and ( sc_amazon_account_product.F_PM = '0' and sc_amazon_account_product.N_PM = '0'
					 and sc_amazon_account_product.U_PM = '0'  and sc_amazon_account_product.FBA_PM = '0' ) " ;
			}else{
				$where .= " and ( sc_amazon_account_product.F_PM = '".$pm."' or sc_amazon_account_product.N_PM = '".$pm."'
					 or sc_amazon_account_product.U_PM = '".$pm."'  or sc_amazon_account_product.FBA_PM = '".$pm."' ) " ;
			}
		}
		
		$sql = "
		      select count(*) from ( 
		              SELECT  sc_amazon_account_product.*
						FROM sc_amazon_account_product
					$where 
			  ) t1 ";
		
		$array = $this->query($sql);
		return $array ;
	}
	
	//seller
	function getConfigRecords($query=null){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$sql = "SELECT * 
		FROM sc_amazon_config limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getConfigCount($query=null){
		$sql = "SELECT count(*) FROM sc_amazon_config";
		$array = $this->query($sql);
		return $array ;
	}
	
	
	//product
	function getProductReplyRecords($query=null , $id = null ){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$accountId = "" ;
		
		$where = " where sc_amazon_account_product.status = 'Y'  " ;
		
		if( isset( $query["accountId"] ) && !empty( $query["accountId"]  ) ){
			$accountId = $query["accountId"] ;
			$where .= " and  sc_amazon_account_product.account_id = '$accountId' " ;
		}
		
		$where1 = " where 1=1 " ;
		
		//询价状态  最低价 FBM TARGET_PRICE ， FBA最低价
		$sql = "
		      select t1.* from (  
		         
		          select t.*
		          from (
		              SELECT  sc_amazon_account_product.*
						FROM sc_amazon_account_product
						$where  and exists (
							SELECT g1.ASIN FROM ( 
						        SELECT COUNT(sku) AS c,ASIN,fulfillment_channel FROM sc_amazon_account_product
						       WHERE account_id = '$accountId' AND STATUS = 'Y' GROUP BY ASIN ,FULFILLMENT_CHANNEL 
						       ) g1 WHERE g1.c > 1  
						  AND g1.asin = sc_amazon_account_product.asin AND 
						 sc_amazon_account_product.fulfillment_channel = g1.fulfillment_channel
					)
		           ) t order by t.asin
			  ) t1
             $where1
			limit ".$start.",".$limit;
		//print_r( $sql ) ;
		$array = $this->query($sql);
		return $array ;
	}

	function getProductReplyCount($query=null , $id = null){
		$where = " where sc_amazon_account_product.status = 'Y'  " ;
		$accountId = "" ;
		
		$where1 = " where 1=1 " ;
		
		if( isset( $query["accountId"] ) && !empty( $query["accountId"]  ) ){
			$accountId = $query["accountId"] ;
			$where .= " and  sc_amazon_account_product.account_id = '$accountId' " ;
		}
		
		$sql = "
		      select count(*) from ( 
		          select t.*
		          from (
		              SELECT  sc_amazon_account_product.*	FROM sc_amazon_account_product
					$where  and exists (
						SELECT g1.ASIN FROM ( 
						        SELECT COUNT(sku) AS c,ASIN,fulfillment_channel FROM sc_amazon_account_product
						       WHERE account_id = '$accountId' AND STATUS = 'Y' GROUP BY ASIN ,FULFILLMENT_CHANNEL 
						       ) g1 WHERE g1.c > 1  
						  AND g1.asin = sc_amazon_account_product.asin AND 
						 sc_amazon_account_product.fulfillment_channel = g1.fulfillment_channel
		         )  ) t 
			  ) t1
             $where1 " ;
		$array = $this->query($sql);
		return $array ;
	}
}