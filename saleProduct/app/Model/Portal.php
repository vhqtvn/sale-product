<?php
class Portal extends AppModel {
	var $useTable = 'sc_election_rule';
	
	function saveDeskConfig( $params ){
		$deskConfig = $params['deskConfig'] ;
		$loginId = $params['loginId'] ;
		
		$sql = "select * from sc_portal_config where login_id = '{@#loginId#}'" ;
		$config = $this->getObject($sql, $params) ;
		
		if(empty($config)){
			$sql = "insert into sc_portal_config(login_id,desk_config) values( '{@#loginId#}', '{@#deskConfig#}')" ;
			$this->exeSql($sql, $params) ;
		}else{
			$sql = "update  sc_portal_config set desk_config = '{@#deskConfig#}' where login_id=  '{@#loginId#}'" ;
			$this->exeSql($sql, $params) ;
		}
	}
	
	function loadDeskConfig($params){
		$sql = "select * from sc_portal_config where login_id = '{@#loginId#}'" ;
		$config = $this->getObject($sql, $params) ;
		if(empty( $config )) return "" ;
		return $config['DESK_CONFIG'] ;
	}
}