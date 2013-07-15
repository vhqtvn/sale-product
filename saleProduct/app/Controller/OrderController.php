<?php

/**
 * 订单controller
 */
class OrderController extends AppController {
	
	var $uses = array('OrderService','Amazonaccount','SqlUtils','Utils');
	
	public function doUpload($accountId){
		
    	$params = $this->request->data  ;
    	$startTime = $params['startTime'] ;
    	$endTime = $params['endTime'] ;
    	if(isset($_FILES['orderFile'])){
    		$fileName = $_FILES['orderFile']["name"] ;
			$myfile = $_FILES['orderFile']['tmp_name'] ;
			$user =  $this->getCookUser() ;
			//save db
			$id = "O_".date('U') ;
			
			$this->OrderService->saveUpload( $id, $fileName,$accountId,$user,$startTime,$endTime ) ;
			
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
			   			try{
			   			$this->OrderService->saveOrderItem($accountId, $map ,$id ,$header ) ;
			   			}catch(Exception $e){
			   				print_r($e) ;
			   			}
			   		}
			   }
			}
			fclose($file_handle);
    		
    		$this->response->type("html");
			$this->response->body("<script type='text/javascript'>window.location.reload()</script>");
    	}
				
		
		return $this->response;
    
	}
	
	/**
	 * 上传订单
	 */
    public function upload($accountId){
    	$this->set("accountId",$accountId);
    }
    
    public function orderDownload($accountId){
    	$this->set("accountId",$accountId);
    }
    
    /**
     * 新订单列表
     */
    public function lists($accountId=null,$status = null){
    	$this->set("accountId",$accountId);
    	$this->set("status",$status);
    }
    
    /**
     * 处理中列表
     */
    public function doingLists($accountId=null,$status = null){
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
    
        
    /**
     * 下载对应账号对应的订单
     */
    public function doDownloadOrder($accountId,$downId=null){
    	if(!empty($downId)){
    		$down = $this->OrderService->getDownloadById($downId) ;
    		$down = $down[0]['sc_amazon_order_download'] ;
    		$this->set("name",$down['NAME']) ;
			$this->set("feed",$down['FEED']) ;
    	}else{
    		$name = "ACCOUNT$accountId".'_'.date("YmdHi") ;
	    	$user =  $this->getCookUser() ;
	    	
	    	$this->set("accountId",$accountId) ;
	    	$accounts = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
	    	$account = $accounts[0]['sc_amazon_account'] ;
	    	$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
	    	$feed = $this->OrderService->downloadOrderFeed($accountId,$MerchantIdentifier,$user,$name) ;
	    	
	    	//保存下载批次
	    	$this->set("name",$name) ;
			$this->set("feed",$feed) ;
    	}
    }
    
    public function saveTrackNumberToAamazon($pickedId = null ){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	
    	//查找系统账户
    	$accounts = $this->Amazonaccount->getAccounts() ;
    	foreach( $accounts as $account ){
    		$account = $account['sc_amazon_account'] ;
    		$accountId = $account['ID'] ;
			$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
			$feed = $this->OrderService->getTrackNumberFeed($params,$user ,$accountId,$MerchantIdentifier) ;

			
		    //build url
			$url = $this->Utils->buildUrl($account,"taskAsynAmazon") ;
			
			//$result = $amazon->updateOrderTrackNumber( $accountId,$feed,$user['LOGIN_ID'] ) ;
			
			//更新订单状态为已发货
			//$this->Amazonaccount->saveAccountFeed($result) ;
			//$this->OrderService->updateTrackNumberStatus($params,$user ,$accountId) ;
    	} ;
    	
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
    
    public function processCompleteOrder($action ,$orderId , $orderItemId){
    	$this->set("action" , $action) ;
    	$this->set("orderId" , $orderId) ;
    	$this->set("orderItemId",$orderItemId) ;
    	
    	$orderUser = $this->OrderService->getOrderUser($orderId ,$orderItemId ) ;
    	$orderUser = $orderUser[0]['sc_amazon_order_user'] ;
    	$isDangerUser = $orderUser['STATUS'] == 'danger' ;
 
		$this->set("isDangerUser",$isDangerUser) ;
    }
    
    public function saveRedoOrder(){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$this->OrderService->saveRedoOrder($params,$user ) ;
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
    
    public function viewTrack($orderId , $orderItemId){
    	$this->set("orderId" , $orderId) ;
    	$this->set("orderItemId",$orderItemId) ;
    } 
    
    public function savePicked(){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$this->OrderService->savePicked($params,$user ) ;
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
    
    public function test(){
    	$sql = $this->OrderService->getDbSql("sql_order_getMaxOrderNumber") ;
		$sql = $this->OrderService->getSql($sql,array()) ;
		$count = $this->OrderService->query($sql) ;
		debug($count[0][0]['ORDER_NUMBER']) ;
    }
    
    public function selectPickedProduct($pickId){	
    	$this->set("pickId",$pickId) ;
    }
    
    //二次分拣是将订单设置为异常订单
    public function repickedException($pickedId = null){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$result = $this->OrderService->repickedException($params,$user,$pickedId ) ;
    	
    	$this->response->type("json");
		$this->response->body($result?1:0);
		return $this->response;
    }
    
    public function savePickedOrder($pickedId = null){
    	$params = $this->request->data  ;
    	$user =  $this->getCookUser() ;
    	$this->OrderService->savePickedOrder($params,$user,$pickedId ) ;
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
    
    public function printPicked($pickId){
    	$this->set("pickId",$pickId) ;
    	$pick = $this->OrderService->getPicked($pickId) ;
    	$this->set("pick",$pick) ;
    }
    
    public function exportPicked($pickId){
    	$this->set("pickId",$pickId) ;
    	$pick = $this->OrderService->getPicked($pickId) ;
    	$this->set("pick",$pick) ;
    }

    
    public function doExportPicked($pickId){
    	
    	$this->set("pickId",$pickId) ;
    	$items = $this->OrderService->getPickOrders($pickId) ;
    	$pick = $this->OrderService->getPicked($pickId) ;
    	
    	$pName = str_replace(" ","",$pick['NAME']) ;
    	
    	$filename = $pName.'-'.date("Ymd").".csv";
		$csv_file = fopen('php://output', 'w');

		header('Content-type: application/csv;charset=GB2312');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		
		// The column headings of your .csv file
		$header_row = array("PICK_ID", "ORDER_NUMBER", "REAL_SKU", "NAME","WEIGHT","WEIGHT_WITH_UNIT","LENGTH","WIDTH","HEIGHT", 
		"PAYMENTS_DATE", "SHIP_SERVICE_LEVEL", "TRACKING_SERVICE", "PACKAGE_VALUE", 
		"SKU", "BUYER_PHONE_NUMBER", "BUYER_EMAIL",
		"QUANTITY_TO_SHIP","ORDER_ID","ORDER_ITEM_ID","RECIPIENT_NAME","SHIP_ADDRESS_1","SHIP_ADDRESS_2","SHIP_ADDRESS_3",
		"SHIP_COUNTRY","SHIP_CITY","SHIP_STATE","SHIP_POSTAL_CODE", "MEMO");
		fputcsv($csv_file,$header_row,',','"');
	
		// Each iteration of this while loop will be a row in your .csv file where each field corresponds to the heading of the column
		foreach($items as $result)
		{
			$result = $result['t'] ;
			// Array indexes correspond to the field names in your db table(s)
			$row = array(
				$result['PICK_ID'],
				$result['ORDER_NUMBER'],
				$result['REAL_SKU'],
				$result['NAME'],
				$result['WEIGHT'],
				$result['WEIGHT_WITH_UNIT'],
				$result['LENGTH'],
				$result['WIDTH'],
				$result['HEIGHT'],
				
				$result['PAYMENTS_DATE'],
				$result['SHIP_SERVICE_LEVEL'],
				$result['TRACKING_SERVICE'],
				$result['PACKAGE_VALUE'],
				
				$result['SKU'],
				$result['BUYER_PHONE_NUMBER'],
				$result['BUYER_EMAIL'],
				$result['QUANTITY_TO_SHIP'],
				$result['ORDER_ID'],
				$result['ORDER_ITEM_ID'],
				$result['RECIPIENT_NAME'],
				$result['SHIP_ADDRESS_1'],
				$result['SHIP_ADDRESS_2'],
				$result['SHIP_ADDRESS_3'],
				$result['SHIP_COUNTRY'],
				$result['SHIP_CITY'],
				$result['SHIP_STATE'],
				$result['SHIP_POSTAL_CODE'],
				$result['MEMO']
			);
	
			fputcsv($csv_file,$row,',','"');
		}
	
		fclose($csv_file);
    	
    }
    
    public function updatePickedStatus($pickId){
    	$this->set("pickId",$pickId) ;
    	$this->OrderService->updatePickedStatus($pickId ) ;
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
		
		
    }
    
    /**
     * 出仓
     */
    public function outWarehouse(){
    }
    
    public function rePrintPicked($pickId = null,$type = null){
    	$this->set("pickId",$pickId) ;
    	$this->set("type",$type) ;
    }
}