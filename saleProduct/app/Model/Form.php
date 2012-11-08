<?php
class Form extends AppModel {
	var $useTable = "sc_product_cost" ;
	
		
	/**
	 * 日志操作
	 */
	public function ajaxSave($params){
		$sqlId = $params['sqlId'] ;
		$sql = $this->getDbSql($sqlId) ;
		$sql = $this->getSql($sql,$params) ;
		$this->query($sql) ;
	}
	

}