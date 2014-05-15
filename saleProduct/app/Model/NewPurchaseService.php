<?php
/**
 * 采购服务类
 * 
 * @author Administrator
 */
class NewPurchaseService extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function createNewPurchaseProduct($params){
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		$guid = $this->create_guid() ;
		try{
				
			$ScRequirement  = ClassRegistry::init("ScRequirement") ;
			
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
					$ScRequirement->createReqItem($ps,true) ;
				}
				
				//更新产品的REQ_PRODUCT_ID
				$sql = "update sc_purchase_product set req_product_id = '{@#reqProductId#}'  where id = '{@#id#}'" ;
				$this->exeSql($sql, array("reqProductId"=>$reqProductId,"id"=>$guid)) ;
			}
			$dataSource->commit() ;
		}catch(Exception $e){
				debug( $e ) ;
				$dataSource->rollback() ;
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
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try{
				
				$status = $data['status'] ;
				if( $status == 2 ){
					$data['status'] = "" ;
				}
				
				$this->exeSql("sql_purchase_new_update" , $data ) ;
				$purchaseProduct = $this->getPurchaseProductById( $data['id'] ) ; 
				$data['status'] 		 = $status ;
				$this->doPurchaseProductStatus($data) ;
				$ScRequirement  = ClassRegistry::init("ScRequirement") ;
				$RamPurchase  = ClassRegistry::init("RamPurchase") ;
				
				
				$reqProductId = $data['reqProductId'] ;//reqProductId
				//判断是否需要创建RMA计划 badProductsNum  noConsistencyNum  outOfNum
				
				if( $data['status'] == 49 ){//交易付款，更新付款时间
					$sql = "update sc_purchase_product set order_date = NOW() where order_date is not null and id='{@#id#}'" ;
					$this->exeSql($sql, $data) ;
				}
				
				if( $data['status'] == 60 ){
					if( (!empty( $data['outOfNum'] ))  && $data['outOfNum'] >0 ) {//缺货数量
						$RamPurchase->createRam( array( "purchaseId"=>$data['id'] , "rmaNum"=>$data['outOfNum'] ,
								"causeCode"=>"CGQH","rmaCharger"=>$purchaseProduct['EXECUTOR'] ) ) ;
						sleep(1);
					}
					
					if( (!empty( $data['badProductsNum'] ))  && $data['badProductsNum'] >0 ) {//残品数量
						$RamPurchase->createRam( array( "purchaseId"=>$data['id'] , "rmaNum"=>$data['badProductsNum'] ,
								"causeCode"=>"CGCP" ,"rmaCharger"=>$purchaseProduct['EXECUTOR'] ) ) ;
						sleep(1);
					}
					
					if( (!empty( $data['noConsistencyNum'] ))  && $data['noConsistencyNum'] >0 ) {//需求不一致数量
						$RamPurchase->createRam( array( "purchaseId"=>$data['id'] , "rmaNum"=>$data['noConsistencyNum'] ,
								"causeCode"=>"CGXQBYZ","rmaCharger"=>$purchaseProduct['EXECUTOR'] ) ) ;
					}
				}
				
				if( isset( $data['purchaseDetails'] ) ){
					if( !empty( $reqProductId ) ){
						//判断需求产品是否存在
						$sql = "select * from sc_supplychain_requirement_plan_product ssri where  req_product_id = '{@#reqProductId#}'" ;
						$planProduct = $this->getObject($sql, array("reqProductId"=>$reqProductId)) ;
						if( empty($planProduct) ){
							$params1 = array() ;
							$params1['PLAN_ID'] = "__auto__" ;
							$params1['REAL_ID'] = $data['realId'] ;
							$params1['REQ_PRODUCT_ID'] = $reqProductId ;
							$params1['STATUS'] = 3 ;
							$params1['status'] = 3 ;
							$this->exeSql("sql_supplychain_requirement_product_insert", $params1) ;
						}
						//创建需求产品
						$purchaseDetails = json_decode($data['purchaseDetails']) ;
						foreach( $purchaseDetails as $item  ){
							$item = get_object_vars($item) ;
							
							$sku = $item['sku'] ;
							$accountId = $item['accountId'] ;
							$quantity = $item['quantity'] ;
							
							$ps = array() ;
							$ps['accountId'] = $accountId ;
							$ps['reqProductId'] = $reqProductId ;
							$ps['sku'] = $sku ;
							$ps['quantity'] =  $item['quantity'] ;
							
							//查找Item是否存在
							$sql = "select * from sc_supplychain_requirement_item ssri where ssri.account_id = '{@#accountId#}' and listing_sku='{@#sku#}' and req_product_id = '{@#reqProductId#}'" ;
						
							$item_ = $this->getObject($sql, $ps) ;
							if( empty($item_) ){
								//不存在添加明细
								$ps['id'] = $this->create_guid() ;
								$ps['planId'] = "__auto__" ;
								$ps['realId'] = $data['realId'] ;
								$ps['listingSku'] = $sku ;
								$ps['fulfillment'] = $item['fulfillment'] ;
								$ps['existQuantity'] =  $item['supplyQuantity'] ;
								$ps['calcQuantity'] = ((int)$item['supplyQuantity'])+( (int)$item['quantity'] )  ;
								$ps['quantity'] =  $item['quantity'] ;
								$ps['urgency'] =  "A" ;
								$ps['reqType'] =  "A" ;//销量需求
								$ScRequirement->createReqItem($ps) ;
							}else{
								$ScRequirement->updateReqItem($ps) ;
							}
						}
					}else{
						$reqProductId =  $this->create_guid() ;
						$params1 = array() ;
						$params1['PLAN_ID'] = "__auto__" ;
						$params1['REAL_ID'] = $data['realId'] ;
						$params1['REQ_PRODUCT_ID'] = $reqProductId ;
						$params1['STATUS'] = 3 ;
						$params1['status'] = 3 ;
						$this->exeSql("sql_supplychain_requirement_product_insert", $params1) ;
						
						//创建需求产品
						$purchaseDetails = json_decode($data['purchaseDetails']) ;
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
							$ps['realId'] = $data['realId'] ;
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
						$this->exeSql( $sql , array("reqProductId"=>$reqProductId,"id"=>$data['id'] ) ) ;
					}
				}
			$dataSource->commit() ;
		}catch(Exception $e){
			$dataSource->rollback() ;
			debug( $e ) ;
		}
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
			
			if($status == 2){
				$sql = "update sc_purchase_product set is_audit=2 where id='{@#id#}'" ;
				$this->exeSql($sql, $data) ;
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
		$sql = "
				SELECT  spc.PURCHASE_CHARGER,
				(SELECT NAME FROM sc_user WHERE login_id = spc.PURCHASE_CHARGER) AS PURCHASE_CHARGER_NAME,
				srp.ID AS REAL_ID
			FROM sc_real_product srp,
			     sc_real_product_category srpc,
			     sc_product_category spc
			WHERE srp.ID = srpc.PRODUCT_ID
			AND srpc.CATEGORY_ID = spc.ID
			AND spc.PURCHASE_CHARGER IS NOT NULL 
			AND spc.PURCHASE_CHARGER != ''
				and srp.id = '{@#realId#}'
			LIMIT 0,1 " ;
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
	
			if( ( !empty( $cost1 )) && $cost1 !=0 ){
				$minCost = min($minCost , $cost1 ) ;
				if($minCost == $cost1  ){
					$PER_PRICE = $indata['PER1_PRICE'] ;
					$PER_SHIP_FEE = $indata['PER1_SHIP_FEE'] ;
					$CurrentData = $indata ;
				}
			}
	
			if(   ( !empty( $cost2)) && $cost2 !=0 ){
				$minCost = min($minCost , $cost2 ) ;
				if($minCost == $cost2  ){
					$PER_PRICE = $indata['PER2_PRICE'] ;
					$PER_SHIP_FEE = $indata['PER2_SHIP_FEE'] ;
					$CurrentData = $indata ;
				}
			}
	
			if(  ( !empty( $cost3 )) && $cost3 !=0 ){
				$minCost = min($minCost , $cost3 ) ;
				if($minCost == $cost3  ){
					$PER_PRICE = $indata['PER3_PRICE'] ;
					$PER_SHIP_FEE = $indata['PER3_SHIP_FEE'] ;
					$CurrentData = $indata ;
				}
			}
		}
	
		if( $PER_PRICE >0 && $limitPrice >0  ){
			$limitPrice = min($PER_PRICE,$limitPrice) ;
		}else if( $PER_PRICE >0 ){
			$limitPrice = $PER_PRICE ;
		}
		return $limitPrice ;
	}
}