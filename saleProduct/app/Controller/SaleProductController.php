<?php

class SaleProductController extends AppController {
	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $uses = array('SaleProduct', 'Amazonaccount');
    
    public function forward($layout , $sku=null){
    	$this->set('sku',$sku);
    	
    	$item = null ;
    	if(!empty($sku)){
    		$item =$this->SaleProduct->getSaleProduct($sku) ;
    		$item = $item[0]['sc_real_product'] ;
    	}
    	$this->set('item',$item);
    	$this->layout = "../SaleProduct/forward/".$layout ;
    }
    
    public function lists(){
    }
    
    public function bindProduct( $sku){
    	$this->set('sku',$sku);
    }
    
    
    public function editProduct($sku = null){
    	$item = null ;
    	if(!empty($sku)){
    		$item =$this->SaleProduct->getSaleProduct($sku) ;
    		$item = $item[0]['sc_real_product'] ;
    	}
    	$this->set('item',$item);
    }
    
    public function saveProduct(){
    	$user =  $this->getCookUser() ;
    	$this->SaleProduct->saveProduct($this->request->data , $user) ;

		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
    
    public function details($sku){
    	$this->set('sku',$sku);
    }

    public function bindProductDetails($accountId , $sku){
    	$this->set('sku',$sku);
    	$this->set("accountId",$accountId) ;
    	
    	$account = $this->Amazonaccount->getAccount($accountId);  
	 	$account = $account[0]['sc_amazon_account'] ;
	 	
	 	$this->set('accountId', $account["ID"]);
		
		$accounts = $this->Amazonaccount->getAccounts();  
    	$this->set("accounts",$accounts) ;
    	
    	$categorys = $this->Amazonaccount->getAmazonProductCategory($accountId);  
    	$this->set("categorys",$categorys) ;
    }
    
     public function saveSelectedProducts(){
    	$user =  $this->getCookUser() ;
    	$this->SaleProduct->saveSelectedProducts($this->request->data , $user) ;

		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
}