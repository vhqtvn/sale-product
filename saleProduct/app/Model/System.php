<?php
class System extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function getAccountPlatformConfig($accountId){
		$items = $this->exeSqlWithFormat("sql_getPlatformConfig_ForAccountId",array("accountId"=>$accountId)) ;
		
		$result = array() ;
		foreach( $items as $item ){
			$result[ $item['KEY'] ] = $item['VALUE'] ;
			$result[ 'PLATFORM_ID' ] = $item['PLATFORM_ID'] ;
		}
		
		return $result ;
	}
	
	public function getPlatformConfigByAsin($asin,$platform=null){
		$system = new System() ;
	
		$product = $this->getObject("sql_getProductByAsin", array("asin"=>$asin)) ;
		if( !empty($product) ){
			$platform_ = $product['PLATFORM_ID'] ;
			if( !empty($platform_) ){
				$platform = $platform_ ;
			}
		}
		if( empty($platform) ) $platform = 1 ;
	
		//通过平台获取URL路径
		$config = $system->getPlatformConfig($platform) ;
		return $config ;
	}
	
	public function getPlatformConfig($platformId){
		$items = $this->exeSqlWithFormat("sql_getPlatformConfigByPlatformId",array("platformId"=>$platformId)) ;
	
		$result = array() ;
		foreach( $items as $item ){
			$result[ $item['KEY'] ] = $item['VALUE'] ;
		}
	
		return $result ;
	}

}