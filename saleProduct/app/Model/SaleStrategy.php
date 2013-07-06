<?php
class SaleStrategy extends AppModel {
	var $useTable = "sc_seller" ;
	
	function saveListingConfig($params){
		$sku 			= $params['sku'];
		$accountId = $params['accountId'];
		$strategy = $params['strategy'] ;
		debug($params);
		//删除数据库所有配置
		$this->exeSql("sql_saleStrategy_deleteConfig", array('sku'=>$sku,'accountId'=>$accountId)) ;
		
		$strategy = json_decode($strategy) ;
		foreach ( $strategy as $s ){
			$week = $s->week ;
			$hour = $s->hour ;
			$price = $s->price ;
			//echo $week.'---'.$hour.'>>'.$price.'\n\r' ;
			
			$insertParams = array(
						'sku'=>$sku,
					'accountId'=>$accountId,
					'loginId'=>$params['loginId'],
					'week'=>$week ,
					'hour'=>$hour,
					'price'=>$price
					) ;
			//插入配置表
			$this->exeSql("sql_saleStrategy_insertListingConfig", $insertParams) ;
			
		}
	}
}