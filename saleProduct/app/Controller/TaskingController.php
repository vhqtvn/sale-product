<?php

/**
 * 执行任务列表
 */
class TaskingController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Tasking', 'Config','Amazonaccount',"Utils");
	
	/**
	 * tab也
	 */
	public function tab($accountId = null){
		$this->set("accountId",$accountId) ;
	}
	
	/**
	 * 获取日志信息显示
	 */
	public function listTasking($accountId = null){
		$this->set("accountId",$accountId) ;
		$alls = $this->Tasking->listAll($accountId) ;
		$this->set("taskings",$alls) ;
	}
	
	
	public function taskingGrid($accountId = null){
		$records =  $this->Tasking->getTaskingRecords( $this->request->query ) ;
		$count   =  $this->Tasking->getTaskingCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	public function taskedGrid($accountId = null){
		$records =  $this->Tasking->getTaskedRecords( $this->request->query ) ;
		$count   =  $this->Tasking->getTaskedCount( $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
	
	
	/**
	 * 获取日志信息显示
	 */
	public function listTasked($accountId = null){
		$this->set("accountId",$accountId) ;
		$alls = $this->Tasking->listAll($accountId) ;
		$this->set("taskings",$alls) ;
	}
	
	
	public function stop($id){
		$this->Tasking->stopByFront($id) ;
		
		$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
	}
}