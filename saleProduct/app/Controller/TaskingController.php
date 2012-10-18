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
	 * 获取日志信息显示
	 */
	public function listAll($accountId){
		$alls = $this->Tasking->listAll($accountId) ;
		
		$this->set("taskings",$alls) ;
	}
}