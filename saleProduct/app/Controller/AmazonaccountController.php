<?php

App :: import('Vendor', 'Amazon');

class AmazonaccountController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Amazonaccount', 'Config');
   
    /**
     * 数据采集管理
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
    	$this->set("categorys",$categorys) ;
    	$this->set("accountId",$accountId) ;
    }
    
    public function assignCategory($asin,$accountId){
    	$this->set("asin",$asin) ;
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($accountId,$asin);  
    	$this->set("categorys",$categorys) ;
    }
    
    public function saveCategory($accountId){
    	$user =  $this->getCookUser() ;
 	
		$this->Amazonaccount->saveCategory($this->request->data,$user,$accountId) ;

    	$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
    
    public function saveProductCategory($asin , $ids){
    	//$ids = $this->request->data['id'] ;
		$someone = $this->Amazonaccount->saveAmazonProductCategory($ids , $asin ); 
		
		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ; 
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
	 		$this->set("account",$this->Amazonaccount->getAccount($id)  ) ;
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
	 	$account = $this->Amazonaccount->getAccount($id);  
	 	$account = $account[0]['sc_amazon_account'] ;
	 	
	 	$this->set('accountId', $account["ID"]);
		
		$accounts = $this->Amazonaccount->getAccounts();  
    	$this->set("accounts",$accounts) ;
    	
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($id);  
    	$this->set("categorys",$categorys) ;
	 }
	 
	 public function productListsPrice($id  ){
	 	$account = $this->Amazonaccount->getAccount($id);  
	 	$account = $account[0]['sc_amazon_account'] ;
	 	
	 	$this->set('accountId', $account["ID"]);
		
		$accounts = $this->Amazonaccount->getAccounts();  
    	$this->set("accounts",$accounts) ;
    	
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($id,null,"price");  
    	$this->set("categorys",$categorys) ;
	 }
	 
	 public function productListsQuantity($id  ){
	 	$account = $this->Amazonaccount->getAccount($id);  
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
	  * 采集操作
	  */
	 public function gatherPage($id=null , $code = null){
	 	$this->set('accountId', $id);
	 	$this->set('code', $code);
	 	
	 	$this->set("account",$this->Amazonaccount->getAccount($id)  ) ;
	 	
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($id);  
    	$this->set("categorys",$categorys) ;
	 }
	 
	 public function gatherDoPage($accountId , $categoryId = null ){
	 	$this->set('accountId', $accountId);
	 	$this->set('categoryId', $categoryId );
	 	$this->set("account",$this->Amazonaccount->getAccount($accountId,$categoryId)  ) ;
	 }
	 
	  public function doAmazonPrice(){
	 	$params = $this->request->data  ;
		$accountId = $params["accountId"] ;
		
		$user =  $this->getCookUser() ;
		$loginId = $user["LOGIN_ID"] ;
		
		$account = $this->Amazonaccount->getAccount($accountId) ;
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
		
		$Feed = $this->getPriceFeed($MerchantIdentifier , $_products) ;
		
		$account = $this->Amazonaccount->getAccount($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		
		$result = $amazon->updatePrice($accountId,$Feed,$loginId) ;
		
		print_r($result) ;
		$this->Amazonaccount->saveAccountFeed($result) ;

		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.parent.uploadSuccess('".$id."');</script>");
		return $this->response;
	 }
	 
	 public function doAmazonQuantity(){
	 	
	 	$id = "UC_Quantity_".date('U') ;
	 	
	 	$params = $this->request->data  ;
		$accountId = $params["accountId"] ;
		
		$user =  $this->getCookUser() ;
		$loginId = $user["LOGIN_ID"] ;
		
		$account = $this->Amazonaccount->getAccount($accountId) ;
		$account = $account[0]['sc_amazon_account'] ;
		$MerchantIdentifier = $account["MERCHANT_IDENTIFIER"] ;
		$products = $this->Amazonaccount->listAccountUpdatableProductForQuantity( $account["ID"] ) ;
		
		$_products = array() ;
		for( $i = 0 ;$i < count($products) ;$i++  ){
			$product = $products[$i]['sc_amazon_account_product'] ;

			$sku = $product["SKU"] ;
			$quantity = $product["FEED_QUANTITY"] ;
			
			$_products[] = array("SKU"=>$sku,"FEED_QUANTITY"=>$quantity) ;
		}
		
		$Feed = $this->getQuantityFeed($MerchantIdentifier , $_products) ;

		$account = $this->Amazonaccount->getAccount($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		
		$result = $amazon->updateInventory($accountId,$Feed,$loginId) ;
		
		print_r($result) ;
		$this->Amazonaccount->saveAccountFeed($result) ;

		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.parent.uploadSuccess('".$id."');</script>");
		return $this->response;

	 }
	 
	
	/**
	 * 更新库存
	 */
	public function doUploadAmazonQuantity(){
		$params = $this->request->data  ;
		$accountId = $params["accountId"] ;
		
		$account = $this->Amazonaccount->getAccount($accountId) ;
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
		   		$quantity = trim($array[1]) ;
		   		if( empty($sku) || empty($quantity) ) continue ;
		 
		   		//up sc_amazon_account_product
		   		$this->Amazonaccount->saveAccountProductFeedQuantity( array('accountId'=>$accountId,'sku'=>$sku,'feedQuantity'=>$quantity) ) ;
		   	
		   		$_products[] = array("SKU"=>$sku,"FEED_QUANTITY"=>$quantity ) ;
		 	}
		}
		fclose($file_handle);
		
		$Feed = $this->getQuantityFeed($MerchantIdentifier , $_products) ;

		$account = $this->Amazonaccount->getAccount($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		
		$result = $amazon->updateInventory($accountId,$Feed,$loginId) ;
		
		print_r($result) ;
		$this->Amazonaccount->saveAccountFeed($result) ;

		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.parent.uploadSuccess('".$id."');</script>");
		return $this->response;
	}

	public function doUploadAmazonPrice(){
		$params = $this->request->data  ;
		$accountId = $params["accountId"] ;
		
		$account = $this->Amazonaccount->getAccount($accountId) ;
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
		
		$Feed = $this->getPriceFeed($MerchantIdentifier , $_products) ;

		$account = $this->Amazonaccount->getAccount($accountId) ;
    	$account = $account[0]['sc_amazon_account'] ;
    	$amazon = new Amazon(
				$account['AWS_ACCESS_KEY_ID'] , 
				$account['AWS_SECRET_ACCESS_KEY'] ,
			 	$account['APPLICATION_NAME'] ,
			 	$account['APPLICATION_VERSION'] ,
			 	$account['MERCHANT_ID'] ,
			 	$account['MARKETPLACE_ID'] ,
			 	$account['MERCHANT_IDENTIFIER'] 
		) ;
		
		$result = $amazon->updatePrice($accountId,$Feed,$loginId) ;
		
		print_r($result) ;
		$this->Amazonaccount->saveAccountFeed($result) ;

		$this->response->type("html");
		$this->response->body("<script type='text/javascript'>window.parent.uploadSuccess('".$id."');</script>");
		return $this->response;
	}
	
	
	function getQuantityFeed($MerchantIdentifier , $products){
		////////////////////////////////////////////////////////////////////////////		
$Feed = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
	<Header>
		<DocumentVersion>1.01</DocumentVersion>
		<MerchantIdentifier>$MerchantIdentifier</MerchantIdentifier>
	</Header>
	<MessageType>Inventory</MessageType>
EOD;
////////////////////////////////////////////////////////////////////////////
		
		$index = 0 ;
		
		for( $i = 0 ;$i < count($products) ;$i++  ){
			$index++ ;
			$product = $products[$i] ;

			$sku = $product["SKU"] ;
			$quantity = $product["FEED_QUANTITY"] ;
	   		
////////////////////////////////////////////////////////////////////////////
$Feed .= <<<EOD
	<Message>
		<MessageID>$index</MessageID>
		<Inventory>
			<SKU>$sku</SKU>
			<Quantity>$quantity</Quantity>
		</Inventory>
	</Message>
EOD;
	
		}
$Feed .= <<<EOD
</AmazonEnvelope>
EOD;
		return $Feed ;
	}	
		
	/**
	 $feed = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
	<Header>
		<DocumentVersion>1.01</DocumentVersion>
		<MerchantIdentifier>M_CYBERKIN_107805233</MerchantIdentifier>
	</Header>
	<MessageType>Price</MessageType>
	<Message>
		<MessageID>1</MessageID>
		<Price>
			<SKU>B003D8GAA0</SKU>
			<StandardPrice currency="USD">16.01</StandardPrice>
		</Price>
	</Message>
</AmazonEnvelope>
EOD;
	 */
	function getPriceFeed($MerchantIdentifier , $products){
////////////////////////////////////////////////////////////////////////////		
$Feed = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
	<Header>
		<DocumentVersion>1.01</DocumentVersion>
		<MerchantIdentifier>$MerchantIdentifier</MerchantIdentifier>
	</Header>
	<MessageType>Price</MessageType>
EOD;
////////////////////////////////////////////////////////////////////////////
		
		$index = 0 ;
		
		for( $i = 0 ;$i < count($products) ;$i++  ){
			$index++ ;
			$product = $products[$i] ;

			$sku = $product["SKU"] ;
			$price = $product["FEED_PRICE"] ;
		   		
/*
<StandardPrice currency="USD">80.00</StandardPrice>
<Sale>
<StartDate>2009-05-15T00:00:01-08:00</StartDate>
<EndDate>2009-05-17T00:00:01-08:00</EndDate>
<SalePrice currency="USD">77.00</SalePrice>
</Sale>*/		   		
////////////////////////////////////////////////////////////////////////////
$Feed .= <<<EOD
	<Message>
		<MessageID>$index</MessageID>
		<Price>
			<SKU>$sku</SKU>
			<StandardPrice currency="USD">$price</StandardPrice>
		</Price>
	</Message>
EOD;
////////////////////////////////////////////////////////////////////////////

		}
////////////////////////////////////////////////////////////////////////////
$Feed .= <<<EOD
</AmazonEnvelope>
EOD;
		return $Feed ;
	}	
}