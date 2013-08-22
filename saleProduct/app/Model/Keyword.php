<?php
class Keyword extends AppModel {
	var $useTable = "sc_keyword_plan" ;
	
	public function savePlan( $params ){
		if( isset( $params['id'] )  ){//update
			$this->exeSql("sql_keyword_plan_update", $params) ;
		}else{//insert
			$this->exeSql("sql_keyword_plan_insert", $params) ;
		}
	}

}