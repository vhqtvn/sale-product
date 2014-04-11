<?php
class ScRequirement extends AppModel {
	var $useTable = "sc_election_rule" ;
	
	public  function transferPlanItem2Product($planId){
		$items = $this->exeSqlWithFormat("sql_supplychain_requirement_getFormatPlanItem2Product", array('planId'=>$planId)) ;
		if( count($items) >0 ){
			foreach($items as $item){
				$realId = $item['ID'] ;
				$sql = "select * from sc_supplychain_requirement_plan_product where plan_id = '{@#planId#}' and real_id = '{@#realId#}'" ;
				$planProduct = $this->getObject($sql, array('planId'=>$planId,'realId'=>$realId)) ;
				if( empty($planProduct) ){
					$params = array() ;
					$params['PLAN_ID'] = $planId ;
					$params['REAL_ID'] = $realId ;
					$this->exeSql("sql_supplychain_requirement_product_insert", $params) ;
				}else{
					//nothing to do
				}
			}
		}
	}
	
	public function saveItemFixQuantity($params){
		$sql = "update sc_supplychain_requirement_item set fix_quantity = '{@#fixQuantity#}' where id = '{@#id#}'" ;
		$this->exeSql($sql, $params) ;
	}
	
	public function add2PurchasePlan($params){
		$purchasePlanId= $params['purchasePlanId'] ;
		$reqPlanId =  $params['reqPlanId'] ;
		$realId = $params['realId'] ;
		$sql="sql_insert_sc_purchase_plan_details_";
		
		$query = array() ;
		$query['planId'] = $purchasePlanId ;
		$query['reqPlanId']=$reqPlanId;
		$query['realId'] = $realId ;
		$query['loginId'] = $params['loginId'] ;
		$query['planNum'] = $params['purchaseQuantity'] ;
 		
		$this->exeSql($sql, $query) ;
		
		//更新需求计划产品状态
		$p = array() ;
		$p['planId'] = $reqPlanId ;
		$p['realId'] = $realId ;
		$p['status'] = '3' ;
		$this->auditReqPlanProduct($p);
		
		//返回采购计划产品
		$sql="select * from sc_purchase_plan_details where plan_id='{@#planId#}' and req_plan_id='{@#reqPlanId#}' and real_id = '{@#realId#}'";
		$purchaseProduct=$this->getObject($sql, array("planId"=>$purchasePlanId,"reqPlanId"=>$reqPlanId,"realId"=>$realId));
		return $purchaseProduct ;
	}
	
	//auditData:data,memo:memo,entityType:"planProduct",entityId:planId+"_"+currentRealId
	public function saveItemAuditInfo($params){
		$auditData = $params['auditData'] ;
		$memo = $params['memo'] ;
		$entityType = $params['entityType'] ;
		$entityId = $params['entityId'] ;
		$auditData =  json_decode( $auditData )   ;
		foreach($auditData as $d){
			$d = get_object_vars($d) ;
			//debug($d) ;
			$sql = "update sc_supplychain_requirement_item set {@fix_quantity = '#fixQuantity#',}{@PURCHASE_QUANTITY='#purchaseQuantity#',}urgency = '{@#urgency#}' where id = '{@#id#}'" ;
			$this->exeSql($sql, $d ) ;
		}
		//保存轨迹
		$track = array() ;
		$track['memo'] = $memo ;
		$track['entityType'] = $entityType ;
		$track['entityId'] = $entityId ;
		$track['loginId'] = $params['loginId'] ;
		
		$SupplyChain  = ClassRegistry::init("SupplyChain") ;
		$SupplyChain->saveTrack($track) ;
		
		if( isset($params['status']) ){
			$es = explode("_", $entityId) ;
			$planId = $es[0] ;
			$realId = $es[1] ;
			
			$audit=array() ;
			$audit['planId'] = $planId ;
			$audit['realId'] = $realId ;
			$audit['status'] = $params['status'] ;
			$this->auditReqPlanProduct($audit) ;
		}
	}
	
	public function auditReqPlanProduct($audit){
		$sql = "update sc_supplychain_requirement_plan_product set status='{@#status#}' where plan_id = '{@#planId#}' and real_id = '{@#realId#}'" ;
		$this->exeSql($sql, $audit ) ;
	}
	
	public function createRequirement(){
		/*
		 $Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
		$accounts = $Amazonaccount->getAllAccountsFormat();
		foreach( $accounts as $account ){
			$this->_createRequirement( $account);
		}*/
		$this->_createRequirement();
	}

