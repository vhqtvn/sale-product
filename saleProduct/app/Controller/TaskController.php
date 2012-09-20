<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App :: import('Vendor', 'Snoopy');
App :: import('Vendor', 'simple_html_dom');
App :: import('Vendor', 'Amazon');

class TaskController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Task', 'Config','Amazonaccount');

	public function sellerAsin($id = null) {
		$this->Task->clearlog($id) ;
		
		$l[$id] = array() ;
		//get seller url
		$sellerurl = $this->Task->getSellerUrl($id);
		$url = $sellerurl[0]['sc_seller']['url'];

		$asinArray = array() ;
		
		//clear taskId asin from GatherAsin
		$this->Task->clearGatherAsin($id ) ;
		
		$index = 0 ;
		
		for ($j = 1; $j < 200; $j++) {
			$this->Task->savelog($id,"from [".($url . "&page=" . $j)."] get products") ;
			$snoopy = new Snoopy;	
			if( $snoopy->fetch($url . "&page=" . $j) ){
				
				$Result = $snoopy->results ;
				$html = new simple_html_dom();
				$html->load( $Result ,true ,false );
				//$html = file_get_html($url . "&page=" . $j); 
				try{
					$products = $html->find('.result');
					
					if (count($products) <= 0) {
						break;
					}
		
					for ($i = 0; $i < count($products); $i++) {
						$productName = $products[$i]->name;
						$index = $index + 1 ;
						$this->Task->savelog($id,"find product[ index: ".$index." ]: ".$productName) ;
						if (empty ($productName))
							continue;
						try {
							//$this->Task->saveAsin($productName);
							$this->Task->saveGatherAsin($id, trim($productName) ) ;
							
							$asinArray[] = trim($productName) ;
						} catch (Exception $e) {
							$this->Task->savelog($id,$productName." has exists!") ;
						}
					}
				}catch(Exception $e){}
				$html->clear() ;
				unset($html) ;
			}else{
				$this->Task->savelog($id,"error fetching document: ".$snoopy->error) ;
			}
			
			unset($snoopy) ;
		}
		
		//开始采集产品信息
		$index = 0 ;
		foreach($asinArray as $asin){
			$index = $index + 1 ;
			$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] details" );
			$this->fetchAsin($asin,$id ,$index) ;
		} 
		//采集产品信息结束
		$this->Task->savelog($id,"end!" );

		$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
	}

	
	public function gatherCompetitions($id){
		//获取商家产品asin
		$array = $this->Task->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Task->savelog($id, "start gather competition" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] competitions" );
			$this->fetchCompetions($asin,$id ) ;
		}
		$this->Task->savelog($id, "end!" );
	}
	
	
	public function gatherFba($id){
		$array = $this->Task->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Task->savelog($id, "start gather fba" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] fba" );
			$this->fetchFba($asin,$id ) ;
		}
		$this->Task->savelog($id, "end!" );
	}
	
	/**
	 * 获取日志信息显示
	 */
	public function getLog($id){
		$logs = $this->Task->getLogs($id) ;
		
		$this->response->type("json");
		$this->response->body(json_encode($logs));
		return $this->response;
	}

	public function fetchAsin($asin,$id=null,$index = null) {
		if( strlen(trim($asin)) < 9 || strlen(trim($asin)) >=11 ) {
			if($id == null ){
				$this->response->type("json");
				$this->response->body("execute complete");
				return $this->response;
			}
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
					$title = $this->Task->getProp($html ,'#btAsinTitle' ) ;
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
					$this->Task->updateProduct($array);
					
					///保存图片
					$images = $html->find("#prodImageCell img",0) ;
					if( $images!=null ){
						$src = $images->src ;
						$title = $images->alt ;
						try{
							$localUrl = "images/amazon/".$asin."/".basename($src) ;
							$this->Task->addImage($asin,$src,$title,$localUrl) ;
							$this->Task->downloads($src,$asin) ;
						}catch(Exception $e){}
					}
					//保存竞争信息
					$this->saveProductPotential($asin , $html) ;
				}catch(Exception $e){
					$this->Task->savelog($id,"get product[".$asin."] details failed:::: ".$e->getMessage()) ;	
				}
				$html->clear() ;
				unset($html) ;
				$this->Task->savelog($id,"get product[".$asin."] details success!") ;	
			}
			
			unset($snoopy) ;
		}catch(Exception $e){
			$this->Task->savelog($id,"get product[".$asin."] error:::".$e->getMessage()) ;	
		}
		
		if($id == null ){
			$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		}
	}
	
	
	public function saveProductPotential($asin ,$html=null,$url=null){
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
			$this->Task->saveSalePotential($asin,$reviews , $point , $rankArray ) ;
		}else {
			
		}
	}
	
	
	public function doUpload(){
		$params = $this->request->data  ;
		$groupId = $params["groupId"] ;
		
		$fileName = $_FILES['productFile']["name"] ;
		$myfile = $_FILES['productFile']['tmp_name'] ;
		$user =  $this->getCookUser() ;
		//save db
		$id = "UC_".date('U') ;
		
		$this->Task->saveUpload($id, $fileName,$groupId,$user ) ;
		
		$file_handle = fopen($myfile , "r");
		while (!feof($file_handle)) {
		   $line = fgets($file_handle);
		   if( trim($line) != "" ){
		   		//save to sc_gather_product id task_id asin
		   		try{
		   		$this->Task->saveGatherAsin($id, trim($line) ) ;
		   		}catch(Exception $e){}
		   }
		}
		fclose($file_handle);
		
		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.parent.uploadSuccess('".$id."');</script>");
		return $this->response;
	}
	
	public function doUploadForInput(){
		$id = "UC_".date('U') ;
		
		$params = $this->request->data  ;
		$user =  $this->getCookUser() ;
		$name 	= $params["name"] ;
		$groupId = $params["groupId"] ;
		$asins 	= $params["asins"] ;
		$this->Task->saveUpload($id, $name,$groupId,$user) ;
		
		$asinss = explode(",",$asins) ;
		foreach( $asinss as $asin ){
			if( trim($asin) != "" ){
				try{
				$this->Task->saveGatherAsin($id, trim($asin) ) ;
				}catch(Exception $e){}
			}
		} ;
		
		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.parent.uploadSuccess('".$id."');</script>");
		return $this->response;
		
	}
	
	public function fetchAsins($id=null) {
		$array = $this->Task->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Task->savelog($id, "start gather details" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] details" );
			$this->fetchAsin($asin ,$id ) ;
		}
		$this->Task->savelog($id, "end!" );
	}
	
	public function fetchFba($asin = null,$id = null ){
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
							$numberofresults =  $e->next_sibling ()->plaintext ;
							$ary = explode("of",$numberofresults) ;
							$_ = str_replace("offers","",$ary[1] ) ;
							$fmNum = trim( $_ ) ;
							$base["FBA_NUM"] = $fmNum ;
							
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
								$priceShippingEl = $table->find(".price_shipping",0) ;
								
								$priceShipping = "0.00" ;
								if( $priceShippingEl!= null ){
									$priceShipping =  $priceShippingEl->plaintext ;
								}
								
								$sellerInformation = $table->find(".sellerInformation",0)  ;
								
								$baseInfo = $sellerInformation->find(".seller a",0) ;
								$sellerUrl= '' ;
								$sellerName = '' ;
								$sellerImg = '' ;
								if($baseInfo != null){
									$sellerUrl = $baseInfo->href ;
									$sellerName = $baseInfo->plaintext ;
								}else {
									$baseInfo = $sellerInformation->first_child() ;
									if($baseInfo->href !=null){
										$sellerUrl = $baseInfo->href ;
										$baseImage = $baseInfo->find("img",0) ;
										$sellerImg = $baseImage->src ;
										$sellerName = $baseImage->alt ;
									}else if($baseInfo->src != null){
										$sellerImg = $baseInfo->src ;
										$sellerName = $baseInfo->alt ;
									}
								}
								
								if($index == 0){
									$base["TARGET_PRICE"] = $price ;
								}
								
								$index++ ;
								$details[] = array("SELLER_NAME"=>$sellerName,
									"SELLER_URL"=>$sellerUrl,
									"SELLER_PRICE"=>$price,
									"SELLER_IMG"=>$sellerImg,
									"SELLER_SHIP_PRICE"=>$priceShipping,
									"TYPE"=> "FBA".$index
								) ;
							}
						}
			        }  
			        
			        $this->Task->saveFba($asin , $base , $details) ;
				}catch(Exception $e){}
				$html->clear() ;
				unset($html) ;
			}
			unset($snoopy) ;
		if($id == null ){
			$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		}
	}	
	
	public function fetchCompetions($asin = null,$id = null ){
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
							$numberofresults =  $e->next_sibling ()->plaintext ;
							$ary = explode("of",$numberofresults) ;
							$_ = str_replace("offers","",$ary[1] ) ;
							$fmNum = trim( $_ ) ;
							$base["FM_NUM"] = $fmNum ;
							
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
								$priceShippingEl = $table->find(".price_shipping",0) ;
								
								$priceShipping = "0.00" ;
								if( $priceShippingEl!= null ){
									$priceShipping =  $priceShippingEl->plaintext ;
								}
								
								$sellerInformation = $table->find(".sellerInformation",0)  ;
								
								$baseInfo = $sellerInformation->find(".seller a",0) ;
								$sellerUrl= '' ;
								$sellerName = '' ;
								$sellerImg = '' ;
								if($baseInfo != null){
									$sellerUrl = $baseInfo->href ;
									$sellerName = $baseInfo->plaintext ;
								}else {
									$baseInfo = $sellerInformation->first_child() ;
									if($baseInfo->href !=null){
										$sellerUrl = $baseInfo->href ;
										$baseImage = $baseInfo->find("img",0) ;
										$sellerImg = $baseImage->src ;
										$sellerName = $baseImage->alt ;
									}else if($baseInfo->src != null){
										$sellerImg = $baseInfo->src ;
										$sellerName = $baseInfo->alt ;
									}
								}
								
								if($index == 0){
									$base["TARGET_PRICE"] = $price ;
								}
								
								$index++ ;
								$details[] = array("SELLER_NAME"=>$sellerName,
									"SELLER_URL"=>$sellerUrl,
									"SELLER_PRICE"=>$price,
									"SELLER_IMG"=>$sellerImg,
									"SELLER_SHIP_PRICE"=>$priceShipping,
									"TYPE"=> "F".$index
									) ;
							}
							
						}else if( $e->plaintext == 'New' ) {
							$numberofresults =  $e->next_sibling ()->plaintext ;
							$ary = explode("of",$numberofresults) ;
							$_ = str_replace("offers","",$ary[1] ) ;
							$nmNum = trim( $_ ) ;
							$base["NM_NUM"] = $nmNum ;
							
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
								
								$index++ ;
								$details[] = array("SELLER_NAME"=>$sellerName,
									"SELLER_URL"=>$sellerUrl,
									"SELLER_PRICE"=>$price,
									"SELLER_IMG"=>$sellerImg,
									"SELLER_SHIP_PRICE"=>$priceShipping,
									"TYPE"=> "N".$index
									) ;
								
							}
							
						}else if( $e->plaintext == 'Used' ) {
							$numberofresults   = $e->next_sibling ()->plaintext ;
							$ary = explode("of",$numberofresults) ;
							$_ = str_replace("offers","",$ary[1] ) ;
							$umNum = trim( $_ ) ;
							$base["UM_NUM"] = $umNum ;
							
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
								
								$index++ ;
								$details[] = array("SELLER_NAME"=>$sellerName,
									"SELLER_URL"=>$sellerUrl,
									"SELLER_PRICE"=>$price,
									"SELLER_IMG"=>$sellerImg,
									"SELLER_SHIP_PRICE"=>$priceShipping,
									"TYPE"=> "U".$index
									) ;
							}
						}
			        }  
			        
			        $this->Task->saveCompetions($asin , $base , $details) ;
				}catch(Exception $e){}
				$html->clear() ;
				unset($html) ;
			}
			unset($snoopy) ;
		//}catch(Exception $e){
		//	$this->Task->savelog($id,"get product[".$asin."] error:::".$e->getMessage()) ;	
		//}
		if($id == null ){
			$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		}
	}
	
	public function doFlowUpload(){
		$fileName = $_FILES['flowFile']["name"] ;
		$myfile = $_FILES['flowFile']['tmp_name'] ;
		
		$data = $this->request->data ;
		$startTime =strtotime($data['startTime']) ;
		$endTime = strtotime($data['endTime']) ;
		
		$Days=round(( $endTime-$startTime )/3600/24);
		
		$id = "F_".date('U') ;
		//save db
		$user =  $this->getCookUser() ;
		$loginId = $user["LOGIN_ID"] ;
		
		$this->Task->saveFlowUpload($id, $fileName ,$user,$data['startTime'] ,$data['endTime'],$Days  ) ;
		
		$file_handle = fopen($myfile , "r");
		
		//"(Parent) ASIN","Title","Page Views","Page Views Percentage","Buy Box Percentage",
		//"Units Ordered","Ordered Product Sales","Orders Placed"
		/*
		TASK_ID, 
				ASIN, 
				TITLE, 
				PAGEVIEWS, 
				PAGEVIEWS_PERCENT, 
				BUY_BOX_PERCENT, 
				UNITS_ORDERED, 
				ORDERED_PRODUCT_SALES, 
				ORDERS_PLACED, 
				CREATOR, 
				CREATTIME*/
		$flowHeaderDBColMap = array('(Parent) ASIN'=>'ASIN',"Title"=>"TITLE",
								'Page Views'=>"PAGEVIEWS",'Page Views Percentage'=>"PAGEVIEWS_PERCENT",
								'Buy Box Percentage'=>"BUY_BOX_PERCENT",'Units Ordered'=>"UNITS_ORDERED",
								'Ordered Product Sales'=>"ORDERED_PRODUCT_SALES",'Orders Placed'=>"ORDERS_PLACED"
								) ;
		$lineCols = array() ;
		$isFirst = true ;
		while (!feof($file_handle)) {
		   $line = fgets($file_handle);
		   if( !empty($line) ){
		   		if( $isFirst ){
		   			$array = explode('","',$line) ;
		   			print_r($array) ;
		   			foreach( $array as $a ){
		   				$a = trim(str_replace('"',"",$a)) ;
		   				if( $this->endsWith($a ,'ASIN' )){//
		   					$lineCols[] = "ASIN" ;
		   				}else{
		   					$column = $flowHeaderDBColMap[$a] ;
		   					$lineCols[] = $column ;
		   				}
		   			} ;
		   		}else{
		   			$lineData = array() ;
		   			$array = explode('","',$line) ;
		   			for( $i=0 ; $i < count($array) ;$i++ ){
		   				$a = $array[$i] ;
		   				$a = trim(str_replace('"',"",$a)) ;
		   				$column = $lineCols[$i] ;
		   				$lineData[$column] = $a ;
		   			}
		   			
		   			$this->Task->saveFlowDetails($id, $lineData ,$loginId,$Days) ;
		   		}
		   		$isFirst = false ;
		   		//save to sc_gather_product id task_id asin
		   }
		}
		fclose($file_handle);
		
		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.parent.uploadSuccess('".$id."');</script>");
		return $this->response;
	}
	
	public function gatherAmazonCompetitions($id,$categoryId = null){
		//更新采集状态
		$this->Amazonaccount->updateAccountGatherStatus($id,"GATHER_STATUS_COMPETE","START",$categoryId) ;
		try{
			//获取商家产品asin
			$array = $this->Amazonaccount->getAccountProducts($id,$categoryId) ;
			$index = 0 ;
			$this->Task->savelog($id, "start gather competition" );
			foreach( $array as $arr ){
				$index = $index + 1 ;
				$asin = $arr['sc_amazon_account_product']['ASIN'] ;
				$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] competitions" );
				$this->fetchCompetions($asin,$id ) ;
			}
			$this->Task->savelog($id, "end!" );
		
		}catch(Exception $e){
			$this->Task->savelog($id, "error::::".$e->getMessage() );
		}
		
		$this->Amazonaccount->updateAccountGatherStatus($id,"GATHER_STATUS_COMPETE","",$categoryId) ;
	}
	
	
	public function gatherAmazonFba($id,$categoryId = null){
		//更新采集状态
		$this->Amazonaccount->updateAccountGatherStatus($id,"GATHER_STATUS_FBA","START",$categoryId) ;
		try{
			$array = $this->Amazonaccount->getAccountProducts($id,$categoryId) ;
			$index = 0 ;
			$this->Task->savelog($id, "start gather fba" );
			foreach( $array as $arr ){
				$index = $index + 1 ;
				$asin = $arr['sc_amazon_account_product']['ASIN'] ;
				$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] fba" );
				$this->fetchFba($asin,$id ) ;
			}
			$this->Task->savelog($id, "end!" );
		}catch(Exception $e){
			$this->Task->savelog($id, "error::::".$e->getMessage() );
		}
		$this->Amazonaccount->updateAccountGatherStatus($id,"GATHER_STATUS_FBA","",$categoryId) ;
	}
	
	/**
	 * 采集amazon账户产品
	 * $id 账户系统编号
	 * $code 账户CODE
	 */
	public function amazonAsin($id , $categoryId = null ){//
		
		//更新采集状态
		$this->Amazonaccount->updateAccountGatherStatus($id,"GATHER_STATUS_PRODUCT","START",$categoryId) ;
		try{
		
			$asintemplate = $this->Config->getAmazonConfig("AMAZON_ACCOUNT_PRODUCT_URL") ;
			$asinArray = $this->Amazonaccount->getAccountProducts($id,$categoryId) ;
			
			//开始采集产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				$asin = $_asin['sc_amazon_account_product']['ASIN'] ;
				$index = $index + 1 ;
				$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] details" );
				echo $asin.'<br>' ;
				$this->fetchAsin($asin,$id) ;
			} 
			//采集产品信息结束
			$this->Task->savelog($id,"end!" );
		}catch(Exception $e){
			try{
				$this->Task->savelog($id, "error::::".$e->getMessage() );
			}catch(Exception $e){}
		}
		$this->Amazonaccount->updateAccountGatherStatus($id,"GATHER_STATUS_PRODUCT","",$categoryId) ;

		$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
	}
	
	/**
	 * gather shipping info
	 */
	public function amazonShippingAsin($id , $categoryId=null ){//
		$this->Task->clearlog($id) ;
	
		$account = $this->Amazonaccount->getAccount($id) ;
		$account = $account[0]['sc_amazon_account'] ;
		//更新采集状态
		$this->Amazonaccount->updateAccountGatherStatus($id,"GATHER_STATUS_PRODUCT_SHIPPING","START",$categoryId) ;
		try{
			//$this->Task->clearlog($id) ;
		
			$asintemplate = $this->Config->getAmazonConfig("AMAZON_ACCOUNT_PRODUCT_URL") ;
			$asinArray = $this->Amazonaccount->getAccountProducts($id,$categoryId) ;
			
			//开始采集产品信息
			$index = 0 ;
			foreach($asinArray as $_asin){
				
				$asin = $_asin['sc_amazon_account_product']['ASIN'] ;
				
				$condition = $_asin['sc_amazon_account_product']['ITEM_CONDITION'] ;
				$condition = $condition == 1?"used":"new" ;
				
				$index = $index + 1 ;
				$this->Task->savelog($id, "start get product[ index: ".$index." ][".$asin."] price" );
				$this->fetchAmazonAsin($_asin['sc_amazon_account_product']['SKU'],$asin,$account['CODE'],$condition,$asintemplate,$id ,$index) ;
				
			} 
			//采集产品信息结束
			$this->Task->savelog($id,"end!" );
		}catch(Exception $e){
			$this->Task->savelog($id, "error::::".$e->getMessage() );
		}
		$this->Amazonaccount->updateAccountGatherStatus($id,"GATHER_STATUS_PRODUCT_SHIPPING","",$categoryId) ;

		$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
	}
	
	public function fetchAmazonAsin($sku,$asin,$code,$condition,$asintemplate=null,$id=null,$index = null) {
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
		
					$this->Task->savelog($id," $isFM [$asin:$url]>>>>>"." $plusShippingText") ;	
					//更新产品基本信息
					$this->Task->updateAmazonProductShipping($array,$code,$id);
				}catch(Exception $e){
					pirnt_r($e) ;
					$this->Task->savelog($id,"get product[".$asin."] price failed:::: ".$e->getMessage()) ;	
				}
				$html->clear() ;
				unset($html) ;
				$this->Task->savelog($id,"get product[".$asin."] price success!") ;	
			}else{
				echo '<br>--------error----------<br>' ;
			}
			unset($snoopy) ;
		}catch( Exception $e){
			pirnt_r($e) ;
			$this->Task->savelog($id,"get product[".$asin."] price error:::".$e->getMessage()) ;	
		}
		
		/*if($id == null ){
			$this->response->type("json");
			$this->response->body("execute complete");
			return $this->response;
		}*/
	}
	
	/////////////////////////////////////////////////////
	function endsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    if ($length == 0) {
	        return true;
	    }
	
	    return (substr($haystack, -$length) === $needle);
	}
}