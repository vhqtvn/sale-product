<?php
class Inbound extends AppModel {
	var $useTable = false;
	
	public function updateShipmentName($params){
		$this->exeSql("
						UPDATE sc_fba_inbound_plan
							SET
							SHIPMENT_NAME = '{@#shipmentName#}'
							WHERE
							ACCOUNT_ID = '{@#accountId#}' AND SHIPMENT_ID = '{@#shipmentId#}' ", $params) ;
	}
	
	function deletePlanItem($params){
		$this->exeSql("
						delete from sc_fba_inbound_local_plan_items
							WHERE
							ITEM_ID = '{@#itemId#}'
					", $params) ;
	}
	
	function deletePlanShipmentItem($params){
		$this->exeSql("
						delete from sc_fba_inbound_plan_items
							WHERE
							ACCOUNT_ID = '{@#accountId#}'
						    AND SHIPMENT_ID = '{@#shipmentId#}'
                            AND  SELLER_SKU = '{@#sku#}'
					", $params) ;
	}
	
	public function updatePlanShipmentItem($params){
		$shipmentId = $params['shipmentId'] ;
		$accountId  = $params['accountId'] ;
	
		//更新订单明细库存
		$items =  json_decode( $params['items'] )   ;
		foreach($items as $item){
			$array = get_object_vars($item);
			$sku = $array['sku'] ;
			$quantity = $array['quantity'] ;
				
			$params['sku'] = $sku ;
			$params['quantity'] = $quantity ;
				
			$this->exeSql("
						UPDATE sc_fba_inbound_plan_items
							SET
							QUANTITY = '{@#quantity#}'
							WHERE
							ACCOUNT_ID = '{@#accountId#}'
						    AND SHIPMENT_ID = '{@#shipmentId#}'
                            AND  SELLER_SKU = '{@#sku#}'
					", $params) ;
		}
	}
	
	public function updatePlanItem($params){
		$shipmentId = $params['shipmentId'] ;
		$accountId  = $params['accountId'] ;
		debug($accountId) ;
		//更新计划当前本地状态
		$this->exeSql("
						UPDATE sc_fba_inbound_plan 
							SET 
							FIX_SHIP_STATUS = '{@#shipmentStatus#}' 
							WHERE
							ACCOUNT_ID = '{@#accountId#}' AND SHIPMENT_ID = '{@#shipmentId#}' ", $params) ;
		//更新订单明细库存
		$items =  json_decode( $params['items'] )   ;
		foreach($items as $item){
			$array = get_object_vars($item);
			$sku = $array['sku'] ;
			$quantity = $array['quantity'] ;
			
			$params['sku'] = $sku ;
			$params['quantity'] = $quantity ;
			
			$this->exeSql("
						UPDATE sc_fba_inbound_plan_items
							SET
							QUANTITY = '{@#quantity#}'
							WHERE
							ACCOUNT_ID = '{@#accountId#}' 
						    AND SHIPMENT_ID = '{@#shipmentId#}'
                            AND  SELLER_SKU = '{@#sku#}'
					", $params) ;
		}
	}
	
	public function loadPlanByShipmentId($params){
		$accountId = $params['accountId'] ;
		
		$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
		$account = $Amazonaccount->getAccountIngoreDomainById($accountId)  ;
		$account = $account[0]['sc_amazon_account']  ;
		
		
		$Utils  = ClassRegistry::init("Utils") ;
		$url = $Utils->buildUrl($account,"taskAsynAmazon/loadByShipmentId") ;
		$url = $url."/".$params['shipmentId'];
		
		$result = file_get_contents($url  );
	}
	
	public function asyncPlanToAmazon($params){
		
		$accountId = $params['accountId'] ;
	
		$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
		$account = $Amazonaccount->getAccountIngoreDomainById($accountId)  ;
		$account = $account[0]['sc_amazon_account']  ;
	
	
		$Utils  = ClassRegistry::init("Utils") ;
		$url = $Utils->buildUrl($account,"taskAsynAmazon/createInboundShipment") ;
		$url = $url."/".$params['shipmentId'];
	
		$result = file_get_contents($url  );
	}
	
	public function updatePlanItemToAmazon($params){
		$this->updatePlanItem($params) ;
		
		$accountId = $params['accountId'] ;
		
		$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
		$account = $Amazonaccount->getAccountIngoreDomainById($accountId)  ;
		$account = $account[0]['sc_amazon_account']  ;
		
		
		$Utils  = ClassRegistry::init("Utils") ;
		$url = $Utils->buildUrl($account,"taskAsynAmazon/updateInboundShipment") ;
		$url = $url."/".$params['shipmentId'];
		
		$result = file_get_contents($url  );
	}
	
	public function savePlan($params){
		if( empty($params['planId']) ){
			$params['planId'] = $this->create_guid() ;
			$this->exeSql("sql_supplychain_inbound_local_plan_insert", $params) ;
		}else{
			$this->exeSql("sql_supplychain_inbound_local_plan_edit", $params) ;
		}
		
		return $params['planId']  ;
	}
	
	
	public function savePlanShipmentSku($params){
		//判断时候存在
		$result = $this->getObject("sql_supplychain_inbound_plan_item_exists", $params) ;
		
		if( empty($result) ){
			$this->exeSql("sql_supplychain_inbound_plan_item_insert", $params) ;
		}else{
			$this->exeSql("sql_supplychain_inbound_plan_item_edit", $params) ;
		}
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
	
	public function saveTracking($params){
		$sql = " 	UPDATE  sc_fba_inbound_plan 
					SET 
						SHIPMENT_TYPE = '{@#shipmentType#}' , 
						IS_PARTNERED = '{@#isPartnered#}' , 
						CARRIER_NAME = '{@#carrierName#}' , 
						TRACKING_ID = '{@#trackingId#}'
					WHERE
					ACCOUNT_ID = '{@#accountId#}' AND SHIPMENT_ID = '{@#shipmentId#}'" ;
		$this->exeSql( $sql , $params) ;
	}
	
	public function saveTrackingToAmazon($params){
		$sql = " 	UPDATE  sc_fba_inbound_plan
					SET
						SHIPMENT_TYPE = '{@#shipmentType#}' ,
						IS_PARTNERED = '{@#isPartnered#}' ,
						CARRIER_NAME = '{@#carrierName#}' ,
						TRACKING_ID = '{@#trackingId#}'
					WHERE
					ACCOUNT_ID = '{@#accountId#}' AND SHIPMENT_ID = '{@#shipmentId#}'" ;
		$this->exeSql( $sql , $params) ;
		
		$accountId = $params['accountId'] ;
		
		$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
		$account = $Amazonaccount->getAccountIngoreDomainById($accountId)  ;
		$account = $account[0]['sc_amazon_account']  ;
		
		
		$Utils  = ClassRegistry::init("Utils") ;
		$url = $Utils->buildUrl($account,"taskAsynAmazon/putTransportContent") ;
		$url = $url."/".$params['shipmentId'];
		
		$result = file_get_contents($url  );
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
		
	}
}