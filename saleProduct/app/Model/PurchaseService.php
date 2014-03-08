<?php
/**
 * 采购服务类
 * 
 * @author Administrator
 */
class PurchaseService extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	/**
	 * 自动为开发产品创建采购单
	 * 
	 * @param unknown_type $params
	 * @return Ambigous <NULL, multitype:multitype:unknown  >
	 */
	public function createItemForProductDev($params){
		//1、检查采购计划是不是存在，如果不存在，则创建开发采购计划
		$planId = $this->createPlanForProductDev( $params['loginId'] ) ;
		$params['planId'] = $planId ;
		
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
						CREATE_TIME,
						STATUS,
						DEV_ID
						)
						VALUES
						(
							'{@#realId#}',
							'{@#planNum#}',
							'{@#planId#}',
							'{@#loginId#}',
							NOW() ,
							10,
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