<?php
class Suggest extends AppModel {
	var $useTable = 'sc_user';
	
	function saveSuggest($params){
		if(empty($params['id'])){
			$this->exeSql("sql_suggest_insert",$params) ;
		}else{
			$this->exeSql("sql_suggest_update",$params) ;
		}
	}
}