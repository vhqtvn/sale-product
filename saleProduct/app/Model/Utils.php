<?php
class Utils extends AppModel {
	var $useTable = 'sc_user';
	
	public function buildUrl( $account , $action  ){
		$domain = $account['DOMAIN'] ;
		$url = "http://".$domain."/saleProductTask/index.php/".$action+"/".$account['ID'] ;
		return $url ;
	}
	
	/**
	 * 格式化为树格式 ID , TEXT ,PARENT_ID
	 */
	public function formatTree($sqlId , $params){
		$records = $this->exeSql($sqlId,$params) ;
		
		$items = array() ;
		$roots = array() ;
		$keyMap = array() ;
		$rootIds = array() ;
		
		foreach( $records as $record ){
			$record = $this->formatObject($record) ;
			$pid = $record['PARENT_ID'] ;
			$id = $record['ID'] ;
			$record = array('id'=>$id,'pid'=>$pid,'text'=>$record['TEXT']) ;
			if(empty( $pid )){
				$roots[] = $record ;
			}
			$keyMap[ $id ] = $record ;
			
			$items[] = $record ;
		}
		
		
		//{id:'$id',text:'$name',pid:'$pid',url:'$url',isexpand:false,code:'$code'} 
		foreach( $items as $item ){
			$parentId 	= $item['pid'] ;
			$id 		= $item['id'] ;
			
			if( !empty($parentId) ){
				//获取父节点
				if(isset($keyMap[$parentId])){
					$parent = $keyMap[$parentId] ;
				}else{
					$parent = array() ;
				}
				
				if( isset( $parent['childNodes'] ) ){
					
				}else{
					$parent['childNodes'] = array() ;
				}
				
				$parent['childNodes'][] = $item ;
				
				$keyMap[$parentId] = $parent ;
			}
		}
		
		$results = array() ;
		foreach($roots as $root){
			$id 		= $root['id'] ;
			if( isset( $keyMap[$id] ) ){
				$results[] = $keyMap[$id] ;
			}
			//$results[] = $root ;
		}
		
		return json_encode($results) ;
	}
	
	public function formatTreeForRecords($records , $params = null,$keys=null){
		$items = array() ;
		$roots = array() ;
		$keyMap = array() ;
		$rootIds = array() ;
		
		foreach( $records as $record ){
			$record = $this->formatObject($record) ;
			$text = "" ;
			if( isset($record['TEXT']) ){
				$text = $record['TEXT'] ;
			}else if( isset($record['NAME']) ){
				$text = $record['NAME'] ;
			}
			
			$pid = $record['PARENT_ID'] ;
			$id = $record['ID'] ;
			//$record = array('id'=>$id,'pid'=>$pid,'text'=>$text) ;
			$record['id'] = $id ;
			$record['pid'] = $pid ;
			$record['text'] = $text ;
			if(empty( $pid )){
				$roots[] = $record ;
			}
			$keyMap[ $id ] = $record ;
			
			$items[] = $record ;
		}
		
		
		//{id:'$id',text:'$name',pid:'$pid',url:'$url',isexpand:false,code:'$code'} 
		foreach( $items as $item ){
			$parentId 	= $item['pid'] ;
			$id 		= $item['id'] ;
			
			if( !empty($parentId) ){
				//获取父节点
				if(isset($keyMap[$parentId])){
					$parent = $keyMap[$parentId] ;
				}else{
					$parent = array() ;
				}
				
				if( isset( $parent['childNodes'] ) ){
					
				}else{
					$parent['childNodes'] = array() ;
				}
				
				$parent['childNodes'][] = $item ;
				
				$keyMap[$parentId] = $parent ;
			}
		}
		
		$results = array() ;
		foreach($roots as $root){
			$id 		= $root['id'] ;
			if( isset( $keyMap[$id] ) ){
				$t = $keyMap[$id] ;
				$cd = array() ;
				if( !empty($t['childNodes']) ){
					foreach($t['childNodes'] as $cnode){
						$_id = $cnode['id'] ;
						if(isset($keyMap[$_id])){
							$cd[] = $keyMap[$_id] ;
						}
					} ;
				}
				$keyMap[$id]['childNodes'] = $cd ;
				$results[] = $keyMap[$id] ;
			}
			//$results[] = $root ;
		}
		return json_encode($results) ;
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
					if( $pos === false ){//不是中国
						$pos = strpos($country, "hongkong");
						if( $pos === false ){
							$country = "" ;
						}else{
							$country = "hongkong" ;
						}
					}else{
						$country = "china" ;
					}
				}
				
				if(!is_numeric($totalRating)){
					$totalRating = "" ;
				}
				
				$sellerName = trim($sellerName) ;
				try{
					//print_r(">>>$sellerName") ;
					$sellerName = iconv( 'ASCII' ,'utf-8//IGNORE' ,$sellerName ) ;
					//print_r($sellerName) ;
				}catch(Exception $e){
				}
				$index++ ;
				$details[] = array(
					"SELLER_NAME"=>$sellerName,
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
	
	function getProp($html , $selector){
		$dom = $html->find($selector,0) ;
		if($dom == null)
			return "" ;
		return trim($dom->plaintext) ;
	}
	
	/**
	 * 下载图片
	 */
	function downloads( $url =null , $asin=null , $local = null  ){
		
		if( $url == null || $url == "" ) return ;
		
		ini_set('user_agent','MSIE 4\.0b2;'); 
		ini_set('user_agent','Mozilla: (compatible; Windows XP)');
		
		$file =  fopen ($url, "rb");
		
		$path = dirname(dirname(dirname(__FILE__)))."/images/amazon/".$asin;
		if($local != null ){
			$path = dirname(dirname(dirname(__FILE__)))."/".$local;
		}
		
		$fullPath =  $path."/".basename($url) ; 
		if( file_exists($fullPath) ) return ;
		
		$this->creatdir($path) ;
		$path = $fullPath ;
		 
		if (!$file) { 
			echo "文件找不到"; 
		} else { 
			//Header("Content-type: application/octet-stream"); 
			//Header("Content-Disposition: attachment; filename=" .basename($url)); 
			//Header("content-Type: text/html; charset=utf-8"); 
			$newf = fopen ($path, "wb");
		    $downlen=0;
		    if ($newf)
		    	ob_start();
				while(!feof($file)) {
			        $data=fread($file, 1024 * 8 );	//默认获取8K
			        $downlen+=strlen($data);	// 累计已经下载的字节数
			        fwrite($newf, $data, 1024 * 8 );
			        ob_flush();
			        flush();
			    }
			    
			if ($file) 
			{
			  fclose($file);
			}
			
			if ($newf) 
			{
			  fclose($newf);
			}
		} 
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