<?php
class Sale extends AppModel {
	var $useTable = "sc_seller" ;
	
	function savePrintTime($params){
		$printTime = $params['printTime'] ;
    	$products = $this->exeSqlWithFormat("sql_purchase_task_productInedForPrint", array("taskId"=>$params['taskId'],"printTimeNull"=>1)) ;
    	
    	foreach( $products as $product ){
    		$id = $product['TASK_ID'] ;
    		
    		$this->exeSql("update sc_purchase_task_products set print_time = '{@#printTime#}' where task_id = '{@#taskId#}'
                    and product_id = '{@#productId#}'
    				and print_time is null", array('taskId'=>$id,'productId'=>$product['PRODUCT_ID'],'printTime'=>$printTime)) ;
    	}
	}
	
	/**
	 * 供应商采用
	 * @param unknown_type $params
	 */
	function setSupplierFlag($params){
		$inquery = $this->getObject("sql_purchase_plan_product_inquiry", $params) ;
		if( empty($inquery) ){
			return "该供应商还未询价！" ;
		}else{
			$sql="sql_purchase_supplier_setUsedFlag";
			$this->exeSql($sql, $params) ;
			return "" ;
		}
	}
	
	function savePurchaseTaskProducts($params){
		$taskId = $params['taskId'] ;
		$products = $params['products'] ;
		
		foreach ( explode(",", $products)  as $product ){
			try{
				$this->exeSql("sql_purchase_task_product_insert", array('taskId'=>$taskId,'productId'=>$product)) ;
			}catch(Exception $e){
				print_r($e) ;
			}
		}
		
	}
	
	function deletePurchaseTask($params){
		$taskId = $params['taskId'] ;
		//删除任务关系
		$this->exeSql("delete from sc_purchase_task_products where task_id = '{@#taskId#}'", $params ) ;
		//删除任务
		$this->exeSql("delete from sc_purchase_task where id = '{@#taskId#}'", $params ) ;
	}
	
	function getPurchasePlan($id){
		$sql = "SELECT sc_purchase_plan.* 
			,( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.creator ) as USERNAME
			,( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.executor ) as EXECUTOR_NAME
			from sc_purchase_plan  where id = '$id'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getLastestPlan($params){
		$limit = $params['limit'] ;
		$sql = "SELECT sc_purchase_plan.*
		,( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.creator ) as USERNAME
		,( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.executor ) as EXECUTOR_NAME
		from sc_purchase_plan 
				order by create_time desc
				limit  $limit
				";
		$array = $this->exeSqlWithFormat($sql, array()) ;
		return $array ;
	}
	
	function updatePlanForPlanProduct($params){
		//$id = $params['id'] ;
		//$planId = $params['planId'] ;
		$sql = "update sc_purchase_plan_details set plan_id = '{@#planId#}' where id='{@#id#}'" ;
		$this->exeSql($sql, $params) ;
	}

	function getPurchasePlanDetails($id){
		return $this->exeSql("sql_purchase_plan_details_listForSKU", array('planId'=>$id) ) ;
		//$sql = "SELECT * from sc_purchase_plan_details  where plan_id = '$id'";
		//$array = $this->query($sql);
		//return $array ;
	}
	
	public function saveSeller($data){
		$sql = "insert into sc_seller(name,url) values('".$data['name']."','".$data['url']."')" ;
		$this->query($sql) ;
	}
	
	public function savePurchaseTask($data){
		$loginId = $data["loginId"] ;
		if( isset($data['id']) && !empty($data['id'])){
			$this->exeSql("sql_purchase_task_update", $data) ;
		}else{
			$this->exeSql("sql_purchase_task_insert", $data) ;
		}
	}
	
	public function savePurchasePlan($data){
		$loginId = $data["loginId"] ;
		if( isset($data['id']) && !empty($data['id'])){
			$this->exeSql("sql_purchase_plan_update", $data) ;
		}else{
			$this->exeSql("sql_purchase_plan_insert", $data) ;
		}
	}
	
	/**
	 * 保存选择的采购产品
	 * @param unknown_type $params
	 */
	public function saveSelectedProduct($params){
			$skus = $params['sku'] ;
			$loginId = $params['loginId'] ;
			$planId = $params['planId'] ;
	
			foreach( explode(",", $skus) as $sku ){
				try{
					    if( empty($sku) ) continue ;
						$query = array( 'sku'=>$sku , 'planId'=>$planId , 'loginId'=> $loginId ) ;
						//判断是否存在
						$ps = $this->getObject("sql_purchasePlanProductsIsExists", $query) ;
						if(empty($ps)){
							//根据sku获取货品ID
							$sql = "select * from sc_real_product where sku = '{@#sku#}'" ;
							$product = $this->getObject($sql, $query) ;
							$query['realId'] = $product['ID'] ;
							$this->exeSql("sql_insert_purchasePlanProducts", $query);
						}
				}catch(Exception $e){ }
			};
	}
	
	public function savePurchasePlanProducts($data,$user){
		$planId = $data['planId'] ;
		$asins  = $data['asins'] ;
		$loginId = $user["LOGIN_ID"] ;
		foreach( explode(",",$asins) as $asin ){
			try{
				$sql = "insert into sc_purchase_plan_details(asin,plan_id,creator,create_time)
					values('".$asin."','".$planId."','$loginId',NOW())" ;
				$this->query($sql) ;
			}catch(Exception $e){}
		} ;
	}
	
	public function deletePurchasePlan( $params,$user ){
		$planId = $params['planId'] ;
	
		//删除计划产品
		//$this->exeSql("sql_delete_sc_pp_product", $params) ;
		
		//删除计划
		$this->exeSql( "sql_delete_sc_pp" , $params ) ;
	}
	
	public function checkValidSkus( $params ){
		$correct = array() ;
		$incorrect = array() ;
		$type = "" ;
		if( isset( $params['asins'] ) ){
			$array =  explode(",", $params['asins'] ) ;
			foreach($array as $asin ){
				$asin = trim($asin) ;
				$product = $this->getObject("sql_checkPurchaseProductIsValid.byAsin", array('asin'=>$asin) ) ;
				if(empty($product)){
					$incorrect[] = $asin ;
				}else{
					$correct[] =$product ;
				}
			}
		}else if(isset($params['skus'])){
			$array =  explode(",", $params['skus'] ) ;
			foreach($array as $sku ){
				$sku = trim($sku) ;
				$product = $this->getObject("sql_checkPurchaseProductIsValid.bySku", array('sku'=>$sku) ) ;
				if(empty($product)){
					$incorrect[] = $sku ;
				}else{
					$correct[] =$product ;
				}
			}
		}
		
		return array('correct'=>$correct,'incorrect'=>$incorrect) ;
		
	}
	
	public function deletePurchasePlanProduct($data){
		$id = $data["id"] ;
		
		$sql = "delete from sc_purchase_plan_details_track where pd_id = '$id'" ;
		$this->query($sql) ;
		$sql = "delete from sc_purchase_plan_details where id = '$id'" ;
		$this->query($sql) ;
	}
	
	public function warehouseIn($data){
		//保存采购canp
		$this->exeSql("sql_update_sc_purchase_task_product" , $data ) ;
		//执行入库操作
		$inventory  = ClassRegistry::init("Inventory") ;
		
		$inventoryParams = array() ;
		$inventoryParams['warehouseId'] =$data['warehouseId']  ;
		//$inventoryParams['diskId']  = $params['diskId'] ;
		
		$id = $data['productId'] ;
		//通过ID找到对应的货品ID
		$obj = $this->getObject("sql_saleproduct_getGoodsIdByPurchasePlanProductId", array('id'=>$id)) ;
		
		$details = array() ;
		$details[] = array(
				'goodsId'=>$obj['ID'] ,
				'quantity'=>$data['qualifiedProductsNum'] ,
				'badQuantity'=>0 ,
				'inventoryType'=>'1' //普通库存
		) ;
		
		$inventoryParams['details'] =  json_encode( $details ) ;
		
		//return $inventoryParams;
		$inventory->in( $inventoryParams ) ;
	}
	
	public function savePurchasePlanProduct($data){
		$this->exeSql("sql_update_sc_purchase_plan_details" , $data ) ;
	}
	
	public function savePurchaseTaskProduct($data){
		$this->exeSql("sql_update_sc_purchase_task_product" , $data ) ;
		
		//更新计划产品实际采购数量
		$this->exeSql(" UPDATE sc_purchase_plan_details SET real_purchase_num = (
				SELECT SUM(sptp.QUALIFIED_PRODUCTS_NUM) FROM sc_purchase_task_products sptp WHERE sptp.PRODUCT_ID = sc_purchase_plan_details.ID
		)  where  id= '{@#productId#}' " , $data ) ;
	}
	
	
	public function updatePurchasePlanProductStatus($data,$user){
		$id = $data['id'] ;
		$status = $data["status"] ;
		
		$sql = "update sc_purchase_plan_details set
				status = '$status'
				where id = '$id'
			" ;
		$this->query($sql) ;
	}
	
	
	
	public function getProductPlanProduct($id){
		/*$sql = "select sc_purchase_plan_details.* ,sc_real_product.*  from sc_purchase_plan_details , sc_real_product where 
		sc_purchase_plan_details.asin = sc_real_product.asin
		and sc_purchase_plan_details.id = '$id'" ;*/
		return $this->getObject("sql_purchase_plan_details_listForSKU", array('id'=>$id) ) ;
		
		//return $this->query($sql) ;
	}
	
		
	public function removeProduct($data,$user){
		//设置产品状态为废弃
		$filterId = $data["filterId"] ;
		$asin     = $data["asin"] ;
		$description =   $data["description"] ;
		
		try{
		$sql = "insert into sc_product_black(asin,description,creator,create_time)
			values('".$asin."','".$description."','".$user['LOGIN_ID']."',NOW())" ;
		$this->query($sql) ;
		}catch(Exception $e){}
	}
	
	public function doTaskProductStatus($data){
		//设置产品状态为废弃
		$id = $data["id"] ;
		$taskId = $data["taskId"] ;
		$memo =   $data["memo"] ;
		$status = $data["status"] ;
		$data['productId'] = $id ;
		//更新状态
		$this->exeSql("sql_purchase_task_product_updateStatus", $data) ;
		//添加轨迹
		$this->exeSql("sql_purchase_plan_product_insertTrack", $data) ;
		
		if( $status == 80 ){ //采购结束
			$sql = "UPDATE sc_product_dev spd SET spd.FLOW_STATUS = 80
						WHERE CONCAT(spd.ASIN,'_',spd.TASK_ID) IN (
							SELECT DEV_ID FROM sc_purchase_plan_details sppd  where id = '{@#productId#}'
						) " ;
			$this->exeSql($sql, $data) ;
		}
		
		//更新采购价格到货品成本区域 "realQuotePrice":"90","realShipFee":"10"
		/*$sku = $data['sku'] ;
		$realQuotePrice = $data['realQuotePrice'] ;
		$realShipFee = $data['realShipFee'] ;*/
		$cost  = ClassRegistry::init("Cost") ;
		$cost->saveCostWithPurchase($data) ;
	}
	
	public function doStatus($data){
		//设置产品状态为废弃
		$id = $data["id"] ;
		$memo =   $data["memo"] ;
		$status = $data["status"] ;
		//更新状态
		$this->exeSql("sql_purchase_plan_product_updateStatus", $data) ;
		//添加轨迹
		$this->exeSql("sql_purchase_plan_product_insertTrack", $data) ;
	}
	
	public function updateProductFilterStatus($data){
		//设置产品状态为废弃
		$filterId = $data["filterId"] ;
		$asin     = $data["asin"] ;
		$description =   $data["description"] ;
		$status = $data["status"] ;
		
		if( $status != 3 ){
			//删除黑名单操作
			$sql = "delete from sc_product_black where asin = '$asin'" ;
			$this->query($sql) ;
		}
		
		$sql = "update sc_product_filter_details set status = '".$status."' where id = '".$filterId."'" ;
		$this->query($sql)  ;
	}
	
	public function updateProductTestStatus($data){
		$asin     = $data["asin"] ;
		$description =   $data["description"] ;
		$testStatus = $data["testStatus"] ;
		
		$flag = "test_status = '".$testStatus."'" ;
		if( $testStatus == 'focus'||$testStatus == 'unfocus' ){
			$flag = "user_status = '".$testStatus."'" ;
		}
		
		$sql = "update sc_product set comment = '".$description."',$flag where asin = '".$asin."'" ;
		$this->query($sql)  ;
	}
	
	public function productKnowlege($data){
		$asin     = $data["asin"] ;
		$description =   $data["description"] ;
		
		$sql = "update sc_product set knowledge = '".$description."' where asin = '".$asin."'" ;
		$this->query($sql)  ;
	}	
}