<?php
class Marketing extends AppModel {
	var $useTable = "sc_seller" ;
	
	function getMarketingTest($id){
		$sql = "SELECT * from sc_marketing_test  where id = '$id'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getMarketingTestDetails($id){
		$sql = "SELECT * from sc_marketing_test_details  where plan_id = '$id'";
		$array = $this->query($sql);
		return $array ;
	}
	

	public function saveMarketingTestPlan($data,$user){
		$loginId = $user["LOGIN_ID"] ;
		$sql = "insert into sc_marketing_test(name,plan_time,creator,create_time,memo)
		values('".$data["name"]."','".$data["plan_time"]."','$loginId',NOW(),'".$data["memo"]."')" ;
		$this->query($sql) ;
	}
	
	public function saveMarketingTestProducts($data,$user){
		$planId = $data['planId'] ;
		$asins  = $data['asins'] ;
		$loginId = $user["LOGIN_ID"] ;
		
		foreach( explode(",",$asins) as $asin ){
			try{
				//查询最低价
				$sql = "select * from sc_sale_competition where sc_sale_competition.asin = '$asin'" ;
				$competition = $this->query($sql) ; 
		
				$targetPrice = '' ;
				/*if( !empty($competition) ){
					$targetPrice = $competition[0]['sc_sale_competition']["TARGET_PRICE"] ;
				} ;
				
				$guidePrice = $targetPrice * 1.2 ;*/
				
				
				$sql = "insert into sc_marketing_test_details(asin,plan_id,creator,create_time,guide_price)
					values('".$asin."','".$planId."','$loginId',NOW(),'')" ;
				
					
				$this->query($sql) ;
				
				//update sc_product status
				$sql = "update sc_product set test_status = 'testing' where asin = '$asin'" ;
				$this->query($sql) ;
			}catch(Exception $e){
			}
		} ;
	}
	
	public function deleteMarketingTestProduct($data){
		$id = $data["id"] ;
		$sql = "delete from sc_marketing_test_details where id = '$id'" ;
		$this->query($sql) ;
		
		$sql = "update sc_product set test_status = '' where asin in ( select sc_marketing_test_details.asin
                from sc_marketing_test_details.id = '$id' )" ;
        
		$this->query($sql) ;
	}
	
	public function saveMarketingTestProduct($data,$user){
		$id = $data['id'] ;
		$guide_price = $data["guide_price"] ;
		$providor = $data["providor"] ;
		
		$sql = "update sc_marketing_test_details set 
				guide_price = '$guide_price',
				providor = '$providor'
				where id = '$id'
			" ;
		$this->query($sql) ;
	}
	
	public function getMarketingTestProduct($id){
		$sql = "select sc_marketing_test_details.* ,sc_product.*  from sc_marketing_test_details , sc_product where 
		sc_marketing_test_details.asin = sc_product.asin
		and sc_marketing_test_details.id = '$id'" ;
		
		return $this->query($sql) ;
	}
	
}