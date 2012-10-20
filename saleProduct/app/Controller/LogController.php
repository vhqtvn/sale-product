<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

App :: import('Vendor', 'Snoopy');
App :: import('Vendor', 'simple_html_dom');
App :: import('Vendor', 'Amazon');

class LogController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Task', 'Config','Amazonaccount',"Utils","Log");
	
	/**
	 * 获取日志信息显示
	 */
	public function getLog($id){
		$logs = $this->Task->getLogs($id) ;
		$this->response->type("json");
		$this->response->body(json_encode($logs));
		return $this->response;
	}
	
	public function taskLog($taskId){
		$this->set("taskId",$taskId) ;
	}
	
	public function taskLogGrid($taskId){
		$records =  $this->Log->getTaskLogGridRecords($taskId, $this->request->query ) ;
		$count   =  $this->Log->getTaskLogGridCount($taskId, $this->request->query ) ;

		$this->response->type("json") ;
		$this->response->body( "{record:".json_encode( $records ) .",count:".json_encode($count)."}" )   ;

		return $this->response ;
	}
}