<?php
class ProductDev extends AppModel {
	var $useTable = 'sc_election_rule';
	
	function loadStatics(){
		
	}
	
	function getProductDev($id){
		return $this->getObject("select * from sc_product_dev where id='{@#id#}'", array("id"=>$id)) ;
	}
	
	//迁移
	function taskProductTransfer($params){
		//get source task
		$sourceTask = $this->getObject("select * from sc_product_filter where id = '{@#taskId#}'", $params) ;
		$targetTask = $this->getObject("select * from sc_product_filter where id = '{@#toTaskId#}'", $params) ;
		
		$this->exeSql("update sc_product_dev set task_id = '{@#toTaskId#}' where task_id = '{@#taskId#}' and asin = '{@#asin#}'", $params) ;
		//处理轨迹迁移
		$this->exeSql("update sc_product_dev_track set task_id = '{@#toTaskId#}' where task_id = '{@#taskId#}' and asin = '{@#asin#}'", $params) ;
		
		$params['TASK_ID'] = $params['toTaskId'] ;
		$params['ASIN'] = $params['asin'] ;
		$params['trackMemo'] = "从任务【".$sourceTask['CODE']."】迁移到任务【".$targetTask['CODE']."】" ;
		$this->exeSql("sql_pdev_track_insert", $params) ;
	}
	
	function addAsinToTask($params){
		$params['FLOW_STATUS'] = 10 ;//默认状态
		$asins = $params['asins'] ;
		
		
		$return = array() ;
		
		foreach ( explode(",", $asins) as $asin ){
			
			if( !empty( $asin ) ){
				//format asin
				preg_match_all("/[A-Za-z0-9-_]/i",$asin,$result);
				$asin=implode('',$result[0]);
				
				$params['ASIN'] = trim($asin);
				
				//判断ASIN是否正在开发中 结束标志：80（结束） 和 （15废弃）
				$p = $this->getObject("sql_productIsDeving", array("asin"=>trim($asin) )) ;
				if( !empty($p) ){
					$return[] = $p['TASK_ID'].'||'.$p['ASIN']."(任务名称：".$p['TASK_NAME'].")" ;
				}else{
					$this->exeSql("sql_pdev_insert", $params) ;
				}
				
			}
		}
		
		return $return ;
	}
	
	function deleteTaskProduct($params){
		$id = $params['id'] ;
		$this->exeSql("delete from sc_product_dev where task_id = '{@#taskId#}' and asin = '{@#asin#}'", $params) ;
	}
	
	function deleteTask($params){
		$taskId = $params['taskId'] ;
		$this->exeSql("delete from sc_product_filter where id = '{@#taskId#}'", $params) ;
	}
	
	function savePlan($params ){
		if( empty($params['id']) ){
			$this->exeSql("sql_pdev_plan_insert", $params) ;
		}else{
			$this->exeSql("sql_pdev_plan_update", $params) ;
		}
	}
	
