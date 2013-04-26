<?php
class ProductDev extends AppModel {
	var $useTable = 'sc_election_rule';
	
	function savePlan($params ){
		if( empty($params['id']) ){
			$this->exeSql("sql_pdev_plan_insert", $params) ;
		}else{
			$this->exeSql("sql_pdev_plan_update", $params) ;
		}
	}
	
	function doFlow( $params ){
		$pd = $this->getObject("sql_pdev_findByAsinAndTaskId", $params) ;
		if( empty($pd) ){
			$this->exeSql("sql_pdev_insert", $params) ;
		}else{
			$this->exeSql("sql_pdev_update", $params) ;
		}
		//保存轨迹
		$this->exeSql("sql_pdev_track_insert", $params) ;
	}
	
	function saveTaskResult($query=null) {
	
		$filterName =  $query["name"] ;
		$id = "F_".date('U') ;
		$loginId = $query['loginId'] ;
		$query['id'] = $id ;
		
		$this->exeSql("sql_pdev_task_insert", $query) ;
		
		//$sql = "insert into sc_product_filter(id,name,create_time,creator) values('$id','$filterName',NOW(),'$loginId')" ;
		//$this->query($sql);
	
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
			and sc_product.asin not in ( select spfd.asin  from sc_product_dev spfd )
			and sc_product.asin not in (select sc_product_black.asin from sc_product_black) '.$accountWhere ;
	
			$_querys = json_decode( $query["querys"]  ) ;
			
			foreach ($_querys as $value) {
				if( gettype($value) == "object" ){
					$key = $value->key ;//["key"] ;
					$type = $value->type ;//["type"] ;
					$val  = $value->value ;//["value"] ;
					$relation = $value->relation ;//['relation'] ;
	
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
			and sc_product.asin not in ( select spfd.asin  from sc_product_filter_details spfd )
			and sc_product.asin not in ( select spfd.asin  from sc_product_dev spfd ) $accountWhere" ;
	
			$_querys = json_decode( $query["querys"]  ) ;
	
			foreach ($_querys as $value) {
				if( gettype($value) == "object" ){
					$key = $value->key ;//["key"] ;
					$type = $value->type ;//["type"] ;
					$val  = $value->value ;//["value"] ;
					$relation = $value->relation ;//['relation'] ;
	
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
		$sql = "insert into sc_product_dev(asin,task_id,create_time,creator) select t.asin , '$id', now(),'$loginId' as task_id from ( ".$sql." ) t" ;

		$this->query($sql);
	}
}