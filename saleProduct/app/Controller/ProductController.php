<?php

class ProductController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
     var $uses = array('Sale', 'Product','Amazonaccount');
    
    function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('*');
	}
	
	function blacklist(){
		
	}

	function enableBlackProduct($asin){
		$this->Product->enableBlackProduct($asin);  
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
	}

	public function index($id=null) {
		$this->set('taskId', $id);
		
		$categorys = $this->Product->getProductCategory();  
    	$this->set("categorys",$categorys) ;
    }

	public function rule($id=null) {
		 if( $id != null  ){
				$rule = $this->Product->findById($id) ;
				$this->set('rule', $rule);
		 }
		 
		$accounts = $this->Amazonaccount->getAccountsFront() ;
		$this->set('accounts', $accounts);
    }

	public function script() {
    }
    
    public function category(){
    	$categorys = $this->Product->getProductCategory();  
    	$this->set("categorys",$categorys) ;
    }
    
    public function assignCategory($asin){
    	$this->set("asin",$asin) ;
    	$categorys = $this->Product->getProductCategory($asin);  
    	$this->set("categorys",$categorys) ;
    }
    
    public function saveCategory(){
    	$user =  $this->getCookUser() ;
 	
		$this->Product->saveCategory($this->request->data,$user) ;

    	$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
    
    public function saveProductCategory($asin , $ids){
    	//$ids = $this->request->data['id'] ;
		$someone = $this->Product->saveProductCategory($ids , $asin ); 
		
		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ; 
    }
	
	public function editScript($id=null){
		if($id!=null){ //update
			$rule = $this->Product->findById($id) ;
			$this->set('rule', $rule);
		}else{
			$this->set('rule', null);
		}
	}

	public function saveScript(){
		$data = $this->request->data ;
		$this->Product->saveConfig($this->request->data) ;

		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
	}


	public function deleteScript($id){
		$this->Product->deleteConfig($id) ;

		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
	}

	public function details($asin,$accountId = null,$sku = null){
		$details = $this->Product->getProductDetails($asin) ;
		$images   = $this->Product->getProductImages($asin) ;
		$competitions  = $this->Product->getProductCompetitionDetails($asin) ;
		$fbas  = $this->Product->getProductFbaDetails($asin) ;
		$rankings  = $this->Product->getProductRankingDetails($asin) ;
		$flows  = $this->Product->getProductFlowDetails($asin) ;
		$strategys  = $this->Product->getProductStrategy() ;
		$suppliers  = $this->Product->getProductSupplier($asin) ;
		
		$this->set('asin', $asin);
		$this->set('sku', $sku);
		$this->set('accountId', $accountId);
		$this->set('details', $details);
		$this->set('images', $images);
		$this->set('competitions', $competitions);
		$this->set('fbas', $fbas);
		$this->set('rankings', $rankings);
		$this->set('flows', $flows);
		$this->set('strategys', $strategys);
		$this->set('suppliers', $suppliers);
	}

	public function seller(){
	}
	
	/**
	 * 上传产品
	 */
	public function upload(){
		$uploadGroup  = $this->Product->getUploadGroup() ;
		
		$this->set('uploadGroup', $uploadGroup);
	}
	
	public function uploadGroup(){
		$uploadGroup  = $this->Product->getUploadGroup() ;
		
		$this->set('uploadGroup', $uploadGroup);
	}
	
	public function saveUploadGroup(){
		$user =  $this->getCookUser() ;
 	
		$this->Product->saveUploadGroup($this->request->data,$user) ;

    	$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
	}
	
	
	public function uploadPage($id=null,$text=null){
		$this->set('id', $id);
		$this->set('text', $text);
	}
	
	public function ruleitem(){
		
	}
	
	public function filterScope(){
		
	}
	
}