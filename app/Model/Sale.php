<?php
class Sale extends AppModel {
	var $useTable = "sc_seller" ;
	
	function getPurchasePlan($id){
		$sql = "SELECT sc_purchase_plan.* 
			,( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.creator ) as USERNAME
			,( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.executor ) as EXECUTOR_NAME
			from sc_purchase_plan  where id = '$id'";
		$array = $this->query($sql);
		return $array ;
	}

	function getPurchasePlanDetails($id){
		$sql = "SELECT * from sc_purchase_plan_details  where plan_id = '$id'";
		$array = $this->query($sql);
		return $array ;
	}
	
	public function saveSeller($data){
		$sql = "insert into sc_seller(name,url) values('".$data['name']."','".$data['url']."')" ;
		$this->query($sql) ;
	}
	
	public function savePurchasePlan($data,$user){
		$loginId = $user["LOGIN_ID"] ;
		if( isset($data['id']) && !empty($data['id'])){
			$sql = "
				UPDATE sc_purchase_plan 
					SET
					NAME = '".$data["name"]."' , 
					PLAN_TIME = '".$data["plan_time"]."' , 
					MEMO = '".$data["memo"]."' , 
					TYPE = '".$data["type"]."' , 
					EXECUTOR = '".$data["executor_id"]."'
					
					WHERE
					ID = '".$data["id"]."' 
				" ;
				$this->query($sql) ;
		}else{
			$sql = "insert into sc_purchase_plan(name,plan_time,creator,create_time,status,memo,type,executor)
				values('".$data["name"]."','".$data["plan_time"]."','$loginId',NOW(),1,'".$data["memo"]."','".$data["type"]."','".$data["executor_id"]."')" ;
			$this->query($sql) ;
		}
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
	
	public function savePurchasePlanProduct($data,$user){
		$id = $data['id'] ;
		$plan_num = $data["plan_num"] ;
		$quote_price = $data["quote_price"] ;
		$providor = $data["providor"] ;
		$sample = $data["sample"] ;
		$sample_code = $data["sample_code"] ;
		$area =   $data["area"] ;
		$memo =   $data["memo"] ;
		
		print_r($data) ;
		
		$sql = "update sc_purchase_plan_details set plan_num = '$plan_num',
				quote_price = '$quote_price',
				providor = '$providor',
				sample = '$sample',
				area = '$area',
				sample_code = '$sample_code',
				memo = '$memo'
				where id = '$id'
			" ;
		$this->query($sql) ;
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
		$sql = "select sc_purchase_plan_details.* ,sc_product.*  from sc_purchase_plan_details , sc_product where 
		sc_purchase_plan_details.asin = sc_product.asin
		and sc_purchase_plan_details.id = '$id'" ;
		
		return $this->query($sql) ;
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