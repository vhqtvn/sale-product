<?php
App::import('Model', 'Utils') ;
App::import('Model', 'Log') ;

class GatherService extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	/**
	 * 产品保存
	 */
	function updateProduct($array){
		$t = $this->formatSqlParams( htmlentities( $array['title'], ENT_QUOTES) ) ;
		$te= $this->formatSqlParams( htmlentities( $array['TECHDETAILS'], ENT_QUOTES)  ) ;
		$d = $this->formatSqlParams( htmlentities( $array['DESCRIPTION'], ENT_QUOTES)  ) ;
		$p = $this->formatSqlParams( htmlentities( $array['PRODUCTDETAILS'], ENT_QUOTES)  ) ;
		$DIMENSIONS = htmlentities( $array['DIMENSIONS'], ENT_QUOTES) ;
		$WEIGHT = htmlentities( $array['WEIGHT'], ENT_QUOTES) ;
		$BRAND = $this->formatSqlParams( htmlentities( $array['BRAND'], ENT_QUOTES)  ) ;
		$a = $array['asin'] ;
		
		$sql = "select * from sc_product where asin =  '".$a."'" ;
		$product = $this->query($sql) ;
		if( count($product)<=0 ){
			$sql = "insert into sc_product(asin,PRODUCTDETAILS,TECHDETAILS,title,DESCRIPTION,DIMENSIONS,BRAND,WEIGHT,COMMITTIME) 
					values('".$a."','".$p."' ,'".$te."' ,'".$t."' ,'".$d."' , '".$DIMENSIONS."' ,'".$BRAND."' ,'".$WEIGHT."',NOW())" ;
		}else{
			$sql = "update sc_product set  
					PRODUCTDETAILS = '".$p."' ,  
					TECHDETAILS = '".$te."' , 
					title = '".$t."' , 
					DESCRIPTION = '".$d."' , 
					DIMENSIONS = '".$DIMENSIONS."' , 
					BRAND = '".$BRAND."' , 
					WEIGHT = '".$WEIGHT."',
					COMMITTIME = NOW()
					where asin = '".$a."'" ;
		}

		$this->query($sql) ;
	}
	
	/**
	 * 保存图片信息
	 */
	function addImage($asin , $url,$title,$localUrl){
		$sql = "select * from sc_product_imgs where asin = '$asin' and url = '$url' and ISNULL(local_url)" ;
		$array = $this->query($sql) ;
		
		if( count( $array ) >= 1 ){
			$sql = "update sc_product_imgs set local_url = '$localUrl' where asin = '$asin' and url = '$url'  " ;
			$this->query($sql) ;
		}else{
			$sql = "insert into sc_product_imgs(asin,url,title,local_url) values('".$asin."','".$url."','".$title."','".$localUrl."')" ;
			$this->query($sql) ;
		}
	}
	
	/**
	 * 销售潜力
	 */
	 function saveSalePotential( $asin , $reviewNum , $qualityPoints ,$rankArray ){
	 	//保存到历史表
	 	$sql = "insert into sc_sale_potential_history select * from sc_sale_potential where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	//删除实时表数据
	 	$sql = "delete from sc_sale_potential where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	//插入新数据
	 	$sql = "insert into sc_sale_potential(ASIN,REVIEWS_NUM,QUALITY_POINTS,PICK_TIME) values('".$asin."','".$reviewNum."','".$qualityPoints."',NOW())" ;
	 	$this->query($sql) ;
	 	
	 	$sql = "select id from sc_sale_potential where asin ='".$asin."'" ;
	 	$result = $this->query($sql) ;
	 	
	 	$id = $result[0]['sc_sale_potential']['id'] ;
	 	
	 	//排名表
	 	$sql = "insert into sc_sale_potential_ranking_history select * from sc_sale_potential_ranking where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	//删除实时表数据
	 	$sql = "delete from sc_sale_potential_ranking where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	foreach(  $rankArray as $rank ){
	 		$ranking = $rank["rank"] ;
	 		$type  = $rank["type"] ;
	 		
	 		$sql = "insert into sc_sale_potential_ranking(ASIN,RANKING,TYPE,POTENTIAL_ID)
				values('".$asin."','".$ranking."','".$type."','".$id."')" ;
	 		$this->query($sql) ;
	 	}
	 	
	 }

	 /**
	  * 保存竞争信息
	  */
	 function saveCompetions($asin,$base , $details ){
	 	$utils = new Utils() ;
		$log = new Log() ;
		
	 	//保存到历史表
	 	$sql = "insert into sc_sale_competition_history select * from sc_sale_competition where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	//删除实时表数据
	 	$sql = "delete from sc_sale_competition where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	$fmNum = trim( $base["FM_NUM"] ) ;
	 	$nmNum = trim( $base["NM_NUM"] ) ;
	 	$umNum = trim( $base["UM_NUM"] ) ;
	 	$totalNum = $fmNum + $nmNum + $umNum ;
	 	$sql = "insert into sc_sale_competition(asin,fm_num,nm_num,um_num,total_num,target_price,create_time)
				values('".$asin."','".$fmNum."','".$nmNum."','".$umNum."','".$totalNum."','',NOW())" ;
		$this->query($sql) ;
		
		$sql = "select id from sc_sale_competition where asin ='".$asin."'" ;
	 	$result = $this->query($sql) ;
	 	
	 	$id = $result[0]['sc_sale_competition']['id'] ;
	 	
	 	//insert into sc_sale_competition_details
	 	//保存到历史表
	 	$sql = "insert into sc_sale_competition_details_history select * from sc_sale_competition_details where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	//删除实时表数据
	 	$sql = "delete from sc_sale_competition_details where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	$targetPrice = 0 ;
	 	
	 	foreach($details as $detail){
	 		$sn = $detail["SELLER_NAME"] ;
	 		
	 		$sn = trim(str_replace(array("'"),"\'",$sn)) ;
			
	 		$sp = $detail["SELLER_PRICE"] ;
	 		$sp = trim(str_replace(array("+","$"),"",$sp)) ;
	 		
	 		$ssp = $detail["SELLER_SHIP_PRICE"] ;
	 		$ssp = trim(str_replace(array("+","$"),"",$ssp)) ;
	 		$type = $detail["TYPE"] ;
	 		if( strpos($type, "F") === 0 || strpos($type, "N") === 0){
	 			if( $targetPrice== 0 ){
	 				$targetPrice = $sp+$ssp ;
	 			}else{
	 				$targetPrice = min($targetPrice,$sp+$ssp) ;
	 			}
	 		}
	 		
	 		$su = $detail["SELLER_URL"] ;
	 		$si = $detail["SELLER_IMG"] ;
	 		
	 		$per_positive = $detail["PER_POSITIVE"] ;
	 		$total_rating = $detail["TOTAL_RATING"] ;
	 		$country = $detail["COUNTRY"] ;
	 		
	 		$localUrl = "" ;
	 		if( $si == null || $si == "" ){
	 			
	 		}else
	 			$localUrl = "images/seller/".basename($si) ;
	 		
	 		$sql = "insert into sc_sale_competition_details(
						asin,seller_name,seller_price,seller_ship_price,seller_url,seller_img,competition_id,type,
						per_positive,total_rating,country
				) values('".$asin."','".$sn."','".$sp."','".$ssp."','".$su."','".$localUrl."'
					,'".$id."','".$type."','".$per_positive."','".$total_rating."','".$country."')" ;
			
			$this->query($sql) ;
			try{
				$utils->downloads($si,"seller","images/seller") ;
			}catch(Exception $e){}
	 	} ;
	 	
	 	//update targetprice
	 	$sql = "update sc_sale_competition set target_price = '".$targetPrice."' where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 }
	
	 /**
	  * 保存FBA竞争信息
	  */
	 function saveFba($asin,$base , $details ){
	 	$utils = new Utils() ;
		$log = new Log() ;
		
	 	//保存到历史表
	 	$sql = "insert into sc_sale_fba_history select * from sc_sale_fba where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	//删除实时表数据
	 	$sql = "delete from sc_sale_fba where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	$fbaNum = trim( $base["FBA_NUM"] ) ;
	 	$totalNum = $fbaNum  ;
	 	$sql = "insert into sc_sale_fba(asin,fba_num,total_num,target_price,create_time)
				values('".$asin."','".$fbaNum."','".$totalNum."','',NOW())" ;
		$this->query($sql) ;
		
		$sql = "select id from sc_sale_fba where asin ='".$asin."'" ;
	 	$result = $this->query($sql) ;
	 	
	 	$id = $result[0]['sc_sale_fba']['id'] ;
	 	
	 	//insert into sc_sale_competition_details
	 	//保存到历史表
	 	$sql = "insert into sc_sale_fba_details_history select * from sc_sale_fba_details where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	//删除实时表数据
	 	$sql = "delete from sc_sale_fba_details where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 	
	 	$targetPrice = 0 ;
	 	
	 	foreach($details as $detail){
	 		$sn = $detail["SELLER_NAME"] ;
	 		
	 		$sn = trim(str_replace(array("'"),"\'",$sn)) ;
			
	 		$sp = $detail["SELLER_PRICE"] ;
	 		$sp = trim(str_replace(array("+","$"),"",$sp)) ;
	 		
	 		$ssp = $detail["SELLER_SHIP_PRICE"] ;
	 		$ssp = trim(str_replace(array("+","$"),"",$ssp)) ;
	 		$type = $detail["TYPE"] ;
	 		if( strpos($type, "F") === 0 || strpos($type, "N") === 0){
	 			if( $targetPrice== 0 ){
	 				$targetPrice = $sp+$ssp ;
	 			}else{
	 				$targetPrice = min($targetPrice,$sp+$ssp) ;
	 			}
	 		}
	 		
	 		$su = $detail["SELLER_URL"] ;
	 		$si = $detail["SELLER_IMG"] ;
	 		
	 		$localUrl = "" ;
	 		if( $si == null || $si == "" ){
	 			
	 		}else
	 			$localUrl = "images/seller/".basename($si) ;
	 		
	 		$sql = "insert into sc_sale_fba_details(
						asin,seller_name,seller_price,seller_ship_price,seller_url,seller_img,competition_id,type
				) values('".$asin."','".$sn."','".$sp."','".$ssp."','".$su."','".$localUrl."','".$id."','".$type."')" ;
			
			$this->query($sql) ;
			
			try{
				$utils->downloads($si,"seller","images/seller") ;
			}catch(Exception $e){}
	 	} ;
	 	
	 	//update targetprice
	 	$sql = "update sc_sale_competition set target_price = '".$targetPrice."' where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 }

	 /**
	  * 更新获取到的价格
	  */
	 function updateAmazonProductShipping($asin,$id,$records){
		$renderType = "" ;
		$index = 0 ;
		
		$sql = "select * from sc_amazon_account_product where ACCOUNT_ID = '$id' and fulfillment_channel = 'Merchant'
			and ASIN = '$asin' and item_condition='11' " ;
		$items = $this->query($sql) ;
		
		foreach($records as $record){
			$isFBA =  $record['isFBA'] ;
			$plusShippingText = $record['plusShippingText'] ;
			$price = $record['priceText'] ;
			
			$where = "" ;
			if($isFBA == 'fba'){
				$renderType = "fba" ;
				$where .= " fulfillment_channel like 'AMAZON%' and " ;
			}else{
				if($renderType == 'common') $index++ ;
				$renderType = "common" ;
				$where .= " fulfillment_channel = 'Merchant' and " ;
			}
			
			$isFM =  $record['isFM'] ;
			$condition = $record['condition'] ;
			
			if( $index == 0 ){
				$sql = "
					UPDATE sc_amazon_account_product 
						SET 
						SHIPPING_PRICE = '$plusShippingText' ,
						PRICE = '$price',
						IS_FM = '$isFM'
						WHERE $where
						ACCOUNT_ID = '$id' and
						ASIN = '$asin' and
						item_condition='$condition'" ;
				 $this->query($sql) ;
			}else if($index == 1 ){
				if( count($items) >=2 ){
					$item = $items[1]['sc_amazon_account_product'] ;
					$sql = "
						UPDATE sc_amazon_account_product 
							SET 
							SHIPPING_PRICE = '$plusShippingText' ,
							PRICE = '$price',
							IS_FM = '$isFM'
							WHERE ID = '".$item["ID"]."'" ;
					 $this->query($sql) ;
				}
			}else if($index == 2 ){
				if( count($items) >=3 ){
					$item = $items[2]['sc_amazon_account_product'] ;
					$sql = "
						UPDATE sc_amazon_account_product 
							SET 
							SHIPPING_PRICE = '$plusShippingText' ,
							PRICE = '$price',
							IS_FM = '$isFM'
							WHERE ID = '".$item["ID"]."'" ;
					 $this->query($sql) ;
				}
			}
		} ;
	}
	
	/**
	 * 保存上传或通过URL获取的产品
	 */
	function saveGatherAsin($id ,$asin){
		$sql = "insert into sc_gather_asin(task_id,asin) values('".$id."','".$asin."' )" ;
		$this->query($sql) ;
	}
	
	function getSellerUrl($id){
			$sql =  "select url from sc_seller where id  = ".$id;
			return $this->query($sql );
	}
	
	function listTaskAsins($taskId){
		$sql = "select asin from sc_gather_asin where task_id = '".$taskId."'" ;
		return $this->query($sql) ;
	}
	
	function clearGatherAsin($taskId){
		$sql = "delete from sc_gather_asin where task_id = '".$taskId."'" ;
		$this->query($sql) ;
	}
	
	
	/**
	 * 保存上传
	 */
	function saveUpload($id ,$fileName,$groupId,$user){
		$loginId = $user["LOGIN_ID"] ;
		$sql = "insert into sc_upload(id , name,group_id,upload_time,uploador) values('".$id."','".$fileName."','$groupId',NOW(),'$loginId')" ;
		$this->query($sql) ;
	}
}