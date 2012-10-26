<?php
class Salegrid extends AppModel {
	var $useTable = "sc_election_rule" ;


	function getFilterRecords($query=null , $id ){
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
		
		if( isset( $query["status"] ) ){
			$status = $query["status"] ;
			if( $status != "" )
			$where .= " and sc_product_filter_details.status in (".$status.") " ;
		}
		
		$sql = "SELECT sc_product.*, (SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL , 
					sc_product_filter_details.id as FILTER_ID, sc_product_filter_details.status as STATUS,
					sc_product_flow_details.DAY_PAGEVIEWS,
				sc_sale_competition.FM_NUM,sc_sale_competition.NM_NUM,sc_sale_competition.UM_NUM,
				sc_sale_fba.FBA_NUM,
				sc_sale_potential.REVIEWS_NUM,sc_sale_potential.QUALITY_POINTS,
				sc_sale_competition.TARGET_PRICE
					 FROM  sc_product_filter_details,sc_product 
					LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
				LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
				LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
                LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
					where sc_product.asin = sc_product_filter_details.asin ".$where." and
					sc_product_filter_details.task_id = '".$id."' limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getFilterCount($query=null , $id ){
		
		
		$where = "  " ;
		
		if( isset( $query["title"] ) ){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] ) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}
		
		if( isset( $query["status"] ) ){
			$status = $query["status"] ;
			if( $status != "" )
			$where .= " and sc_product_filter_details.status in (".$status.") " ;
		}
		
