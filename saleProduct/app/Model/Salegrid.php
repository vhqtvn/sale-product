<?php
class Salegrid extends AppModel {
	var $useTable = "sc_election_rule" ;

	function getFilterTaskRecords($query=null,$type,$user){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$loginId = $user["LOGIN_ID"] ;
		
		$where = " where 1 = 1 " ;
		if( $type == 1){
			$where = $where." and sc_product_filter.creator = '$loginId' " ;
		}
		
		$sql = "SELECT sc_product_filter.* ,
(SELECT COUNT(*) FROM sc_product_filter_details	 scfd WHERE scfd.task_id = sc_product_filter.id  ) AS TOTAL,
(SELECT COUNT(*) FROM sc_product_filter_details	 scfd WHERE scfd.task_id = sc_product_filter.id  AND scfd.status IN (1,2)) AS STATUS12,
(SELECT COUNT(*) FROM sc_product_filter_details	 scfd WHERE scfd.task_id = sc_product_filter.id  AND scfd.status = 3 ) AS  STATUS3,
(SELECT COUNT(*) FROM sc_product_filter_details	 scfd WHERE scfd.task_id = sc_product_filter.id  AND scfd.status = 4) AS  STATUS4,
(SELECT COUNT(*) FROM sc_product_filter_details	 scfd WHERE scfd.task_id = sc_product_filter.id  AND scfd.status = 5 ) AS  STATUS5,
(SELECT COUNT(*) FROM sc_product_filter_details	 scfd WHERE scfd.task_id = sc_product_filter.id  AND scfd.status = 6 ) AS  STATUS6,
(SELECT COUNT(*) FROM sc_product_filter_details	 scfd WHERE scfd.task_id = sc_product_filter.id  AND scfd.status = 7 ) AS  STATUS7,
( select sc_user.name from sc_user where sc_user.login_id = sc_product_filter.creator ) as USERNAME 
  FROM sc_product_filter $where  order by sc_product_filter.create_time desc limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getFilterTaskCount($query=null,$type,$user){
		$loginId = $user["LOGIN_ID"] ;
		
		$where = " where 1 = 1 " ;
		if( $type == 1){
			$where = $where." and sc_product_filter.creator = '$loginId' " ;
		}
		
		$sql = "SELECT count(*) FROM sc_product_filter $where ";
		$array = $this->query($sql);
		return $array ;
	}
	
	
	function getFilterApplyRecords($query=null , $id ){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$where = "  " ;
		
		if( isset( $query["title"] ) ){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] ) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}
		
		if( isset( $query["taskId"] ) ){
			$taskId = $query["taskId"] ;
			$where .= " and sc_product_filter_details.task_id = '".$taskId."'" ;
		}
		
		//and sc_product_filter_details.task_id = '".$id."'

		$where .= " and sc_product_filter_details.status in (5,7) " ;

		
		$sql = "SELECT sc_product.*, (SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL , 
					sc_product_filter_details.id as FILTER_ID, sc_product_filter_details.status as STATUS
					 FROM sc_product, sc_product_filter_details 
					where sc_product.asin = sc_product_filter_details.asin ".$where."  limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getFilterApplyCount($query=null , $id ){
		
		
		$where = "  " ;
		
		if( isset( $query["title"] ) ){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] ) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}
		
		if( isset( $query["taskId"] ) ){
			$taskId = $query["taskId"] ;
			$where .= " and sc_product_filter_details.task_id = '".$taskId."'" ;
		}
		
		$where .= " and sc_product_filter_details.status in (5,7) " ;
		//." and sc_product_filter_details.task_id = '".$id."'"
		$sql = "SELECT count(*) FROM sc_product, sc_product_filter_details
			where sc_product.asin = sc_product_filter_details.asin  ".$where;
		$array = $this->query($sql);
		return $array ;
	}
}