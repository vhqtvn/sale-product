<?php

class GridController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('*');
	}

	public function product($id = null ){
		 $records=  null ;
		 $count   = null ;
		if( $id == null ){
			 $records=  $this->Grid->getProductRecords( $this->request->query  , $id ) ;
			 $count   =  $this->Grid->getProductCount( $this->request->query  , $id  ) ;
		}else{
			 $records=  $this->Grid->getTaskProductRecords( $this->request->query  , $id ) ;
		 	$count   =  $this->Grid->getTaskProductCount( $this->request->query  , $id  ) ;
		}
		
		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function productBlack($id = null ){
		$user =  $this->getCookUser() ;
		 $records=  null ;
		 $count   = null ;
		$records=  $this->Grid->getProductBlackRecords( $this->request->query  , $user ) ;
		$count   =  $this->Grid->getProductBlackCount( $this->request->query  , $user  ) ;

		
		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	

	public function seller(){
		 $records=  $this->Grid->getSellerRecords( $this->request->query ) ;
		 $count   =  $this->Grid->getSellerCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function upload(){
		 $records=  $this->Grid->getUploadRecords( $this->request->query ) ;
		 $count   =  $this->Grid->getUploadCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function sellerUpload(){
		 $records=  $this->Grid->getSellerUploadRecords( $this->request->query ) ;
		 $count   =  $this->Grid->getSellerUploadCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function saveFilterResult(){
		
		$user =  $this->getCookUser() ;
		
		$this->Grid->saveFilterResult( $this->request->data , $user ) ;
		/*print_r($this->request->data) ;
		
		echo $this->request->data["filterName"] ;
		
		$query = $this->request->data ;
		
		foreach ($query["querys"] as $value) {  
				if( gettype($value) == "array" ){
					  $key = $value["key"] ; 
					  $type = $value["type"] ;
					  $val  = $value["value"] ; 
					  $relation = $value['relation'] ;
					  
					  echo $val ;
				}
		}*/
		
		$this->response->type("json") ;
		$this->response->body( "success" )   ;

		return $this->response ;
	}


	public function rule(){
		
		 $records=  $this->Grid->getRuleRecords( $this->request->query ) ;
		 $count=  $this->Grid->getRuleCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
		  //return new CakeResponse( array('body'=> $array  , 'type' =>'json')  );
	}

	public function script(){
		 $records=  $this->Grid->getScriptRecords( $this->request->query ) ;
		 $count=  $this->Grid->getScriptCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function scriptitem(){
		 $records=  $this->Grid->getScriptRecords( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body(  json_encode( $records )  )   ;

		return $this->response ;
	}
	
	public function configitem(){
		$records=  $this->Grid->getConfigRecords( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body(  json_encode($records) )   ;

		return $this->response ;
	}

	public function config(){
		$records=  $this->Grid->getConfigRecords( $this->request->query ) ;
		$count=  $this->Grid->getConfigCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function users(){
		$records=  $this->Grid->getUsersRecords( $this->request->query ) ;
		$count=  $this->Grid->getUsersCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function groups(){
		$records=  $this->Grid->getGroupsRecords( $this->request->query ) ;
		$count=  $this->Grid->getGroupsCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function functionItems(){
		
		$user =  $this->getCookUser() ;
		
		$records=  $this->Grid->getFunctionItemsRecords( $this->request->query ,$user) ;

		$this->response->type("json") ;
		$this->response->body(  json_encode($records) )   ;

		return $this->response ;
	}
	
	public function functions(){
		$records=  $this->Grid->getFunctionsRecords( $this->request->query ) ;
		$count=  $this->Grid->getFunctionsCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function flow(){
		$records=  $this->Grid->getFlowRecords( $this->request->query ) ;
		$count=  $this->Grid->getFlowCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
	public function flowDetail($taskId = null ){
		
		$records=  $this->Grid->getFlowDetailRecords( $this->request->query ,$taskId) ;
		$count=  $this->Grid->getFlowDetailCount( $this->request->query ,$taskId) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;
		return $this->response ;
	}
	
}