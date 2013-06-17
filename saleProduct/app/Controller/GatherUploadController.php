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
	 * 通过商家ID获取商品
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
		
			$this->Log->saveException($this->taskId, $e );
			$this->Tasking->stop("gather_seller",$id,"") ;
		}	
    }

	public function uploadAsins(){
	
		$params = $this->request->data  ;
		$groupId = $params["groupId"] ;
		$platformId = $params["platformId"] ;
		
		$fileName = $_FILES['productFile']["name"] ;
		$myfile = $_FILES['productFile']['tmp_name'] ;
		$user =  $this->getCookUser() ;
		//save db
		$id = "UC_".date('U') ;
		
		$this->GatherService->saveUpload($id, $fileName,$groupId,$user,$platformId ) ;
		
		$file_handle = fopen($myfile , "r");
		while (!feof($file_handle)) {
		   $line = fgets($file_handle);
		   if( trim($line) != "" ){
		   		if( strlen(trim($line)) < 9 || strlen(trim($line)) >=11 ) {
					continue ;
				} ;
		   		//save to sc_gather_product id task_id asin
		   		try{
		   		$this->GatherService->saveGatherAsin($id, trim($line) ,$platformId) ;
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
		$platformId = $params["platformId"] ;
		$asins 	= $params["asins"] ;
		$this->GatherService->saveUpload($id, $name,$groupId,$user,$platformId) ;
		
		$asinss = explode(",",$asins) ;
		foreach( $asinss as $asin ){
			if( trim($asin) != "" ){
				if( strlen(trim($asin)) < 9 || strlen(trim($asin)) >=11 ) {
					continue ;
				} ; 
				try{
					$this->GatherService->saveGatherAsin($id, trim($asin),$platformId ) ;
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
			$this->Log->saveException($this->taskId, $e );
			$this->Tasking->stop("gather_seller_all",$id,"") ;
		}
	}
		  
   /**
    * 获取基本信息
    */ 
    public function taskBaseInfo($id=null) {
		$array = $this->GatherService->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Log->savelog($this->taskId, "start gather details" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$platformId = $arr['sc_gather_asin']['platform_id'] ;
			$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."]($platformId) details" );
			$this->GatherData->asinInfoPlatform($asin, $platformId ,$id,$index,$this->taskId ) ;
		}
		$this->Log->savelog($this->taskId, "end!" );
	} 
	
   /**
    * 获取竞争信息
    */
    public function taskCompetition($id){
    	//获取商家产品asin
		$array = $this->GatherService->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Log->savelog($this->taskId, "start gather competition" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$platformId = $arr['sc_gather_asin']['platform_id'] ;
			$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] competitions" );
			$this->GatherData->asinCompetitionPlatform($asin,$platformId,$id ,$index,$this->taskId ) ;
		}
		$this->Log->savelog($this->taskId, "end!" );
    }
    
    /**
     * 获取FBA竞争信息
     */
    public function taskFba($id){
    	$array = $this->GatherService->listTaskAsins( $id ) ;
		$index = 0 ;
		$this->Log->savelog($this->taskId, "start gather fba" );
		foreach( $array as $arr ){
			$index = $index + 1 ;
			$asin = $arr['sc_gather_asin']['asin'] ;
			$platformId = $arr['sc_gather_asin']['platform_id'] ;
			$this->Log->savelog($this->taskId, "start get product[ index: ".$index." ][".$asin."] fba" );
			$this->GatherData->asinFbasPlatform($asin,$platformId,$id ,$index,$this->taskId ) ;
		}
		$this->Log->savelog($this->taskId, "end!" );
    }
    
}