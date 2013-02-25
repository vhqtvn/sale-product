<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

class GatherUploadController extends AppController {
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Tasking', 'Config','Amazonaccount',"Utils","Log","GatherData","GatherService");
	public $taskId = null ;
	
	/**
	 * 通过商家ID采集商品
	 */
    public function sellerAsins($id){
    	$status = $this->Tasking->status("gather_seller",$id,"") ;
		if( $status ){//执行中
			return ;
		}else{
			$this->taskId = $this->Tasking->start("gather_seller",$id,"") ;
		}
		try{
			$this->GatherService->clearGatherAsin($id ) ;
			$this->GatherData->sellerAsins($id,$this->taskId) ;
			$this->taskAll($id) ;
			
			$this->Tasking->stop("gather_seller",$id,"") ;
		}catch(Exception $e){
		
			$this->Log->saveLog($this->taskId,"error::::::::".$e->getMessage()) ;
			$this->Tasking->stop("gather_seller",$id,"") ;
		}	
    }

	public function uploadAsins(){
	
		$params = $this->request->data  ;
		$groupId = $params["groupId"] ;
		
		$fileName = $_FILES['productFile']["name"] ;
		$myfile = $_FILES['productFile']['tmp_name'] ;
		$user =  $this->getCookUser() ;
		//save db
		$id = "UC_".date('U') ;
		
		$this->GatherService->saveUpload($id, $fileName,$groupId,$user ) ;
		
		$file_handle = fopen($myfile , "r");
		while (!feof($file_handle)) {
		   $line = fgets($file_handle);
		   if( trim($line) != "" ){
		   		if( strlen(trim($line)) < 9 || strlen(trim($line)) >=11 ) {
					continue ;
				} ;
		   		//save to sc_gather_product id task_id asin
		   		try{
		   		$this->GatherService->saveGatherAsin($id, trim($line) ) ;
		   		}catch(Exception $e){}
		   }
		}
		fclose($file_handle);
	
		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.opener.uploadSuccess('".$id."');</script>");
		return $this->response;
	}
	
	public function inputAsins(){
		$id = "UC_".date('U') ;
		
		$params = $this->request->data  ;
		$user =  $this->getCookUser() ;
		$name 	= $params["name"] ;
		$groupId = $params["groupId"] ;
		$asins 	= $params["asins"] ;
		$this->GatherService->saveUpload($id, $name,$groupId,$user) ;
		
		$asinss = explode(",",$asins) ;
		foreach( $asinss as $asin ){
			if( trim($asin) != "" ){
				if( strlen(trim($asin)) < 9 || strlen(trim($asin)) >=11 ) {
					continue ;
				} ; 
				try{
				$this->GatherService->saveGatherAsin($id, trim($asin) ) ;
				}catch(Exception $e){}
			}
		} ;
		
		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.opener.uploadSuccess('".$id."');</script>");
		return $this->response;
	}

	public function taskAll($id){
		$status = $this->Tasking->status("gather_seller_all",$id,"") ;
		if( $status ){//执行中
			return ;
		}else{
			$this->taskId = $this->Tasking->start("gather_seller_all",$id,"") ;
		}
		try{
			$this->taskBaseInfo($id) ;
			$this->taskCompetition($id) ;
			$this->taskFba($id) ;
			
			$this->Tasking->stop("gather_seller_all",$id,"") ;
		}catch(Exception $e){
			$this->Log->saveLog($this->taskId,"error::::::::".$e->getMessage()) ;
			$this->Tasking->stop("gather_seller_all",$id,"") ;
		}
	}
		  
   /**
    * 采集基本信息
    */ 
    public function taskBaseInfo($id=null) {
		$array = $this->GatherService->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Log->savelog($this->taskId, "start gather details" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] details" );
			$this->GatherData->asinInfo($asin ,$id,$index,$this->taskId ) ;
		}
		$this->Log->savelog($this->taskId, "end!" );
	} 
	
   /**
    * 采集竞争信息
    */
    public function taskCompetition($id){
    	//获取商家产品asin
		$array = $this->GatherService->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Log->savelog($this->taskId, "start gather competition" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] competitions" );
			$this->GatherData->asinCompetition($asin,$id ,$index,$this->taskId ) ;
		}
		$this->Log->savelog($this->taskId, "end!" );
    }
    
    /**
     * 采集FBA竞争信息
     */
    public function taskFba($id){
    	$array = $this->GatherService->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Log->savelog($this->taskId, "start gather fba" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] fba" );
			$this->GatherData->asinFbas($asin,$id ,$index,$this->taskId ) ;
		}
		$this->Log->savelog($this->taskId, "end!" );
    }
    
}