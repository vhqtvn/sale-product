<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App :: import('Vendor', 'Snoopy');
App :: import('Vendor', 'simple_html_dom');

class TaskUploadController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Task', 'Config','Amazonaccount',"Utils");

	public function doFlowUpload(){
		$fileName = $_FILES['flowFile']["name"] ;
		$myfile = $_FILES['flowFile']['tmp_name'] ;
		
		$data = $this->request->data ;
		$startTime =strtotime($data['startTime']) ;
		$endTime = strtotime($data['endTime']) ;
		
		$Days=round(( $endTime-$startTime )/3600/24);
		
		$Days = $Days + 1 ;
		
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
		//"(Parent) ASIN","Title","Sessions","Page Views","Page Views Percentage","Buy Box Percentage","Units Ordered","Gross Product Sales","Orders Placed"
		
		//"(Parent) ASIN","(Child) ASIN","Title","SKU","Sessions","Session Percentage","Page Views","Page Views Percentage",
		//"Buy Box Percentage","Units Ordered","Unit Session Percentage","Gross Product Sales","Orders Placed"
		
		//"(Parent) ASIN","(Child) ASIN","Title","Sessions","Session Percentage","Page Views","Page Views Percentage",
		//"Buy Box Percentage","Units Ordered","Unit Session Percentage","Gross Product Sales","Orders Placed"
		//Parent ASIN
		$flowHeaderDBColMap = array(
				'Parent ASIN'=>'PARENT_ASIN',
				'Child ASIN'=>'ASIN',
				"Title"=>"TITLE",
				'SKU'=>'SKU',
				'Sessions'=>'SESSIONS',
				'Session Percentage'=>'SESSION_PERCENTAGE',
				'Page Views'=>"PAGEVIEWS",
				'Page Views Percentage'=>"PAGEVIEWS_PERCENT",
				'Buy Box Percentage'=>"BUY_BOX_PERCENT",
				'Units Ordered'=>"UNITS_ORDERED",
				'Unit Session Percentage'=>"UNIT_SESSION_PERCENTAGE",
				'Gross Product Sales'=>"ORDERED_PRODUCT_SALES",
				'Orders Placed'=>"ORDERS_PLACED"
		) ;
		$lineCols = array() ;
		$isFirst = true ;
		while (!feof($file_handle)) {
		   $line = fgets($file_handle);
		   if( !empty($line) ){
		   		if( $isFirst ){
		   			$array = explode('","',$line) ;
		   			foreach( $array as $a ){
		   				$a = trim(str_replace('"',"",$a)) ;
		   				$a = trim(str_replace('(',"",$a)) ;
		   				$a = trim(str_replace(')',"",$a)) ;
		   				echo $a.'>>>' ;
		   				//debug($flowHeaderDBColMap) ;
		   				if( $this->endsWith($a ,'Parent ASIN' )){//
		   					$lineCols[] = "PARENT_ASIN" ;
		   				}else if( $this->endsWith($a ,'Child ASIN' )){//
		   					$lineCols[] = "ASIN" ;
		   				}else{
			   				$test = $flowHeaderDBColMap[$a] ;
			   				echo $test.'-----' ;
			   				if( isset( $flowHeaderDBColMap[$a] ) ){
			   					$column = $flowHeaderDBColMap[$a] ;
			   					$lineCols[] = $column ;
			   				}else{
			   					$lineCols[] = '-' ;
			   				}
		   				}
		   			} ;
					echo 111111;
		   			print_r($lineCols) ;
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
}