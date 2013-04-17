<?php

class SaleProductController extends AppController {
	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $uses = array('SaleProduct', 'Amazonaccount');
    
    public function forward($layout , $id=null){
    	$this->set('id',$id);
    	
    	$item = null ; 
    	if(!empty($id)){
    		$item1 =$this->SaleProduct->getSaleProductById($id) ;
    		
    		$item = $this->SaleProduct->formatObject($item1[0]) ;
    		
    		//$item = $item1[0]['sc_real_product'] ;
    		//$item['CATEGORY_NAME'] = $item1[0][0]['CATEGORY_NAME'] ;
    	}else{
    		$max =$this->SaleProduct->getMaxSku() ;
    		$this->set('realSku',$max);
    	}
    	$this->set('item',$item);
    	$this->layout = "../SaleProduct/forward/".$layout ;
    }
    
    public function lists(){
    }
  
    
    public function bindProduct( $id ,$type=null){
    	$this->set('id',$id);
		$this->set('type',$type);
    }
    
    public function giveup($id,$type){
    	$this->SaleProduct->giveup($id,$type) ;
    	$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;
		return $this->response ;
    }
    
    public function editProduct( $id = null ){
    	$item = null ;
    	if(!empty($sku)){
    		$item =$this->SaleProduct->getSaleProductById($id) ;
    		$item = $item[0]['sc_real_product'] ;
    	}else{
    		$max =$this->SaleProduct->getMaxSku() ;
    		$item = array('REAL_SKU',$max) ;
    	}
    	$this->set('item',$item);
    }
    
    public function saveProduct(){
    	//上传图片 imageUrl
    	$fileName = $_FILES['imageUrl']["name"] ;
		
		
		$params = $this->request->data ;
		$user =  $this->getCookUser() ;
		
		$params['imageUrl'] = "" ;
		
		if( !empty($fileName) ){
			$myfile   = $_FILES['imageUrl']['tmp_name'] ;
			$path = dirname(dirname(dirname(__FILE__)))."/images/real_product/";
		
			if( !file_exists($path) ) {
				$this->creatdir($path) ;
			}
			$fileUrl = $path.trim( $params['sku'] );
			move_uploaded_file($myfile,$fileUrl) ;
			
			$params['imageUrl'] = "/images/real_product/".trim( $params['sku']);
		}
		
    	$this->SaleProduct->saveProduct($params , $user) ;

		$this->response->type("html");
		$this->response->body("<script>window.top.window.close();</script>");
		return $this->response;
    }
    
    public function creatdir($path){
		if(!is_dir($path))
		{
			if($this->creatdir(dirname($path)))
			{
				mkdir($path,0777);
				return true;
			}
		}
		else
		{
			return true;
		}
	}
    
    public function details($id ,$type = null ){
    	
    	$item = null ;
    	if(!empty($id)){
    		if( $type == 'sku' ){
    			$item = $this->SaleProduct->getSaleProduct($id) ;
    			$this->set('action','view');
    		}else{
    			$item =$this->SaleProduct->getSaleProductById($id) ;
    		}
    		
    		$item = $item[0]['sc_real_product'] ;
    		
    		$this->set('id',$item['ID']);
    	}
    	$this->set('item',$item);
    }

    public function bindProductDetails($accountId , $id ,$type=null){
    	$this->set('id',$id);
		$this->set('type',$type);
    	$this->set("accountId",$accountId) ;
    	
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId);  
	 	$account = $account[0]['sc_amazon_account'] ;
	 	
	 	$this->set('accountId', $account["ID"]);
		
		$accounts = $this->Amazonaccount->getAllAccounts();  
    	$this->set("accounts",$accounts) ;
    	
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($accountId);  
    	$this->set("categorys",$categorys) ;
    }
    
    public function bindSkuDetails($accountId , $id,$type=null){
    	$this->set('id',$id);
		$this->set('type',$type);
    	$this->set("accountId",$accountId) ;
    	
    	$account = $this->Amazonaccount->getAccountIngoreDomainById($accountId);  
	 	$account = $account[0]['sc_amazon_account'] ;
	 	
	 	$this->set('accountId', $account["ID"]);
		
		$accounts = $this->Amazonaccount->getAllAccounts();  
    	$this->set("accounts",$accounts) ;
    }
    
    function saveSkuToRealProduct(){
    	$user =  $this->getCookUser() ;
    	$this->SaleProduct->saveSkuToRealProduct($this->request->data , $user) ;

		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
    
     public function saveSelectedProducts(){
    	$user =  $this->getCookUser() ;
    	$this->SaleProduct->saveSelectedProducts($this->request->data , $user) ;

		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
    
    public function deleteRelProduct(){
    	$user =  $this->getCookUser() ;
    	$this->SaleProduct->deleteRelProduct($this->request->data , $user) ;

		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
    
      
    public function deleteComposition(){
    	$user =  $this->getCookUser() ;
    	$this->SaleProduct->deleteComposition($this->request->data , $user) ;

		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
}