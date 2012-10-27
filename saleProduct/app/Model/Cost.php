<?php
class Cost extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function saveCost($data,$user){
		$loginId = $user['LOGIN_ID'] ;
		
		if( isset($data['ID']) && !empty($data["ID"]) ){
			$sql = " 
					UPDATE sc_product_cost 
						SET
						TYPE = '".$data['TYPE']."' , 
						PURCHASE_COST = '".$data['PURCHASE_COST']."' , 
						ASIN = '".$data['ASIN']."' , 
						BEFORE_LOGISTICS_COST = '".$data['BEFORE_LOGISTICS_COST']."' , 
						TARIFF = '".$data['TARIFF']."' , 
						AMAZON_FEE = '".$data['AMAZON_FEE']."' , 
						VARIABLE_CLOSURE_COST = '".$data['VARIABLE_CLOSURE_COST']."' , 
						OORDER_PROCESSING_FEE = '".$data['OORDER_PROCESSING_FEE']."' , 
						USPS_COST = '".$data['USPS_COST']."' , 
						TAG_COST = '".$data['TAG_COST']."' , 
						PACKAGE_COST = '".$data['PACKAGE_COST']."' , 
						STABLE_COST = '".$data['STABLE_COST']."' , 
						WAREHOURSE_COST = '".$data['WAREHOURSE_COST']."' , 
						LOST_FEE = '".$data['LOST_FEE']."' , 
						LABOR_COST = '".$data['LABOR_COST']."' , 
						SERVICE_COST = '".$data['SERVICE_COST']."' , 
						OTHER_COST = '".$data['OTHER_COST']."' ,
						TOTAL_COST = '".$data['TOTAL_COST']."' 
						WHERE
						ID = '".$data['ID']."'
					" ;
			$this->query($sql) ;
		}else{
			$sql = "INSERT INTO sc_product_cost 
					( 
					TYPE, 
					PURCHASE_COST, 
					ASIN, 
					BEFORE_LOGISTICS_COST, 
					TARIFF, 
					AMAZON_FEE, 
					VARIABLE_CLOSURE_COST, 
					OORDER_PROCESSING_FEE, 
					USPS_COST, 
					TAG_COST, 
					PACKAGE_COST, 
					STABLE_COST, 
					WAREHOURSE_COST, 
					LOST_FEE, 
					LABOR_COST, 
					SERVICE_COST, 
					OTHER_COST,
					TOTAL_COST
					)
					VALUES
					(
					'".$data['TYPE']."', 
					'".$data['PURCHASE_COST']."', 
					'".$data['ASIN']."', 
					'".$data['BEFORE_LOGISTICS_COST']."', 
					'".$data['TARIFF']."', 
					'".$data['AMAZON_FEE']."', 
					'".$data['VARIABLE_CLOSURE_COST']."', 
					'".$data['OORDER_PROCESSING_FEE']."', 
					'".$data['USPS_COST']."', 
					'".$data['TAG_COST']."', 
					'".$data['PACKAGE_COST']."', 
					'".$data['STABLE_COST']."', 
					'".$data['WAREHOURSE_COST']."', 
					'".$data['LOST_FEE']."', 
					'".$data['LABOR_COST']."', 
					'".$data['SERVICE_COST']."', 
					'".$data['OTHER_COST']."',
					'".$data['TOTAL_COST']."'
					) " ;
			$this->query($sql) ;
		}
	}
	
	public function getProductCost($id){
		$sql = "SELECT sc_product_cost.* FROM sc_product_cost where id = '$id'";
		$array = $this->query($sql);
		return $array ;
	}
	
	public function getProductCostByAsinType($asin , $type){
		$sql = "" ;
		if( $type == 'FBA' ){
			$sql = "SELECT sc_product_cost.* FROM sc_product_cost where asin = '$asin' and type = 'FBA' ";
		}else if( $type == 'FBM' ){
			$sql = "SELECT sc_product_cost.* FROM sc_product_cost where asin = '$asin' and ( type = 'FBM' or type is null ) ";
		}
		
		$array = $this->query($sql);
		return $array ;
	}
}