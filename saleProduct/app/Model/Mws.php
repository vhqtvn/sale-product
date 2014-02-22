<?php
class Mws extends AppModel {
	var $useTable = "sc_seller" ;
	
	function exeService($params){
		$accountId = $params['accountId'] ;
		if( !empty($accountId) ){
			$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
			$account = $Amazonaccount->getAccountIngoreDomainById($accountId)  ;
			$account = $account[0]['sc_amazon_account']  ;
			
		}
	}
	
	
}