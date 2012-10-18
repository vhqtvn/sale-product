<?php

/**
 * 正在执行任务管理
 */
class Tasking extends AppModel {
	var $useTable = "sc_tasking" ;
	
	/**
	 * 开始执行任务
	 * 
	 * 通过插入数据库标记任务开始
	 */
	public function start( $type , $asin , $accountId){
		$sql = "insert sc_tasking(task_type,asin,account_id,start_time,executor)
			values('$type','$asin','$accountId',NOW(),'')" ;
			
		$this->query($sql) ;
	}
	
	public function isStop($type , $asin , $accountId){
		$sql = "select * from sc_tasking 
			 		where task_type ='$type' and asin='$asin' and account_id = $accountId " ;
		
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->_queryCache[$sql] = null ;
		$s = $this->query($sql) ;
		if( count($s) >= 1 ){
			$stopFlag = $s[0]['sc_tasking']['FORCE_STOP'] ;
			return $stopFlag === 1 || $stopFlag === '1' ;
		}
		return false ;
	}
	
	/**
	 * 判断当前任务状态
	 */
	public function status($type , $asin , $accountId){
		$sql = "select * from sc_tasking 
			 		where task_type ='$type' and asin='$asin' and account_id = '$accountId'" ;
		$s = $this->query($sql) ;
		if(count($s) >= 1){
			return true ;
		}
		return false ;
	}
	
	/**
	 * 停止执行任务
	 */
	public function stop($type , $asin , $accountId){
		$sql = "delete from sc_tasking
				where task_type ='$type' and asin='$asin' and account_id = '$accountId'" ;
		$this->query($sql) ;
	}
	
	/**
	 * 列出所有的任务
	 */
	public function listAll($accountId){
		$sql = "select * from sc_tasking,sc_tasking_type where sc_tasking.task_type = sc_tasking_type.code
				and  account_id = '$accountId'" ;
		return $this->query($sql) ;
	}
}