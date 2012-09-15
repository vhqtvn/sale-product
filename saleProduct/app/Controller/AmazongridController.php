<?php

class AmazongridController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    /**
     * 账户列表
     */
	public function account(){
		 $records=  $this->Amazongrid->getAccountRecords( $this->request->query ) ;
		 $count   =  $this->Amazongrid->getAccountCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	/**
	 * 账户产品列表
	 */
	public function product(){
		 $records=  $this->Amazongrid->getProductRecords( $this->request->query ) ;
		 $count   =  $this->Amazongrid->getProductCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	/**
	 * amazon配置列表
	 */
	public function config(){
		 $records=  $this->Amazongrid->getConfigRecords( $this->request->query ) ;
		 $count   =  $this->Amazongrid->getConfigCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function productAsynsHistory($accountId){
		$records=  $this->Amazongrid->getProductAsynsHistoryRecords( $this->request->query ,$accountId) ;
		$count   =  $this->Amazongrid->getProductAsynsHistoryCount( $this->request->query ,$accountId) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function productActiveAsynsHistory($accountId){
		$records=  $this->Amazongrid->getProductActiveAsynsHistoryRecords( $this->request->query ,$accountId) ;
		$count   =  $this->Amazongrid->getProductActiveAsynsHistoryCount( $this->request->query ,$accountId) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function productFeedHistory($accountId){
		$records=  $this->Amazongrid->getProductFeedHistoryRecords( $this->request->query ,$accountId) ;
		$count   =  $this->Amazongrid->getProductFeedHistoryCount( $this->request->query ,$accountId) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function productFeedQuantityHistory($accountId){
		$records=  $this->Amazongrid->getProductFeedQuantityHistoryRecords( $this->request->query ,$accountId) ;
		$count   =  $this->Amazongrid->getProductFeedQuantityHistoryCounts( $this->request->query ,$accountId) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	
}