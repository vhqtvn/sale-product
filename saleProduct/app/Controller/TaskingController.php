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

	
	/**
	 * 获取日志信息显示
	 */
	public function listTasked($accountId = null){
		$this->set("accountId",$accountId) ;
		$alls = $this->Tasking->listAll($accountId) ;
		$this->set("taskings",$alls) ;
	}
	
	
	public function stop($id,$isforce = null ){
		$this->Tasking->stopByFront($id,$isforce) ;
		
		$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
	}
}