	function getLowestLimitPrice($goodsId){
		$dev = $this->getObject("sql_pdev_getLastestLimitPriceProduct", array('id'=>$goodsId)) ;
		return $dev;
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
			$pd = $this->getObject("sql_pdev_findByAsinAndTaskId", $params) ;
			if( empty($pd) ){
				$this->exeSql("sql_pdev_insert", $params) ;
			}else{
				$this->exeSql("sql_pdev_update", $params) ;
			}
			
			if( $params['FLOW_STATUS'] == 72 ){ //审批通过，生成采购单
				/*$PurchaseService  = ClassRegistry::init("PurchaseService") ;
				
				$p = array('realId'=>$params['REAL_PRODUCT_ID'],
						'planNum'=>$params['TRY_PURCHASE_NUM'],
						'loginId'=>$params['loginId'],
						'devId'=>$params['ASIN'].'_'.$params['TASK_ID']
				) ;
				$PurchaseService->createItemForProductDev($p) ;*/
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
				$NewPurchaseService->createNewPurchaseProduct($params) ;
				/*
				$p = array('realId'=>$params['REAL_PRODUCT_ID'],
						'planNum'=>$params['TRY_PURCHASE_NUM'],
						'loginId'=>$params['loginId'],
						'devId'=>$params['ASIN'].'_'.$params['TASK_ID']
				) ;
				$PurchaseService->createItemForProductDev($p) ;
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
	
	function saveLimitPriceToGoods( $params ){
		$pd = $this->getObject("sql_pdev_findByAsinAndTaskId", $params) ;
		if( empty($pd) ){
			$this->exeSql("sql_pdev_insert", $params) ;
		}else{
			$this->exeSql("sql_pdev_update", $params) ;
		}
		//保存轨迹
		$this->exeSql("sql_pdev_track_insert", $params) ;
	}
	
	function saveTask($query){
		if(empty($query['id'])){
			$id = "F_".date('U') ;
			$query['id'] = $id ;
			$this->exeSql("sql_pdev_task_insert", $query) ;
		}else{
			$this->exeSql("sql_pdev_task_update", $query) ;
		}
	}
	
	function saveTaskResult($query=null) {
	
		$filterName =  $query["name"] ;
		$id = "F_".date('U') ;
		$loginId = $query['loginId'] ;
		$query['id'] = $id ;
		
		$this->exeSql("sql_pdev_task_insert", $query) ;
		
		//$sql = "insert into sc_product_filter(id,name,create_time,creator) values('$id','$filterName',NOW(),'$loginId')" ;
		//$this->query($sql);
	
		$scope =  $query["scope"] ;
		$accounts = $query['accounts'] ;
		$platformId = $query['platformId'] ;
	
		$accountWhere = "" ;
		if( !empty($accounts) ){
			$accountWhere = " and sc_product.asin in ( select asin from sc_amazon_account_product where account_id in ($accounts) and status = 'Y' ) " ;
		}
	
		$sql = '' ;
		if( trim($scope)== ""){
			$sql = "SELECT DISTINCT sc_product.asin FROM sc_product
			LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
			LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
			LEFT JOIN sc_sale_potential_ranking  ON sc_sale_potential_ranking.asin = sc_product.asin
			LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
			LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
			WHERE 1 = 1  and sc_product.asin not in ( select spfd.asin  from sc_product_filter_details spfd )
			and sc_product.asin not in ( select spfd.asin  from sc_product_dev spfd )
			and sc_product.asin not in (select sc_product_black.asin from sc_product_black)
							and sc_product.platform_id = '$platformId' 
							 $accountWhere" ;
	
			$_querys = json_decode( $query["querys"]  ) ;
			
			foreach ($_querys as $value) {
				if( gettype($value) == "object" ){
					$key = $value->key ;//["key"] ;
					$type = $value->type ;//["type"] ;
					$val  = $value->value ;//["value"] ;
					$relation = $value->relation ;//['relation'] ;
	
					if($relation == "like"){
						$sql =  $sql.' and '.$key.' '.$relation.' \'%'.$val.'%\'' ;
					}else{
						if( is_numeric($val) ){
							$sql =  $sql.' and '.$key.' '.$relation.' '.$val.'' ;
						}else{
							$sql =  $sql.' and '.$key.' '.$relation.' \''.$val.'\'' ;
						}
					}
				}
			}
			
		}else{
			$scopes =  "'".str_replace(",","','",trim($scope))."'" ;
	
			$sql = "SELECT DISTINCT sc_product.asin FROM sc_gather_asin , sc_product
			LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
			LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
			LEFT JOIN sc_sale_potential_ranking  ON sc_sale_potential_ranking.asin = sc_product.asin
			LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
			LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
			WHERE sc_product.asin = sc_gather_asin.asin and sc_gather_asin.task_id in  ( $scopes )
			and sc_product.asin not in ( select spfd.asin  from sc_product_filter_details spfd )
			and sc_product.asin not in ( select spfd.asin  from sc_product_dev spfd ) 
			and sc_product.platform_id = '$platformId'
			$accountWhere" ;
	
			$_querys = json_decode( $query["querys"]  ) ;
	
			foreach ($_querys as $value) {
				if( gettype($value) == "object" ){
					$key = $value->key ;//["key"] ;
					$type = $value->type ;//["type"] ;
					$val  = $value->value ;//["value"] ;
					$relation = $value->relation ;//['relation'] ;
	
					if($relation == "like"){
						$sql =  $sql.' and '.$key.' '.$relation.' \'%'.$val.'%\'' ;
					}else{
						if( is_numeric($val) ){
							$sql =  $sql.' and '.$key.' '.$relation.' '.$val.'' ;
						}else{
							$sql =  $sql.' and '.$key.' '.$relation.' \''.$val.'\'' ;
						}
					}
				}
			}
		}
	
		//避免重复，不能插入重复的
		$sql = "insert into sc_product_dev(asin,task_id,flow_status,create_time,creator) select t.asin , '$id',10, now(),'$loginId' as task_id from ( ".$sql." ) t" ;

		$this->query($sql);
	}
}