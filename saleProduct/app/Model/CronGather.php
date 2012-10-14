<?php

App::import('Model', 'Amazonaccount') ;
App::import('Model', 'Task') ;
App::import('Model', 'Config') ;
App::import('Vendor', 'Snoopy');
App::import('Vendor', 'simple_html_dom');
App::import('Vendor', 'Amazon');

class CronGather extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function gatherAmazonCompetitions($id,$level){
		
		$amazonaccount = new Amazonaccount() ;
		$Task = new Task() ;
		$Config = new Config() ;
				
		//更新采集状态
		try{
			//获取商家产品asin
			$array = $amazonaccount->getAccountProductsForLevel($id,$level) ;
			$index = 0 ;
			foreach( $array as $arr ){
				$index = $index + 1 ;
				$asin = $arr['sc_amazon_account_product']['ASIN'] ;
				$this->fetchCompetions($asin,$id ) ;
			}
		
		}catch(Exception $e){
			$Task->savelog($id, "error::::".$e->getMessage() );
		}
	}
	
	
	public function gatherAmazonFba($id,$level){
		
		$amazonaccount = new Amazonaccount() ;
		$Task = new Task() ;
		$Config = new Config() ;
		//更新采集状态
		try{
			$array = $amazonaccount->getAccountProductsForLevel($id,$level) ;
			$index = 0 ;
			foreach( $array as $arr ){
				$index = $index + 1 ;
				$asin = $arr['sc_amazon_account_product']['ASIN'] ;
				$this->fetchFba($asin,$id ) ;
			}
		}catch(Exception $e){
			$Task->savelog($id, "error::::".$e->getMessage() );
		}
	}
	
	/**
	 * 采集amazon账户产品
	 * $id 账户系统编号
	 * $code 账户CODE
	 */
	public function amazonAsin($id , $level ){//
	
		$amazonaccount = new Amazonaccount() ;
		$Task = new Task() ;
		$Config = new Config() ;
		
		$Task->savelog("cron", "start............" );
		
		//更新采集状态
		try{
		
			$asintemplate = $Config->getAmazonConfig("AMAZON_ACCOUNT_PRODUCT_URL") ;
			$asinArray = $amazonaccount->getAccountProductsForLevel($id,$level) ;
			
			//开始采集产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				$asin = $_asin['sc_amazon_account_product']['ASIN'] ;
				$index = $index + 1 ;
				$this->fetchAsin($asin,$id) ;
			} 
		}catch(Exception $e){
			try{
				$Task->savelog("cron", "error::::".$e->getMessage() );
			}catch(Exception $e){}
		}

	}
	
	/**
	 * gather shipping info
	 */
	public function amazonShippingAsin($id , $level ){//
	
		$amazonaccount = new Amazonaccount() ;
		$Task = new Task() ;
		$Config = new Config() ;
		
		$account = $amazonaccount->getAccount($id) ;
		$account = $account[0]['sc_amazon_account'] ;
		try{
			//$this->Task->clearlog($id) ;
		
			$asintemplate = $Config->getAmazonConfig("AMAZON_ACCOUNT_PRODUCT_URL") ;
			$asinArray = $amazonaccount->getAccountProductsForLevel($id,$level) ;
			
			//开始采集产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				
				$asin = $_asin['sc_amazon_account_product']['ASIN'] ;
				
				$condition = $_asin['sc_amazon_account_product']['ITEM_CONDITION'] ;
				$condition = $condition == 1?"used":"new" ;
				
				$index = $index + 1 ;
				$this->fetchAmazonAsin($_asin['sc_amazon_account_product']['SKU'],$asin,$account['CODE'],$condition,$asintemplate,$id ,$index) ;
				
			} 
		}catch(Exception $e){
			$Task->savelog($id, "error::::".$e->getMessage() );
		}

	}
	
	public function fetchAmazonAsin($sku,$asin,$code,$condition,$asintemplate=null,$id=null,$index = null) {
		
		$amazonaccount = new Amazonaccount() ;
		$Task = new Task() ;
		$Config = new Config() ;
		
		try{
				$url = str_replace("{code}",$code,$asintemplate) ;
			    $url = str_replace("{asin}",$asin,$url) ;
			    $url = str_replace("{condition}",$condition,$url) ;

				$d = date("U") ;
				$url = $url."&dd=$d" ;
				
			$snoopy = new Snoopy;
			
			$snoopy->agent = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)";
			$snoopy->referer = "http://www.amazon.com/";
			$snoopy->rawheaders['Pragma'] = 'no-cache' ;

			if( $snoopy->fetch($url) ){
			
				$Result = $snoopy->results ;

				$html = new simple_html_dom();
				$html->load( $Result  ,true ,false );
				
				try{
					$array['asin'] = $asin ;
					$array['sku'] = $sku ;
			
					$isFM = "" ;
					if($condition == "new"){
						if( $html->find(".buckettitle h2",0) != null ){
							$isFM = trim($html->find(".buckettitle h2",0)->plaintext) ;
							if( $isFM == "Featured Merchants" ){
								$isFM = "FM" ;
							}else{
								$isFM = "NEW" ;
							}
						}
					}
	
					$plusShippingText = "" ;
					if( $html->find(".result .price_shipping",0) != null ){
						$plusShippingText = trim($html->find(".result .price_shipping",0)->plaintext) ;
					}
					
					$priceText = "" ;
					if( $html->find(".result .price",0) != null ){
						$priceText = trim($html->find(".result .price",0)->plaintext) ;
					}
					
					$priceText = trim( str_replace(array("&nbsp;","+","Shipping","Free","shipping",'$'),"",$priceText) ) ;
					
					
					$plusShippingText = trim( str_replace(array("&nbsp;","+","Shipping","Free","shipping",'$'),"",$plusShippingText) ) ;
					
					$array['plusShippingText'] = $plusShippingText ;
					$array['priceText'] = $priceText ;
					$array['isFM'] = $isFM ;
		

					$Task->updateAmazonProductShipping($array,$code,$id);
				}catch(Exception $e){
					$Task->savelog($id,"get product[".$asin."] price failed:::: ".$e->getMessage()) ;	
				}
				$html->clear() ;
				unset($html) ;	
			}else{
			}
			unset($snoopy) ;
		}catch( Exception $e){
			$Task->savelog($id,"get product[".$asin."] price error:::".$e->getMessage()) ;	
		}
	}
	
	public function fetchAsin($asin,$id=null,$index = null) {
		
		
		$amazonaccount = new Amazonaccount() ;
		$Task = new Task() ;
		$Config = new Config() ;
		
		if( strlen(trim($asin)) < 9 || strlen(trim($asin)) >=11 ) {
			return ;
		} ; 
		
		try{
			$url = "http://www.amazon.com/dp/" . $asin;
			//$url = "http://www.amazon.com/gp/offer-listing/".$asin ;
			
			$snoopy = new Snoopy;
			
			if( $snoopy->fetch($url) ){
				//http://www.amazon.com/gp/offer-listing/B00005NPOB
				$Result = $snoopy->results ;
				$html = new simple_html_dom();
				$html->load( $Result  ,true ,false );
				
				//$html = file_get_html($url); 
				try{
					//get title
					$title = $Task->getProp($html ,'#btAsinTitle' ) ;
					 //$html->find('#btAsinTitle',0)->innertext;
					
					//byLineReorder
					$brand = "" ;
					foreach( $html->find('.buying a') as $e ){
						$href = $e->href ;
						if( strripos( $href , "brandtextbin" )>0 ){
							$brand = trim( $e->plaintext ) ;
							break;	
						}
					}
					//$brand =$this->Task->getProp($html ,'.buying a' ) ;
					
					$technical = "" ;
					if($html->find("#technical_details",0)!=null){
						$technical = $html->find("#technical_details",0)->parent()->find(".content",0)->plaintext ;
					}
		
					$h2s = $html->find("h2") ;
					$productDetails = '' ;
					$Dimensions = '' ;
					$Weight = '' ;
					foreach(  $html->find("h2") as $e){ 
						if( $e->plaintext == 'Product Details' ) {
							$productDetails = $e->next_sibling ()->plaintext ;
							
							foreach(  $e->next_sibling()->find("b") as $f ){
								if( trim( $f->plaintext ) == "Product Dimensions:" ){
									$Dimensions = $f->parent()->plaintext ;
									$Dimensions = str_replace("Product Dimensions:" ,"",$Dimensions);
								}else if( trim( $f->plaintext ) == "Shipping Weight:" ){
									$Weight = $f->parent()->plaintext ;
									$Weight = str_replace("Shipping Weight:" ,"",$Weight);
									$Weight = str_replace("(View shipping rates and policies)" ,"",$Weight);
								}
							}
						}
			        }  
					
					$productDescription = "" ;
					if( $html->find('#productDescription',0) != null){
						$productDescription = $html->find('#productDescription',0)->find(".content",0)->plaintext ;
					}
					
					$array['asin'] = $asin ;
					$array['title'] = trim($title) ;
					$array['TECHDETAILS'] = trim($technical) ;
					$array['PRODUCTDETAILS'] = trim($productDetails) ;
					$array['DESCRIPTION'] = trim($productDescription) ;
					$array['DIMENSIONS'] = trim($Dimensions) ;
					$array['WEIGHT'] = trim($Weight) ;
					$array['BRAND'] = trim($brand) ;
					//DIMENSIONS
					//WEIGHT
					
					//更新产品基本信息
					$Task->updateProduct($array);
					
					///保存图片
					$images = $html->find("#prodImageCell img",0) ;
					if( $images!=null ){
						$src = $images->src ;
						$title = $images->alt ;
						try{
							$localUrl = "images/amazon/".$asin."/".basename($src) ;
							$Task->addImage($asin,$src,$title,$localUrl) ;
							$Task->downloads($src,$asin) ;
						}catch(Exception $e){}
					}
					//保存竞争信息
					$this->saveProductPotential($asin , $html) ;
				}catch(Exception $e){
					$Task->savelog($id,"get product[".$asin."] details failed:::: ".$e->getMessage()) ;	
				}
				$html->clear() ;
				unset($html) ;
			}
			
			unset($snoopy) ;
		}catch(Exception $e){
			$Task->savelog($id,"get product[".$asin."] error:::".$e->getMessage()) ;	
		}
	}
	
		
	public function fetchFba($asin = null,$id = null ){
		
		$amazonaccount = new Amazonaccount() ;
		$Task = new Task() ;
		$Config = new Config() ;
		//try{
		
			$d = date("U") ;
			
			$url = "http://www.amazon.com/gp/offer-listing/$asin?shipPromoFilter=1&dd=$d" ;
			
			//echo $url ;
			$snoopy = new Snoopy;
			
			$snoopy->agent = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)";
			$snoopy->referer = "http://www.amazon.com/";

			if( $snoopy->fetch($url) ){
				
				$Result = $snoopy->results ;
				
				$html = new simple_html_dom();
				$html->load( $Result ,true ,false );
				try{
					//$html = file_get_html($url); 
					
					$h2s = $html->find("h2") ;
					
					$base = array() ;
					$details = array() ;
			
					foreach(  $html->find("h2") as $e){ 
						if( $e->plaintext == 'Featured Merchants' ) {//1-5 of 15 offers 
							$this->_processRowCompetetion($e,$details,"FBA" ,$base , 'FBA_NUM') ;
						}
					}  
			        
			        $Task->saveFba($asin , $base , $details) ;
				}catch(Exception $e){}
				$html->clear() ;
				unset($html) ;
			}
			unset($snoopy) ;
	}	
	
	public function fetchCompetions($asin = null,$id = null ){
		
		$amazonaccount = new Amazonaccount() ;
		$Task = new Task() ;
		$Config = new Config() ;
		//try{
		
			$d = date("U") ;
			
			//http://www.amazon.com/gp/offer-listing/B00007GQLU?shipPromoFilter=1
			$url =  "http://www.amazon.com/gp/offer-listing/".$asin."?dd=$d"  ;
			
			//echo $url ;
			$snoopy = new Snoopy;
			
			$snoopy->agent = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)";
			$snoopy->referer = "http://www.amazon.com/";

			if( $snoopy->fetch($url) ){
				
				$Result = $snoopy->results ;
				
				$html = new simple_html_dom();
				$html->load( $Result ,true ,false );
				try{
					//$html = file_get_html($url); 
					
					$h2s = $html->find("h2") ;
					
					$base = array('FM_NUM'=>'0','NM_NUM'=>'0','UM_NUM'=>'0') ;
					$details = array() ;
					
					foreach(  $html->find("h2") as $e){ 
						if( $e->plaintext == 'Featured Merchants' ) {//1-5 of 15 offers 
							$this->_processRowCompetetion($e,$details,"F" ,$base , 'FM_NUM') ;
							
						}else if( $e->plaintext == 'New' ) {
							$this->_processRowCompetetion($e,$details,"N" ,$base , 'NM_NUM') ;
							
						}else if( $e->plaintext == 'Used' ) {
							$this->_processRowCompetetion($e,$details,"U" ,$base , 'UM_NUM') ;
						}
			        }   
			        
			        $Task->saveCompetions($asin , $base , $details) ;
				}catch(Exception $e){}
				$html->clear() ;
				unset($html) ;
			}
			unset($snoopy) ;
		//}catch(Exception $e){
		//	$this->Task->savelog($id,"get product[".$asin."] error:::".$e->getMessage()) ;	
		//}
	}
	
	
	public function _processRowCompetetion($e , $details ,$type , $base , $numType){
		
			$numberofresults   = $e->next_sibling ()->plaintext ;
			$ary = explode("of",$numberofresults) ;
			$_ = str_replace("offers","",$ary[1] ) ;
			$umNum = trim( $_ ) ;
			$base[$numType] = $umNum ;
			
			$detailTables = $e->parent ;
			while(true){
				if( $detailTables->class == "resultsheader" ){
					break ;
				}
				$detailTables = $detailTables->parent ;
			}
			$index = 0 ;
			foreach( $detailTables->next_sibling()->find(".result") as $table ){
				$price = $table->find(".price",0)->plaintext ;
				$priceShipping =  $table->find(".price_shipping",0)->plaintext ;
				$sellerInformation = $table->find(".sellerInformation",0)  ;
				
				$baseInfo = $sellerInformation->find(".seller a",0) ;
				$sellerUrl= '' ;
				$sellerName = '' ;
				$sellerImg = '' ;
				$prePositive = '' ;
				$totalRating = '' ;
				$country = '' ;
				if($baseInfo != null){
					$sellerUrl = $baseInfo->href ;
					$sellerName = $baseInfo->plaintext ;
				}else {
					$baseInfo = $sellerInformation->find("a",0) ;
					if($baseInfo !=null){
						$sellerUrl = $baseInfo->href ;
						$baseImage = $baseInfo->find("img",0) ;
						$sellerImg = $baseImage->src ;
						$sellerName = $baseImage->alt ;
					}
				}
				
				$positiveInfo = $sellerInformation->find(".rating a b",0) ;
				if($positiveInfo != null){
					$prePositive = $positiveInfo->plaintext ;
					$prePositive = trim( str_replace(array("positive",'%'),"",$prePositive) ) ;
				}
				
				$totalRatingInfo = $sellerInformation->find(".rating",0) ;
				if($totalRatingInfo != null){
					$totalRating = $totalRatingInfo->plaintext ;
					$totalRating = explode("(" ,$totalRating ) ;
					if( count($totalRating) >=2 ){
						$totalRating = $totalRating[1] ;
						$totalRating = explode("total ratings" ,$totalRating ) ;
						$totalRating = $totalRating[0] ;
						$totalRating = trim( str_replace(array(",",'%'),"",$totalRating) ) ;
					}else{
						$totalRating = "" ;
					}
				}
				
				$countryInfo = $sellerInformation->find(".availability",0) ;
				if($countryInfo != null){
					$country = strtolower( $countryInfo->plaintext ) ;
					$pos = strpos($country, "china");
					if( $pos === false ){
						$country = "" ;
					}else{
						$country = "china" ;
					}
					
				}
				
				
				$index++ ;
				$details[] = array("SELLER_NAME"=>$sellerName,
					"SELLER_URL"=>$sellerUrl,
					"SELLER_PRICE"=>$price,
					"SELLER_IMG"=>$sellerImg,
					"SELLER_SHIP_PRICE"=>$priceShipping,
					"TYPE"=> $type.$index,
					"PRE_POSITIVE"=>$prePositive,
					"TOTAL_RATING"=>$totalRating,
					"COUNTRY"=>$country
					) ;
			}
					
	}
	
	
	public function saveProductPotential($asin ,$html=null,$url=null){
	
		$amazonaccount = new Amazonaccount() ;
		$Task = new Task() ;
		$Config = new Config() ;
		
		if( $html != null ){
			//get point
			$rating = $html->find(".acrRating",0) ;
			$point = "" ;
			if($rating != null ){
				$txt = $rating->plaintext ;
				$arry = explode("out of",$txt) ;
				$point = trim( $arry[0] ) ;
			}
			//get review
			$views = $html->find(".acrCount",0) ;
			$reviews = "" ;
			if($views != null ){
				$txt = $views->plaintext ;
				$txt = str_replace( array('"',')','(',","),"",$txt ) ;
				$reviews = trim( $txt ) ;
				$reviews = str_replace( array("reviews","review"),"",$reviews ) ;
				$reviews = trim($reviews) ;
			}
			//get ranking
			$salesRank = $html->find("#SalesRank",0) ;
			$rankArray = array() ;
			if( $salesRank != null ){
				foreach( $salesRank->find(".zg_hrsr_item") as $item ){
					$rank = $item->find(".zg_hrsr_rank",0) ;
					$type = $item->find(".zg_hrsr_ladder",0) ;
					
					$rankText = str_replace("#","",$rank->plaintext) ;
					$typeText = str_replace("in&nbsp;","",$type->plaintext) ;
					
					$rankArray[] = array("rank"=>trim( $rankText ),"type"=>trim($typeText) ) ;
				}
			}
			$Task->saveSalePotential($asin,$reviews , $point , $rankArray ) ;
		}else {
			
		}
	}
}