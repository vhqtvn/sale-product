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
		
		$where = " where 1=1  " ;
		
		if( isset( $query["title"] )  && !empty( $query["title"]  )){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] )  && !empty( $query["asin"]  )){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
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
		
		
		$where1 = " where 1=1 " ;
		if( isset( $query["pm"] )  && !empty( $query["pm"]  )){
			$pm = $query["pm"] ;
			if($pm == 'other'){
				$where1 .= " and ( t1.F_PM = '0' and t1.N_PM = '0'  and t1.U_PM = '0'  and t1.FBA_PM = '0' ) " ;
			}else{
				$where1 .= " and ( t1.F_PM = '".$pm."' or t1.N_PM = '".$pm."'  or t1.U_PM = '".$pm."'  or t1.FBA_PM = '".$pm."' ) " ;
			}
		}
		
		//询价状态  最低价 FBM TARGET_PRICE ， FBA最低价
		$sql = "
		      select t1.* from (  
		         
		          select t.*,
						(SELECT COUNT(*) FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = t.asin AND sc_sale_competition_details.type LIKE 'F%'
							AND sc_sale_competition_details.ID <= t.f_index ) AS F_PM,
						(SELECT COUNT(*) FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = t.asin AND sc_sale_competition_details.type LIKE 'N%'
							AND sc_sale_competition_details.ID <= t.n_index ) AS N_PM,
						(SELECT COUNT(*) FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = t.asin AND sc_sale_competition_details.type LIKE 'U%'
							AND sc_sale_competition_details.ID <= t.u_index ) AS U_PM,
						(SELECT COUNT(*) FROM sc_sale_fba_details 
							WHERE sc_sale_fba_details.asin = t.asin 
							AND sc_sale_fba_details.ID <= t.fba_index ) AS FBA_PM
		          from (
		              SELECT  sc_amazon_account_product.*,
						 sc_product.TITLE , sc_product_flow_details.DAY_PAGEVIEWS ,
						(select sc_amazon_config.label from sc_amazon_config where sc_amazon_account_product.strategy = sc_amazon_config.name ) as STRATEGY_LABEL  ,
		                ( SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL,
						( SELECT TOTAL_COST 
						from sc_product_cost where sc_product_cost.asin = sc_product.asin and
							( sc_product_cost.type='FBM' or sc_product_cost.type is null )  ) as FBM_COST,
						( select min(sc_sale_competition_details.seller_price + sc_sale_competition_details.seller_ship_price ) from sc_sale_competition_details
								where sc_sale_competition_details.asin = sc_product.asin and sc_sale_competition_details.type like 'F%'  ) as FBM_F_PRICE,
						( select min(sc_sale_competition_details.seller_price + sc_sale_competition_details.seller_ship_price) from sc_sale_competition_details
								where sc_sale_competition_details.asin = sc_product.asin and sc_sale_competition_details.type like 'N%'  ) as FBM_N_PRICE,
						( select min(sc_sale_competition_details.seller_price + sc_sale_competition_details.seller_ship_price) from sc_sale_competition_details
								where sc_sale_competition_details.asin = sc_product.asin and sc_sale_competition_details.type like 'U%'  ) as FBM_U_PRICE,
						( select min(sc_sale_fba_details.seller_price) from sc_sale_fba_details
								where sc_sale_fba_details.asin = sc_product.asin ) as FBA_PRICE,
		
						(SELECT ID FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = sc_product.asin AND sc_sale_competition_details.type LIKE 'F%'
							AND sc_sale_competition_details.seller_name = 'Cyberkin' ) AS f_index,
						(SELECT ID FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = sc_product.asin AND sc_sale_competition_details.type LIKE 'N%'
							AND sc_sale_competition_details.seller_name = 'Cyberkin' ) AS n_index,
						(SELECT ID FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = sc_product.asin AND sc_sale_competition_details.type LIKE 'U%'
							AND sc_sale_competition_details.seller_name = 'Cyberkin' ) AS u_index,
						(SELECT ID FROM sc_sale_fba_details 
							WHERE sc_sale_fba_details.asin = sc_product.asin 
							AND sc_sale_fba_details.seller_name = 'Cyberkin' ) AS fba_index,
		
		
						( SELECT TOTAL_COST 
						from sc_product_cost where sc_product_cost.asin = sc_product.asin and
							( sc_product_cost.type='FBA' or sc_product_cost.type is null )  ) as FBA_COST,
						(select count(*) from sc_product_supplier where sc_product_supplier.asin = sc_product.asin
									and sc_product_supplier.num1 is not null and  sc_product_supplier.offer1 is not null  ) as XJ 
						FROM sc_amazon_account_product
						LEFT JOIN sc_product on sc_product.asin = sc_amazon_account_product.asin
					   left join sc_product_flow_details on sc_product_flow_details.asin = sc_amazon_account_product.asin
						LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_amazon_account_product.asin
					$where 
		           ) t order by cast(t.DAY_PAGEVIEWS as signed) desc
			  ) t1
             $where1
			limit ".$start.",".$limit;
		//print_r( $sql ) ;
		$array = $this->query($sql);
		return $array ;
	}

	function getProductCount($query=null , $id = null){
		$where = " where 1=1  " ;
		
		if( isset( $query["title"] )  && !empty( $query["title"]  )){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
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
		
		if( isset( $query["fulfillmentChannel"] )  && !empty( $query["fulfillmentChannel"]  )){
			$fulfillmentChannel = $query["fulfillmentChannel"] ;
			if($fulfillmentChannel == '-'){
				$where .= " and ( sc_amazon_account_product.fulfillment_channel is null or sc_amazon_account_product.fulfillment_channel = '' )" ;
			}else{
				$where .= " and sc_amazon_account_product.fulfillment_channel like '%".$fulfillmentChannel."%' " ;
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
		
		
		$where1 = " where 1=1 " ;
		if( isset( $query["pm"] )  && !empty( $query["pm"]  )){
			$pm = $query["pm"] ;
			if($pm == 'other'){
				$where1 .= " and ( t1.F_PM = '0' and t1.N_PM = '0'  and t1.U_PM = '0'  and t1.FBA_PM = '0' ) " ;
			}else{
				$where1 .= " and ( t1.F_PM = '".$pm."' or t1.N_PM = '".$pm."'  or t1.U_PM = '".$pm."'  or t1.FBA_PM = '".$pm."' ) " ;
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
		
		$sql = "
		      select count(*) from (  
		         
		          select t.*,
						(SELECT COUNT(*) FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = t.asin AND sc_sale_competition_details.type LIKE 'F%'
							AND sc_sale_competition_details.ID <= t.f_index ) AS F_PM,
						(SELECT COUNT(*) FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = t.asin AND sc_sale_competition_details.type LIKE 'N%'
							AND sc_sale_competition_details.ID <= t.n_index ) AS N_PM,
						(SELECT COUNT(*) FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = t.asin AND sc_sale_competition_details.type LIKE 'U%'
							AND sc_sale_competition_details.ID <= t.u_index ) AS U_PM,
						(SELECT COUNT(*) FROM sc_sale_fba_details 
							WHERE sc_sale_fba_details.asin = t.asin 
							AND sc_sale_fba_details.ID <= t.fba_index ) AS FBA_PM
		          from (
		              SELECT  sc_amazon_account_product.*,
						(SELECT ID FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = sc_amazon_account_product.asin AND sc_sale_competition_details.type LIKE 'F%'
							AND sc_sale_competition_details.seller_name = 'Cyberkin' ) AS f_index,
						(SELECT ID FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = sc_amazon_account_product.asin AND sc_sale_competition_details.type LIKE 'N%'
							AND sc_sale_competition_details.seller_name = 'Cyberkin' ) AS n_index,
						(SELECT ID FROM sc_sale_competition_details 
							WHERE sc_sale_competition_details.asin = sc_amazon_account_product.asin AND sc_sale_competition_details.type LIKE 'U%'
							AND sc_sale_competition_details.seller_name = 'Cyberkin' ) AS u_index,
						(SELECT ID FROM sc_sale_fba_details 
							WHERE sc_sale_fba_details.asin = sc_amazon_account_product.asin 
							AND sc_sale_fba_details.seller_name = 'Cyberkin' ) AS fba_index
		
						FROM sc_amazon_account_product
					$where 
		           ) t 
			  ) t1
             $where1 " ;
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
}