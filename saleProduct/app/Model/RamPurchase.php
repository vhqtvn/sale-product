<?php
class RamPurchase extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function createRam($params){
		$defaultCode = "RMA-P".date("Ymd")."-".date("His")  ;
		$sqlParams = array( "code"=>$defaultCode ,
				"purchaseId"=>$params['purchaseId'] ,
				'rmaType'=>'P',
				'rmaNum'=>$params['rmaNum'],
				"causeCode"=>$params['causeCode'] ,
				"charger"=>$params['rmaCharger'],
				"policyCode"=>'',
				"loginId"=>'',
				"status"=>'', //状态为空
				"memo"=>'' ) ;
		
		$sql = "select * from sc_ram_event where purchase_id = '{@#purchaseId#}' and cause_code='{@#causeCode#}' and rma_type='{@#rmaType#}'" ;
		$ram = $this->getObject($sql,$sqlParams ) ;
		
		if( !empty($ram) ){
			$sqlParams['id'] = $ram['ID'] ;
			$this->exeSql("sql_ram_event_update",$sqlParams) ;
		}else{
			$this->exeSql("sql_ram_event_insert",$sqlParams) ;
		}
	}

}