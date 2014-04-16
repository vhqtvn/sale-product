<?php
/**
 * 采购服务类
 * 
 * @author Administrator
 */
class NewPurchaseService extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function createNewPurchaseProduct($params){
		
		$ScRequirement  = ClassRegistry::init("ScRequirement") ;
		$guid = $this->create_guid() ;
		$params['guid'] = $guid ;
		$params['code'] = $this->getUserDefaultCode("PT")  ;
		$this->exeSql("sql_purchase_new_create", $params) ;
		
		if( isset( $params['purchaseDetails'] ) ){
			//创建需求产品
			$reqProductId =  $this->create_guid() ;
			$params1 = array() ;
			$params1['PLAN_ID'] = "__auto__" ;
			$params1['REAL_ID'] = $params['realId'] ;
			$params1['REQ_PRODUCT_ID'] = $reqProductId ;
			$params1['STATUS'] = 3 ;
			$this->exeSql("sql_supplychain_requirement_product_insert", $params1) ;
			$purchaseDetails = json_decode($params['purchaseDetails']) ;
			
			foreach( $purchaseDetails as $item  ){
				$item = get_object_vars($item) ;
				
				$sku = $item['sku'] ;
				$accountId = $item['accountId'] ;
				$quantity = $item['quantity'] ;
				
				//创建需求明细
				$ps = array() ;
				$ps['accountId'] = $accountId ;
				$ps['reqProductId'] = $reqProductId ;
				$ps['id'] = $this->create_guid() ;
				$ps['planId'] = "__auto__" ;
				$ps['realId'] = $params['realId'] ;
				$ps['listingSku'] = $sku ;
				$ps['fulfillment'] = $item['fulfillment'] ;
				$ps['existQuantity'] =  $item['supplyQuantity'] ;
				$ps['calcQuantity'] = ((int)$item['supplyQuantity'])+( (int)$item['quantity'] )  ;
				$ps['quantity'] =  $item['quantity'] ;
				$ps['urgency'] =  "A" ;
				$ps['reqType'] =  "A" ;//销量需求
				$ScRequirement->createReqItem($ps) ;
			}
			
			//更新产品的REQ_PRODUCT_ID
			$sql = "update sc_purchase_product set req_product_id = '{@#reqProductId#}'  where id = '{@#id#}'" ;
			$this->exeSql($sql, array("reqProductId"=>$reqProductId,"id"=>$guid)) ;
		}
		return $guid ;
	}
	
	function savePrintTime($params){
		//$printTime = $params['printTime'] ;
		
		if( isset( $params['printTime'] ) ){
			ini_set('date.timezone','Asia/Shanghai');
			$printTime = date('Y-m-d H:i:s');
			$params['printTime'] =$printTime ;
		}else if( isset( $params['inPrintTime'] ) ){
			//ini_set('date.timezone','Asia/Shanghai');
			//$printTime = date('Y-m-d H:i:s');
			//$params['inPrintTime'] =$printTime ;
		}
		
		$this->exeSql("update sc_purchase_product set id= '{@#purchaseProductId#}'{@, print_time = '#printTime#'}{@, in_print_time = '#inPrintTime#'} where id = '{@#purchaseProductId#}' ", 
				$params ) ;
	}
	
	public function loadStatics(){
		$result =  $this->exeSqlWithFormat("sql_purchase_new_loadStatics", array()) ;
		return $result ;
	}
	
	
	public function loadRepaireStatics(){
		return $this->exeSqlWithFormat("sql_purchase_new_loadRepaireStatics", array()) ;
	}
	
	
	/**
	 * 保存采购产品
	 * @param unknown_type $data
	 * ('{@#guid#}', 
			'{@#realId#}', 
			'{@#planNum#}', 
			{@#limitPrice:NULL#}, 
			'{@#executor#}', 
			'{@#startTime#}', 
			'{@#endTime#}', 
			'{@#reqProductId#}', 
			'{@#devId#}',
			'{@#loginId#}', 
			NOW(), 
			'{@#loginId#}', 
			NOW(), 
			'{@#tags#}'
	 */
	public function savePurchaseProduct($data){
		$this->exeSql("sql_purchase_new_update" , $data ) ;
		$this->doPurchaseProductStatus($data) ;
	}
	
	public function getPurchaseProductById($id){
		return $this->getObject("select * from sc_purchase_product where id ='{@#id#}'", array("id"=>$id)) ;
	}
	
	public function doPurchaseProductStatus($data){
		if( isset($data['status'])  && !empty($data['status']) ){
			
			$currentStatus = $data['currentStatus'] ;
			$isTerminal = false ;
			if( isset( $data['isTerminal'])  &&$data['isTerminal']  ){
				$isTerminal = true ;
			}
			//设置产品状态为废弃
			$id = $data["id"] ;
			
			$purchaseProduct = $this->getPurchaseProductById($id) ;
			
			$memo =   $data["memo"] ;
			$status = $data["status"] ;
			$data['productId'] = $id ;
			
			if(status == 2){
				$sql = "update sc_purchase_product set is_audit=2 where id='{@#productId#}'" ;
				$data['memo'] = $data['trackMemo'] ;
				$this->exeSql("sql_purchase_plan_product_insertTrack", $data) ;
				return ;
			}
			
			//更新状态
			$this->exeSql("sql_purchase_new_product_updateStatus", $data) ;
			//添加轨迹
			$data['memo'] = $data['trackMemo'] ;
			$this->exeSql("sql_purchase_plan_product_insertTrack", $data) ;
			
			if( $status == 80 || $isTerminal ){ //采购结束
				//产品开发结束流程
				$sql = "UPDATE sc_product_dev spd SET spd.FLOW_STATUS = 80
						WHERE CONCAT(spd.ASIN,'_',spd.TASK_ID) IN (
							SELECT DEV_ID FROM sc_purchase_product sppd  where id = '{@#productId#}'
						) " ;
				$this->exeSql($sql, $data) ;
				
					//需求结束
				if( !empty($purchaseProduct['REQ_PRODUCT_ID']) ){
					$sql = "update sc_supplychain_requirement_plan_product set status = 6  where req_product_id = '{@#reqProductId#}'" ;
					$this->exeSql($sql , array("reqProductId"=>$purchaseProduct['REQ_PRODUCT_ID']) ) ;
				}
				
			}
			
			if( $isTerminal  ){
				//终止采购
				$sql = "update sc_purchase_product set IS_TERMINATION = 1 where  id = '{@#productId#}'" ;
				$this->exeSql($sql, $data) ;
			}
			
			//更新采购价格到货品成本区域 "realQuotePrice":"90","realShipFee":"10"
			/*$sku = $data['sku'] ;
			 $realQuotePrice = $data['realQuotePrice'] ;
			$realShipFee = $data['realShipFee'] ;*/
			$cost  = ClassRegistry::init("Cost") ;
			$cost->saveCostWithPurchase($data) ;
		}
	}
	
	public  function loadDefault($params){
		$realId = $params['realId'] ;
		$charger = $this->getDefaultCharger($realId) ;
		$limitPrice = $this->getDefaultLimitPrice($realId) ;
		return array("charger"=>$charger,"limitPrice"=>$limitPrice) ;
	}
	
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
	
	public function getDefaultLimitPrice($realId){
		$limitPrice = 0 ;
		//获取最近的实际采购价格
		$sql = " SELECT t.REAL_ID,t.REAL_QUOTE_PRICE FROM sc_purchase_product t,
						  sc_real_product srp
						  WHERE t.REAL_ID = srp.ID
						  AND srp.ID = '{@#realId#}'
				           and t.REAL_QUOTE_PRICE is not null
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
}