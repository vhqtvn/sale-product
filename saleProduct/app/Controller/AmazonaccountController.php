<?php

class AmazonaccountController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Amazonaccount', 'Config','Tasking','Warning','Utils');
   
    /**
     * 数据获取管理
     */
    public function gather($accountId){
    	$this->set("accountId",$accountId) ;
    }
    
    /**
     * 价格更新
     */
    public function priceUpdate($accountId){
    	$this->set("accountId",$accountId) ;
    }
    
    /**
     * 库存更新
     */
    public function quantityUpdate($accountId){
    	$this->set("accountId",$accountId) ;
    }
    
    public function category($accountId){
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($accountId);  
    	$warnings = $this->Warning->getWarnings($accountId);  
    	$this->set("categorys",$categorys) ;
    	$this->set("accountId",$accountId) ;
    	$this->set("warnings",$warnings) ;
    }
    
    public function assignCategory($asin,$accountId,$sku=null){
    	$this->set("asin",$asin) ;
    	$this->set("sku",$sku) ;
    	$this->set("accountId",$accountId) ;
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($accountId,$asin,null,$sku);  
    	$this->set("categorys",$categorys) ;
    }
    
    public function saveCategory($accountId){
    	$user =  $this->getCookUser() ;
 	
		$this->Amazonaccount->saveCategory($this->request->data,$user,$accountId) ;

    	$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
    
    public function saveProductCategory($accountId , $sku , $ids = null){
    	//$ids = $this->request->data['id'] ;
		$someone = $this->Amazonaccount->saveAmazonProductCategory($ids , $sku ,$accountId); 
		
		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ; 
    }
    
     public function saveCategoryProducts(){
     	$someone = $this->Amazonaccount->saveAmazonProductsCategory($this->request->data); 
		
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ; 
     }
    
     public function assignCategoryProduct($accountId,$categoryId){
     	$this->set("categoryId",$categoryId ) ;
     	$this->set("accountId",$accountId ) ;
     }
    
    
	 /**
	  * 账户列表页面
	  */
	 public function lists(){
	 }
	
	  /**
	  * 账户新增页面
	  */
	 public function add($id = null){
	 	if( !empty($id) ){
	 		$this->set("account",$this->Amazonaccount->getAccountIngoreDomainById($id)  ) ;
	 	}else{
	 		$this->set("account",null ) ;
	 	}
	 }
	 
	 /**
	  * 编辑账户产品价格页面
	  */
	 public function editAccountProduct($id){
	 	$this->set("accountProduct",$this->Amazonaccount->getAccountProduct($id)  ) ;
	 	//getAmazonConfigByType getAccountProduct
	 	$this->set("strategy",$this->Config->getAmazonConfigByType("strategy")  ) ;
	 }
	 
	 public function saveAccountProductFeed(){
	 	$user =  $this->getCookUser() ;
	 	
	 	$this->Amazonaccount->saveAccountProductFeed($this->request->data,$user) ;
	 	$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	 /**
	  * 保存账户产品价格
	  */
	 public function saveAccountProduct(){
	 	$user =  $this->getCookUser() ;
	 	
	 	$this->Amazonaccount->saveAccountProduct($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	 /**
	  * 保存账号
	  */
	 public function saveAccount(){
	 	$user =  $this->getCookUser() ;
	 	
	 	$this->Amazonaccount->saveAccount($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	 /**
	  * 产品列表
	  */
	 public function productLists($id  ){
	 	$account = $this->Amazonaccount->getAccountIngoreDomainById($id);  
	 	$account = $account[0]['sc_amazon_account'] ;
	 	
	 	$this->set('accountId', $account["ID"]);
		
		$accounts = $this->Amazonaccount->getAccounts();  
    	$this->set("accounts",$accounts) ;
    	
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($id);  
    	$this->set("categorys",$categorys) ;
	 }
	 
	 public function productListsPrice($id  ){
	 	$account = $this->Amazonaccount->getAccountIngoreDomainById($id);  
	 	$account = $account[0]['sc_amazon_account'] ;
	 	
	 	$this->set('accountId', $account["ID"]);
		
		$accounts = $this->Amazonaccount->getAccounts();  
    	$this->set("accounts",$accounts) ;
    	
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($id,null,"price");  
    	$this->set("categorys",$categorys) ;
	 }
	 
	 public function productListsQuantity($id  ){
	 	$account = $this->Amazonaccount->getAccountIngoreDomainById($id);  
	 	$account = $account[0]['sc_amazon_account'] ;
	 	
	 	$this->set('accountId', $account["ID"]);
		
		$accounts = $this->Amazonaccount->getAccounts();  
    	$this->set("accounts",$accounts) ;
    	
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($id,null,"quantity");  
    	$this->set("categorys",$categorys) ;
	 }
	 
	  
	 /**
	  * 配置管理
	  */
	 public function configLists(){
	 }
	 
	 /**
	  * 添加配置页面
	  */
	 public function addConfig($id = null){
	 	if( empty($id) ){
	 		$this->set('configItem', null);
	 	}else{
	 		$this->set("configItem", $this->Config->getAmazonConfigById($id) ) ;
	 	}
	 }
	 
	 /**
	  * 保存配置明细
	  */
	 public function saveConfigItem(){
	 	$user =  $this->getCookUser() ;
	 	
	 	$this->Amazonaccount->saveConfigItem($this->request->data,$user) ;

		$this->response->type("json") ;
		$this->response->body( "success")   ;

		return $this->response ;
	 }
	 
	 /**
	  * 同步Amazon产品操作
	  */
	 public function asynPage($type ,$id=null , $code = null){
	 	$this->set('type', $type);
	 	$this->set('id', $id);
	 	$this->set('code', $code);
	 }
	 
	 /**
	  * 获取操作
	  */
	 public function gatherPage($id=null , $code = null){
	 	$this->set('accountId', $id);
	 	$this->set('code', $code);
	 	
	 	$this->set("account",$this->Amazonaccount->getAccountIngoreDomainById($id)  ) ;
	 	
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($id);  
    	$this->set("categorys",$categorys) ;
	 }
	 
	 public function gatherDoPage($accountId , $categoryId = null ){
	 	//最近的获取任务
	 	$lastGatherTask = $this->Tasking->getLastGatherTask("gather_category",$categoryId ,$accountId) ;
	 	$tasking = $this->Tasking->getTasking("gather_category",$categoryId ,$accountId) ;
	 	$this->set("tasking",$tasking) ;
	 	$this->set("lastGatherTask",$lastGatherTask) ;
	 	$this->set('accountId', $accountId);
	 	$this->set('categoryId', $categoryId );
	 	$this->set("account",$this->Amazonaccount->getAccountIngoreDomainById($accountId,$categoryId)  ) ;
	 }
	 
	 public function doAmazonPrice(){
	 	try{
	 		$params = $this->request->data  ;
	 		$accountId = $params["accountId"] ;
	 		
	 		$user =  $this->getCookUser() ;
	 		$loginId = $user["LOGIN_ID"] ;
	 		
	 		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
	 		$account = $account[0]['sc_amazon_account'] ;
	 		
	 		$products = $this->Amazonaccount->listAccountUpdatableProductForPrice( $account["ID"] ) ;
	 		
	 		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
	 		
	 		$id = "UC_Price_".date('U') ;
	 		
	 		$_products = array() ;
	 		for( $i = 0 ;$i < count($products) ;$i++  ){
	 			$product = $products[$i]['sc_amazon_account_product'] ;
	 		
	 			$sku = $product["SKU"] ;
	 			$price = $product["FEED_PRICE"] ;
	 		
	 			$_products[] = array("SKU"=>$sku,"FEED_PRICE"=>$price) ;
	 		}
	 		
	 		$Feed = $this->Amazonaccount->getPriceFeed($MerchantIdentifier , $_products) ;
	 		
	 		$url = $this->Utils->buildUrl($account,"taskAsynAmazon/price") ;
	 		echo $url."?feed=".urlencode($Feed) ;
	 		file_get_contents($url."?feed=".urlencode($Feed));
	 		
	 	}catch(Exception $e){
	 		
	 		$this->response->type("html");
	 		$this->response->body( $e);
	 		return $this->response;
	 	}
	 	
		$this->response->type("html");
		$this->response->body("success");
		return $this->response;
	 }
	 

	public function doUploadAmazonPrice(){
		$params = $this->request->data  ;
		$accountId = $params["accountId"] ;
		
		$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		
		$fileName = $_FILES['priceFile']["name"] ;
		$myfile = $_FILES['priceFile']['tmp_name'] ;
		$user =  $this->getCookUser() ;
		$loginId = $user["LOGIN_ID"] ;
		//save db
		$id = "UC_".date('U') ;
		
		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
		
		
		$file_handle = fopen($myfile , "r");
		$index = 0 ;
		$_products = array() ;
		while (!feof($file_handle)) {
		   $line = fgets($file_handle);
		   if( trim($line) != "" ){
		   		$array = explode(",",$line) ;
		   		$sku = trim($array[0]) ;
		   		$price = trim($array[1]) ;
		   		if( empty($sku) || empty($price) ) continue ;
		 
		   		//up sc_amazon_account_product
		   		$this->Amazonaccount->saveAccountProductFeedPrice( array('accountId'=>$accountId,'sku'=>$sku,'feedPrice'=>$price) ) ;
		   		
		   		$_products[] = array("SKU"=>$sku,"FEED_PRICE"=>$price ) ;
		 	}
		}
		fclose($file_handle);
		
		$Feed = $this->Amazonaccount->getPriceFeed($MerchantIdentifier , $_products) ;
		
		$url = $this->Utils->buildUrl($account,"taskAsynAmazon/price") ;
		file_get_contents($url."?feed=".urlencode($Feed));

		$this->response->type("html");
		$this->response->body("success");
		return $this->response;
	}
}