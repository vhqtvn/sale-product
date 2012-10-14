<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App :: import('Vendor', 'Snoopy');
App :: import('Vendor', 'simple_html_dom');
App :: import('Vendor', 'Amazon');

class CronController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Task', 'Config','Amazonaccount');
	
	public function formatAmazonProducts($accountId){

		$where = " where sc_amazon_account_product.status = 'Y'  " ;
		
		$where .= " and  sc_amazon_account_product.account_id = '$accountId' " ;
		$where1 = " where 1=1 " ;

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
		              	SELECT  sc_amazon_account_product.ID,sc_amazon_account_product.ASIN,sc_amazon_account_product.SKU,
						 sc_product.TITLE as TITLE , sc_product_flow_details.DAY_PAGEVIEWS as DAY_PAGEVIEWS,
						( SELECT COUNT(1) FROM sc_sale_competition_details WHERE ASIN = sc_amazon_account_product.asin
								and country <> '' AND country IS NOT NULL  ) AS COUNTRY ,
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
		
						(SELECT sc_sale_competition_details.ID FROM sc_sale_competition_details  , sc_amazon_account
							WHERE sc_sale_competition_details.asin = sc_amazon_account_product.asin AND sc_sale_competition_details.type LIKE 'F%'
								and sc_amazon_account.name = sc_sale_competition_details.seller_name 
							AND sc_amazon_account.id = '$accountId' LIMIT 0,1) AS f_index,
						(SELECT sc_sale_competition_details.ID FROM sc_sale_competition_details  , sc_amazon_account
							WHERE sc_sale_competition_details.asin = sc_amazon_account_product.asin AND sc_sale_competition_details.type LIKE 'N%'
								and sc_amazon_account.name = sc_sale_competition_details.seller_name 
							AND sc_amazon_account.id = '$accountId' LIMIT 0,1) AS n_index,
						(SELECT sc_sale_competition_details.ID FROM sc_sale_competition_details  , sc_amazon_account
							WHERE sc_sale_competition_details.asin = sc_amazon_account_product.asin AND sc_sale_competition_details.type LIKE 'U%'
								and sc_amazon_account.name = sc_sale_competition_details.seller_name 
							AND sc_amazon_account.id = '$accountId' LIMIT 0,1) AS u_index,
						(SELECT sc_sale_fba_details.ID FROM sc_sale_fba_details , sc_amazon_account
							WHERE sc_sale_fba_details.asin = sc_amazon_account_product.asin
								and sc_amazon_account.name = sc_sale_fba_details.seller_name 
							AND sc_amazon_account.id = '$accountId' LIMIT 0,1) AS fba_index,
		
						( SELECT TOTAL_COST 
						from sc_product_cost where sc_product_cost.asin = sc_product.asin and
							( sc_product_cost.type='FBA' or sc_product_cost.type is null )  ) as FBA_COST 
						FROM sc_amazon_account_product
						LEFT JOIN sc_product on sc_product.asin = sc_amazon_account_product.asin
					   left join sc_product_flow_details on sc_product_flow_details.asin = sc_amazon_account_product.asin
						LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_amazon_account_product.asin
					$where 
		           ) t 
			  ) t1
             $where1 " ;
       
		$array = $this->Amazonaccount->query($sql);
		
		/**
		 * Array ( [t1] => Array ( [ID] => 3040 [ASIN] => B001EVL21A [SKU] => 10015B 
		 * [TITLE] => GTMax High Resolution DVI-DVI Male to Male Gold Plated 15 feet Cable 4.5m for HDTV, Plasma, LCD
		 *  [DAY_PAGEVIEWS] => [STRATEGY_LABEL] =>
		 *  [LOCAL_URL] => images/amazon/B001EVL21A/41IuaVtMXJL._SL500_AA280_.jpg [FBM_COST] => [FBM_F_PRICE] => 11.52
		 *  [FBM_N_PRICE] => 26.28 [FBM_U_PRICE] => [FBA_PRICE] => [f_index] => 26172 [n_index] => [u_index] => 
		 * [fba_index] => 
		 * [FBA_COST] => [F_PM] => 3 [N_PM] => 0 [U_PM] => 0 [FBA_PM] => 0 ) ) ﻿
		 */
		foreach($array  as $product){
			$record = $product['t1'] ;
			$sql = "UPDATE sc_amazon_account_product 
						SET 
						TITLE = '".$record['TITLE']."' , 
						LOCAL_URL = '".$record['LOCAL_URL']."' , 
						DAY_PAGEVIEWS = '".$record['DAY_PAGEVIEWS']."' , 
						STRATEGY_LABEL = '".$record['STRATEGY_LABEL']."' , 
						FBM_COST = '".$record['FBM_COST']."' , 
						FBM_F_PRICE = '".$record['FBM_F_PRICE']."' , 
						FBM_N_PRICE = '".$record['FBM_N_PRICE']."' , 
						FBM_U_PRICE = '".$record['FBM_U_PRICE']."' , 
						FBA_PRICE = '".$record['FBA_PRICE']."' , 
						FBA_COST = '".$record['FBA_COST']."' , 
						F_PM = '".$record['F_PM']."' , 
						N_PM = '".$record['N_PM']."' , 
						U_PM = '".$record['U_PM']."' , 
						FBA_PM = '".$record['FBA_PM']."',
						COUNTRY = '".$record['COUNTRY']."'
						
						WHERE
						ID = '".$record['ID']."' 
					" ;
			$this->Amazonaccount->query($sql);
		} 
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	

	public function startAsynAmazonProducts($accountId){
		$accountAsyn = $this->Amazonaccount->getAccountAsyn($accountId,"_GET_FLAT_FILE_OPEN_LISTINGS_DATA_") ;
		$account = $this->Amazonaccount->getAccount($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	$user    = array("LOGIN_ID"=>"cron") ;
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		if( empty($accountAsyn) ){//未开始采集
			$request = $amazon->getProductReport1($accountId) ;
			if( !empty($request) ){
				$this->Amazonaccount->saveAccountAsyn($accountId ,$request , $user) ;
			}
		}else{
			$requestReportId = $accountAsyn[0]["sc_amazon_account_asyn"]["REPORT_REQUEST_ID"] ;
			$reportId = $accountAsyn[0]["sc_amazon_account_asyn"]["REPORT_ID"] ;
			$status = $accountAsyn[0]["sc_amazon_account_asyn"]["STATUS"] ;
			if(empty($requestReportId)){
				$request = $amazon->getProductReport1($accountId) ;
				if( !empty($request) ){
					$this->Amazonaccount->saveAccountAsyn($accountId ,$request , $user) ;
				}
			}
		}
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
	public function startAsynAmazonActiveProducts($accountId){
		$accountAsyn = $this->Amazonaccount->getAccountAsyn($accountId,"_GET_MERCHANT_LISTINGS_DATA_") ;
		$account = $this->Amazonaccount->getAccount($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	$user    = array("LOGIN_ID"=>"cron") ;
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		if( empty($accountAsyn) ){//未开始采集
			$request = $amazon->getProductActiveReport1($accountId) ;
			if( !empty($request) ){
				$this->Amazonaccount->saveAccountAsyn($accountId ,$request , $user) ;
			}
		}else{
			$requestReportId = $accountAsyn[0]["sc_amazon_account_asyn"]["REPORT_REQUEST_ID"] ;
			$reportId = $accountAsyn[0]["sc_amazon_account_asyn"]["REPORT_ID"] ;
			$status = $accountAsyn[0]["sc_amazon_account_asyn"]["STATUS"] ;
			if(empty($requestReportId)){
				$request = $amazon->getProductActiveReport1($accountId) ;
				if( !empty($request) ){
					$this->Amazonaccount->saveAccountAsyn($accountId ,$request , $user) ;
				}
			}
		}
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
		
	
	//同步产品信息
	public function asynAmazonProducts($accountId){
		$accountAsyn = $this->Amazonaccount->getAccountAsyn($accountId,"_GET_FLAT_FILE_OPEN_LISTINGS_DATA_") ;
		$account = $this->Amazonaccount->getAccount($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	$user    = array("LOGIN_ID"=>"cron") ;
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		
		if( empty($accountAsyn) ){//未开始采集
		}else{
			$requestReportId = $accountAsyn[0]["sc_amazon_account_asyn"]["REPORT_REQUEST_ID"] ;
			$reportId = $accountAsyn[0]["sc_amazon_account_asyn"]["REPORT_ID"] ;
			$status = $accountAsyn[0]["sc_amazon_account_asyn"]["STATUS"] ;
			if(empty($requestReportId)){
				//do nothing
			}else{
				if( empty($reportId) ){//获取reportId
					$request = $amazon->getProductReport2($accountId,$requestReportId) ;
					print_r($request) ;
					if( !empty($request) ){
						$this->Amazonaccount->updateAccountAsyn2($accountId ,$request , $user) ;
					}
				}else if(empty($status)){//获取产品数据
					$this->Amazonaccount->asynProductStatusStart($accountId , "_GET_FLAT_FILE_OPEN_LISTINGS_DATA_") ;
					$request = $amazon->getProductReport3($accountId , $reportId ) ;
					$this->Amazonaccount->asynProductStatusEnd($accountId  , "_GET_FLAT_FILE_OPEN_LISTINGS_DATA_") ;
					
					$this->Amazonaccount->updateAccountAsyn3($accountId ,array("reportId"=>$reportId,"reportType"=>"_GET_FLAT_FILE_OPEN_LISTINGS_DATA_") , $user) ;
				}
			}
		}
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
	public function asynAmazonActiveProducts($accountId){//同步激活的产品信息
		$accountAsyn = $this->Amazonaccount->getAccountAsyn($accountId,"_GET_MERCHANT_LISTINGS_DATA_") ;
		if( empty($accountAsyn) ){//未开始采集
			//do nothing
		}else{
			$requestReportId = $accountAsyn[0]["sc_amazon_account_asyn"]["REPORT_REQUEST_ID"] ;
			$reportId = $accountAsyn[0]["sc_amazon_account_asyn"]["REPORT_ID"] ;
			$status = $accountAsyn[0]["sc_amazon_account_asyn"]["STATUS"] ;
			if(empty($requestReportId)){
				//do nothing
			}else{
				$account = $this->Amazonaccount->getAccount($accountId) ;
		    	$account = $account[0]['sc_amazon_account'] ;
		    	$user    = array("LOGIN_ID"=>"cron") ;
		    	$amazon = new Amazon(
						$account['AWS_ACCESS_KEY_ID'] , 
						$account['AWS_SECRET_ACCESS_KEY'] ,
					 	$account['APPLICATION_NAME'] ,
					 	$account['APPLICATION_VERSION'] ,
					 	$account['MERCHANT_ID'] ,
					 	$account['MARKETPLACE_ID'] ,
					 	$account['MERCHANT_IDENTIFIER'] 
				) ;
				
				if( empty($reportId) ){//获取reportId
					$request = $amazon->getProductActiveReport2($accountId,$requestReportId) ;
					if( !empty($request) ){
						$this->Amazonaccount->updateAccountAsyn2($accountId ,$request , $user) ;
					}
				}else if(empty($status)){//获取产品数据
					$request = $amazon->getProductActiveReport3($accountId , $reportId ) ;
					$this->Amazonaccount->updateAccountAsyn3($accountId ,array("reportId"=>$reportId,"reportType"=>"_GET_MERCHANT_LISTINGS_DATA_")) ;
				}
			}
		}
		
		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	}
	
	
	//采集产品信息

	public function gatherCompetitions($id){
		//获取商家产品asin
		$array = $this->Task->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Task->savelog($id, "start gather competition" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] competitions" );
			$this->fetchCompetions($asin,$id ) ;
		}
		$this->Task->savelog($id, "end!" );
	}
	
	public function gatherFba($id){
		$array = $this->Task->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Task->savelog($id, "start gather fba" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] fba" );
			$this->fetchFba($asin,$id ) ;
		}
		$this->Task->savelog($id, "end!" );
	}
	
	public function fetchAsin($asin,$id=null,$index = null) {
		if( strlen(trim($asin)) < 9 || strlen(trim($asin)) >=11 ) {
			if($id == null ){
				$this->response->type("json");
				$this->response->body("execute complete");
				return $this->response;
			}
			return ;
		} ; 
		
		try{
			$url = "http://www.amazon.com/dp/" . $asin;
			//$url = "http://www.amazon.com/gp/offer-listing/".$asin ;
			
			$snoopy = new Snoopy ;
			$snoopy->agent =  $this->getAgent($index) ;
			$snoopy->referer = $url ;
			$snoopy->rawheaders["Pragma"] = "no-cache"; 
			
			if( $snoopy->fetch($url) ){
				//http://www.amazon.com/gp/offer-listing/B00005NPOB
				$Result = $snoopy->results ;
				$html = new simple_html_dom();
				$html->load( $Result  ,true ,false );
				
				//$html = file_get_html($url); 
				try{
					//get title
					$title = $this->Task->getProp($html ,'#btAsinTitle' ) ;
					 //$html->find('#btAsinTitle',0)->innertext;
					
					//byLineReorder
					$brand = "" ;
					foreach( $html->find('.buying a') as $e ){
						$href = $e->href ;
						if( strripos( $href , "brandtextbin" )>0 ){
							$brand = trim( $e->plaintext ) ;
							break;	
						}
					}
					//$brand =$this->Task->getProp($html ,'.buying a' ) ;
					
					$technical = "" ;
					if($html->find("#technical_details",0)!=null){
						$technical = $html->find("#technical_details",0)->parent()->find(".content",0)->plaintext ;
					}
		
					$h2s = $html->find("h2") ;
					$productDetails = '' ;
					$Dimensions = '' ;
					$Weight = '' ;
					foreach(  $html->find("h2") as $e){ 
						if( $e->plaintext == 'Product Details' ) {
							$productDetails = $e->next_sibling ()->plaintext ;
							
							foreach(  $e->next_sibling()->find("b") as $f ){
								if( trim( $f->plaintext ) == "Product Dimensions:" ){
									$Dimensions = $f->parent()->plaintext ;
									$Dimensions = str_replace("Product Dimensions:" ,"",$Dimensions);
								}else if( trim( $f->plaintext ) == "Shipping Weight:" ){
									$Weight = $f->parent()->plaintext ;
									$Weight = str_replace("Shipping Weight:" ,"",$Weight);
									$Weight = str_replace("(View shipping rates and policies)" ,"",$Weight);
								}
							}
						}
			        }  
					
					$productDescription = "" ;
					if( $html->find('#productDescription',0) != null){
						$productDescription = $html->find('#productDescription',0)->find(".content",0)->plaintext ;
					}
					
					$array['asin'] = $asin ;
					$array['title'] = trim($title) ;
					$array['TECHDETAILS'] = trim($technical) ;
					$array['PRODUCTDETAILS'] = trim($productDetails) ;
					$array['DESCRIPTION'] = trim($productDescription) ;
					$array['DIMENSIONS'] = trim($Dimensions) ;
					$array['WEIGHT'] = trim($Weight) ;
					$array['BRAND'] = trim($brand) ;
					//DIMENSIONS
					//WEIGHT
					
					//更新产品基本信息
					$this->Task->updateProduct($array);
					
					///保存图片
					$images = $html->find("#prodImageCell img",0) ;
					if( $images!=null ){
						$src = $images->src ;
						$title = $images->alt ;
						try{
							$localUrl = "images/amazon/".$asin."/".basename($src) ;
							$this->Task->addImage($asin,$src,$title,$localUrl) ;
							$this->Task->downloads($src,$asin) ;
						}catch(Exception $e){}
					}
					//保存竞争信息
					$this->saveProductPotential($asin , $html) ;
				}catch(Exception $e){
					$this->Task->savelog($id,"get product[".$asin."] details failed:::: ".$e->getMessage()) ;	
				}
				$html->clear() ;
				unset($html) ;
				$this->Task->savelog($id,"get product[".$asin."] details success!") ;	
			}
			
			unset($snoopy) ;
		}catch(Exception $e){
			$this->Task->savelog($id,"get product[".$asin."] error:::".$e->getMessage()) ;	
		}
		
		if($id == null ){
			$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		}
	}
	
	public function saveProductPotential($asin ,$html=null,$url=null){
		if( $html != null ){
			//get point
			$rating = $html->find(".acrRating",0) ;
			$point = "" ;
			if($rating != null ){
				$txt = $rating->plaintext ;
				$arry = explode("out of",$txt) ;
				$point = trim( $arry[0] ) ;
			}
			//get review
			$views = $html->find(".acrCount",0) ;
			$reviews = "" ;
			if($views != null ){
				$txt = $views->plaintext ;
				$txt = str_replace( array('"',')','(',","),"",$txt ) ;
				$reviews = trim( $txt ) ;
				$reviews = str_replace( array("reviews","review"),"",$reviews ) ;
				$reviews = trim($reviews) ;
			}
			//get ranking
			$salesRank = $html->find("#SalesRank",0) ;
			$rankArray = array() ;
			if( $salesRank != null ){
				foreach( $salesRank->find(".zg_hrsr_item") as $item ){
					$rank = $item->find(".zg_hrsr_rank",0) ;
					$type = $item->find(".zg_hrsr_ladder",0) ;
					
					$rankText = str_replace("#","",$rank->plaintext) ;
					$typeText = str_replace("in&nbsp;","",$type->plaintext) ;
					
					$rankArray[] = array("rank"=>trim( $rankText ),"type"=>trim($typeText) ) ;
				}
			}
			$this->Task->saveSalePotential($asin,$reviews , $point , $rankArray ) ;
		}else {
			
		}
	}
	
	public function fetchAsins($id=null) {
		$array = $this->Task->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Task->savelog($id, "start gather details" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] details" );
			$this->fetchAsin($asin ,$id ) ;
		}
		$this->Task->savelog($id, "end!" );
	}
	
	public function fetchFba($asin = null,$id = null ){
		//try{
		
			$d = date("U") ;
			
			$url = "http://www.amazon.com/gp/offer-listing/$asin?shipPromoFilter=1&dd=$d" ;
			
			//echo $url ;
			$snoopy = new Snoopy ;
			$snoopy->agent =  $this->getAgent(0) ;
			$snoopy->referer = $url ;
			$snoopy->rawheaders["Pragma"] = "no-cache"; 

			if( $snoopy->fetch($url) ){
				
				$Result = $snoopy->results ;
				
				$html = new simple_html_dom();
				$html->load( $Result ,true ,false );
				try{
					//$html = file_get_html($url); 
					
					$h2s = $html->find("h2") ;
					
					$base = array() ;
					$details = array() ;
			
					foreach(  $html->find("h2") as $e){
						if( $e->plaintext == 'Featured Merchants' ) {//1-5 of 15 offers 
							$returns = $this->_processRowCompetetion($e,$details,"FBA" ,$base , 'FBA_NUM') ;
							$details = $returns[0] ;
							$base = $returns[1] ;						}
					}  
			        
			        $this->Task->saveFba($asin , $base , $details) ;
				}catch(Exception $e){}
				$html->clear() ;
				unset($html) ;
			}
			unset($snoopy) ;
		if($id == null ){
			$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		}
	}	
	
	public function fetchCompetions($asin = null,$id = null ){
		//try{
		
			$d = date("U") ;
			
			//http://www.amazon.com/gp/offer-listing/B00007GQLU?shipPromoFilter=1
			$url =  "http://www.amazon.com/gp/offer-listing/".$asin."?dd=$d"  ;
			
			//echo $url ;
			$snoopy = new Snoopy ;
			$snoopy->agent =  $this->getAgent(0) ;
			$snoopy->referer = $url ;
			$snoopy->rawheaders["Pragma"] = "no-cache"; 

			if( $snoopy->fetch($url) ){
				
				$Result = $snoopy->results ;
				
				$html = new simple_html_dom();
				$html->load( $Result ,true ,false );
				try{
					//$html = file_get_html($url); 
					
					$h2s = $html->find("h2") ;
					
					$base = array('FM_NUM'=>'0','NM_NUM'=>'0','UM_NUM'=>'0') ;
					$details = array() ;
					
					foreach(  $html->find("h2") as $e){ 
						if( $e->plaintext == 'Featured Merchants' ) {//1-5 of 15 offers 
							$returns = $this->_processRowCompetetion($e,$details,"F" ,$base , 'FM_NUM') ;
							$details = $returns[0] ;
							$base = $returns[1] ;
						}else if( $e->plaintext == 'New' ) {
							$returns = $this->_processRowCompetetion($e,$details,"N" ,$base , 'NM_NUM') ;
							$details = $returns[0] ;
							$base = $returns[1] ;
						}else if( $e->plaintext == 'Used' ) {
							$returns = $this->_processRowCompetetion($e,$details,"U" ,$base , 'UM_NUM') ;
							$details = $returns[0] ;
							$base = $returns[1] ;
						}
			        }  
			        
			        $this->Task->saveCompetions($asin , $base , $details) ;
				}catch(Exception $e){}
				$html->clear() ;
				unset($html) ;
			}
			unset($snoopy) ;
		//}catch(Exception $e){
		//	$this->Task->savelog($id,"get product[".$asin."] error:::".$e->getMessage()) ;	
		//}
		if($id == null ){
			$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		}
	}
	
	public function _processRowCompetetion($e , $details ,$type , $base , $numType){
		
			$numberofresults   = $e->next_sibling ()->plaintext ;
			$ary = explode("of",$numberofresults) ;
			$_ = str_replace("offers","",$ary[1] ) ;
			$umNum = trim( $_ ) ;
			$base[$numType] = $umNum ;
			
			$detailTables = $e->parent ;
			while(true){
				if( $detailTables->class == "resultsheader" ){
					break ;
				}
				$detailTables = $detailTables->parent ;
			}
			$index = 0 ;
			foreach( $detailTables->next_sibling()->find(".result") as $table ){
				$price = $table->find(".price",0)->plaintext ;
				
				$priceShipping =  $table->find(".price_shipping",0) ;
				if($priceShipping != null){
					$priceShipping = $priceShipping->plaintext ;
				}else {
					$priceShipping = "" ;
				}
				
				$sellerInformation = $table->find(".sellerInformation",0)  ;
				
				$baseInfo = $sellerInformation->find(".seller a",0) ;
				$sellerUrl= '' ;
				$sellerName = '' ;
				$sellerImg = '' ;
				$prePositive = '' ;
				$totalRating = '' ;
				$country = '' ;
				if($baseInfo != null){
					$sellerUrl = $baseInfo->href ;
					$sellerName = $baseInfo->plaintext ;
				}else {
					$baseInfo = $sellerInformation->find("a",0) ;
					if($baseInfo !=null){
						$sellerUrl = $baseInfo->href ;
						$baseImage = $baseInfo->find("img",0) ;
						if($baseImage!=null){
							$sellerImg = $baseImage->src ;
							$sellerName = $baseImage->alt ;
						}
					}
				}
				
				$positiveInfo = $sellerInformation->find(".rating a b",0) ;
				if($positiveInfo != null){
					$prePositive = $positiveInfo->plaintext ;
					$prePositive = trim( str_replace(array("positive",'%'),"",$prePositive) ) ;
				}
				
				$totalRatingInfo = $sellerInformation->find(".rating",0) ;
				if($totalRatingInfo != null){
					$totalRating = $totalRatingInfo->plaintext ;
					$totalRating = explode("(" ,$totalRating ) ;
					if( count($totalRating) >=2 ){
						$totalRating = $totalRating[1] ;
						$totalRating = explode("total ratings" ,$totalRating ) ;
						$totalRating = $totalRating[0] ;
						$totalRating = trim( str_replace(array(",",'%'),"",$totalRating) ) ;
					}else{
						$totalRating = "" ;
					}
				}
				
				$countryInfo = $sellerInformation->find(".availability",0) ;
				if($countryInfo != null){
					$country = strtolower( $countryInfo->plaintext ) ;
					$pos = strpos($country, "china");
					if( $pos === false ){
						$country = "" ;
					}else{
						$country = "china" ;
					}
					
				}
				
				
				$index++ ;
				$details[] = array("SELLER_NAME"=>$sellerName,
					"SELLER_URL"=>$sellerUrl,
					"SELLER_PRICE"=>$price,
					"SELLER_IMG"=>$sellerImg,
					"SELLER_SHIP_PRICE"=>$priceShipping,
					"TYPE"=> $type.$index,
					"PER_POSITIVE"=>$prePositive,
					"TOTAL_RATING"=>$totalRating,
					"COUNTRY"=>$country
					) ;
			}
			return array($details,$base) ;
					
	}
	
	
	public function gatherAmazonCompetitions($id,$level){
		
		try{
			//获取商家产品asin
			$array = $this->Amazonaccount->getAccountProductsForLevel($id,$level) ;
			print_r($array) ;
			$index = 0 ;
			$this->Task->savelog($id, "start gather competition" );
			foreach( $array as $arr ){
				$index = $index + 1 ;
				$asin = $arr['sc_amazon_account_product']['ASIN'] ;
				$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] competitions" );
				$this->fetchCompetions($asin,$id ) ;
			}
			$this->Task->savelog($id, "end!" );
		
		}catch(Exception $e){
			print_r($e) ;
			$this->Task->savelog($id, "error::::".$e->getMessage() );
		}
		
		$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		
	}
	
	public function gatherAmazonFba($id,$level){
		//更新采集状态
		try{
			$array =  $this->Amazonaccount->getAccountProductsForLevel($id,$level) ;
			$index = 0 ;
			$this->Task->savelog($id, "start gather fba" );
			foreach( $array as $arr ){
				$index = $index + 1 ;
				$asin = $arr['sc_amazon_account_product']['ASIN'] ;
				$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] fba" );
				$this->fetchFba($asin,$id ) ;
			}
			$this->Task->savelog($id, "end!" );
		}catch(Exception $e){
			$this->Task->savelog($id, "error::::".$e->getMessage() );
		}
		
		$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		
	}
	
	/**
	 * 采集amazon账户产品
	 * $id 账户系统编号
	 * $code 账户CODE
	 */
	public function amazonAsin($id ,$level ){//
		try{
		
			$asintemplate = $this->Config->getAmazonConfig("AMAZON_ACCOUNT_PRODUCT_URL") ;
			$asinArray =  $this->Amazonaccount->getAccountProductsForLevel($id,$level) ;
			
			//开始采集产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				$asin = $_asin['sc_amazon_account_product']['ASIN'] ;
				$index = $index + 1 ;
				$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] details" );
				echo $asin.'<br>' ;
				$this->fetchAsin($asin,$id) ;
			} 
			//采集产品信息结束
			$this->Task->savelog($id,"end!" );
		}catch(Exception $e){
			try{
				$this->Task->savelog($id, "error::::".$e->getMessage() );
			}catch(Exception $e){}
		}

