<?php
class Utils extends AppModel {
	var $useTable = false;
	
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
				
				$priceShipping =  $table->find(".price_shipping",0) ;
				if($priceShipping != null){
					$priceShipping = $priceShipping->plaintext ;
				}else {
					$priceShipping = "" ;
				}
				
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
						if($baseImage!=null){
							$sellerImg = $baseImage->src ;
							$sellerName = $baseImage->alt ;
						}
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
					}else{
						$country = "china" ;
					}
					
					$pos = strpos($country, "hongkong");
					if( $pos === false ){
						$country = "" ;
					}else{
						$country = "hongkong" ;
					}
					
				}
				
				
				$index++ ;
				$details[] = array("SELLER_NAME"=>$sellerName,
					"SELLER_URL"=>$sellerUrl,
					"SELLER_PRICE"=>$price,
					"SELLER_IMG"=>$sellerImg,
					"SELLER_SHIP_PRICE"=>$priceShipping,
					"TYPE"=> $type.$index,
					"PER_POSITIVE"=>$prePositive,
					"TOTAL_RATING"=>$totalRating,
					"COUNTRY"=>$country
					) ;
			}
			return array($details,$base) ;
					
	}
	
	function _processRowPrice($id , $e , $condition,$arrays , $isFM){
			$detailTables = $e->parent ;
			while(true){
				if( $detailTables->class == "resultsheader" ){
					break ;
				}
				$detailTables = $detailTables->parent ;
			}
			
			$index = 0 ;
			foreach( $detailTables->next_sibling()->find(".result") as $table ){
				$plusShippingText = "" ;
				if( $table->find(".price_shipping",0) != null ){
					$plusShippingText = trim($table->find(".price_shipping",0)->plaintext) ;
				}
				
				$priceText = "" ;
				if( $table->find(".price",0) != null ){
					$priceText = trim($table->find(".price",0)->plaintext) ;
				}
				
				$isFBA = "" ;
				if($table->find(".fba_link",0) != null ){
					$isFBA = "fba" ;
				}
				
				
				$priceText = trim( str_replace(array("&nbsp;","+","Shipping","Free","shipping",'$'),"",$priceText) ) ;
				$plusShippingText = trim( str_replace(array("&nbsp;","+","Shipping","Free","shipping",'$'),"",$plusShippingText) ) ;
				
				$record = array() ;
				$record["isFM"] = $isFM ;
				$record["isFBA"] = $isFBA ;
				$record['plusShippingText'] = $plusShippingText ;
				$record['priceText'] = $priceText ;
				$record['condition'] = $condition ;
				$arrays[] = $record ;
			}
			return $arrays ;			
		
	}
	
	function endsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    if ($length == 0) {
	        return true;
	    }
	
	    return (substr($haystack, -$length) === $needle);
	}
}