<?php
/**
 * 采购服务类
 * 
 * @author Administrator
 */
class PurchaseService extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	/**
	 * 获取默认采购负责人
	 */
	public function getDefaultCharger($realId){
		$charger = "" ;
		$chargerName = "" ;
		//获取分类采购负责人
		$sql = "SELECT  spc.PURCHASE_CHARGER,
				(SELECT NAME FROM sc_user WHERE login_id = spc.PURCHASE_CHARGER) AS PURCHASE_CHARGER_NAME,
				srp.ID AS REAL_ID 
			FROM sc_real_product srp
			LEFT JOIN sc_product_category spc
			 ON srp.CATEGORY_ID = spc.ID
			where srp.id = '{@#realId#}'" ;
		$item = $this->getObject($sql, array("realId"=>$realId)) ;
		if( empty( $item['PURCHASE_CHARGER'] ) ){
			//获取全局默认采购人
			$sql = "SELECT sac.VALUE , su.NAME FROM sc_amazon_config sac , sc_user su
								WHERE sac.value = su.LOGIN_ID
								AND sac.name = 'DEFAULT_PURCHASE_CHARGER'" ;
			$item = $this->getObject($sql,array()) ;
			
			$charger = $item['VALUE'] ;
			$chargerName = $item['NAME'] ;
		}else{
			$charger = $item['PURCHASE_CHARGER'] ;
			$chargerName = $item['PURCHASE_CHARGER_NAME'] ;
		}
		return array("charger"=>$charger,"chargerName"=>$chargerName) ;
	}
	
	//获取最近采购记录
	public function getDefaultLimitPrice($realId){
		$limitPrice = 0 ;
		//获取最近的实际采购价格
		$sql = " SELECT sppd.REAL_ID,t.REAL_QUOTE_PRICE FROM sc_purchase_task_products t,
						  sc_purchase_plan_details sppd
						  WHERE t.REAL_QUOTE_PRICE > 0
						  AND sppd.ID = t.PRODUCT_ID
						  AND sppd.REAL_ID = '{@#realId#}'
						  ORDER BY t.WAREHOUSE_TIME DESC
						  LIMIT 0 ,1 " ;
		$item = $this->getObject($sql, array("realId"=>$realId)) ;
		if(!empty($item)){
			$limitPrice = $item['REAL_QUOTE_PRICE'] ;
		}
		
		//获取最近询价价格
	   $sql = "sql_inquiry_cost_calc_all" ;
	   $inquiryData = $this->exeSqlWithFormat($sql, array("realId"=>$realId)) ;
	   
	   $minCost = 999999 ;
	   $PER_PRICE = 0 ;
	   $PER_SHIP_FEE = 0 ;
	   $CurrentData = null ;
	   foreach($inquiryData as $indata){
	   	$cost1 = $indata['COST1'] ;
	   	$cost2 = $indata['COST2'] ;
	   	$cost3 = $indata['COST3'] ;
	   		
	   	if( $cost1 !=0 ){
	   		$minCost = min($minCost , $cost1 ) ;
	   		if($minCost == $cost1  ){
	   			$PER_PRICE = $indata['PER1_PRICE'] ;
	   			$PER_SHIP_FEE = $indata['PER1_SHIP_FEE'] ;
	   			$CurrentData = $indata ;
	   		}
	   	}
	   		
	   	if( $cost2 !=0 ){
	   		$minCost = min($minCost , $cost2 ) ;
	   		if($minCost == $cost2  ){
	   			$PER_PRICE = $indata['PER2_PRICE'] ;
	   			$PER_SHIP_FEE = $indata['PER2_SHIP_FEE'] ;
	   			$CurrentData = $indata ;
	   		}
	   	}
	   		
	   	if( $cost3 !=0 ){
	   		$minCost = min($minCost , $cost3 ) ;
	   		if($minCost == $cost3  ){
	   			$PER_PRICE = $indata['PER3_PRICE'] ;
	   			$PER_SHIP_FEE = $indata['PER3_SHIP_FEE'] ;
	   			$CurrentData = $indata ;
	   		}
	   	}
	   }
	   
	   if( $PER_PRICE >0  ){
	   	$limitPrice = min($PER_PRICE,$limitPrice) ;
	   }
	   return $limitPrice ;
		
	}
	
	/**
	 * 自动为开发产品创建采购单
	 * 
	 * @param unknown_type $params
	 * @return Ambigous <NULL, multitype:multitype:unknown  >
	 */
	public function createItemForProductDev($params){
		//1、检查采购计划是不是存在，如果不存在，则创建开发采购计划
		$planId = $this->createPlanForProductDev( $params['loginId'] ) ;
		$realId = $params['realId'] ;
		$params['planId'] = $planId ;
		$status = 41 ;
		//限价
		$params['limitPrice'] = $this->getDefaultLimitPrice($realId) ;
		//执行人
		$charger = $this->getDefaultCharger($realId) ;//executor
		$params['executor'] = $charger['charger'] ;
		
		//判断采购单是否存在
		$existSql = "select * from sc_purchase_plan_details where dev_id = '{@#devId#}' " ;
		$item = $this->getObject($existSql, $params) ;
		if( empty($item) ){
			$sql = " INSERT INTO  sc_purchase_plan_details
						(
						REAL_ID,
						PLAN_NUM,
						PLAN_ID,
						CREATOR,
						PLAN_START_TIME,
						PLAN_END_TIME,
						CREATE_TIME,
						LIMIT_PRICE,
						EXECUTOR,
						STATUS,
						DEV_ID
						)
						VALUES
						(
							'{@#realId#}',
							'{@#planNum#}',
							'{@#planId#}',
							'{@#loginId#}',
							NOW(),
							DATE_ADD(NOW(),INTERVAL 3 DAY),
							NOW() ,
							'{@#limitPrice#}',
							'{@#executor#}',
							$status,
							'{@#devId#}'
						)" ;
			$this->exeSql($sql, $params) ;
		}
	}
	
	/*'{@#name#}', 
				'{@#code#}', 
				'{@#planTime#}', 
				'{@#planEndTime#}', 
				NOW(), 
				'{@#loginId#}', 
				1, 
				'{@#memo#}', 
				'{@#type#}', 
				'{@#executorId#}',
				'{@#requireSourceId#}'*/
	public  function createPlanForProductDev($loginId=null){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache = array() ;
		
		$start = $this->this_monday(0,false)  ;
		$end  = $this->this_sunday(0,false) ;
		
		$params = array(
				'name'=>'产品开发采购计划',
				'planTime'=>$this->this_monday(0,false) ,
				'planEndTime'=>$this->this_sunday(0,false) ,
				'loginId'=>$loginId,
				'type'=>3,
				'memo'=>'产品开发相关采购计划'
		) ;
		
		//查找计划是否存在
		$existSql = "select * from sc_purchase_plan where plan_time = '{@#planTime#}' and plan_end_time = '{@#planEndTime#}' and type=3" ;
		
		$purchasePlan = $this->getObject( $existSql, $params ) ;
		if(empty( $purchasePlan )){
			$defaultCode = $this->getDefaultCode("PPA") ;
			$params['code'] = $defaultCode ;
			$this->exeSql("sql_purchase_plan_insert", $params) ;
		}
		
		$purchasePlan = $this->getObject( $existSql, $params ) ;
		return $purchasePlan['ID'] ;
	}
	
	function this_monday($timestamp=0,$is_return_timestamp=true){
		static $cache ;
		$id = $timestamp.$is_return_timestamp;
		if(!isset($cache[$id])){
			if(!$timestamp) $timestamp = time();
			$monday_date = date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400));
			if($is_return_timestamp){
				$cache[$id] = strtotime($monday_date);
			}else{
				$cache[$id] = $monday_date;
			}
		}
		return $cache[$id];
	}
	
	//这个星期的星期天
	// @$timestamp ，某个星期的某一个时间戳，默认为当前时间
	// @is_return_timestamp ,是否返回时间戳，否则返回时间格式
	function this_sunday($timestamp=0,$is_return_timestamp=true){
		static $cache ;
		$id = $timestamp.$is_return_timestamp;
		if(!isset($cache[$id])){
			if(!$timestamp) $timestamp = time();
			$sunday = $this->this_monday($timestamp) + /*6*86400*/518400;
			if($is_return_timestamp){
				$cache[$id] = $sunday;
			}else{
				$cache[$id] = date('Y-m-d',$sunday);
			}
		}
		return $cache[$id];
	}
	
}