		$sql = "SELECT count(*) FROM sc_product, sc_product_filter_details
			where sc_product.asin = sc_product_filter_details.asin  ".$where." and sc_product_filter_details.task_id = '".$id."'";
		$array = $this->query($sql);
		return $array ;
	}
	
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
	
	function getFilterTask4Records($query=null,$user){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$loginId = $user["LOGIN_ID"] ;
		
		$where = " where 1 = 1 " ;
		
		if(isset($query["filterName"])){
			$filterName = $query["filterName"] ;
			$where .= " and sc_product_filter.name like '%$filterName%'" ;
		}
		

		$sql = "select t.* from (
           SELECT sc_product_filter.* ,
          (SELECT COUNT(*) FROM sc_product_filter_details scfd WHERE scfd.task_id = sc_product_filter.id  ) AS TOTAL,
          (SELECT COUNT(*) FROM sc_product_filter_details scfd WHERE scfd.task_id = sc_product_filter.id AND scfd.status in (5,7)) AS STATUS57
          FROM sc_product_filter $where ) t where t.STATUS57 > 0  order by t.create_time desc limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getFilterTask4Count($query=null,$user){
		$loginId = $user["LOGIN_ID"] ;
		
		$where = " where 1 = 1 " ;
		
		if(isset($query["filterName"])){
			$filterName = $query["filterName"] ;
			$where .= " and sc_product_filter.name like '%$filterName%'" ;
		}
		
		$sql = "select count(*) from (
           SELECT sc_product_filter.* ,
          (SELECT COUNT(*) FROM sc_product_filter_details scfd WHERE scfd.task_id = sc_product_filter.id  ) AS TOTAL,
          (SELECT COUNT(*) FROM sc_product_filter_details scfd WHERE scfd.task_id = sc_product_filter.id AND scfd.status in (5,7)) AS STATUS57
          FROM sc_product_filter $where ) t";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getPurchasePlanRecords($query=null,$user,$flag){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$loginId = $user["LOGIN_ID"] ;
		
		$where = " where 1 = 1 " ;
		
		if($flag == 2){
			$where .= " and sc_purchase_plan.executor = '".$loginId."' " ;
		}
		
		if( isset( $query["name"] )&& !empty($query["name"]) ){
			$title = $query["name"] ;
			$where .= " and sc_purchase_plan.name = '".$title."' " ;
		}
		
		if( isset( $query["type"] )&& !empty($query["type"]) ){
			$type = $query["type"] ;
			$where .= " and sc_purchase_plan.type = '".$type."' " ;
		}
		//EXECUTOR_NAME  EXECUTOR_NAME
		$sql = "SELECT  sc_purchase_plan.* ,
				( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.creator ) as USERNAME,
				( select sc_user.name from sc_user where sc_user.login_id = sc_purchase_plan.executor ) as EXECUTOR_NAME,
				( select count(*) from sc_purchase_plan_details where sc_purchase_plan_details.plan_id = sc_purchase_plan.id
						and ( sc_purchase_plan_details.status = 1 or sc_purchase_plan_details.status is null ) ) as STATUS1,
				( select count(*) from sc_purchase_plan_details where sc_purchase_plan_details.plan_id = sc_purchase_plan.id
						and  sc_purchase_plan_details.status = 2  ) as STATUS2,
				( select count(*) from sc_purchase_plan_details where sc_purchase_plan_details.plan_id = sc_purchase_plan.id
						and sc_purchase_plan_details.status = 3 ) as STATUS3,
				( select count(*) from sc_purchase_plan_details where sc_purchase_plan_details.plan_id = sc_purchase_plan.id
						and sc_purchase_plan_details.status = 4 ) as STATUS4,
				( select count(*) from sc_purchase_plan_details where sc_purchase_plan_details.plan_id = sc_purchase_plan.id
						and sc_purchase_plan_details.status = 5 ) as STATUS5
               FROM sc_purchase_plan $where  order by sc_purchase_plan.create_time desc limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getPurchasePlanCount($query=null,$user,$flag){
		$loginId = $user["LOGIN_ID"] ;
		
		$where = " where 1 = 1 " ;
		
		if($flag == 2){
			$where .= " and sc_purchase_plan.executor = '".$loginId."' " ;
		}
		
		
		if( isset( $query["name"] ) && !empty($query["name"]) ){
			$title = $query["name"] ;
			$where .= " and sc_purchase_plan.name = '".$title."' " ;
		}
		
		if( isset( $query["type"] ) && !empty($query["type"]) ){
			$type = $query["type"] ;
			$where .= " and sc_purchase_plan.type = '".$type."' " ;
		}
		
		$sql = "SELECT count(*) FROM sc_purchase_plan $where ";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getPurchasePlanDetailsRecords($query=null,$user){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$loginId = $user["LOGIN_ID"] ;
		$planId = $query["planId"] ;
		
		
		$where = " where 1 = 1 and  sc_purchase_plan_details.asin = sc_product.asin and sc_purchase_plan_details.plan_id = '$planId' " ;
		
		if(isset($query['status']) && !empty($query['status'])){
			$status = $query['status'] ;
			if( $status == 1 ){
				$where .=  " and ( sc_purchase_plan_details.status = '1' or sc_purchase_plan_details.status is null ) " ;
			}else{
				$where .=  " and sc_purchase_plan_details.status = '".$status."' " ;
			}
		}
		
		//询价状态  最低价 FBM TARGET_PRICE ， FBA最低价
		$sql = "SELECT  sc_purchase_plan_details.* ,
				 sc_product.TITLE , sc_product_flow_details.DAY_PAGEVIEWS ,
                (select sc_supplier.name from sc_supplier where sc_supplier.id = sc_purchase_plan_details.PROVIDOR ) as PROVIDOR_NAME,
				(SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL,
				( SELECT TOTAL_COST - PURCHASE_COST
				from sc_product_cost where sc_product_cost.asin = sc_product.asin and
					( sc_product_cost.type='FBM' or sc_product_cost.type is null )  ) as FBM_COST,
				sc_sale_competition.TARGET_PRICE as FBM_PRICE,
				( select min(sc_sale_fba_details.seller_price) from sc_sale_fba_details
						where sc_sale_fba_details.asin = sc_product.asin ) as FBA_PRICE,
				( SELECT TOTAL_COST - PURCHASE_COST
				from sc_product_cost where sc_product_cost.asin = sc_product.asin and
					( sc_product_cost.type='FBA' or sc_product_cost.type is null )  ) as FBA_COST,
				(select count(*) from sc_product_supplier where sc_product_supplier.asin = sc_product.asin
							and sc_product_supplier.num1 is not null and  sc_product_supplier.offer1 is not null  ) as XJ 
               FROM sc_purchase_plan_details , sc_product
			   left join sc_product_flow_details on sc_product_flow_details.asin = sc_product.asin
				LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
			$where
			limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getPurchasePlanDetailsCount($query=null,$user){
		$loginId = $user["LOGIN_ID"] ;
		$planId = $query["planId"] ;
		
		$where = " where 1 = 1 and  sc_purchase_plan_details.asin = sc_product.asin and sc_purchase_plan_details.plan_id = '$planId' " ;
		
		if(isset($query['status']) && !empty($query['status'])){
			$status = $query['status'] ;
			if( $status == 1 ){
				$where .=  " and ( sc_purchase_plan_details.status = '1' or sc_purchase_plan_details.status is null ) " ;
			}else{
				$where .=  " and sc_purchase_plan_details.status = '".$status."' " ;
			}
		}
		$sql = "SELECT count(*) FROM sc_purchase_plan_details , sc_product $where ";
		$array = $this->query($sql);
		return $array ;
	}
	
	
	function getPurchasePlanPrintsRecords($query=null,$user){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$loginId = $user["LOGIN_ID"] ;
		$planId = $query["planId"] ;
		
		$where = " where 1 = 1 and sc_purchase_plan_details.status = '3' and  sc_purchase_plan_details.asin = sc_product.asin and sc_purchase_plan_details.plan_id = '$planId' " ;
		//询价状态  最低价 FBM TARGET_PRICE ， FBA最低价
		$sql = "SELECT  sc_purchase_plan_details.* ,
				 sc_product.TITLE ,
				 sc_product.KNOWLEDGE ,
                (select sc_supplier.name from sc_supplier where sc_supplier.id = sc_purchase_plan_details.PROVIDOR ) as PROVIDOR_NAME,
                (select sc_supplier.contactor from sc_supplier where sc_supplier.id = sc_purchase_plan_details.PROVIDOR ) as PROVIDOR_CONTACTOR,
                (select sc_supplier.phone from sc_supplier where sc_supplier.id = sc_purchase_plan_details.PROVIDOR ) as PROVIDOR_PHONE,
				(SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL
               FROM sc_purchase_plan_details , sc_product
			$where
			limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getPurchasePlanPrintsCount($query=null,$user){
		$loginId = $user["LOGIN_ID"] ;
		$planId = $query["planId"] ;
		
		$where = " where 1 = 1 and sc_purchase_plan_details.status = '3'  and  sc_purchase_plan_details.asin = sc_product.asin and sc_purchase_plan_details.plan_id = '$planId' " ;
		
		$sql = "SELECT count(*) FROM sc_purchase_plan_details , sc_product $where ";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getDeletePurchasePlanDetailsRecords($query=null,$user){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$loginId = $user["LOGIN_ID"] ;
		
		$where = " where 1 = 1  and sc_purchase_plan_details_delete.asin = sc_product.asin order by sc_purchase_plan_details_delete.DELETE_TIME desc " ;

		$sql = "SELECT  sc_purchase_plan_details_delete.* ,
				 sc_product.TITLE , sc_product_flow_details.DAY_PAGEVIEWS ,
                (SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL,
				( SELECT
					TOTAL_COST - PURCHASE_COST
				from sc_product_cost where sc_product_cost.asin = sc_product.asin limit 0,1 ) as P_COST,
				sc_sale_competition.TARGET_PRICE as TARGET_PRICE,
				(select name from sc_user where sc_user.login_id =sc_purchase_plan_details_delete.creator ) as CREATOR_NAME,
				(select name from sc_user where sc_user.login_id =sc_purchase_plan_details_delete.deletor ) as DELETOR_NAME,
				(select name from sc_purchase_plan where sc_purchase_plan.id =sc_purchase_plan_details_delete.plan_id ) as PLAN_NAME
               FROM sc_purchase_plan_details_delete , sc_product
			   left join sc_product_flow_details on sc_product_flow_details.asin = sc_product.asin
				LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
			$where
			limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getDeletePurchasePlanDetailsCount($query=null,$user){
		$loginId = $user["LOGIN_ID"] ;
		
		$where = " where 1 = 1 and sc_purchase_plan_details_delete.asin = sc_product.asin " ;
		
		$sql = "SELECT count(*) FROM sc_purchase_plan_details_delete , sc_product $where ";
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