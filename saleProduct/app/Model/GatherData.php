<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App::import('Model', 'GatherImpl/AmazonGatherImpl') ;
App::import('Model', 'GatherImpl/EbayGatherImpl') ;
App::import('Model', 'GatherService') ;

/**
 * 数据获取执行PHP
 * forward to 不同平台
 */
class GatherData extends AppModel {
	var $useTable = "sc_product_flow" ;
	var $platformId = null ;
	
	public function getPlatform($asin , $platform){
		$product = $this->getObject("sql_getProductByAsin", array("asin"=>$asin)) ;
		if( !empty($product) ){
			$platform_ = $product['PLATFORM_ID'] ;
			if( !empty($platform_) ){
				$platform = $platform_ ;
			}
		}
		
		if( empty($platform) ) $platform = 1 ;
		
		$this->platformId = $platform ;
		
		//获取
		$plat = $this->getObject("select * from sc_platform where id = '{@#platformId#}'", array("platformId"=>$platform)) ;
		return $plat ;
	}
	
	public function asinInfoPlatform($params ){
		$asin = $params['asin'] ;
		$platformId = $params['platformId'] ;
		
		$plat = $this->getPlatform($asin, $platformId ) ;
		
		$process = $plat['PROCCESS'] ;
		
		$r = new ReflectionClass($process);
		$instance = $r->newInstance();
		$instance->baseinfo( $this->platformId , $params ) ;
	}
	
	public function asinCompetitionPlatform($params ){
		
		$asin = $params['asin'] ;
		$platformId = $params['platformId'] ;
		
		$plat = $this->getPlatform($asin, $platformId ) ;
		
		$process = $plat['PROCCESS'] ;
		
		$r = new ReflectionClass($process);
		$instance = $r->newInstance();
		$instance->competition( $this->platformId , $params) ;
	}
	
	public function asinFbasPlatform($params ){
		$asin = $params['asin'] ;
		$platformId = $params['platformId'] ;
		
		$plat = $this->getPlatform($asin, $platformId ) ;
		
		$process = $plat['PROCCESS'] ;
		
		$r = new ReflectionClass($process);
		$instance = $r->newInstance();
		$instance->fbas( $this->platformId , $params) ;
	}
	
	/**
	 * 获取ASIN价格信息
	 */
	public function asinPrice( $params ) {
		$asin = $params['asin'] ;
		$platformId = $params['platformId'] ;
		
		$plat = $this->getPlatform($asin, $platformId ) ;
		
		$process = $plat['PROCCESS'] ;
		
		$r = new ReflectionClass($process);
		$instance = $r->newInstance();
		$instance->price( $this->platformId , $params) ;
	}

	/////////////////////////////////////////////////////
	///////////////////////获取产品///////////////////////
	/////////////////////////////////////////////////////
	/**
	 * 通过URL获取产品
	 * @param $id  商家采集ID
	 */
	public function sellerAsins($id,$logId=null){
		$service = new GatherService() ;
		
		$sellerurl = $service->getSellerUrl($id);
		$url = $sellerurl[0]['sc_seller']['url'];
		$platformId =  $sellerurl[0]['sc_seller']['platform_id'];
		
		$plat = $this->getObject("select * from sc_platform where id = '{@#platformId#}'", array("platformId"=>$platformId)) ;
		
		$process = $plat['PROCCESS'] ;
		
		$r = new ReflectionClass($process);
		$instance = $r->newInstance();
		$instance->url( $platformId , $id , $logId) ;

	}
}