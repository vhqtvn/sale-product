<?php
class NewProductDev extends AppModel {
	var $useTable = 'sc_election_rule';
	
	function deleteProduct($params){
		$sql = "delete  from sc_product_developer where dev_id = '{@#devId#}'" ;
		$this->exeSql($sql, $params ) ;
	}
	
	function confirmSampleTime($params){
		ini_set('date.timezone','Asia/Shanghai');
		$printTime = date('Y-m-d H:i:s');
		
		if( isset($params['order']) && $params['order'] == 1 ){
			$params['sampleOrderTime'] = $printTime ;
		}else if( isset($params['arrive']) && $params['arrive'] == 1 ){
			$params['sampleArriveTime'] = $printTime ;
		}
		$this->exeSql("sql_productDev_new_updateSampleTime", $params) ;
		
		$sql = "select * from sc_product_developer where dev_id = '{@#devId#}'" ;
		$productDeveloper = $this->getObject($sql, $params) ;
		
		if( isset( $params['sampleOrderTime']  ) ){//更新流程到样品下单下一环节
			//更改状态
			$sql = "update sc_product_developer set flow_status=42 where flow_status = 41 and dev_id = '{@#devId#}' " ;
			$this->exeSql($sql, $params) ;
			//保存轨迹'{@#ASIN#}',  '{@#TASK_ID#}',  '{@#trackMemo#}',  '{@#loginId#}', 
			$this->exeSql("sql_pdev_track_insert", array(
						'ASIN'=>$productDeveloper['ASIN'],
						'TASK_ID'=>$params['devId'],
						'trackMemo'=>'样品下单确认',
						'loginId'=>$params['loginId']
					)) ;
		}else  if( isset( $params['sampleArriveTime']  ) ){//更新流程到样品到达下一环节
			//更改状态
			$sql = "update sc_product_developer set flow_status=43 where flow_status = 42 and dev_id = '{@#devId#}' " ;
			$this->exeSql($sql, $params) ;
			//保存轨迹
			$this->exeSql("sql_pdev_track_insert", array(
						'ASIN'=>$productDeveloper['ASIN'],
						'TASK_ID'=>$params['devId'],
						'trackMemo'=>'样品到达确认',
						'loginId'=>$params['loginId']
					)) ;
		}
	}
	
	function addNewAsinDev($params){
		$params['FLOW_STATUS'] = 10 ;//默认状态
		$asins = $params['asins'] ;
		$platformId = $params['platformId'] ;
		$return = array() ;
		foreach ( explode(",", $asins) as $asin ){
				
			if( !empty( $asin ) ){
				//format asin
				preg_match_all("/[A-Za-z0-9-_]/i",$asin,$result);
				$asin=implode('',$result[0]);

				$params['ASIN'] = trim($asin);
	
				//判断ASIN是否正在开发中 结束标志：80（结束） 和 （15废弃）
				$p = $this->getObject("sql_productDev_new_IsDeving", array("asin"=>trim($asin) )) ;
				if( !empty($p) ){
					$return[] = $p['DEV_ID']."||".$p['ASIN'];
				}else{
					$devId = $this->create_guid() ;
					$p = array(
								'devId'=>$devId,
								'asin'=>$params['ASIN'] ,
								'loginId'=>$params['loginId'],
								'platformId'=>$platformId
							) ;
					
					if( isset($params['categoryId']) ){
						//获取分类询价负责人
						$categoryId= $params['categoryId'] ;
						$inquiryCharger = $this->getCategoryInquiryCharger( $categoryId ) ;
						//INQUIRY_CHARGER  category_id
						$p['categoryId'] = $categoryId ;
						$p['inquiryCharger'] = $inquiryCharger ;
					}
					$this->exeSql("sql_pdev_new_insert", $p) ;
				}
	
			}
		}
	
		return $return ;
	}
	
	/**
	 * 获取分类询价负责人
	 */
	function getCategoryInquiryCharger( $categoryId ){
		$inquiryCharger = $this->_getCategoryInquiryCharger($categoryId) ;
		return $inquiryCharger;
	}
	
	function _getCategoryInquiryCharger( $categoryId ){
		$sql = "select * from SC_PRODUCT_CATEGORY where id = '{@#categoryId#}'" ;
		$category = $this->getObject($sql, array("categoryId"=>$categoryId)) ;
		$inquiryCharger = $category['INQUIRY_CHARGER']  ;
		if( empty( $inquiryCharger ) ){
			$parentId = $category['PARENT_ID'] ;
			if( !empty($parentId) ){
				return $this->_getCategoryInquiryCharger($parentId) ;
			}
		}
		return $inquiryCharger ;
	}
	
	
	function initDevPropToProduct(){
		
	}
	
