<?php
class ScRequirement extends AppModel {
	var $useTable = "sc_election_rule" ;
	
	/*
	 1、列出当前已经到达Amazon入库的流程对应的采购单
	 2、判断采购单是否对应存在的需求，如果存在对应的需求，判断对应需求listing的库存是否已经到达Amazon，
		  如果已经达到，结束该需求；未到达，不处理
	 3、采购单不存在对应的需求，检查对应该货品的未完成需求，是否满足，如果满足，结束改需求
	*/
	public function clearAmazonInventoryReq(){
		$sql = "select * from sc_purchase_product where status = '75'" ;
		$items = $this->exeSqlWithFormat( $sql , array() ) ;
		foreach( $items as $item ){
			$reqProductId = $item['REQ_PRODUCT_ID'] ;
			$realId  = $item['REAL_ID'] ;
			$qualifiedProductsNum = $item['QUALIFIED_PRODUCTS_NUM'] ;//采购合格数量
			if( empty($reqProductId) ){
				//不存在对应的需求单，只判断是否存在对应的库存
				$sql = "SELECT sfsi.* FROM  sc_fba_supply_inventory sfsi,
								   sc_real_product_rel srpr
								 WHERE sfsi.ACCOUNT_ID = srpr.ACCOUNT_ID
								 AND sfsi.SELLER_SKU = srpr.SKU and srpr.real_id = '{@#realId#}'   " ;
				$inventorys = $this->exeSqlWithFormat($sql, array("realId"=>$realId)) ;
				if( empty($inventorys) || count($inventorys) <=0 ){
						//不存在该产品的库存，则不处理
						continue ;
				}else{
						//QUALIFIED_PRODUCTS_NUM
						$isComplete = true ;
						$count = 0 ;
						foreach($inventorys as $inventory){
							$totalSupplyQuantity = $inventory['TOTAL_SUPPLY_QUANTITY'] ;//当前总库存
							$quantityInbound = $inventory['QUANTITY_INBOUND'] ;//当前在途库存
							$count = $count+$totalSupplyQuantity;
						}
						if( $count >= ($qualifiedProductsNum-3)  ){
							
						}else{
							$isComplete = false ;
						}
						if( $isComplete ){
							//更新采购计划完成
							$sql = "update sc_purchase_product set status=80 where id = '{@#id#}'" ;
							$this->exeSql($sql, array("id"=>$item['ID'])) ;

							$data=array() ;
							$data['status'] = 80 ;
							$data['productId'] = $item['ID'] ;
							$data['memo'] = "FBA入库完成" ;
							$this->exeSql("sql_purchase_plan_product_insertTrack", $data) ;
							
							//产品开发结束流程
							$sql = "UPDATE sc_product_dev spd SET spd.FLOW_STATUS = 80
							WHERE CONCAT(spd.ASIN,'_',spd.TASK_ID) IN (
								SELECT DEV_ID FROM sc_purchase_product sppd  where id = '{@#productId#}'
							) " ;
							$this->exeSql($sql, $data) ;
						}
				}
			}else{ //存在对应的需求单
				$sql = "select ssri.*,sfsi.TOTAL_SUPPLY_QUANTITY ,
											sfsi.QUANTITY_INBOUND 
						from sc_supplychain_requirement_item ssri
						left join sc_fba_supply_inventory sfsi
						on ssri.account_id = sfsi.account_id and ssri.listing_sku=sfsi.seller_sku
						where ssri.req_product_id = '{@#reqProductId#}'" ;
				$records = $this->exeSqlWithFormat($sql, array("reqProductId"=>$reqProductId)) ;
				$isComplete = true ;
				foreach( $records as $record ){
					$existQuantity = $record['EXIST_QUANTITY'] ;//创建需求时存在的库存数量
					$purchaseQuantity = $record['PURCHASE_QUANTITY'] ;
					if( empty($purchaseQuantity) || $purchaseQuantity==0 ){
						continue ;
					}
					$totalSupplyQuantity = $record['TOTAL_SUPPLY_QUANTITY'] ;//当前总库存
					$quantityInbound = $record['QUANTITY_INBOUND'] ;//当前在途库存
					if( $totalSupplyQuantity > $existQuantity ){//如果当前库存大于创建需求时库存，说明已经存在入库产品
						//更新需求为已完成	
						//$sql = "update sc_supplychain_requirement_plan_product set status=6 where req_product_id = '{@#reqProductId#}'" ;
						//$this->exeSql($sql, array("reqProductId"=>$reqProductId)) ;
					}else{
						$isComplete = false ;
					}
				}
				if( $isComplete ){
					//更新需求计划完成
					$sql = "update sc_supplychain_requirement_plan_product set status=6 where req_product_id = '{@#reqProductId#}'" ;
					$this->exeSql($sql, array("reqProductId"=>$reqProductId)) ;
					//更新采购计划完成
					$sql = "update sc_purchase_product set status=80 where id = '{@#id#}'" ;
					$this->exeSql($sql, array("id"=>$item['ID'])) ;
					$data=array() ;
					$data['status'] = 80 ;
					$data['productId'] = $item['ID'] ;
					$data['memo'] = "FBA入库完成" ;
					$this->exeSql("sql_purchase_plan_product_insertTrack", $data) ;
					
					//产品开发结束流程
					$sql = "UPDATE sc_product_dev spd SET spd.FLOW_STATUS = 80
						WHERE CONCAT(spd.ASIN,'_',spd.TASK_ID) IN (
							SELECT DEV_ID FROM sc_purchase_product sppd  where id = '{@#productId#}'
						) " ;
					$this->exeSql($sql, $data) ;
				}
			}
		}
	}
	
