<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App::import('Model', 'GatherService') ;
App::import('Model', 'System') ;
App::import('Model', 'Utils') ;
App::import('Model', 'Log') ;
App::import('Model', 'Task') ;
App::import('Model', 'System') ;

App :: import('Vendor', 'Snoopy');
App :: import('Vendor', 'simple_html_dom');
App :: import('Vendor', 'Amazon');


class EbayGatherImpl extends AppModel {
	var $useTable = "sc_product_cost" ;
	var $platformId = null ;
	
	public function getEbaySiteUrl($asin=null ,$platform){
		$system = new System() ;
	
		$this->platformId = $platform;
	
		//通过平台获取URL路径
		$config = $system->getPlatformConfig($platform) ;
		$this->config = $config ;
		$siteUrl = $config["EBAY_SITE_URL"] ;
	
		return $siteUrl ;
	}
	
	public function baseinfo($platformId,$params){
		$asin = $params['asin'] ;
		$platform = $platformId ;
		$id = $params['id'] ;
		$index = $params['index'] ;
		$logId = $params['taskId'] ;
		
		
		$utils = new Utils() ;
		$service = new GatherService() ;
		$log = new Log() ;
		$system = new System() ;
		//判断是否为合法的ASIN
		if( strlen(trim($asin)) < 10 || strlen(trim($asin)) >=15 ) {
			return ;
		} ;
		
		$siteUrl = $this->getEbaySiteUrl($asin,$platform) ;//  $config["AMAZON_SITE_URL"] ;
		
		try{
			$url = "$siteUrl/itm/" . $asin;
			$snoopy = new Snoopy ;
			$snoopy->agent =  $this->getAgent($index) ;
			$snoopy->referer = $url ;
			$snoopy->rawheaders["Pragma"] = "no-cache";
		
			if( $snoopy->fetch($url) ){
				$Result = $snoopy->results ;
				//debug($Result) ;
				$html = new simple_html_dom();
				$html->load( $Result  ,true ,false );
		
				$config = $this->config ;//$system->getPlatformConfig( $this->platformId ) ;
		
				try{
					//get title
					$title = $utils->getProp($html ,'#itemTitle' ) ;
		
					
					//byLineReorder
					$brand = "" ;
					$technical = $html->find(".itemAttr",0) ;
					$productDetails = '' ;//
					$Dimensions = '' ;
					$Weight = '' ;

					$productDescription = "" ;
					if( $html->find("#desc_div",0) != null ){
						$productDescription =  $html->find("#desc_div",0)  ;//desc_div
					}
					
					$array['asin'] = $asin ;
					$array['title'] = trim($title) ;
					$array['TECHDETAILS'] = trim($technical) ;//itemAttr
					$array['PRODUCTDETAILS'] = trim($productDetails) ;
					$array['DESCRIPTION'] = trim($productDescription) ;
					$array['DIMENSIONS'] = trim($Dimensions) ;
					$array['WEIGHT'] = trim($Weight) ;
					$array['BRAND'] = trim($brand) ;
					$array['PLATFORM_ID'] = $platform;

					//更新产品基本信息
					$service->updateProduct($array);
					//echo "start get Image...." ;
					///保存图片
					$images = $html->find("#icImg",0) ;

					if( $images!=null ){
						$src = $images->src ;
						$title = $images->alt ;
		
						try{
							$localUrl = "images/ebay/".$asin."/".basename($src) ;
							$utils->downloads($src,$asin,"images/ebay/".$asin ) ;
							$service->addImage($asin,$src,$title,$localUrl) ;
						}catch(Exception $e){
							//print_r($e) ;
						}
					}
					//return ;
					//保存竞争信息-----------------------------------------------------
		
						//get point
						$point = $html->find("#si-fb",0)->plaintext ; ;//si-fb

						//get review
						$reviews = $html->find("span.mbg-l a",0)->plaintext ;

						//get ranking
						$rankArray = array() ;
						$service->saveSalePotential($asin,$reviews , $point , $rankArray ) ;
			
					//----------------------------------------------------------------
				}catch(Exception $e){
					$log->savelog($logId,"get product[".$asin."] details failed:::: ".$e->getMessage()) ;
				}
				$html->clear() ;
				unset($html) ;
				$log->savelog($logId,"get product[".$asin."] details success!") ;
			}
		
			unset($snoopy) ;
		}catch(Exception $e){
			$log->saveException($logId,saveException) ;
		}
	}
	
	public function competition($platformId,$params){
	
	}
	
	public function fbas($platformId,$params){
	
	}
	
	public function price($platformId,$params){
	
	}
	
	public function url($platformId,$params){
	
	}
}