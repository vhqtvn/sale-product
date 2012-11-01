<?php

/**
 * 订单controller
 */
class OrderController extends AppController {
	
	var $uses = array('OrderService');
	
	public function doUpload($accountId){
		
    	$params = $this->request->data  ;
    	if(isset($_FILES['orderFile'])){
    		$fileName = $_FILES['orderFile']["name"] ;
			$myfile = $_FILES['orderFile']['tmp_name'] ;
			$user =  $this->getCookUser() ;
			//save db
			$id = "O_".date('U') ;
			
			$this->OrderService->saveUpload( $id, $fileName,$accountId,$user ) ;
			
			$file_handle = fopen($myfile , "r");
			
			$isFirst = true ;
			$header = null ;
			while (!feof($file_handle)) {
			   $line = fgets($file_handle);
			   if( trim($line) != "" ){
			   		$line = trim($line) ;
			   		if( $isFirst === true ){//head
			   			$isFirst = false ;
			   			$header = explode("\t",$line) ;
			   		}else{
			   			$item  = explode("\t",$line) ;
			   			$index = 0 ;
			   			$map = array() ;
			   			foreach($item as $col){
			   				$map[ $header[$index] ] = trim($col) ;
			   				$index++ ;
			   			} 
			   			if( empty( $map['order-id']) ) continue ;
			   			$this->OrderService->saveOrderItem($accountId, $map ,$id ) ;
			   		}
			   }
			}
			fclose($file_handle);
    		
    		$this->response->type("html");
			$this->response->body("<script type='text/javascript'>window.opener.location.reload();</script>");
    	}
				
		
		return $this->response;
    
	}
	
	/**
	 * 上传订单
	 */
    public function upload($accountId){
    	$this->set("accountId",$accountId);
    }
    
    public function lists($accountId,$status = null){
    	$this->set("accountId",$accountId);
    	$this->set("status",$status);
    }
    
    /**
     * 进入审计页面
     */
    public function audit( $status = null ){
    	$this->set("status",$status);
    }
    
    public function saveAudit( $status = null ){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$this->OrderService->saveAudit($params,$user ) ;
    	
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
}