	function doFlow( $params ){
		ini_set('date.timezone','Asia/Shanghai');
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		
		$isGlobal = $params['isGlobal'] ;
		if( !empty($isGlobal) && $isGlobal == 1){
			$sql = "update sc_amazon_config set current_value = '{@#currentValue#}'  WHERE  name = 'DEFAULT_INQUIRY_CHARGER'" ;
			$this->exeSql($sql, array("currentValue"=>$params['INQUIRY_CHARGER'])) ;
		}
		//return ;
		try{
			$this->exeSql("sql_pdev_new_update", $params) ;
			
			$sql = "select * from sc_product_developer where dev_id = '{@#DEV_ID#}'" ;
			$productDeveloper = $this->getObject($sql, $params) ;
			
			if( isset( $params['isRelProduct'] ) && $params['isRelProduct'] == 1  ){
				/**
				 * 关联产品操作，将开发属性拷贝到开发产品中
				 * 1、分类
				 * 2、产品属性
				 * 3、成本数据
				 */
				//1、检查分类
				$categoryId = $productDeveloper['CATEGORY_ID'] ;
				$sql = "select * from sc_real_product_category where product_id= '{@#productId#}' and category_id = '{@#categoryId#}'" ;
				$productCategory= $this->getObject($sql, array("productId"=>$params['REAL_PRODUCT_ID'],"categoryId"=>$categoryId)) ;
				if( empty($productCategory) ){//添加分类
					$sql = "INSERT INTO sc_real_product_category  (PRODUCT_ID, CATEGORY_ID) VALUES ('{@#productId#}',  '{@#categoryId#}' )" ;
					$this->exeSql($sql,array("productId"=>$params['REAL_PRODUCT_ID'],"categoryId"=>$categoryId)) ;
				}
				
				/*保存产品属性*/
				$this->exeSql("sql_productDev_propCopyToRealProduct", $productDeveloper) ;
			}
			
			if( $params['FLOW_STATUS'] == 60 ||  $params['FLOW_STATUS']  == 70){
				//自动关联listing与货品
				$realId = $params['REAL_PRODUCT_ID'] ;
				$listingSku = $params['LISTING_SKU'] ;
				$accountId = $params['ACCOUNT_ID'] ;
				if( !empty($listingSku) && !empty($accountId) ){
					//1、自动关联listing
					$sql = "select * from sc_real_product where id='{@#realId#}'" ;
					$real = $this->exeSql($sql, array("realId"=>$realId)) ;
					$realSku = $real['REAL_SKU'] ;
					//判断关联是否存在
					$sql ="select * from sc_real_product_rel where real_id='{@#realId#}' and real_sku='{@#realSku#}' and account_id='{@#accountId#}' and sku='{@#sku#}'" ;
					$_params = array("realId"=>$realId,"reslSku"=>$realSku,"accountId"=>$accountId,"sku"=>$listingSku) ;
					$realRel = $this->exeSql($sql, $_params) ;
					if( empty($realRel) ){
						$sql = " INSERT INTO sc_real_product_rel 
										(REAL_SKU, 
										SKU, 
										ACCOUNT_ID, 
										REAL_ID
										)
										VALUES
										('{@#realSku#}', 
										'{@#sku#}', 
										'{@#accountId#}', 
										'{@#realId#}'
										)";
						$this->exeSql($sql, $_params) ;
					}
					//自动同步listing
					try{
						$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
						$Amazonaccount->checkProductValid($accountId,$listingSku)  ;
					}catch(Exception $e){}
				}
			} 
			
			if( $params['FLOW_STATUS'] == 72 ){ //审批通过，生成采购单
				$realId = $params['REAL_PRODUCT_ID'] ;
				$NewPurchaseService  = ClassRegistry::init("NewPurchaseService") ;
				
				$limitPrice = $NewPurchaseService->getDefaultLimitPrice( $realId ) ;
				$execut 	= $NewPurchaseService->getDefaultCharger( $realId ) ;
				
				$startTime = date('Y-m-d');
				$endTime  = date('Y-m-d',strtotime('+3 day'));
				$executor 			= $execut['charger'] ;
				//$purchaseQuantity = $params['purchaseQuantity'] ;
				//$params['status'] 加入采购计划
				$params = array(
						'realId'=>$realId,
						'planNum'=>$params['TRY_PURCHASE_NUM'] ,
						'limitPrice'=>$limitPrice ,
						'executor'=>$executor,
						'startTime'=>$startTime,
						'endTime'=>$endTime,
						'reqProductId'=>'',
						'devId'=>$params['ASIN'].'_'.$params['TASK_ID'],
						'loginId'=>'auto'
				);
				
				$sql = "select * from sc_amazon_account_product where account_id = '{@#ACCOUNT_ID#}' and sku = '{@#LISTING_SKU#}'" ;
				$accountProduct = $this->getObject($sql, $productDeveloper) ;
				
				$channel = 'AMAZON_NA' ;
				if(!empty($accountProduct)){
					$channel = $accountProduct['FULFILLMENT_CHANNEL'] ;
				}else{
					try{
						$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
						$Amazonaccount->checkProductValid($accountId,$listingSku)  ;
					}catch(Exception $e){}
				}
				
				$purchaseDetails= array() ;
				$purchaseDetails['sku'] 		  = $productDeveloper['LISTING_SKU'] ;
				$purchaseDetails['accountId'] = $productDeveloper['ACCOUNT_ID'] ;
				$purchaseDetails['quantity'] = $params['TRY_PURCHASE_NUM'];
				$purchaseDetails['asin'] = $productDeveloper['ASIN'] ;
				
				$purchaseDetails['fulfillment'] = $channel ;
				$purchaseDetails['supplyQuantity'] = '0' ;
				$_ = array() ;
				$_[] = $purchaseDetails ;
				$params['purchaseDetails'] = json_encode($_) ;
				
				$NewPurchaseService->createNewPurchaseProduct($params) ;
				
				/*
				sku:record.LISTING_SKU,
				accountId:record.ACCOUNT_ID,
				quantity:pq,
				asin:record.ASIN,
				fulfillment:record.FULFILLMENT_CHANNEL,
				supplyQuantity:record.TOTAL_SUPPLY_QUANTITY||'0'}
				*/

			}
			//保存轨迹
			
			$this->exeSql("sql_pdev_track_insert", $params) ;
			$dataSource->commit() ;
		}catch(Exception $e){
			$dataSource->rollback() ;
			print_r($e) ;
		}
	}
}