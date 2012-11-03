<?php
App :: import('Vendor', 'Amazon');
/**
 * 订单controller
 */
class OrderController extends AppController {
	
	var $uses = array('OrderService','Amazonaccount');
	
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
    
    /**
     * 新订单列表
     */
    public function lists($accountId,$status = null){
    	$this->set("accountId",$accountId);
    	$this->set("status",$status);
    }
    
    /**
     * 处理中列表
     */
    public function doingLists($accountId,$status = null){
    	$this->set("accountId",$accountId);
    	$this->set("status",$status);
    }
    
    public function updateTrackNumber(){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$this->OrderService->updateTrackNumber($params,$user ) ;

    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
    
    public function saveTrackNumberToAamazon($accountId){
    	$account = $this->Amazonaccount->getAccount($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
		
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$feed = $this->OrderService->getTrackNumberFeed($params,$user ,$accountId,$MerchantIdentifier) ;
    	
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		
		$result = $amazon->updateOrderTrackNumber( $accountId,$feed,$user['LOGIN_ID'] ) ;
		
		//更新订单状态为已发货
		$this->Amazonaccount->saveAccountFeed($result) ;
		$this->OrderService->updateTrackNumberStatus($params,$user ,$accountId) ;
		
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
    
    /**
     * 处理完成列表
     */
    public function doneLists($accountId,$status = null){
    	$this->set("accountId",$accountId);
    	$this->set("status",$status);
    }
    
    /**
     * 进入审计页面
     */
    public function audit( $status = null ){
    	$this->set("status",$status);
    }
    
    /**
     * 保存审查结果
     */
    public function saveAudit( $status = null ){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$this->OrderService->saveAudit($params,$user ) ;
    	
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
    
    /**
     * 列出将货单
     */
    public function listPicked(){
    	
    }
    
    public function editPicked($pickedId = null){
    }
    
    public function savePicked(){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$this->OrderService->savePicked($params,$user ) ;
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
    
    public function selectPickedProduct($pickId){	
    	$this->set("pickId",$pickId) ;
    }
    
    public function savePickedOrder($pickedId){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$this->OrderService->savePickedOrder($params,$user,$pickedId ) ;
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
}