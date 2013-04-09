<?php
class Sale extends AppModel {
	var $useTable = "sc_seller" ;
	
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
	
	function getPurchasePlan($id){
		$sql = "SELECT sc_purchase_plan.* 
			,( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.creator ) as USERNAME
			,( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.executor ) as EXECUTOR_NAME
			from sc_purchase_plan  where id = '$id'";
		$array = $this->query($sql);
		return $array ;
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
	
	public function deletePurchasePlanProduct($data,$user){
		$id = $data["id"] ;
		$loginId = $user["LOGIN_ID"] ;
		
		$sql = "SELECT * from sc_purchase_plan_details  where id = '$id'";
		$details = $this->query($sql);
		$detail = $details[0]['sc_purchase_plan_details'] ;
		
		//copy to 
		$sql = "
				INSERT INTO sc_purchase_plan_details_delete 
					( 
					ASIN, 
					PLAN_NUM, 
					REAL_NUM, 
					PLAN_ID, 
					CREATOR, 
					CREATE_TIME, 
					QUOTE_PRICE, 
					COST, 
					PROVIDOR, 
					SAMPLE, 
					SAMPLE_CODE, 
					DELETOR, 
					DELETE_TIME
					)
					VALUES
					(
					'".$detail['ASIN']."', 
					'".$detail['PLAN_NUM']."', 
					'".$detail['REAL_NUM']."', 
					'".$detail['PLAN_ID']."', 
					'".$detail['CREATOR']."', 
					'".$detail['CREATE_TIME']."', 
					'".$detail['QUOTE_PRICE']."', 
					'".$detail['COST']."', 
					'".$detail['PROVIDOR']."', 
					'".$detail['SAMPLE']."', 
					'".$detail['SAMPLE_CODE']."', 
					'$loginId', 
					NOW()
					)" ;
	 	$this->query($sql)  ;
		
		
		$sql = "delete from sc_purchase_plan_details where id = '$id'" ;
		$this->query($sql) ;
	}
	
	public function savePurchasePlanProduct($data){
		
		$this->exeSql("sql_update_sc_purchase_plan_details" , $data ) ;
		/*$id = $data['id'] ;
		$plan_num = $data["plan_num"] ;
		$quote_price = $data["quote_price"] ;
		$providor = $data["providor"] ;
		$sample = $data["sample"] ;
		$sample_code = $data["sample_code"] ;
		$area =   $data["area"] ;
		$memo =   $data["memo"] ;
		
		$sql = "update sc_purchase_plan_details set plan_num = '$plan_num',
				quote_price = '$quote_price',
				providor = '$providor',
				sample = '$sample',
				area = '$area',
				sample_code = '$sample_code',
				memo = '$memo'
				where id = '$id'
			" ;
		$this->query($sql) ;*/
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