	public  function transferPlanItem2Product($planId){
		$items = $this->exeSqlWithFormat("sql_supplychain_requirement_getFormatPlanItem2Product", array('planId'=>$planId)) ;
		if( count($items) >0 ){
			foreach($items as $item){
				$realId = $item['ID'] ;
				$sql = "select * from sc_supplychain_requirement_plan_product where plan_id = '{@#planId#}' and real_id = '{@#realId#}'" ;
				$planProduct = $this->getObject($sql, array('planId'=>$planId,'realId'=>$realId)) ;
				if( empty($planProduct) ){
					$reqProductId =  $this->create_guid() ;
					$params = array() ;
					$params['PLAN_ID'] = $planId ;
					$params['REAL_ID'] = $realId ;
					$params['REQ_PRODUCT_ID'] = $reqProductId ;
					$this->exeSql("sql_supplychain_requirement_product_insert", $params) ;
					
					//更新需求明细关联到采购单REQ_PRODUCT_ID
					$sql = "update sc_supplychain_requirement_item set req_product_id = '{@#REQ_PRODUCT_ID#}' where plan_id='{@#PLAN_ID#}' and real_id='{@#REAL_ID#}'" ;
					$this->exeSql($sql, $params) ;
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
		$p['reqProductId'] = $params['reqProductId'] ;
		$this->auditReqPlanProduct($p);
		
		//返回采购计划产品
		$sql="select * from sc_purchase_plan_details where plan_id='{@#planId#}' and req_plan_id='{@#reqPlanId#}' and real_id = '{@#realId#}'";
		$purchaseProduct=$this->getObject($sql, array("planId"=>$purchasePlanId,"reqPlanId"=>$reqPlanId,"realId"=>$realId));
		return $purchaseProduct ;
	}
	
	//auditData:data,memo:memo,entityType:"planProduct",entityId:planId+"_"+currentRealId
	public function saveItemAuditInfo($params){
		ini_set('date.timezone','Asia/Shanghai');
		$NewPurchaseService = ClassRegistry::init("NewPurchaseService") ;
		$auditData = $params['auditData'] ;
		$memo = $params['memo'] ;
		$entityType = $params['entityType'] ;
		$entityId = $params['entityId'] ;
		$reqProductId = $params['reqProductId'] ;
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
			$audit['reqProductId'] = $reqProductId;
			$audit['status'] = $params['status'] ;
			$this->auditReqPlanProduct($audit) ;
			
			if( $params['status'] == 3  ){//采购中
				$limitPrice = $NewPurchaseService->getDefaultLimitPrice( $realId ) ;
				$execut 	= $NewPurchaseService->getDefaultCharger( $realId ) ;
				
				$startTime = date('Y-m-d');
				$endTime  = date('Y-m-d',strtotime('+3 day'));
				$executor 			= $execut['charger'] ;
				$purchaseQuantity = $params['purchaseQuantity'] ;
				//$params['status'] 加入采购计划
				$params = array(
						'realId'=>$realId,
						'planNum'=>$purchaseQuantity,
						'limitPrice'=>$limitPrice ,
						'executor'=>$executor,
						'startTime'=>$startTime,
						'endTime'=>$endTime,
						'reqProductId'=>$reqProductId,
						'loginId'=>'auto'
				);
				$NewPurchaseService->createNewPurchaseProduct($params) ;
			}else if( $params['status'] == 2  ){
				//审批不通过，设置需求审批不通过时间，下次生成需求的时候在一定时间段类直接过滤掉这些Listing
				//REQ_AUDIT_NO_TIME
				$sql = "update sc_real_product set REQ_AUDIT_NO_TIME=NOW() where id = '{@#realId#}' " ;
				$this->exeSql($sql, array("realId"=>$realId)) ;
			}
		}
	}
	
	public function auditReqPlanProduct($audit){
		$sql = "update sc_supplychain_requirement_plan_product set status='{@#status#}' where req_product_id = '{@#reqProductId#}'" ;
		$this->exeSql($sql, $audit ) ;
	}
	
	public function createRequirement(){
		/*
		 $Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
		$accounts = $Amazonaccount->getAllAccountsFormat();
		foreach( $accounts as $account ){
			$this->_createRequirement( $account);
		}*/
		//$this->_createRequirement();
		$this->_createRequirementV12() ;
	}

	public  function _createRequirementV12(){
		//$sql = "sql_supplychain_requirement_cancreate_list_V1.2" ;
		$start = 0 ;
		$limit = 200 ;
		$planId= null  ;
		$itemCount = 0 ;
		
		//中止清除没有处理的需求
		$sql = "update sc_supplychain_requirement_plan_product set status=2 where status = 0" ;
		$this->exeSql($sql, array() ) ;
		
		while(true){
			$sql = "SELECT 
			    	        saap.*,
			    	        saa.name AS  ACCOUNT_ANME,
			    	        saa.SUPPLY_CYCLE,
			    	        saa.REQ_ADJUST,
			    	        saa.SECURITY_STOCK_DAYS,
			    	        saa.CONVERSION_RATE,
			    	        srp.ID AS REAL_ID,
			    	        sfsi.TOTAL_SUPPLY_QUANTITY,
			    	        sfsi.IN_STOCK_SUPPLY_QUANTITY
    	FROM sc_amazon_account_product saap,
    		    sc_amazon_account saa,
    		    sc_real_product srp,
    		    sc_real_product_rel srpr
    		    LEFT JOIN sc_fba_supply_inventory sfsi
    		    ON sfsi.ACCOUNT_ID = srpr.ACCOUNT_ID
    		    AND srpr.SKU = sfsi.SELLER_SKU
    	where saap.IS_ANALYSIS = 1
    			and saap.status = 'Y'
    			and saap.account_id=saa.id
    			and saap.fulfillment_channel like '%AMAZON%'
    			and saa.status = 1
    			and srp.is_onsale = 1
    			AND srpr.ACCOUNT_ID = saap.ACCOUNT_ID
    			AND srpr.SKU = saap.SKU
    			AND  srpr.REAL_ID = srp.ID
    			and not exists (
	    	    	select 1 from sc_purchase_product spp
	    	               where spp.real_id = srpr.real_id
	    	                and spp.status !=80 
	    	                and spp.IS_TERMINATION = 0
	    	   )
	    	   and not exists (
		    	   SELECT * FROM sc_supplychain_requirement_plan_product ssrp
				   WHERE  ssrp.status not in (2,6)
				   		AND ssrp.REAL_ID = srp.ID
		    	)
		    	limit $start ,$limit" ;
			$items = $this->exeSqlWithFormat($sql,array()) ;
			$start = $start+$limit ;
			if( empty( $items ) || count($items)<=0 ) break ;
			
			if( empty($planId)  ){
				//创建需求生成批次（计划）
				$planId = $this->create_guid() ;
				$params['planId']  = $planId ;
				$params['name']   = date("Ymd-His") ;
				$this->exeSql("sql_supplychain_requirement_plan_insert", $params) ;
			}
			
			foreach( $items as $item  ){
				$ic = $this->_createItemRequirementV13($item,$planId) ;
				$itemCount = $itemCount+$ic ;
			}
		}
		
		//预处理需求
		$this->preProcess(array(
				'planId'=>$planId,
				'itemCount'=>$itemCount
		)) ;
	}
	
	public function _createItemRequirementV13($item,$planId){
		/**
		 * 1、计算每日销售量
		 * @$daySaleNum 根据销量计算每日需求量,取最近7天  （前3天*60% + 前7天*40%）
		 */
		$saleDataLast3 = $this->getLastestSaleDataDays( $item['ACCOUNT_ID']  , $item['SKU'] ,3 ) ;//获取当前SKU的最近3天销售数据
		$saleDataLast7 = $this->getLastestSaleDataDays( $item['ACCOUNT_ID']  , $item['SKU'] ,7 ) ;//获取当前SKU的最近7天销售数据
		$daySaleNum = 0 ;
		if( $saleDataLast7 - $saleDataLast3 == 0  ){//如果只存在3天销量
			$daySaleNum = $saleDataLast3/3 ;
		}else{//如果只存在7天销量
			$daySaleNum =( ($saleDataLast3/3)*0.5) +(($saleDataLast7/7)*0.5);
		}

		$supplyCycle = (empty($item['SUPPLY_CYCLE'])|| $item['SUPPLY_CYCLE']==0)?14: $item['SUPPLY_CYCLE'] ;
		$reqAdjust    = (empty($item['REQ_ADJUST'])|| $item['REQ_ADJUST']==0)?1.2 : $item['REQ_ADJUST'] ;
		$ConversionRate = $item['CONVERSION_RATE'] ; 
		if(empty($ConversionRate)){
			$ConversionRate = 0.001 ;
		}
		
		/**
		 * 2、计算当前有效库存 $validStockQuantity
		 *      安全库存数据 $securityStockQuantity
		 */
		$securityStockQuantity = $daySaleNum * $item['SECURITY_STOCK_DAYS'] ; //8天库存为安全库存天数
		$validStockQuantity = 0 ;
		$existQuantity=$item['QUANTITY'] ;
		$totalSupplyQuantity 		= $item['TOTAL_SUPPLY_QUANTITY'] ;
		$inStockSupplyQuantity 	= $item['IN_STOCK_SUPPLY_QUANTITY'] ;
		if( empty($totalSupplyQuantity) ){
			$totalSupplyQuantity = 0 ;
		}
		if( empty($inStockSupplyQuantity) ){
			$inStockSupplyQuantity = 0 ;
		}
		$inboundSupplyQuantity = $totalSupplyQuantity - $inStockSupplyQuantity;
		
		if( $inStockSupplyQuantity == 0 ){//如果在库数量为0，则当前有效库存为总库存
			$validStockQuantity = $totalSupplyQuantity ;
		}else{//有效库存为总库存-在库库存
			$temp = $inStockSupplyQuantity - $securityStockQuantity ;//在库库存-安全库存
			if( $temp >0 ){
				$validStockQuantity = $inboundSupplyQuantity + $temp ;
			}else{
				$validStockQuantity = $inboundSupplyQuantity ;
			}
		}
		
		
	
		if( $daySaleNum >0 ){
			/**
			 * 3、计算周期需求量 = $daySaleNum*供应周期*调整系数
			 */
			$reqNum =$daySaleNum * $supplyCycle ;// * $reqAdjust ;// * $reqAdjust ;,暂时不需要调整系数
			
			/**
			 * 4、计算账户需求量 = 周期需求量（$reqNum） - 当前有效库存（$validStockQuantity）
			 */
			$accountReqNum = $reqNum - $validStockQuantity ;

			if(  $accountReqNum <=0  ){
				//如果账户需求量小于0，不存在供应需求
				return 0 ;
			}else{
				$ps = array() ;
				$ps['accountId'] = $item['ACCOUNT_ID'] ;
				$ps['id'] = $this->create_guid() ;
				$ps['planId'] = $planId ;
				$ps['realId'] = $item['REAL_ID'] ;
				$ps['listingSku'] = $item['SKU'] ;
				$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
				$ps['existQuantity'] =  $existQuantity ;
				$ps['calcQuantity'] =  $reqNum ;
				$ps['quantity'] =  ceil( $accountReqNum ) ;
				$ps['urgency'] =  "A" ;
				$ps['reqType'] =  "A" ;//销量需求
			
				$this->createReqItem($ps) ;
				return 1 ;
			}
		}
		
		//获取流量数据
		$flowData = $this->getLastestFlowData($item['ACCOUNT_ID']  , $item['SKU'] ) ;
		$flowDays =  count( $flowData  ) ;
		
		if( $flowDays>=3  ){
			$count = 0 ;
			foreach( $flowData as $i ){
				$C = $i['C'] ;
				$count = $count + $C ;
			}
			
			//计算到的需求数量
			$reqNum = ($count/14) * $ConversionRate * $reqAdjust ;
			
			/**
			 * 4、计算账户需求量 = 周期需求量（$reqNum） - 当前有效库存（$validStockQuantity）
			 */
			$accountReqNum = $reqNum - $validStockQuantity ;
			if( $accountReqNum<=0 ){
				//账号库存数量大于需求数量
				return 0  ;
			}
		
			$ps = array() ;
			$ps['accountId'] = $item['ACCOUNT_ID'] ;
			$ps['id'] = $this->create_guid() ;
			$ps['planId'] = $planId ;
			$ps['realId'] = $item['REAL_ID'] ;
			$ps['listingSku'] = $item['SKU'] ;
			$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
			$ps['existQuantity'] =  $existQuantity ;
			$ps['calcQuantity'] =  $reqNum ;
			$ps['quantity'] = ceil( $accountReqNum ) ;
			$ps['urgency'] =  "B" ;
			$ps['reqType'] =  "B" ;//流量需求
			$this->createReqItem($ps) ;
			return 1 ;
		}
		
		//如果存在库存大于0，则不创建需求
		if( $existQuantity >= 10 ) return 0 ;
		$ps = array() ;
		$ps['accountId'] = $item['ACCOUNT_ID'] ;
		$ps['id'] = $this->create_guid() ;
		$ps['planId'] = $planId ;
		$ps['realId'] = $item['REAL_ID'] ;
		$ps['listingSku'] = $item['SKU'] ;
		$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
		$ps['existQuantity'] =  $existQuantity ;
		$ps['calcQuantity'] =  0 ;
		$ps['quantity'] =  0 ;
		$ps['urgency'] =  "C" ;
		$ps['reqType'] =  "E" ;//其他原因需求
		$this->createReqItem($ps) ;
		return 1 ;
	}
	
	
	public function _createItemRequirementV12($item,$planId){
		//账号当前库存
		$existQuantity=$item['QUANTITY'] ;
		//获取当前SKU的最近7天销售数据
		//最近7天存在销售数量的天数
		$saleDataLast3 = $this->getLastestSaleDataDays( $item['ACCOUNT_ID']  , $item['SKU'] ,3 ) ;
		$saleDataLast7 = $this->getLastestSaleDataDays( $item['ACCOUNT_ID']  , $item['SKU'] ,7 ) ;
		$daySaleNum = 0 ;
		if( $saleDataLast7 - $saleDataLast3 == 0  ){//如果只存在3天销量
			$daySaleNum = $saleDataLast3/3 ;
		}else{//如果只存在7天销量
			$daySaleNum =( ($saleDataLast3/3)*0.8) +((($saleDataLast7-$saleDataLast3)/4)*0.2);
		}
		/*else{//如果存在14天销量
			$daySaleNum =( ($saleDataLast3/3)*0.7) +((($saleDataLast7-$saleDataLast3)/4)*0.2)+((($saleDataLast14-$saleDataLast3)/4)*0.2);
		}
		/*echo 'sku:::'.$item['SKU'].'<br/>' ;
		echo '$existQuantity:'.$existQuantity.'<br/>' ;
		echo 'daySaleNum:'.$daySaleNum.'<br/>' ;*/
		
		$supplyCycle = (empty($item['SUPPLY_CYCLE'])|| $item['SUPPLY_CYCLE']==0)?14: $item['SUPPLY_CYCLE'] ;
		$reqAdjust    = (empty($item['REQ_ADJUST'])|| $item['REQ_ADJUST']==0)?1.2 : $item['REQ_ADJUST'] ;
		$ConversionRate = $item['CONVERSION_RATE'] ; 
		if(empty($ConversionRate)){
			$ConversionRate = 0.001 ;
		}
		
		if( $daySaleNum >0 ){
			//计算需求数量
			$reqNum =$daySaleNum * $supplyCycle * $reqAdjust ;

			if( $existQuantity >= $reqNum ){
				//如果存在库存大于需求量，忽略不处理
				return 0 ;
			}else{
				$ps = array() ;
				$ps['accountId'] = $item['ACCOUNT_ID'] ;
				$ps['id'] = $this->create_guid() ;
				$ps['planId'] = $planId ;
				$ps['realId'] = $item['REAL_ID'] ;
				$ps['listingSku'] = $item['SKU'] ;
				$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
				$ps['existQuantity'] =  $existQuantity ;
				$ps['calcQuantity'] =  $reqNum ;
				$ps['quantity'] =  ceil( $reqNum -  $existQuantity ) ;
				$ps['urgency'] =  "A" ;
				$ps['reqType'] =  "A" ;//销量需求
			
				$this->createReqItem($ps) ;
				return 1 ;
			}
		}
		
		//获取流量数据
		$flowData = $this->getLastestFlowData($item['ACCOUNT_ID']  , $item['SKU'] ) ;
		$flowDays =  count( $flowData  ) ;
		
		if( $flowDays>=3  ){
			$count = 0 ;
			foreach( $flowData as $i ){
				$C = $i['C'] ;
				$count = $count + $C ;
			}
		
			//计算到的需求数量
			$reqNum = ($count/14) * $ConversionRate * $reqAdjust ;
			if( $existQuantity >= $reqNum ){
				//账号库存数量大于需求数量
				return 0  ;
			}
		
			$ps = array() ;
			$ps['accountId'] = $item['ACCOUNT_ID'] ;
			$ps['id'] = $this->create_guid() ;
			$ps['planId'] = $planId ;
			$ps['realId'] = $item['REAL_ID'] ;
			$ps['listingSku'] = $item['SKU'] ;
			$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
			$ps['existQuantity'] =  $existQuantity ;
			$ps['calcQuantity'] =  $reqNum ;
			$ps['quantity'] = ceil( $reqNum -  $existQuantity ) ;
			$ps['urgency'] =  "B" ;
			$ps['reqType'] =  "B" ;//流量需求
			$this->createReqItem($ps) ;
			return 1 ;
		}
		
		//如果存在库存大于0，则不创建需求
		if( $existQuantity >= 10 ) return 0 ;
		$ps = array() ;
		$ps['accountId'] = $item['ACCOUNT_ID'] ;
		$ps['id'] = $this->create_guid() ;
		$ps['planId'] = $planId ;
		$ps['realId'] = $item['REAL_ID'] ;
		$ps['listingSku'] = $item['SKU'] ;
		$ps['fulfillment'] = $item['FULFILLMENT_CHANNEL'] ;
		$ps['existQuantity'] =  $existQuantity ;
		$ps['calcQuantity'] =  0 ;
		$ps['quantity'] =  0 ;
		$ps['urgency'] =  "C" ;
		$ps['reqType'] =  "E" ;//其他原因需求
		$this->createReqItem($ps) ;
		return 1 ;
	}
	
	/**
	 * 需求类型： 
	 * A: 销量需求
	 * B: 流量需求
	 * C: 成本不完善
	 * D: 利润不达标
	 * E: 其他需求
	 * @param unknown_type $account
	 * @deprecated
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
					/*if( empty( $cost ) ){
						$this->reqLog(array(
								'REQ_PLAN_ID'=>$planId,
								'ACCOUNT_ID'=>$item['ACCOUNT_ID'],
								'SKU'=>$item['SKU'],
								'REAL_ID' => $item['REAL_ID'] ,
								"TYPE"=>'C',
								'MEMO'=>"未设置成本数据"
						)) ;
						continue ;
					}*/
					
					//获取当前账户库存
					$sql="select * from sc_amazon_account_product where account_id='{@#accountId#}' and sku='{@#sku#}'";
					$accountProduct = $this->getObject($sql, array('accountId'=>$accountId,"sku"=>$item['SKU'] )) ;	
					$accountQuantity=$accountProduct['QUANTITY'] ;
					
					$channel = $item['FULFILLMENT_CHANNEL'] ;
					
					//获取总成本
					$totalCost = empty($cost)?null:$cost['TOTAL_COST'] ;
					//获取销售价
					$sellerPrice = null ;
					$totalPrice = null ;
					if( !empty($cost) ){
						if( $channel == 'Merchant'){
							$sellerPrice = $cost['LOWEST_PRICE'] ;
						}else{
							$sellerPrice = $cost['LOWEST_FBA_PRICE'] ;
						}
						$totalPrice = $sellerPrice ;
					}
					
					//echo ">>>>>>>>>>>>>".$item['SKU']."<br/>" ;
					
					echo '[SKU:'.$item['SKU'].'   accountId:'.$accountId.']Price>>>["'.$totalCost.'"]["'.$totalPrice.'"]<br>' ;
					
					//供应周期
					$supplyCycle = 14 ;
					if( !empty($cost) ){
						$supplyCycle = $cost['SUPPLY_CYCLE'] ;
						if( empty($supplyCycle) || $supplyCycle==0 ){
							$supplyCycle = $cost['A_SUPPLY_CYCLE'] ;
						}
						if(empty($supplyCycle)){
							$supplyCycle = 14 ;
						}
					}
					
					echo 'SupplyCycle>>>["'.$supplyCycle.'"]<br>' ;
					
					//需求调整系数
					$reqAdjust = 1.2 ;
					if( !empty($cost) ){
						$reqAdjust = $cost['REQ_ADJUST'] ;
						if( empty($reqAdjust) || $reqAdjust==0 ){
							$reqAdjust = $cost['A_REQ_ADJUST'] ;
						}
					}
					
					if( empty($reqAdjust) ){//获取全局的配置
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
						$this->createReqItem($ps) ;
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
						$this->createReqItem($ps) ;
						continue ;
					}
					
					//如果账户库存大于10个，则不生成需求
					if( $accountQuantity >= 10 ) continue ;
					
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
						$this->createReqItem($ps) ;
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
						$this->createReqItem($ps) ;
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
					$this->createReqItem($ps) ;
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
		//debug($params) ;
		//return ;
		$planId = $params['planId'] ;
		$itemCount = $params['itemCount'] ;
		$NewPurchaseService = ClassRegistry::init("NewPurchaseService") ;
		
		if( $itemCount <=0  ){
			//不存在需求明细，删除该需求
			$sql = "delete from sc_supplychain_requirement_plan where id = '{@#planId#}'" ;
			$this->exeSql($sql, $params) ;
		}else{
			//设置初始化数据
			ini_set('date.timezone','Asia/Shanghai');
			$startTime = date('Y-m-d');
			$endTime  = date('Y-m-d',strtotime('+3 day'));
			
			//1、转换计划需求listing到产品
			$this->transferPlanItem2Product($planId) ;
			//2、自动生成采购单，需求量大于10的
			$reqProducts = $this->exeSqlWithFormat("sql_supplychain_requirement_plan_product_list", $params) ;
			//debug($reqProducts) ;
			foreach($reqProducts as $product){
				//判断当前采购计划是否存在该货品的采购单，如果存在，则自动关联到现在的需求单，不存在，则创建新的采购单
				$sql = "select * from sc_purchase_product where real_id='{@#realId#}' and status <80 " ;//未完成的采购单
				$purchaseProduct = $this->getObject($sql, array("realId"=>$product['ID'])) ;
				if( !empty($purchaseProduct) ){
					//更新采购计划单的需求单位该需求
					$sql = "update sc_purchase_product set req_product_id = '{@#reqProductId#}' where id = '{@#id#}'" ;
					$this->exeSql($sql, array("id"=>$purchaseProduct['ID'],"reqProductId"=>$product['REQ_PRODUCT_ID'])) ;
					//更新需求单状态为采购中
					$sql = "update sc_supplychain_requirement_plan_product set status = 3 where req_product_id = '{@#REQ_PRODUCT_ID#}'" ;//采购中
					$this->exeSql($sql, $product) ;
					continue ;
				}
				
				//采购数量
				$quantity = $product['FIX_QUANTITY'] ;
				if( $quantity >=10 ){
					
					$limitPrice = $NewPurchaseService->getDefaultLimitPrice($product['ID']) ;
					$execut 	= $NewPurchaseService->getDefaultCharger($product['ID']) ;
					$executor 			= $execut['charger'] ;
					$executorName 	= $execut['chargerName'] ;
					
					$params = array(
							'realId'=>$product['ID'],
							'planNum'=>$quantity,
							'limitPrice'=>$limitPrice ,
							'executor'=>$executor,
							'startTime'=>$startTime,
							'endTime'=>$endTime,
							'reqProductId'=>$product['REQ_PRODUCT_ID'],
							'loginId'=>'auto'
							);
					  $NewPurchaseService->createNewPurchaseProduct($params) ;
				  //debug($params) ;
				  $sql = "update sc_supplychain_requirement_plan_product set status = 3 where req_product_id = '{@#REQ_PRODUCT_ID#}'" ;//采购中
				  $this->exeSql($sql, $product) ;
				 
				}
			}
		}
	}
	
	function  createReqItem($ps){
		//debug( $ps ) ;
		//return ;
		$existQuantity = $ps['existQuantity'] ;
		$quantity = $ps['quantity'] ;
		
		$this->exeSql("sql_supplychain_requirement_item_insert", $ps) ;
	}
	
	function  updateReqItem($ps){
		//debug( $ps ) ;
		//return ;
		$sql= "update sc_supplychain_requirement_item  set PURCHASE_QUANTITY= '{@#quantity#}' where account_id = '{@#accountId#}'
				and listing_sku='{@#sku#}' and req_product_id = '{@#reqProductId#}' ";
	
		$this->exeSql($sql , $ps) ;
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
	
	public function getLastestSaleDataDays($accountId , $listingSku , $days ){
		$cost = $this->getObject("sql_supplychain_requirement_getLastestSaleDataDays", array('accountId'=>$accountId,'listingSku'=>$listingSku,"days"=>$days)) ;
		return $cost['C'] ;
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