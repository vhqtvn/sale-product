<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App :: import('Vendor', 'Snoopy');
App :: import('Vendor', 'simple_html_dom');
App :: import('Vendor', 'Amazon');

class TaskAsynAmazonController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Task', 'Config','Amazonaccount','Utils');
	
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
}