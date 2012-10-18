<?php
class Log extends AppModel {
	var $useTable = "sc_product_cost" ;
	
		
	/**
	 * 日志操作
	 */
	public function savelog($taskId, $message){
		$message = $this->formatSqlParams($message) ;
		$sql = "insert into sc_exe_log(task_id,message) values('".$taskId."','".$message."')" ;
		$this->query($sql) ;
	}
	
	function clearlog($taskId){
		$sql = "delete from sc_exe_log where task_id = '".$taskId."'" ;
		$this->query($sql) ;
	}
	
	function getLogs($taskId){
		//delete
		$sql = "delete from sc_exe_log where task_id = '".$taskId."' and status = 'read'" ;
		$this->query($sql) ;
		
		$sql = "update sc_exe_log set status='read' where task_id = '".$taskId."'" ;
		$this->query($sql) ;
		
		$sql = "select * from sc_exe_log where task_id = '".$taskId."' and status = 'read'" ;
		return $this->query($sql) ;
	}
}