$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
	}
	
	/**
	 * gather shipping info
	 */
	public function amazonShippingAsin($id , $level ){//
		
		$account = $this->Amazonaccount->getAccount($id) ;
		$account = $account[0]['sc_amazon_account'] ;
		
		try{
			//$this->Task->clearlog($id) ;
		
			$asintemplate = $this->Config->getAmazonConfig("AMAZON_ACCOUNT_PRODUCT_URL") ;
			$asinArray =  $this->Amazonaccount->getAccountProductsForLevel($id,$level) ;
			
			//开始采集产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				
				$asin = $_asin['sc_amazon_account_product']['ASIN'] ;
				
				$condition = $_asin['sc_amazon_account_product']['ITEM_CONDITION'] ;
				$condition = $condition == 1?"used":"new" ;
				
				$index = $index + 1 ;
				$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] price" );
				$this->fetchAmazonAsin($asin,$account['CODE'],$condition,$asintemplate,$id ,$index) ;
				
			} 
			//采集产品信息结束
			$this->Task->savelog($id,"end!" );
		}catch(Exception $e){
			$this->Task->savelog($id, "error::::".$e->getMessage() );
		}
	$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
	}
	
	public function fetchAmazonAsin($asin,$code,$condition,$asintemplate=null,$id=null,$index = null) {
		try{
				$url = str_replace("{code}",$code,$asintemplate) ;
			    $url = str_replace("{asin}",$asin,$url) ;
			    $url = str_replace("{condition}",$condition,$url) ;

				$d = date("U") ;
				$url = $url."&dd=$d" ;
				
			$snoopy = new Snoopy ;
			$snoopy->agent =  $this->getAgent($index) ;
			$snoopy->referer = $url ;
			$snoopy->rawheaders["Pragma"] = "no-cache"; 

			if( $snoopy->fetch($url) ){
			
				$Result = $snoopy->results ;

				$html = new simple_html_dom();
				$html->load( $Result  ,true ,false );
				
				try{
					//////////////////////////////////////////////////////////////////////////////////////////
					$arrays = array() ;
					foreach(  $html->find("h2") as $e){
						if( $e->plaintext == 'Featured Merchants' ) {//1-5 of 15 offers 
							$arrays = $this->_processRowPrice($id,$e,'11',$arrays,"FM") ;
						}else if( $e->plaintext == 'New' ) {
							$arrays = $this->_processRowPrice($id,$e,'11',$arrays,"NEW") ;
						}else if( $e->plaintext == 'Used' ) {
							$arrays = $this->_processRowPrice($id,$e,'1',$arrays,"") ;
						}
					}  
					//////////////////////////////////////////////////////////////////////////////////////////
					
					
					//更新产品基本信息
					$this->Task->updateAmazonProductShipping($asin,$id,$arrays);
				}catch(Exception $e){
					pirnt_r($e) ;
					$this->Task->savelog($id,"get product[".$asin."] price failed:::: ".$e->getMessage()) ;	
				}
				$html->clear() ;
				unset($html) ;
				$this->Task->savelog($id,"get product[".$asin."] price success!") ;	
			}else{
				echo '<br>--------error----------<br>' ;
			}
			unset($snoopy) ;
		}catch( Exception $e){
			pirnt_r($e) ;
			$this->Task->savelog($id,"get product[".$asin."] price error:::".$e->getMessage()) ;	
		}

		/*if($id == null ){
			$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		}*/
	}
	
	function _processRowPrice($id , $e , $condition,$arrays , $isFM){
			$detailTables = $e->parent ;
			while(true){
				if( $detailTables->class == "resultsheader" ){
					break ;
				}
				$detailTables = $detailTables->parent ;
			}
			
			$index = 0 ;
			foreach( $detailTables->next_sibling()->find(".result") as $table ){
				$plusShippingText = "" ;
				if( $table->find(".price_shipping",0) != null ){
					$plusShippingText = trim($table->find(".price_shipping",0)->plaintext) ;
				}
				
				$priceText = "" ;
				if( $table->find(".price",0) != null ){
					$priceText = trim($table->find(".price",0)->plaintext) ;
				}
				
				$isFBA = "" ;
				if($table->find(".fba_link",0) != null ){
					$isFBA = "fba" ;
				}
				
				
				$priceText = trim( str_replace(array("&nbsp;","+","Shipping","Free","shipping",'$'),"",$priceText) ) ;
				$plusShippingText = trim( str_replace(array("&nbsp;","+","Shipping","Free","shipping",'$'),"",$plusShippingText) ) ;
				
				$record = array() ;
				$record["isFM"] = $isFM ;
				$record["isFBA"] = $isFBA ;
				$record['plusShippingText'] = $plusShippingText ;
				$record['priceText'] = $priceText ;
				$record['condition'] = $condition ;
				$arrays[] = $record ;
				
				$this->Task->savelog($id,"FM:$isFM FBA:$isFBA plusShippingText:$plusShippingText priceText:$priceText  condition:$condition") ;	
			}
			return $arrays ;			
		
	}
	
	
	/////////////////////////////////////////////////////
	function endsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    if ($length == 0) {
	        return true;
	    }
	
	    return (substr($haystack, -$length) === $needle);
	}
}