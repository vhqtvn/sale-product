<?php

class SalegridController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript

	public function filter( $id = null ){
		//if( empty($id) ){
			$id = $this->request->query['id'] ;
		
		//}
		
		$records=  $this->Salegrid->getFilterRecords( $this->request->query , $id ) ;
		$count=  $this->Salegrid->getFilterCount( $this->request->query  , $id ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function filterTask4(){ //1\产品专员 2、产品经理 3、总经理 4、审批通过
		$user =  $this->getCookUser() ;
		$records=  $this->Salegrid->getFilterTask4Records( $this->request->query,$user ) ;
		$count=  $this->Salegrid->getFilterTask4Count( $this->request->query ,$user ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function filterTask($type){ //1\产品专员 2、产品经理 3、总经理 4、审批通过
		$user =  $this->getCookUser() ;
		$records=  $this->Salegrid->getFilterTaskRecords( $this->request->query ,$type,$user ) ;
		$count=  $this->Salegrid->getFilterTaskCount( $this->request->query ,$type,$user ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function purchasePlan($flag){
		$user =  $this->getCookUser() ;
		$records=  $this->Salegrid->getPurchasePlanRecords( $this->request->query ,$user,$flag ) ;
		$count=  $this->Salegrid->getPurchasePlanCount( $this->request->query ,$user,$flag ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function purchasePlanDetails(){
		$user =  $this->getCookUser() ;
		$records=  $this->Salegrid->getPurchasePlanDetailsRecords( $this->request->query ,$user ) ;
		$count=  $this->Salegrid->getPurchasePlanDetailsCount( $this->request->query ,$user ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function purchasePlanPrints(){
		$user =  $this->getCookUser() ;
		$records=  $this->Salegrid->getPurchasePlanPrintsRecords( $this->request->query ,$user ) ;
		$count=  $this->Salegrid->getPurchasePlanPrintsCount( $this->request->query ,$user ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function deletePurchasePlanDetails(){
		$user =  $this->getCookUser() ;
		$records=  $this->Salegrid->getDeletePurchasePlanDetailsRecords( $this->request->query ,$user ) ;
		$count=  $this->Salegrid->getDeletePurchasePlanDetailsCount( $this->request->query ,$user ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	
	
	//获取所有审批通过产品
	public function filterApply($id = null){
		$records=  $this->Salegrid->getFilterApplyRecords( $this->request->query , $id ) ;
		$count=  $this->Salegrid->getFilterApplyCount( $this->request->query  , $id ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
}