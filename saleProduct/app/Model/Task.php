<?php
ignore_user_abort(1);
set_time_limit(0);

class Task extends AppModel {
	var $useTable = "sc_election_rule" ;

	/*
	function saveAsin( $productName ){
		if(empty($productName)) return ;

		$sql =  "insert into sc_product(asin) values('".$productName."')" ;
		$this->query($sql );
	}*/

	function getSellerUrl($id){
			$sql =  "select url from sc_seller where id  = ".$id;
			return $this->query($sql );
	}
	
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
	
	function updateAmazonProduct($array,$code,$id){
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
		} ;
		
		$this->query($sql) ;
		
		//update sc_amazon_account_product
		/*$listPriceValue = htmlentities( $array['listPriceValue'], ENT_QUOTES) ;
		$actualPriceValue = htmlentities( $array['actualPriceValue'], ENT_QUOTES) ;
		$plusShippingText = htmlentities( $array['plusShippingText'], ENT_QUOTES) ;
		
		$sql = "
			UPDATE sc_amazon_account_product 
				SET 
				LIST_PRICE = '".$listPriceValue."' , 
				PRICE = '$actualPriceValue' , 
				SHIPPING_PRICE = '$plusShippingText' 
				WHERE
				ACCOUNT_ID = '$id' and
				ASIN = '$a' " ;
		
		 $this->query($sql) ;*/
		
	}
	
	function updateAmazonProductShipping($array,$code,$id){
		$sku = $array['sku'] ;
		
		$plusShippingText = htmlentities( $array['plusShippingText'], ENT_QUOTES) ;
		$isFM =  $array['isFM']  ;
		$price = htmlentities( $array['priceText'], ENT_QUOTES) ;
		
		if( !empty($sku) && ( !empty($isFM) || !empty($plusShippingText)) ){
			$sql = "
				UPDATE sc_amazon_account_product 
					SET 
					SHIPPING_PRICE = '$plusShippingText' ,
					PRICE = '$price',
					IS_FM = '$isFM'
					WHERE
					ACCOUNT_ID = '$id' and
					SKU = '$sku' " ;
			
			 $this->query($sql) ;
		}
	}
	
	
	
	function getProp($html , $selector){
		$dom = $html->find($selector,0) ;
		if($dom == null)
			return "" ;
		return trim($dom->plaintext) ;
	}
	
	function savelog($taskId, $message){
		$message = $this->formatSqlParams($message) ;
		$sql = "insert into sc_exe_log(task_id,message) values('".$taskId."','".$message."')" ;
		$this->query($sql) ;
	}
	
	function clearlog($taskId){
		$sql = "delete from sc_exe_log where task_id = '".$taskId."'" ;
		$this->query($sql) ;
	}
	
	function getLogs($taskId){
		//delete
		$sql = "delete from sc_exe_log where task_id = '".$taskId."' and status = 'read'" ;
		$this->query($sql) ;
		
		$sql = "update sc_exe_log set status='read' where task_id = '".$taskId."'" ;
		$this->query($sql) ;
		
		$sql = "select * from sc_exe_log where task_id = '".$taskId."' and status = 'read'" ;
		return $this->query($sql) ;
	}
	
	function saveFlowUpload($id ,$fileName , $user,$startTime ,$endTime ,$Days  ) {
		$loginId = $user["LOGIN_ID"] ;
		$sql = "insert into sc_product_flow(id , name,create_time,creator,start_time,end_time,days)
                values('".$id."','".$fileName."',NOW(),'$loginId','$startTime','$endTime',$Days )" ;
		$this->query($sql) ;
	}
	
	function saveFlowDetails($id ,$lineData,$loginId,$Days){
		//解析列
		//选择对应的ASIN插入到历史表
		//保存到历史表
	 	$sql = "insert into sc_product_flow_details_history select * from sc_product_flow_details where asin = '".$lineData["ASIN"]."'" ;
	 	$this->query($sql) ;
	 	
	 	//删除实时表数据
	 	$sql = "delete from sc_product_flow_details where asin = '".$lineData["ASIN"]."'" ;
	 	$this->query($sql) ;
		
		//插入最新记录
		//计算每天情况
		$pageviews = str_replace(",","",$lineData["PAGEVIEWS"]) ;
		$dayPageViews = $pageviews/$Days ;
		
		$dayUnitOrdered = $lineData["UNITS_ORDERED"]/$Days ;
		
		$sql = " INSERT INTO  sc_product_flow_details 
				(TASK_ID, 
				ASIN, 
				TITLE, 
				PAGEVIEWS, 
				PAGEVIEWS_PERCENT, 
				BUY_BOX_PERCENT, 
				UNITS_ORDERED, 
				ORDERED_PRODUCT_SALES, 
				ORDERS_PLACED, 
				CREATOR, 
				CREATTIME,
				DAY_PAGEVIEWS,
                DAY_UNITS_ORDERED
				)
				VALUES
				(
				'$id', 
				'".$lineData["ASIN"]."', 
				'".$lineData["TITLE"]."', 
				'".$lineData["PAGEVIEWS"]."', 
				'".$lineData["PAGEVIEWS_PERCENT"]."', 
				'".$lineData["BUY_BOX_PERCENT"]."', 
				'".$lineData["UNITS_ORDERED"]."', 
				'".$lineData["ORDERED_PRODUCT_SALES"]."', 
				'".$lineData["ORDERS_PLACED"]."', 
				'$loginId', 
				NOW(),
				$dayPageViews,
				$dayUnitOrdered
				) " ;
		$this->query($sql) ;
	}
	
	function saveUpload($id ,$fileName,$groupId,$user){
		$loginId = $user["LOGIN_ID"] ;
		$sql = "insert into sc_upload(id , name,group_id,upload_time,uploador) values('".$id."','".$fileName."','$groupId',NOW(),'$loginId')" ;
		$this->query($sql) ;
	}
	
	
	/**
	 * 保存amazon账户产品信息
	 */
	function saveAmazonAsin($id ,$asin){
		try{
		$sql = "insert into sc_amazon_account_product(account_id,asin) values('".$id."','".$asin."' )" ;
		$this->query($sql) ;
		}catch(Exception $e){}
	}
	
	function saveGatherAsin($id ,$asin){
		$sql = "insert into sc_gather_asin(task_id,asin) values('".$id."','".$asin."' )" ;
		$this->query($sql) ;
	}
	
	function listTaskAsins($taskId){
		$sql = "select asin from sc_gather_asin where task_id = '".$taskId."'" ;
		return $this->query($sql) ;
	}
	
	function clearGatherAsin($taskId){
		$sql = "delete from sc_gather_asin where task_id = '".$taskId."'" ;
		$this->query($sql) ;
	}
	
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
	 
	 function saveCompetions($asin,$base , $details ){
	 	
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
	 		
	 		$localUrl = "" ;
	 		if( $si == null || $si == "" ){
	 			
	 		}else
	 			$localUrl = "images/seller/".basename($si) ;
	 		
	 		$sql = "insert into sc_sale_competition_details(
						asin,seller_name,seller_price,seller_ship_price,seller_url,seller_img,competition_id,type
				) values('".$asin."','".$sn."','".$sp."','".$ssp."','".$su."','".$localUrl."','".$id."','".$type."')" ;
			
			$this->downloads($si,"seller","images/seller") ;
				
			$this->query($sql) ;
	 	} ;
	 	
	 	//update targetprice
	 	$sql = "update sc_sale_competition set target_price = '".$targetPrice."' where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 }
	 
	 function saveFba($asin,$base , $details ){
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
			
			$this->downloads($si,"seller","images/seller") ;
				
			$this->query($sql) ;
	 	} ;
	 	
	 	//update targetprice
	 	$sql = "update sc_sale_competition set target_price = '".$targetPrice."' where asin = '".$asin."'" ;
	 	$this->query($sql) ;
	 }
	 
	 
	function downloads( $url =null , $asin=null , $local = null  ){
		
		if( $url == null || $url == "" ) return ;
		
		ini_set('user_agent','MSIE 4\.0b2;'); 
		ini_set('user_agent','Mozilla: (compatible; Windows XP)');
		
		$file =  fopen ($url, "rb");
		
		$path = dirname(dirname(dirname(__FILE__)))."/images/amazon/".$asin;
		if($local != null ){
			$path = dirname(dirname(dirname(__FILE__)))."/".$local;
		}
		
		$fullPath =  $path."/".basename($url) ; 
		if( file_exists($fullPath) ) return ;
		
		$this->creatdir($path) ;
		$path = $fullPath ;
		 
		if (!$file) { 
			echo "文件找不到"; 
		} else { 
			//Header("Content-type: application/octet-stream"); 
			//Header("Content-Disposition: attachment; filename=" .basename($url)); 
			//Header("content-Type: text/html; charset=utf-8"); 
			$newf = fopen ($path, "wb");
		    $downlen=0;
		    if ($newf)
				while(!feof($file)) {
			        $data=fread($file, 1024 * 8 );	//默认获取8K
			        $downlen+=strlen($data);	// 累计已经下载的字节数
			        fwrite($newf, $data, 1024 * 8 );
			        ob_flush();
			        flush();
			    }
			    
			if ($file) 
			{
			  fclose($file);
			}
			
			if ($newf) 
			{
			  fclose($newf);
			}
		} 
	}
	
	public function creatdir($path)
	{
		if(!is_dir($path))
		{
			if($this->creatdir(dirname($path)))
			{
				mkdir($path,0777);
				return true;
			}
		}
		else
		{
			return true;
		}
	}
}

