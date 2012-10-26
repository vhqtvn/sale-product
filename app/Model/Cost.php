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
	
	
	
	//product
	function getProductRecords($query=null , $id = null ){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$where = " where 1=1 " ;
		
		if( isset( $query["title"] ) ){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] ) && !empty($query["asin"]) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}else{
			$where .= " and sc_product.asin in ( select sc_product_filter_details.asin from sc_product_filter_details where sc_product_filter_details.status in (5,7)  ) " ;
		}
		
		$sql = "SELECT DISTINCT sc_product.*,sc_product_flow_details.DAY_PAGEVIEWS,
				sc_sale_competition.FM_NUM,sc_sale_competition.NM_NUM,sc_sale_competition.UM_NUM,
				sc_sale_fba.FBA_NUM,sc_sale_competition.TARGET_PRICE,
				sc_sale_potential.REVIEWS_NUM,sc_sale_potential.QUALITY_POINTS,
				(SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL  
				FROM sc_product
				LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
				LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
				LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
                LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
				".$where." limit ".$start.",".$limit;
		
		$array = $this->query($sql);
		return $array ;
	}

	function getProductCount($query=null , $id = null){
		$where = " where 1=1 " ;
		
		if( isset( $query["title"] ) ){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] ) && !empty($query["asin"]) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}else{
			$where .= " and sc_product.asin in ( select sc_product_filter_details.asin from sc_product_filter_details where sc_product_filter_details.status in (5,7)  ) " ;
		}
		
		$sql = "SELECT count(*) FROM sc_product  ".$where."";
		$array = $this->query($sql);
		return $array ;
	}
	
	//product
	function getProductCostRecords($query=null , $id = null ){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$where = " where 1=1 " ;
		
		if( isset( $query["asin"] ) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product_cost.asin = '".$asin."' " ;
		}
		
		$sql = "SELECT * from sc_product_cost
				".$where." limit ".$start.",".$limit;
		
		$array = $this->query($sql);
		return $array ;
	}

	function getProductCostCount($query=null , $id = null){
		$where = " where 1=1 " ;
		
		if( isset( $query["asin"] ) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product_cost.asin = '".$asin."' " ;
		}
		
		$sql = "SELECT count(*) FROM sc_product_cost  ".$where."";
		$array = $this->query($sql);
		return $array ;
	}

}