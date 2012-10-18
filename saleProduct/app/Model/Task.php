<?php
ignore_user_abort(1);
set_time_limit(0);

class Task extends AppModel {
	var $useTable = "sc_election_rule" ;

	/*
	function saveAsin( $productName ){
		if(empty($productName)) return ;

		$sql =  "insert into sc_product(asin) values('".$productName."')" ;
		$this->query($sql );
	}*/


	
	function saveFlowUpload($id ,$fileName , $user,$startTime ,$endTime ,$Days  ) {
		$loginId = $user["LOGIN_ID"] ;
		$sql = "insert into sc_product_flow(id , name,create_time,creator,start_time,end_time,days)
                values('".$id."','".$fileName."',NOW(),'$loginId','$startTime','$endTime',$Days )" ;
		$this->query($sql) ;
	}
	
	function saveFlowDetails($id ,$lineData,$loginId,$Days){
		//解析列
		//选择对应的ASIN插入到历史表
		//保存到历史表
	 	$sql = "insert into sc_product_flow_details_history select * from sc_product_flow_details where asin = '".$lineData["ASIN"]."'" ;
	 	$this->query($sql) ;
	 	
	 	//删除实时表数据
	 	$sql = "delete from sc_product_flow_details where asin = '".$lineData["ASIN"]."'" ;
	 	$this->query($sql) ;
		
		//插入最新记录
		//计算每天情况
		$pageviews = str_replace(",","",$lineData["PAGEVIEWS"]) ;
		$dayPageViews = round( $pageviews/$Days ) ;
		
		$dayUnitOrdered = round( $lineData["UNITS_ORDERED"]/$Days ) ;
		
		$sql = " INSERT INTO  sc_product_flow_details 
				(TASK_ID, 
				ASIN, 
				TITLE, 
				PAGEVIEWS, 
				PAGEVIEWS_PERCENT, 
				BUY_BOX_PERCENT, 
				UNITS_ORDERED, 
				ORDERED_PRODUCT_SALES, 
				ORDERS_PLACED, 
				CREATOR, 
				CREATTIME,
				DAY_PAGEVIEWS,
                DAY_UNITS_ORDERED
				)
				VALUES
				(
				'$id', 
				'".$lineData["ASIN"]."', 
				'".$lineData["TITLE"]."', 
				'".$lineData["PAGEVIEWS"]."', 
				'".$lineData["PAGEVIEWS_PERCENT"]."', 
				'".$lineData["BUY_BOX_PERCENT"]."', 
				'".$lineData["UNITS_ORDERED"]."', 
				'".$lineData["ORDERED_PRODUCT_SALES"]."', 
				'".$lineData["ORDERS_PLACED"]."', 
				'$loginId', 
				NOW(),
				$dayPageViews,
				$dayUnitOrdered
				) " ;
		$this->query($sql) ;
	}
}

