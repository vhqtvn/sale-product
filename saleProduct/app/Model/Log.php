<?php
class Log extends AppModel {
	var $useTable = "sc_product_cost" ;
	
		
	/**
	 * 日志操作
	 */
	public function savelog($taskId, $message){
		if(empty($taskId)){
			$taskId = "anomys" ;
		}
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
	
	function getTaskLogGridRecords($taskid,$query=null){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		
		$sql = "SELECT *
		FROM sc_exe_log where task_id = '$taskid'
		limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getTaskLogGridCount($taskid,$query=null){
		$sql = "SELECT count(*) FROM sc_exe_log where task_id = '$taskid'";
		$array = $this->query($sql);
		return $array ;
	}
}