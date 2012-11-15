<?php

class GridController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $uses = array('SqlUtils',"Grid");
    
    /**
     * 从配置文件加载sql
     */
    public function loadSql(){
    	 $this->SqlUtils->loadSqls() ;
    }
    
    
    /**
     * 通用查询方法
     */
    public function query(){
    	$query = $this->request->query ;
    	$recordSql = $this->SqlUtils->getRecordSql( $query) ;
    	$countSql = $this->SqlUtils->getCountSql( $query) ;
    	$records = $this->Grid->query($recordSql) ;
    	$count = $this->Grid->query($countSql) ;
    	
    	$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
    }
    
    
    function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('*');
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

	
	public function configitem(){
		$records=  $this->Grid->getConfigRecords( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( json_encode( $records ) )   ;

		return $this->response ;
	}

	
	public function functionItems(){
		
		$user =  $this->getCookUser() ;
		
		$records=  $this->Grid->getFunctionItemsRecords( $this->request->query ,$user) ;

		$this->response->type("json") ;
		$this->response->body(  json_encode($records) )   ;

		return $this->response ;
	}
	
}