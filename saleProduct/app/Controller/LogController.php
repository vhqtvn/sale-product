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
	
	var $uses = array('Task', 'Config','Amazonaccount',"Utils");
	
	/**
	 * 获取日志信息显示
	 */
	public function getLog($id){
		$logs = $this->Task->getLogs($id) ;
		$this->response->type("json");
		$this->response->body(json_encode($logs));
		return $this->response;
	}
}