	/**
	 * 需求类型： 
	 * A: 销量需求
	 * B: 流量需求
	 * C: 成本不完善
	 * D: 利润不达标
	 * E: 其他需求
	 * @param unknown_type $account
	 */
	public function _createRequirement( $account=null ){
		$dataSource = $this->getDataSource();
		//$dataSource->begin();
		//创建需求之前先初始化成本
		
		try{
		//2、检测是否需要创建需求；新增加的需求产品是否都包括在未完成的需求产品里面
			$accountId = "" ;
			$accountName = "";
			if( !empty($account) ){
				$accountId = $account['ID'] ;
				$accountName = substr($account['NAME'],0,4);
			}
			//1.获取可创建采购计划的Listing列表
			$items = $this->exeSqlWithFormat("sql_supplychain_requirement_cancreate_list", array('accountId'=>$accountId)) ;
			if( count($items) >0 ){
				//创建需求计划
				$planId = $this->create_guid() ;
				$params['planId']  = $planId ;
				$params['accountId'] = $accountId ;
				//$time = new DateTime('now', new DateTimeZone('UTC')) ;
				$prefix = empty($accountName)?"":( $accountName.'-' ) ;
				$params['name']   = $prefix.date("Ymd-His") ;
				$this->exeSql("sql_supplychain_requirement_plan_insert", $params) ;
				
				//如果存在可创建Listing计划的列表
				$itemCount = 0 ;
				foreach($items as $item){
					$accountId = $item['ACCOUNT_ID'] ;
					$sql= "select * from sc_amazon_account where id = '{@#accountId#}'" ;
					$account = $this->getObject($sql, array('accountId'=>$accountId)) ;
					
					//计算Listing是否需要创建需求计划
					$cost = $this->getListingCost( $item['ACCOUNT_ID']  , $item['SKU'] ) ;
					if( empty( $cost ) ){
						$this->reqLog(array(
								'REQ_PLAN_ID'=>$planId,
								'ACCOUNT_ID'=>$item['ACCOUNT_ID'],
								'SKU'=>$item['SKU'],
								'REAL_ID' => $item['REAL_ID'] ,
								"TYPE"=>'C',
								'MEMO'=>"未设置成本数据"
						)) ;
						continue ;
					}
					
					//获取当前账户库存
					$sql="select * from sc_amazon_account_product where account_id='{@#accountId#}' and sku='{@#sku#}'";
					$accountProduct = $this->getObject($sql, array('accountId'=>$accountId,"sku"=>$item['SKU'] )) ;	
					$accountQuantity=$accountProduct['QUANTITY'] ;
					
					$channel = $item['FULFILLMENT_CHANNEL'] ;
					
					//获取总成本
					$totalCost = $cost['TOTAL_COST'] ;
					//获取销售价
					$sellerPrice = null ;
					if( $channel == 'Merchant'){
						$sellerPrice = $cost['LOWEST_PRICE'] ;
					}else{
						$sellerPrice = $cost['LOWEST_FBA_PRICE'] ;
					}
					$totalPrice = $sellerPrice ;
					
					//echo ">>>>>>>>>>>>>".$item['SKU']."<br/>" ;
					
					echo '[SKU:'.$item['SKU'].'   accountId:'.$accountId.']Price>>>["'.$totalCost.'"]["'.$totalPrice.'"]<br>' ;
					
					//供应周期
					$supplyCycle = $cost['SUPPLY_CYCLE'] ;
					if( empty($supplyCycle) || $supplyCycle==0 ){
						$supplyCycle = $cost['A_SUPPLY_CYCLE'] ;
					}
					if(empty($supplyCycle)){
						$supplyCycle = 14 ;
					}
					
					echo 'SupplyCycle>>>["'.$supplyCycle.'"]<br>' ;
					
					//需求调整系数
					$reqAdjust = $cost['REQ_ADJUST'] ;
					if( empty($reqAdjust) || $reqAdjust==0 ){
						$reqAdjust = $cost['A_REQ_ADJUST'] ;
					}
					if( empty($reqAdjust) ){
						//获取全局的配置
						$reqAdjust = $this->getGlobalReqAdjust($item['ACCOUNT_ID']  , $item['SKU'] ) ;
					}
					if( empty($reqAdjust) ){
						$reqAdjust = 1 ;
					}
					echo 'Req Adjust>>>["'.$reqAdjust.'"]<br>' ;
					
					//最近14天存在销售数量的天数
					$saleData = $this->getLastestSaleData($item['ACCOUNT_ID']  , $item['SKU'] ) ;
					$saleDays = count( $saleData  ) ;
					
					echo 'SayDays>>>["'.$saleDays.'"]<br>' ;
					
					if( $saleDays >= 7 ){//超过7天存在销售数据
						
						$count = 0 ;
						foreach( $saleData as $i ){
							$C = $i['C'] ;
							$count = $count + $C ;
						}
						
						//判断当前库存是否已经满足需求，如果已经满足需求，则不需要生成需求明细
						//计算到的需求数量
						$reqNum = ($count/14) * $supplyCycle * $reqAdjust ;
						
						if( $accountQuantity >= $reqNum ){
							continue ;
						}
						
						$ps = array() ;
						$ps['accountId'] = $item['ACCOUNT_ID'] ;
						$ps['id'] = $this->create_guid() ;
						$ps['planId'] = $planId ;
						$ps['realId'] = $item['REAL_ID'] ;
						$ps['listingSku'] = $item['SKU'] ;
						$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
						$ps['existQuantity'] =  $accountQuantity ;
						$ps['calcQuantity'] =  $reqNum ;
						$ps['quantity'] =  $reqNum -  $accountQuantity ;
						$ps['urgency'] =  "A" ;
						$ps['reqType'] =  "A" ;//销量需求
						$itemCount++ ;
						$this->exeSql("sql_supplychain_requirement_item_insert", $ps) ;
						continue ;
					}
					
					//获取最近14天的流量数据
					$flowData = $this->getLastestFlowData($item['ACCOUNT_ID']  , $item['SKU'] ) ;
					$flowDays =  count( $flowData  ) ;
					
					echo 'FlowDays >>>["'.$flowDays.'"]<br>' ;
					
					if( $flowDays>=3  ){
						$ConversionRate = $account['CONVERSION_RATE'] ;//$this->getConversionRate($item['ACCOUNT_ID']  , $item['SKU'] ) ;//CONVERSION_RATE
						if(empty($ConversionRate)){
							$ConversionRate = 0.001 ;
						}
						$count = 0 ;
						foreach( $flowData as $i ){
							$C = $i['C'] ;
							$count = $count + $C ;
						}
						
						//计算到的需求数量
						$reqNum = ($count/14) * $ConversionRate * $reqAdjust ;
						if( $accountQuantity >= $reqNum ){
							//账号库存数量大于需求数量
							continue ;
						}
						
						$ps = array() ;
						$ps['accountId'] = $item['ACCOUNT_ID'] ;
						$ps['id'] = $this->create_guid() ;
						$ps['planId'] = $planId ;
						$ps['realId'] = $item['REAL_ID'] ;
						$ps['listingSku'] = $item['SKU'] ;
						$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
						$ps['existQuantity'] =  $accountQuantity ;
						$ps['calcQuantity'] =  $reqNum ;
						$ps['quantity'] =  $reqNum -  $accountQuantity ;
						$ps['urgency'] =  "B" ;
						$ps['reqType'] =  "B" ;//流量需求
						$itemCount++ ;
						$this->exeSql("sql_supplychain_requirement_item_insert", $ps) ;
						continue ;
					}
					
					if( empty($cost) || empty($totalCost) || empty($totalPrice) || $totalPrice==0 ){
						$ps = array() ;
						$ps['accountId'] = $item['ACCOUNT_ID'] ;
						$ps['id'] = $this->create_guid() ;
						$ps['planId'] = $planId ;
						$ps['realId'] = $item['REAL_ID'] ;
						$ps['listingSku'] = $item['SKU'] ;
						$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
						$ps['existQuantity'] =  $accountQuantity ;
						$ps['calcQuantity'] =  0 ;
						$ps['quantity'] =  0 ;
						$ps['urgency'] =  "C" ;
						$ps['reqType'] =  "C" ;//成本不完整
						$itemCount++ ;
						$this->exeSql("sql_supplychain_requirement_item_insert", $ps) ;
						continue ;
					}
						
					//获取利润水平
					$profileLevel = $this->getProfileLevel($totalCost, $totalPrice) ;
					if( $profileLevel == 1  ){
						//忽略，利润不达标
						$ps = array() ;
						$ps['accountId'] = $item['ACCOUNT_ID'] ;
						$ps['id'] = $this->create_guid() ;
						$ps['planId'] = $planId ;
						$ps['realId'] = $item['REAL_ID'] ;
						$ps['listingSku'] = $item['SKU'] ;
						$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
						$ps['existQuantity'] =  $accountQuantity ;
						$ps['calcQuantity'] =  0 ;
						$ps['quantity'] =  0 ;
						$ps['urgency'] =  "C" ;
						$ps['reqType'] =  "D" ;//利润不达标
						$itemCount++ ;
						$this->exeSql("sql_supplychain_requirement_item_insert", $ps) ;
						continue;
					}
					
					//创建需求，数量为0，需要用户自己指定
					$ps = array() ;
					$ps['accountId'] = $item['ACCOUNT_ID'] ;
					$ps['id'] = $this->create_guid() ;
					$ps['planId'] = $planId ;
					$ps['realId'] = $item['REAL_ID'] ;
					$ps['listingSku'] = $item['SKU'] ;
					$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
					$ps['existQuantity'] =  $accountQuantity ;
					$ps['calcQuantity'] =  0 ;
					$ps['quantity'] =  0 ;
					$ps['urgency'] =  "C" ;
					$ps['reqType'] =  "E" ;//其他原因需求
					$itemCount++ ;
					$this->exeSql("sql_supplychain_requirement_item_insert", $ps) ;
				}
				
				//预处理需求
				$this->preProcess(array(
							'planId'=>$planId,
							'itemCount'=>$itemCount
				)) ;
			}
			
			//$dataSource->commit() ;
		}catch(Exception $e){
			//$dataSource->rollback() ;
			print_r($e->getMessage()) ;
		}
	}
	
