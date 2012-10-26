<?php
class Grid extends AppModel {
	var $useTable = "sc_election_rule" ;

	function getScriptRecords($query=null) {
		$limit =  $query["limit"] ;
		$start =  $query["start"] ;
		$sql = "SELECT * FROM sc_election_rule where status='enabled' limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}
	
	function getScriptCount($query=null){
		$sql = "SELECT count(*) FROM sc_election_rule where status='enabled'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getConfigRecords($query=null) {
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$where = " where 1 = 1 ";
		if( isset( $query["type"] ) && !empty( $query["type"]  ) ){
			$type = $query["type"] ;
			$where .= " and  sc_config.type = '$type' " ;
		}
		
		$sql = "SELECT * FROM sc_config $where limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}
	
	function getConfigCount($query=null){
		$where = " where 1 = 1 ";
		if( isset( $query["type"] ) && !empty( $query["type"]  ) ){
			$type = $query["type"] ;
			$where .= " and  sc_config.type = '$type' " ;
		}
		
		$sql = "SELECT count(*) FROM sc_config $where";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getUsersRecords($query=null) {
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$sql = "SELECT sc_user.* , (select name from sc_security_groups where sc_security_groups.code = sc_user.group_code ) as GROUP_NAME
			FROM sc_user limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}
	
	function getUsersCount($query=null){
		$sql = "SELECT count(*) FROM sc_user";
		$array = $this->query($sql);
		return $array ;
	}
	

	function getRuleRecords($query=null) {
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$scope =  $query["scope"] ;
		$accounts = $query['accounts'] ;
		
		$accountWhere = "" ;
		if( !empty($accounts) ){
			$accountWhere = " and sc_product.asin in ( select asin from sc_amazon_account_product where account_id in ($accounts) and status = 'Y') " ;
		}
		
		if( trim($scope)== ""){
			$sql = 'SELECT DISTINCT sc_product.*,sc_product_flow_details.DAY_PAGEVIEWS,
				sc_sale_competition.FM_NUM,sc_sale_competition.NM_NUM,sc_sale_competition.UM_NUM,
				sc_sale_fba.FBA_NUM,
				sc_sale_potential.REVIEWS_NUM,sc_sale_potential.QUALITY_POINTS,
				(SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL  
				FROM sc_product
				LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
				LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
				LEFT JOIN sc_sale_potential_ranking  ON sc_sale_potential_ranking.asin = sc_product.asin
				LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
                LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
				WHERE 1 = 1 and sc_product.asin not in ( select spfd.asin  from sc_product_filter_details spfd )
				and sc_product.asin not in (select sc_product_black.asin from sc_product_black) '.$accountWhere;
	
	
			foreach ($query["querys"] as $value) {  
					if( gettype($value) == "array" ){
						  $key = $value["key"] ; 
						  $type = $value["type"] ;
						  $val  = $value["value"] ; 
						  $relation = $value['relation'] ;
						  
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
	
			$sql = $sql." limit ".$start.",".$limit ;
			$array = $this->query($sql);
			return $array ;
 		}else{
 			$scopes =  "'".str_replace(",","','",trim($scope))."'" ;
 			
 			$sql = "SELECT DISTINCT  DISTINCT sc_product.*,sc_product_flow_details.DAY_PAGEVIEWS,
				sc_sale_competition.FM_NUM,sc_sale_competition.NM_NUM,sc_sale_competition.UM_NUM,
				sc_sale_fba.FBA_NUM,
				sc_sale_potential.REVIEWS_NUM,sc_sale_potential.QUALITY_POINTS,
				(SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL  
				FROM  sc_gather_asin , sc_product 
				LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
				LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
				LEFT JOIN sc_sale_potential_ranking  ON sc_sale_potential_ranking.asin = sc_product.asin
				LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
				LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
				WHERE sc_product.asin = sc_gather_asin.asin and sc_gather_asin.task_id in (".$scopes.")
                 and sc_product.asin not in ( select spfd.asin  from sc_product_filter_details spfd )
				and sc_product.asin not in (select sc_product_black.asin from sc_product_black) $accountWhere ";
	
			foreach ($query["querys"] as $value) {  
					if( gettype($value) == "array" ){
						  $key = $value["key"] ; 
						  $type = $value["type"] ;
						  $val  = $value["value"] ; 
						  $relation = $value['relation'] ;
						  
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
			$sql = $sql." limit ".$start.",".$limit ;
			$array = $this->query($sql);
			return $array ;
 		}
	}

	function getRuleCount($query=null){
		
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$scope =  $query["scope"] ;
		$accounts = $query['accounts'] ;
		
		$accountWhere = "" ;
		if( !empty($accounts) ){
			$accountWhere = " and sc_product.asin in ( select asin from sc_amazon_account_product where account_id in ($accounts) and status = 'Y' ) " ;
		}
		
		if( trim($scope)== ""){
			$sql = 'SELECT DISTINCT sc_product.* FROM sc_product
							LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
				LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
							LEFT JOIN sc_sale_potential_ranking  ON sc_sale_potential_ranking.asin = sc_product.asin
							LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
							LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
							where 1 = 1 and sc_product.asin not in ( select spfd.asin  from sc_product_filter_details spfd ) 
							 and sc_product.asin not in (select sc_product_black.asin from sc_product_black) '.$accountWhere ;
	
			foreach ($query["querys"] as $value) {  
					if( gettype($value) == "array" ){
						  $key = $value["key"] ; 
						  $type = $value["type"] ;
						  $val  = $value["value"] ; 
						  $relation = $value['relation'] ;
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
			
			$sql = "SELECT count(*) FROM (".$sql.") t ";
			$array = $this->query($sql);
			return $array ;
 		}else{
 			$scopes =  "'".str_replace(",","','",trim($scope))."'" ;
 			
 			$sql = "SELECT DISTINCT sc_product.* FROM sc_gather_asin , sc_product 
				LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
				LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
				LEFT JOIN sc_sale_potential_ranking  ON sc_sale_potential_ranking.asin = sc_product.asin
				LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
				LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
				WHERE sc_product.asin = sc_gather_asin.asin and sc_gather_asin.task_id in (".$scopes.")
				 and sc_product.asin not in ( select spfd.asin  from sc_product_filter_details spfd ) 
				and sc_product.asin not in (select sc_product_black.asin from sc_product_black) $accountWhere";
	
			foreach ($query["querys"] as $value) {  
					if( gettype($value) == "array" ){
						  $key = $value["key"] ; 
						  $type = $value["type"] ;
						  $val  = $value["value"] ; 
						  $relation = $value['relation'] ;
						  
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
	
			$sql = "SELECT count(*) FROM (".$sql.") t ";
			$array = $this->query($sql);
			return $array ;
 		}
		

		
	}
	
	function saveFilterResult($query=null,$user) {
		
		$filterName =  $query["filterName"] ;
		$id = "F_".date('U') ;
		$loginId = $user['LOGIN_ID'] ;
		$sql = "insert into sc_product_filter(id,name,create_time,creator) values('$id','$filterName',NOW(),'$loginId')" ;
		$this->query($sql);
		
		$scope =  $query["scope"] ;
		$accounts = $query['accounts'] ;
		
		$accountWhere = "" ;
		if( !empty($accounts) ){
			$accountWhere = " and sc_product.asin in ( select asin from sc_amazon_account_product where account_id in ($accounts) and status = 'Y' ) " ;
		}
		
		
		$sql = '' ;
		if( trim($scope)== ""){
			$sql = 'SELECT DISTINCT sc_product.asin FROM sc_product
				LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
				LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
				LEFT JOIN sc_sale_potential_ranking  ON sc_sale_potential_ranking.asin = sc_product.asin
				LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
				LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
				WHERE 1 = 1  and sc_product.asin not in ( select spfd.asin  from sc_product_filter_details spfd )
                 and sc_product.asin not in (select sc_product_black.asin from sc_product_black) '.$accountWhere ;
			
	
			foreach ($query["querys"] as $value) {  
					if( gettype($value) == "array" ){
						  $key = $value["key"] ; 
						  $type = $value["type"] ;
						  $val  = $value["value"] ; 
						  $relation = $value['relation'] ;
						  
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
				and sc_product.asin not in ( select spfd.asin  from sc_product_filter_details spfd ) $accountWhere" ;
	
			foreach ($query["querys"] as $value) {  
					if( gettype($value) == "array" ){
						  $key = $value["key"] ; 
						  $type = $value["type"] ;
						  $val  = $value["value"] ; 
						  $relation = $value['relation'] ;
						  
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

		$sql = "insert into sc_product_filter_details(asin,task_id) select t.asin , '".$id."' as task_id from ( ".$sql." ) t" ;

		$this->query($sql);
	}
	//product
	function getProductRecords($query=null , $id = null ){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$where = " where 1=1 " ;
		
		if( isset( $query["title"] )  && !empty( $query["title"]  )){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] )  && !empty( $query["asin"]  )){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}
		
		if( isset( $query["test_status"] )  && !empty( $query["test_status"]  )){
			$teststatus = $query["test_status"] ;
			$field = "test_status" ; 
			if($teststatus == 'focus'){
				$field = "user_status" ;
			}
			$where .= " and sc_product.$field = '".$teststatus."' " ;
		}
		
		if( isset( $query["categoryId"] ) && !empty( $query["categoryId"]  ) ){
			$categoryId = $query["categoryId"] ;
			$where .= " and sc_product.asin in ( select sc_product_category_rel.asin  from sc_product_category_rel where sc_product_category_rel.category_id = '$categoryId' ) " ;
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
		
		if( isset( $query["title"] )  && !empty( $query["title"]  )){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] )  && !empty( $query["asin"]  )){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}
		
		if( isset( $query["test_status"] )  && !empty( $query["test_status"]  )){
			$teststatus = $query["test_status"] ;
			$field = "test_status" ; 
			if($teststatus == 'focus'){
				$field = "user_status" ;
			}
			$where .= " and sc_product.$field = '".$teststatus."' " ;
		}
		
		if( isset( $query["categoryId"] ) && !empty( $query["categoryId"]  ) ){
			$categoryId = $query["categoryId"] ;
			$where .= " and sc_product.asin in ( select sc_product_category_rel.asin  from sc_product_category_rel where sc_product_category_rel.category_id = '$categoryId' ) " ;
		}
		
		$sql = "SELECT count(*) FROM sc_product  ".$where."";
		$array = $this->query($sql);
		return $array ;
	}
	
	
	
	function getTaskProductRecords($query=null , $id = null ){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$where = " where sc_product.asin = sc_gather_asin.asin and sc_gather_asin.task_id = '$id' " ;
		
		if( isset( $query["title"] ) ){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] ) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}
		
		$sql = "SELECT DISTINCT sc_product.*,sc_product_flow_details.DAY_PAGEVIEWS,
				sc_sale_competition.FM_NUM,sc_sale_competition.NM_NUM,sc_sale_competition.UM_NUM,
				sc_sale_fba.FBA_NUM,sc_sale_competition.TARGET_PRICE,
				sc_sale_potential.REVIEWS_NUM,sc_sale_potential.QUALITY_POINTS,
				(SELECT spi.local_url FROM sc_product_imgs spi WHERE spi.asin = sc_product.asin LIMIT 0,1 ) AS LOCAL_URL  
				FROM sc_gather_asin  , sc_product
				LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
				LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
				LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
                LEFT JOIN sc_product_flow_details  ON sc_product_flow_details.asin = sc_product.asin
				 ".$where." limit ".$start.",".$limit;
		
		$array = $this->query($sql);
		return $array ;
	}

	function getTaskProductCount($query=null , $id = null){
		$where = " where sc_product.asin = sc_gather_asin.asin and sc_gather_asin.task_id = '$id' " ;
		
		if( isset( $query["title"] ) ){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] ) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}
		
		$sql = "SELECT count(*) FROM  sc_product , sc_gather_asin  ".$where."";
		$array = $this->query($sql);
		return $array ;
	}

	//seller
	function getSellerRecords($query=null){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$sql = "SELECT sc_seller.*,( select count(1) from sc_gather_asin where sc_gather_asin.task_id = sc_seller.id ) as TOTAL
		,( select sc_user.name from sc_user where sc_user.login_id = sc_seller.creator ) as USERNAME
		FROM sc_seller limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getSellerCount($query=null){
		$sql = "SELECT count(*) FROM sc_seller";
		$array = $this->query($sql);
		return $array ;
	}
	
	//seller
	function getUploadRecords($query=null){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		//groupId
		$where = " where 1=1 " ;
		if( isset( $query["groupId"] ) && !empty( $query["groupId"]  ) ){
			$groupId = $query["groupId"] ;
			$where .= " and sc_upload.group_id = '$groupId' " ;
		}
		
		
		$sql = "SELECT sc_upload.*,( select count(1) from sc_gather_asin where sc_gather_asin.task_id = sc_upload.id ) as TOTAL,
				( select sc_user.name from sc_user where sc_user.login_id = sc_upload.uploador ) as USERNAME
              FROM sc_upload $where order by upload_time desc  limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getUploadCount($query=null){
		$where = " where 1=1 " ;
		if( isset( $query["groupId"] ) && !empty( $query["groupId"]  ) ){
			$groupId = $query["groupId"] ;
			$where .= " and sc_upload.group_id = '$groupId' " ;
		}
		
		$sql = "SELECT count(*) FROM sc_upload $where ";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getSellerUploadRecords($query=null){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$sql = "SELECT sc_seller.*,
				( select sc_user.name from sc_user where sc_user.login_id = sc_seller.creator ) as USERNAME
            FROM sc_seller order by create_time desc  limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getSellerUploadCount($query=null){
		$sql = "SELECT count(*) FROM sc_seller";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getGroupsRecords($query=null) {
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$sql = "SELECT * FROM sc_security_groups limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}
	
	function getGroupsCount($query=null){
		$sql = "SELECT count(*) FROM sc_security_groups";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getFunctionsRecords($query=null) {
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$sql = "SELECT * FROM sc_security_function limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}
	
	function getFunctionItemsRecords($query=null,$user) {
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$group = $user["GROUP_CODE"] ;
		$sql = "      SELECT ssf.* FROM sc_security_function ssf , sc_security_group_function ssgf 
                   WHERE ssf.code = ssgf.FUNCTION_CODE AND ssgf.GROUP_CODE = '$group'   ";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getFunctionsCount($query=null){
		$sql = "SELECT count(*) FROM sc_security_function";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getFlowRecords($query=null) {
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$sql = "SELECT sc_product_flow.*,
			( select sc_user.name from sc_user where sc_user.login_id = sc_product_flow.creator ) as USERNAME
			FROM sc_product_flow order by create_time desc limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}
	
	function getFlowCount($query=null){
		$sql = "SELECT count(*) FROM sc_product_flow";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getFlowDetailRecords($query=null,$taskId = null) {
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$where = " where 1 = 1 " ;
		if(!empty($taskId)){
			$sql = "SELECT * FROM
			( SELECT * FROM sc_product_flow_details WHERE task_id = '$taskId'
			UNION
			SELECT * FROM sc_product_flow_details_history WHERE task_id = '$taskId' ) t
			limit ".$start.",".$limit;
			$array = $this->query($sql);
			return $array ;
		}else{
			$sql = "SELECT * FROM sc_product_flow_details order by creattime desc limit ".$start.",".$limit;
			$array = $this->query($sql);
			return $array ;
		}
		
		
	}
	
	function getFlowDetailCount($query=null,$taskId = null){
		$where = " where 1 = 1 " ;
		if(!empty($taskId)){
			$sql = "SELECT count(*) FROM
			( SELECT * FROM sc_product_flow_details WHERE task_id = '$taskId'
			UNION
			SELECT * FROM sc_product_flow_details_history WHERE task_id = '$taskId' ) t";
			$array = $this->query($sql);
			return $array ;
		}else{
			$sql = "SELECT count(*) FROM sc_product_flow_details";
			$array = $this->query($sql);
			return $array ;
		}
	}
	
	function getProductBlackRecords($query=null , $user = null ){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$where = " where sc_product.asin = sc_product_black.asin and
            ( sc_product_black.creator = '".$user['LOGIN_ID']."' or (
			  sc_product_black.creator is null
			  and exists(
				select 1 from sc_product_filter_details,sc_product_filter
				where sc_product_filter_details.asin = sc_product_black.asin
                and sc_product_filter.creator = '".$user['LOGIN_ID']."'
              )
			) )
            " ;
		
		if( isset( $query["title"] ) && !empty( $query["title"]  ) ){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] )&& !empty( $query["asin"]  ) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}
		
		if( isset( $query["categoryId"] ) && !empty( $query["categoryId"]  ) ){
			$categoryId = $query["categoryId"] ;
			$where .= " and sc_product.asin in ( select sc_product_category_rel.asin  from sc_product_category_rel where sc_product_category_rel.category_id = '$categoryId' ) " ;
		}
		
		$sql = "SELECT DISTINCT sc_product.*,sc_product_black.*
				FROM sc_product , sc_product_black
				".$where." limit ".$start.",".$limit;
		
		$array = $this->query($sql);
		return $array ;
	}

	function getProductBlackCount($query=null , $user = null){
		$where = " where sc_product.asin = sc_product_black.asin and
            ( sc_product_black.creator = '".$user['LOGIN_ID']."' or (
			  sc_product_black.creator is null
			  and exists(
				select 1 from sc_product_filter_details,sc_product_filter
				where sc_product_filter_details.asin = sc_product_black.asin
                and sc_product_filter.creator = '".$user['LOGIN_ID']."'
              )
			) )
            " ;
		
		if( isset( $query["title"] ) && !empty( $query["title"]  ) ){
			$title = $query["title"] ;
			$where .= " and sc_product.title like '%".$title."%' " ;
		}
		
		if( isset( $query["asin"] )&& !empty( $query["asin"]  ) ){
			$asin = $query["asin"] ;
			$where .= " and sc_product.asin = '".$asin."' " ;
		}
		
		if( isset( $query["categoryId"] ) && !empty( $query["categoryId"]  ) ){
			$categoryId = $query["categoryId"] ;
			$where .= " and sc_product.asin in ( select sc_product_category_rel.asin  from sc_product_category_rel where sc_product_category_rel.category_id = '$categoryId' ) " ;
		}
		
		$sql = "SELECT count(*) FROM sc_product , sc_product_black
				".$where."";
		$array = $this->query($sql);
		return $array ;
	}
}