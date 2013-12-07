<?php
class Inbound extends AppModel {
	var $useTable = false;
	
	public function savePlan($params){
		if( empty($params['planId']) ){
			$params['planId'] = $this->create_guid() ;
			$this->exeSql("sql_supplychain_inbound_local_plan_insert", $params) ;
		}else{
			$this->exeSql("sql_supplychain_inbound_local_plan_edit", $params) ;
		}
		
		return $params['planId']  ;
	}
	
	public function savePlanSku($params){
		if( empty($params['itemId']) ){
			$params['itemId'] = $this->create_guid() ;
			$this->exeSql("sql_supplychain_inbound_local_plan_item_insert", $params) ;
		}else{
			$this->exeSql("sql_supplychain_inbound_local_plan_item_edit", $params) ;
		}
		return $params['itemId'];
	}
	
	public function saveToAmazon($params){
		$plan = $this->getObject("sql_supplychain_inbound_local_plan_getByPlanId", $params ) ;
		//调用Amazon同步接口
		$accountId = $plan['ACCOUNT_ID'] ;
		
		$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
		$account = $Amazonaccount->getAccountIngoreDomainById($accountId)  ;
		$account = $account[0]['sc_amazon_account']  ;
		
		
		$Utils  = ClassRegistry::init("Utils") ;
		$url = $Utils->buildUrl($account,"taskAsynAmazon/quantity") ;
		$url = $url.'/'.$params['planId'] ;
		
		$result = file_get_contents($url  );
		
		if( explode("failed", $result ) ){
			//echo "处理失败" ;
			$this->exeSql("update sc_fba_inbound_local_plan set status = '2'  where plan_id = '{@#planId#}'", $params) ;
		}else{
			//更新本地状态到已同步
			$this->exeSql("update sc_fba_inbound_local_plan set status = '1'  where plan_id = '{@#planId#}'", $params) ;
			
		}
		
		
		echo '111111111111111111' ;
		
	}
}