	/**
	 * 需求预处理
	 */
	public function preProcess( $params ){
		$planId = $params['planId'] ;
		$itemCount = $params['itemCount'] ;
		
		if( $itemCount <=0  ){
			//不存在需求明细，删除该需求
			$sql = "delete from sc_supplychain_requirement_plan where id = '{@#planId#}'" ;
			$this->exeSql($sql, $params) ;
		}else{
			//1、转换计划需求listing到产品
			$this->transferPlanItem2Product($planId) ;
			//2、库存预处理
		}
	}
	
	public function reqLog($params){
		$this->exeSql("sql_supplychain_requirement_insertlog", $params) ;
	}
	

	public function getConversionRate( $accountId , $listingSku ){
		//周总订单数
		$orderCount = $this->getObject("sql_supplychain_requirement_getOrderOneWeek", array('accountId'=>$accountId,'listingSku'=>$listingSku)) ;
		$orderCount = $orderCount['C'] ;
		//周总流量
		$flowCount = $this->getObject("sql_supplychain_requirement_getFlowOneWeek", array('accountId'=>$accountId,'listingSku'=>$listingSku)) ;
		$flowCount = $orderCount['C'] ;
		return $orderCount/$flowCount ;
	}
	

	/**
	 * 获取listing成本
	 * @param unknown_type $accountId
	 * @param unknown_type $listingSku
	 */
	public function getListingCost($accountId , $listingSku){
		return $this->getObject("sql_supplychain_requirement_getListingCostNoView", array('accountId'=>$accountId,'listingSku'=>$listingSku)) ;
	}
	
