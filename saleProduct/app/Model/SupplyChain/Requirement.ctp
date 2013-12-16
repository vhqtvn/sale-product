<?php
class Requirement extends AppModel {
	var $useTable = false;

	
	public function createRequirement(){
		//2、检测是否需要创建需求；新增加的需求产品是否都包括在未完成的需求产品里面
		$items = $this->exeSqlWithFormat("sql_supplychain_requirement_cancreate_list", array()) ;
		
		if( count($items) >0 ){
			//3、创建需求
			$params = array() ;
			$planId = $this->create_guid() ;
			
			//创建计划
			$params['planId']  = $planId ;
			$time = new DateTime('now', new DateTimeZone('UTC')) ;
			$params['name']   = $time->format("c") ;
			$this->exeSql("sc_supplychain_requirement_plan", $params) ;
			
			//创建requirement items
			foreach($items as $item){
				$ps = array() ;
				$ps['accountId'] = $item['ACCOUNT_ID'] ;
				$ps['id'] = $this->create_guid() ; 
				$ps['planId'] = $planId ;
				$ps['listingSku'] = $item['SKU'] ;
				$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
				$ps['quantity'] = $item['RECOMMENDED_INBOUND_QUANTITY'] ;
				$ps['last14'] = $item['SALES_FOR_THELAST14DAYS'] ;
				$ps['last30'] = $item['SALES_FOR_THELAST30DAYS'] ;
				
				$this->exeSql("sql_supplychain_requirement_item_insert", $ps) ;
			}
			
			$SqlUtils  = ClassRegistry::init("SqlUtils") ;
			//根据需求创建采购计划
			$query = array() ;
			$purPlanId=time();
			$query['id'] = $purPlanId ;
			
			$defaultCode = $SqlUtils->getDefaultCode("PP") ;
			$query['code'] = 'R'.$defaultCode ;
			$query['name'] = $query['code']  ;
			$query['requireSourceId'] = $planId ;
			
			//创建采购计划
			$this->exeSql("sql_purchase_plan_insert_byGenId", $query) ;
			
			$purchaseItems = $this->exeSqlWithFormat("sql_supplychain_requirement_item_formatRealSku", $params) ;
			foreach($purchaseItems as $item){
				//插入明细
				$q = array() ;
				$q['asin'] = '' ;
				$q['sku'] = $item['REAL_SKU'] ;
				$q['planId'] = $purPlanId  ;
				$q['planNum'] = $item['QUANTITY'] ;
				$q['loginId'] = 'system' ;
				$this->exeSql("sql_insert_purchasePlanProductsWithPlanNum", $q) ;
			}
		}
	}
}