	//获取Listing库存
	public function getListingInventory($accountId , $listingSku){
		$cost = $this->getObject("sql_supplychain_requirement_getListingCost", array('accountId'=>$accountId,'listingSku'=>$listingSku)) ;
		return $cost ;
	}
	
	public function getGlobalReqAdjust($accountId , $listingSku){
		$sql = "select * from sc_amazon_config where name='REQ_ADJUST'" ;
		$cost = $this->getObject($sql, array('accountId'=>$accountId,'listingSku'=>$listingSku)) ;
		return $cost['VALUE'] ;
	}
	
	//获取销售数据
	public function getLastestSaleData($accountId , $listingSku){
		$cost = $this->exeSqlWithFormat("sql_supplychain_requirement_getLastestSaleData", array('accountId'=>$accountId,'listingSku'=>$listingSku)) ;
		return $cost ;
	}
	
	//获取流量数据
	public function getLastestFlowData($accountId , $listingSku){
		$cost = $this->exeSqlWithFormat("sql_supplychain_requirement_getLastestFlowData", array('accountId'=>$accountId,'listingSku'=>$listingSku)) ;
		return $cost ;
	}
	
	//是否合适利润
	public function getProfileLevel( $totalCost , $totalPrice ){
		$bv = ($totalPrice - $totalCost )/$totalPrice ;
		if( $bv < 0.1 ) return 1 ;
		if( $bv < 0.15 ) return 2 ;
		return 3 ;
	}
}
?>